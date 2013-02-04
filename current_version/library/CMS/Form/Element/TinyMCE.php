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

class CMS_Form_Element_TinyMCE extends CMS_Form_Element_Textarea
{
	// Configuration Toolbars
    
    // Groupement des boutons (toolbar 1)
    protected $code					= array('code');
    protected $textStyle			= array('bold','italic','underline','strikethrough');
    protected $textAlignment		= array('justifyleft','justifycenter','justifyright','justifyfull');
    protected $style				= array('styleselect','fontsizeselect');
	protected $print				= array('print');
	protected $pdw					= array('pdw_toggle');

	// Groupement des boutons (toolbar 2)
    protected $textColor			= array('forecolor','backcolor');
    protected $textList				= array('bullist','numlist');
    protected $textIndent			= array('outdent','indent');
    protected $textHook				= array('link','unlink','anchor','cleanup');
    protected $textManip			= array('cut','copy','paste','pastetext','pasteword');
    protected $textSearch			= array('search','replace');
    protected $textReview			= array('undo','redo');
	protected $preview				= array('preview');
	protected $spellchecker			= array('spellchecker');

	// Groupement des boutons (toolbar 3)
    protected $mediaInsert			= array('insertimage','image','media','charmap','advhr');
    protected $textIndice			= array('sub','sup');
    protected $tinyManip			= array('hr','removeformat','visualaid');
    protected $insertTable			= array('tablecontrols');
	protected $insertDateTime		= array('insertdate','inserttime');
	protected $fullScreen			= array('fullscreen');

	// Groupement de boutons pour chaque toolbar
    protected $toolbar1				= array('textStyle', 'textAlignment', 'textList','style','textColor','pdw');
    protected $toolbar2				= array('textHook', 'mediaInsert', 'insertTable', 'preview','spellchecker');
    protected $toolbar3				= array('textReview','textIndent', 'tinyManip' , 'textManip','textSearch','textIndice','insertDateTime','print','code','fullScreen');
    
    // Toolbars pour TinyMCE
    protected $toolbars				= array('toolbar1', 'toolbar2', 'toolbar3');
     
    protected $_hasCode				= true;
    protected $_hasTextStyle		= true;
    protected $_hasTextAlignment	= true;
    protected $_hasStyle			= true;
	protected $_hasPrint			= true;
	protected $_hasPdw				= true;
    
    protected $_hasTextColor		= true;
    protected $_hasTextList			= true;
    protected $_hasTextIndent		= true;
    protected $_hasTextHook			= true;
    protected $_hasTextManip		= true;
    protected $_hasTextSearch		= true;
    protected $_hasTextReview		= true;
	protected $_hasPreview			= true;
	protected $_hasSpellchecker		= true;
    
    protected $_hasMediaInsert		= true;
    protected $_hasTextIndice		= true;
    protected $_hasTinyManip		= true;
    protected $_hasInsertTable		= true;
	protected $_hasInsertDateTime	= true;
	protected $_hasFullScreen		= true;
	
	// Configuration TinyMCE
	protected $_tinyMCEOptions		= array(
		'verify_html'				=> 'true',
		'relative_urls'				=> 'false',
		'height'					=> '"250px"'
	);
	
	protected $_automaticallyAddControl = true;

	/**
	 * Permet de désactiver certaines groupement de boutons de TinyMCE
	 * 
	 * @param array|string $option nom(s) des option(s) à régler
	 * @param bool $flagOption Statut des options du paramètre précédent
	 * @param boot $flagAllOptions Statut de toutes les autres options
	 */
    public function setToolbarsOptions($option, $flagOption = false, $flagAllOptions = true)
    {
		if($flagAllOptions !== true) {
			$this->setAllToolbarsOptions($flagAllOptions);
		}
		
        if(!is_array($option)){
            $option = array($option);
        }

        foreach($option as $o) {
            if(property_exists(__CLASS__, '_has' . ucfirst($o))) {
                $this->{'_has' . ucfirst($o)} = $flagOption;
            }
            else
                throw new Exception(_t('This option doesn\'t exists'));
        }
	}
	
	public function setAllToolbarsOptions($flag = true) {
		
		// Pour chaque TOOLBAR
		foreach($this->toolbars as $ts) {
			// Pour chaque GROUPEMENT de BOUTONS
			foreach($this->{$ts} as $t) {
				// option courante activé ?
				$this->{'_has' . ucfirst($t)} = $flag;
			}
		}
	}
	
	public function automaticallyAddControl($flag = true)
    {
		$this->_automaticallyAddControl = $flag;
	}
	
	public function setTinyMCEOptions($option, $value)
    {
		$this->_tinyMCEOptions[$option] = $value;
	}
	
	public function render(Zend_View_Interface $view = null) 
	{
		$this->setAttrib('class', 'tinyMCE-'.$this->_name);

		$j = 1;
        $toolbarsContent = array();

		// Pour chaque TOOLBAR
		foreach($this->toolbars as $ts) {	
			$toolbarsContent[$j] = '';
			
			// Pour chaque GROUPEMENT de BOUTONS
			foreach($this->{$ts} as $elem){
				
				// option courante activé ?
				if($this->{'_has' . ucfirst($elem)} == true){
					$toolbarsContent[$j] .= implode(',' ,$this->{$elem}) . ',|,';
				}
			}
			// Retrait du dernier séparateur
			$toolbarsContent[$j] = substr($toolbarsContent[$j], 0, -3);
			$j++;
		}
		
		CMS_Application_ProcessLayout::getInstance()->appendJsFile(COMMON_LIB_PATH . '/lib/tiny_mce/tiny_mce.js'); 
		$output = 'tinyMCE.init({';
		
		$output .= ($this->_automaticallyAddControl !== true) ? 'mode : "none",' : 'mode : "specific_textareas",	editor_selector : "tinyMCE-'.$this->_name.'",';
			
		$output .='
			language : "' . CURRENT_LANG_CODE . '",
			file_browser_callback : "mcimagemanager",
			
			theme : "advanced",
			skin : "o2k7",
			skin_variant : "silver",
		
			
			plugins : "fullscreen,pdw,style,table,advhr,advimage,advlink,inlinepopups,media,searchreplace,contextmenu,paste,visualchars,advlist,autosave,filemanager,imagemanager,preview,print,insertdatetime,spellchecker",
			
			theme_advanced_buttons1 : "'.$toolbarsContent[1].'",
            theme_advanced_buttons2 : "'.$toolbarsContent[2].'",
			theme_advanced_buttons3 : "'.$toolbarsContent[3].'",
			theme_advanced_resizing : true,
			theme_advanced_font_sizes : "xx-small=8px,x-small=10px,small=12px,medium=14px,large=18px,x-large=24px,xx-large=36px",
	
			// Drop lists for link/image/media/template dialogs
			external_link_list_url : "'.BASE_URL.'/ajax/seo/getexternallistlink",
			content_css : "'.BASE_URL.'/lib/resetcss/reset.min.css,'.BASE_URL.'/skins/' . SKIN_FRONT.'/css/content.css",
			';
			
			$output .= (defined('TINYMCE_STYLES')) ? 'style_formats : ' . TINYMCE_STYLES . ',' : '';
			
			foreach ( $this->_tinyMCEOptions as $key => $val) {
				$output .= $key . ' : ' . $val . ',';
			}
			
			$output .= '
				// Configuration plugin pdw : cache les toolbar 2 et 3
				pdw_toggle_on : 1,
				pdw_toggle_toolbars : "2,3",
				// Configuration plugin spellchecker : Français par default
				spellchecker_languages : "+French=fr, English=en"
			});';	
			
        CMS_Application_ProcessLayout::getInstance()->appendJsScript($output);
		
		return parent::render($view);
	}
}