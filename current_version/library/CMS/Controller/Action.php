<?php

/**
 * CMS Aïdoo
 *
 * Copyright (C) 2013  Flamant Bleu Studio
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 */

abstract class  CMS_Controller_Action extends Zend_Controller_Action 
{
	protected $_context = array("html", "json");
	
	protected static $_rights 			= array(
															'nomExemple1' 			=> array('mod_exemple' => 'edit_exemple'),
															'nomExemple2.OR' 		=> array(array('mod_exemple' => 'edit_exemple'), array('mod_exemple' => 'create_exemple')),
															'nomExemple3.AND' 	=> array(array('mod_exemple' => 'edit_exemple'), array('mod_exemple' => 'create_exemple'))
														);
	
	public function init()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$this->checkRigths();
		
		foreach ($this->_context as $context){
			$ajaxContext->addActionContext($this->getRequest()->get('action'), $context)->setSuffix($context, '')->initContext();
		}
		
		$ajaxContext->addActionContext('error', 'json')->setSuffix('json', '')->initContext();
				
		/* Generation de la clef de sécurité pour le JSON AJAX */
		if (!isset($_SESSION['ajax_apiKey_expire']) || isset($_SESSION['ajax_apiKey_expire']) && $_SESSION['ajax_apiKey_expire'] <= time()) {
			$ajax_apiKey = CMS_Application_Tools::generateApiKey();
			$_SESSION['ajax_apiKey'] = $ajax_apiKey;
			$_SESSION['ajax_apiKey_expire'] = time() + (15 * 60);
			$this->view->ajax_apiKey = $ajax_apiKey;
		} else if(isset($_SESSION['ajax_apiKey'])) {
			$this->view->ajax_apiKey = $_SESSION['ajax_apiKey'];
		}
		
		$this->view->backAcl = CMS_Acl_Back::getInstance();
	}
	
	/**
	* Return the Request object
	*
	* @return CMS_Controller_Request_Http
	*/
	public function getRequest()
	{
		return parent::getRequest();
	}
	
	public function checkRigths()
	{
		if ($this->_request->getParam('_isAdmin')) {
			$auth = Zend_Auth::getInstance();
			if($auth->hasIdentity())
			{	
				$actionName = $this->getRequest()->get('action');
				$acl = CMS_Acl_Back::getInstance();
				$aclok = true;
				
				// If only one right has to be check
				if (!empty(static::$_rights[$actionName]) && count(static::$_rights[$actionName]) == 1){
					if (!$acl->hasPermission(key(static::$_rights[$actionName]), reset(static::$_rights[$actionName]))) {
						$aclok = false;
					}
				// If many rights has to be check in OR operator
				}elseif (!empty(static::$_rights[$actionName.'.OR'])){
					$aclok = false;
					foreach (static::$_rights[$actionName.'.OR'] as $tabRight) {
						if ($acl->hasPermission(key($tabRight), reset($tabRight))) {
							$aclok = true;
							break;
						}
					}
					// If many rights has to be check in AND operator				
				} elseif (!empty(static::$_rights[$actionName.'.AND'])){ 
					foreach (static::$_rights[$actionName.'.AND'] as $tabRight) {
						if (!$acl->hasPermission(key($tabRight), reset($tabRight))) {
							$aclok = false;
							break;
						}
					}
				} else {
// 					TODO : ajouter cette exception, mais pour un soucis de rétro-compatibilité, ne pas ajouter avant d'avoir
// 					réalisé une passe sur TOUT les controllers
// 					throw new Zend_Controller_Action_Exception(_t('You have to explicit action\'s right'), 404);
				}

				if (!$aclok){
					_error(_t("Insufficient rights"));
					$this->_redirect($this->_helper->route->full('admin'));
				}
			}
		}
	}
	
	/**
	 * Redirection vers l'accueil de l'admin si l'utilisateur n'a pas les droits spécifiés
	 * @param string $name nom de la permission
	 * @param string $right niveau de permission
	 */
	protected function redirectIfNoRights($name, $right)
	{
		$backAcl = CMS_Acl_Back::getInstance();
		
		if (!$backAcl->hasPermission($name, $right)) {
			_error(_t("Insufficient rights"));
			$this->_redirect($this->_helper->route->full('admin'));
		}
	}
	
	/**
	 * Ferme la fancybox courante (iframe), et redirige la fenêtre parente si le param 'url' est fournit
	 * @param string $url Page à charger dans la fenêtre parente
	 */
	public function closeFancybox($url = null)
	{
		if ($this->_helper->layout()->getLayout() != 'lightbox')
			throw new Exception('Invalid layout');
		
		if ($url !== null)
			echo '<html><script language="javascript">parent.location.href="'.BASE_URL.$url.'";</script></html>';
		else
			echo '<script language="javascript">parent.$.fancybox.close();</script>';
	}
	
	public function disableSmartyCache()
	{
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$viewRenderer->disableCache();
	}
	
	public function suffixSmartyCache($suffix)
	{
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$viewRenderer->setSuffixCacheId($suffix);
	}
	
	public function dispatch($action) {
		// Notify helpers of action preDispatch state
        $this->_helper->notifyPreDispatch();

        $this->preDispatch();
        if ($this->getRequest()->isDispatched()) {
            if (null === $this->_classMethods) {
                $this->_classMethods = get_class_methods($this);
            }
			
            // If pre-dispatch hooks introduced a redirect then stop dispatch
            // @see ZF-7496
            if (!($this->getResponse()->isRedirect())) {
                // preDispatch() didn't change the action, so we can continue
                if ($this->getInvokeArg('useCaseSensitiveActions') || in_array($action, $this->_classMethods)) {
                    if ($this->getInvokeArg('useCaseSensitiveActions')) {
                        trigger_error('Using case sensitive actions without word separators is deprecated; please do not rely on this "feature"');
                    }
                    /**
                     * @todo : call only if cache smarty no exist
                     */
                    $this->$action();
                } else {
                    $this->__call($action, array());
                }
            }
            $this->postDispatch();
        }

        // whats actually important here is that this action controller is
        // shutting down, regardless of dispatching; notify the helpers of this
        // state
        $this->_helper->notifyPostDispatch();
	}
}