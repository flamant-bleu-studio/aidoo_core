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

class Menu_Model_DbTable_Menu extends CMS_Model_MultiLang {
	
	protected $_name = 'menu';
	
	protected $_primaryKey 	= "id_menu";
	
	protected $_values 				= array('menu_id', 'type', 'link', 'image', 'hidetitle', 'loadAjax', 'access', 'active', 'tblank', 'cssClass', 'lft', 'rgt', 'level');
	protected $_translatedValues 	= array('label', 'subtitle');
	
	protected $_columns =
		array(
			'id_menu',
			'menu_id',
			'type',
			'link',
			'label',
			'subtitle',
			'image',
			'hidetitle',
			'access',
			'active',
			'tblank',
			'cssClass',
			'loadAjax',
			'lft',
			'rgt'
		);
	
	public function migrationMultiLang() {
		
		$db = $this->getAdapter();
		
		$items = $db->query('SELECT * FROM 1_menu_back')->fetchAll(PDO::FETCH_OBJ);
		
		foreach ($items as $item) {
			
			$datas_common = array(
				'id_menu' 	=> $item->id,
				'menu_id' 	=> $item->menu_id,
				'type'		=> $item->type,
				'link'		=> $item->link,
				'image'		=> $item->image,
				'hidetitle' => $item->hidetitle,
				'access'	=> $item->access,
				'active'	=> $item->active,
				'tblank'	=> $item->tblank,
				'cssClass' 	=> $item->cssClass,
				'lft'		=> $item->lft,
				'rgt'		=> $item->rgt
			);
			
			$datas_lang_fr = array(
				'id_menu' => $item->id,
				'id_lang' => 1,
				'label'	  => $item->label,
				'subtitle' => $item->subtitle
			);
			
			$db->insert('1_menu', $datas_common);
			$db->insert('1_menu_lang', $datas_lang_fr);
		}
	}
	
	public function maj_menu_v2()
	{
		die;
		/**
		 
		 DROP TABLE IF EXISTS `1_menu`;
CREATE TABLE `1_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `link` varchar(254) CHARACTER SET latin1 DEFAULT NULL,
  `label` varchar(254) NOT NULL,
  `subtitle` varchar(254) DEFAULT NULL,
  `image` varchar(150) DEFAULT NULL,
  `hidetitle` tinyint(1) NOT NULL DEFAULT '0',
  `access` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `tblank` tinyint(1) NOT NULL DEFAULT '0',
  `cssClass` varchar(50) DEFAULT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
		 
		 */
		
		$db = $this->getAdapter();
		
		$results_menus = $db->query("SELECT * FROM 1_menu_old");
		$menus = $results_menus->fetchAll();
		
		foreach ($menus as $menu)
		{
			$results_root = $db->query("SELECT * FROM 1_menu_items WHERE label = 'ROOT' AND menu_id = '".$menu['id']."'");
			$root = $results_root->fetchAll();
			
			$db->query("INSERT INTO 1_menu (menu_id, label, subtitle, type, lft, rgt) VALUES('0', '".$menu['title']."', '".$menu['description']."', '0', '".$root[0]['lft']."', '".$root[0]['rgt']."')");
			
			$lastId = $db->lastInsertId();
			
			$db->update(
				"1_menu",
				array("menu_id" => $lastId),
				"id = '".$lastId."'"
				 
			);
			
			$results_items = $db->query("SELECT * FROM 1_menu_items WHERE menu_id = '".$menu['id']."' AND label != 'ROOT'");
			$items = $results_items->fetchAll();
			
			foreach ($items as $item)
			{
				if($item["type"] == "folder")
				{
					$type = 7;
					$link = null;
				}
				elseif($item["type"] == "fullurl")
				{
					$type = 2;
					$link = $item["url"];
				}
				else
				{
					$type = 1;
					if( $item["url"] == "/")
					{
						$link = 1;
					}
					else
					{
						$obj = CMS_Page_Object::get($item["url"]);
						$link = $obj->id;
					}
				}
				
				$db->query("INSERT INTO 1_menu(menu_id, type, link, label, subtitle, image, hidetitle, access, active, tblank, cssClass, lft, rgt) VALUES('".$lastId."', '".$type."', '".$link."', '".addslashes($item['label'])."', '".addslashes($item['subtitle'])."', '".$item['image']."', '".$item['hidetitle']."', '".$item['access']."', '".$item['active']."', '".$item['tblank']."', '".$item['cssclass']."', '".$item['lft']."', '".$item['rgt']."')");
			}
			
		}
	}
	
	/*
	private function getSqlColumn($prefix = '')
	{
		if( empty($this->_columns) )
			throw new Zend_Exception(_t('Missing name columns'));
		
		$return = '';
		
		$nbr = count($this->_columns)-1;
		
		foreach($this->_columns as $key => $column)
		{
			if( $prefix ) // Préfixe
				$return .= $prefix.'.';
			
			$return .= $column; // Nom de la collone
			
			if( $key < $nbr)
				$return .= ',';
		}
		
		return $return;
	}*/
	

	/*
	 * Fonctions de RÉCUPÉRATION
	 */
		
	public function getMenus()
	{
		return $this->getAdapter()->fetchAll('SELECT id_menu FROM ' . $this->getTableName() . ' WHERE lft = 1', null, PDO::FETCH_COLUMN);
	}
	public function getItemsByFolder($menu_id, $id_folder, $filters = array())
	{
		if($id_folder === null || $menu_id === null)
			throw new Zend_Exception(_t('Missing parameter'));
		
		$sql = 'SELECT node.*, lang.*, (COUNT(parent.id_menu) - (sub_tree.level + 1)) AS level
				FROM 
				
					('.$this->getTableName().' AS node 
					LEFT JOIN '.$this->getTableName().'_lang AS lang
					ON node.' . $this->_primaryKey . ' = lang.' . $this->_primaryKey . ' ),
						
				     '.$this->getTableName().' AS parent,
				     '.$this->getTableName().' AS sub_parent,
				     (
		                SELECT node.id_menu, node.menu_id, (COUNT(parent.id_menu) - 1) AS level
		                FROM '.$this->getTableName().' AS node,
		               	  	 '.$this->getTableName().' AS parent
		                WHERE node.lft BETWEEN parent.lft AND parent.rgt
		                AND node.id_menu = ?
		                AND node.menu_id = ?
		                AND parent.menu_id = ?
		                GROUP BY node.id_menu
		                ORDER BY node.lft
				     ) AS sub_tree
				     
				WHERE node.lft BETWEEN parent.lft AND parent.rgt
				        AND node.lft BETWEEN sub_parent.lft AND sub_parent.rgt
				        AND sub_parent.id_menu = sub_tree.id_menu
				        AND node.menu_id = ?
				        AND parent.menu_id = ?
				GROUP BY node.id_menu, lang.id_lang
				ORDER BY node.lft;';
		
		$params = array( $id_folder, $menu_id, $menu_id, $menu_id, $menu_id );
		
		return $this->prepareFieldsForLoad($this->getAdapter()->fetchAll($sql, $params, PDO::FETCH_OBJ));
	}
	public function getItemsByMenu($id, $filters = array())
	{
		if($id === null)
			throw new Zend_Exception(_t('Missing parameter'));
		
		$cache = CMS_Cache::getInstance();
		$id_cache = 'menu_'.$id.'_'.md5($id.serialize($filters));
		
		if ($cache->exist($id_cache)) {
			$result = $cache->get($id_cache);
		}
		else {
			$sql = 'SELECT node.*, lang.*, (COUNT(parent.id_menu)-1) AS level
			
					FROM ('.$this->getTableName().' AS node 
							LEFT JOIN '.$this->getTableName().'_lang AS lang
							ON node.' . $this->_primaryKey . ' = lang.' . $this->_primaryKey . ' ),
					
					'.$this->getTableName().' AS parent
									
					WHERE node.lft BETWEEN parent.lft AND parent.rgt
					
					      AND node.menu_id   = ?
					      AND parent.menu_id = ?
					      
					GROUP BY node.id_menu, lang.id_lang
					ORDER BY node.lft; ';
			
			$result = $this->getAdapter()->fetchAll($sql, array( $id, $id ), PDO::FETCH_OBJ);
			$cache->set($id_cache, $result);
		}
		
		return $this->prepareFieldsForLoad($result);
	}
	
	
	
	/*
	 * Fonctions d'AJOUT
	 */
	
	public function addMenu($datas = array())
	{
		if($datas['label'] === null || $datas['type'] === null)
			throw new Zend_Exception(_t('Missing parameter'));
		
		$db = $this->getAdapter();

		$id = parent::insert(array(
			'label' 	=> $datas['label'],
			'subtitle' 	=> $datas['subtitle'],
			'type' 		=> $datas['type'],
			'lft' 		=> '1',
			'rgt' 		=> '2'
		), false);
		
		$db->update(
			$this->getTableName(), 
		 	array(
				'menu_id' => $id
			),
			$db->quoteInto('id_menu = ?', $id)
		 );
		
		return $id;
	}
	
	public function addItem($datas = array())
	{
		$datas["menu_id"] 	= (int)$datas["menu_id"];
		$datas["type"] 		= (int)$datas["type"];
		
		if(!$datas["menu_id"] || !$datas["label"] || !$datas["access"] || !$datas["type"])
			throw new Zend_Exception(_t('Missing parameter'));
		
		// Ajout de "http://" si protocole non précisé 
		if( $datas["type"] == Menu_Object_Item::$TYPE_EXTERNAL_LINK || $datas["type"] == Menu_Object_Item::$TYPE_FOLDER_EXTERNAL )
		{
			if( strpos($datas["link"], "mailto") !== 0 )
			{
				if( !preg_match("#^([a-zA-Z]+):\/\/#", $datas["link"])){
					$datas["link"] = "http://".$datas["link"];
				}
			}
		}
		
		$parent_id = ($datas['parent_id']) ? $datas['parent_id'] : $datas['menu_id'];
		
		$db = $this->getAdapter();
		
		try
		{
			$db->beginTransaction();
			
			$db->query("SELECT @myRight := rgt FROM " . $this->getTableName() . " WHERE id_menu = ? AND menu_id = ?", array($parent_id, $datas['menu_id']));
			
			$db->update(
				$this->getTableName(),
				array( "rgt" => new Zend_Db_Expr("rgt + 2") ),
				array( "rgt >= @myRight", "menu_id = '".$datas['menu_id']."'" )
			);
			
			$db->update(
				$this->getTableName(),
				array( "lft" => new Zend_Db_Expr("lft + 2") ),
				array( "lft > @myRight", "menu_id = '".$datas['menu_id']."'" )
			);
			
			$id = parent::insert(array(
				"menu_id" 	=> $datas['menu_id'],
				"type" 		=> $datas['type'],
				"link" 		=> $datas['link'],
				"label" 	=> $datas['label'],
				"subtitle" 	=> $datas['subtitle'],
				"image" 	=> $datas['image'],
				"hidetitle" => $datas['hidetitle'] ? $datas['hidetitle'] : 0,
				"loadAjax" => $datas['loadAjax'] ? $datas['loadAjax'] : 0,
				"access" 	=> $datas['access'],
				"tblank" 	=> $datas['tblank'] ? $datas['tblank'] : 0,
				"cssClass" 	=> $datas['cssClass'],
				"lft" 		=> new Zend_Db_Expr('@myRight'),
				"rgt" 		=> new Zend_Db_Expr('@myRight+1')
			), false);

			$db->commit();
		}
		catch (Exception $e)
		{
			$db->rollBack();
			throw new Zend_Db_Exception($e->getMessage(), $e->getCode());
		}
		
		return $id;
	}
	
	
	
	/*
	 * Fonctions de SUPPRESSION
	 */
	
	public function deleteMenu($menu_id)
	{
		if(!$menu_id)
			throw new Zend_Exception(_t('Missing parameter'));
		
		$return = $this->getAdapter()->delete($this->getTableName(), "menu_id = '".$menu_id."'" );
		
		return $return;
	}
	public function deleteItem($id)
	{
		if(!$id)
			throw new Zend_Exception(_t('Missing parameter'));
		
		$db = $this->getAdapter();
				
		try
		{
			$menu_id = (int)$db->fetchOne("SELECT menu_id FROM ". $this->getTableName() ." WHERE id_menu = ?", $id);
			
			$db->beginTransaction();
			
			// /!\ Delete children if folder 
			$db->query("SELECT @myLeft := lft, @myRight := rgt, @myWidth := rgt - lft + 1 FROM " . $this->getTableName() . " WHERE  id_menu = ? AND menu_id = ?", array($id, $menu_id));
			
			parent::delete(array(
				"menu_id" => $menu_id,
				array("lft BETWEEN @myLeft AND @myRight")
			));
			
			$db->update(
				$this->getTableName(),
				array( "rgt" => new Zend_Db_Expr("rgt - @myWidth") ),
				array( "rgt > @myRight", "menu_id = '".$menu_id."'" )
			);
			
			$db->update(
				$this->getTableName(),
				array( "lft" => new Zend_Db_Expr("lft - @myWidth") ),
				array( "lft > @myRight", "menu_id = '".$menu_id."'" )
			);
			
			$db->commit();
		}
		catch (Exception $e)
		{
			$db->rollBack();
			throw new Zend_Db_Exception($e->getMessage(), $e->getCode());
		}
	}
	public function deleteFolder($id, $deleteChildren = false)
	{
		if(!$id)
			throw new Zend_Exception(_t('Missing parameter'));
		
		if( $deleteChildren ) // Delete children of folder
			$this->deleteItem($id);
		else {
			
			$db = $this->getAdapter();
			
			try {
				
				$menu_id = (int)$db->fetchOne("SELECT menu_id FROM ". $this->getTableName() ." WHERE id_menu = ?", $id);
				
				$db->beginTransaction();
				
				$db->query("SELECT @myLeft := lft, @myRight := rgt, @myWidth := rgt - lft + 1 FROM " . $this->getTableName() . " WHERE id_menu = ?", array($id));
				
				parent::delete(array(
					"menu_id" => $menu_id,
					array("lft = @myLeft")
				));
				
				$db->update(
					$this->getTableName(),
					array(
						"rgt" => new Zend_Db_Expr("rgt - 1"),
						"lft" => new Zend_Db_Expr("lft - 1"),
					),
					array( "lft BETWEEN @myLeft AND @myRight", "menu_id = '".$menu_id."'" )
				);
				
				$db->update(
					$this->getTableName(),
					array(
						"rgt" => new Zend_Db_Expr("rgt - 2")
					),
					array( "rgt > @myRight", "menu_id = '".$menu_id."'" )
				);
				
				$db->update(
					$this->getTableName(),
					array(
						"lft" => new Zend_Db_Expr("lft - 2")
					),
					array( "lft > @myRight", "menu_id = '".$menu_id."'" )
				);
				
				$db->commit();
			}
			catch (Exception $e) {
				$db->rollBack();
				throw new Zend_Db_Exception($e->getMessage(), $e->getCode());
			}
		}
	}
	
		
	
	/** OTHERS **/
	
	/**
	 * Enable OR Disable item
	 * @param int $id
	 * @param bool $enable (true = enable, false = disable)
	 */
	public function active($id, $enable = true)
	{
		if(!$id)
			throw new Zend_Exception(_t('Missing parameter'));
		
		$active = $enable ? 1 : 0;
		
		$return = $this->getAdapter()->update(
			$this->getTableName(),
			array("active" => $active),
			"id_menu = '".$id."'"
		);
		
		return $return;
	}
	

	/** NESTED TREE **/
	
	public function updateParent($itemId, $parentId) {
		$row = new Menu_Object_Item((int)$itemId, "all");
		
		if ($row) 
		{
			$destItem = new Menu_Object_Item((int)$parentId, "all");
			
			if($destItem) 
			{
				if (($destItem->lft > $row->lft) && ($destItem->rgt < $row->rgt))
					return false;
				
				$pos = $this->_moveToLastChild($row,$destItem);
				$row->lft = $pos['l'];
				$row->rgt = $pos['r'];
				$row->save();
				
				return true;
			}
		}
		
		return false;
	}
	
	public function moveItemsToPreviousSibling($srcId, $destId)
	{
		$scrItem = new Menu_Object_Item((int)$srcId, "all");
		$destItem = new Menu_Object_Item((int)$destId, "all");
		
		if ($scrItem && $destItem)
		{
			$newpos = $this->_moveToPreviousSibling($scrItem,$destItem);
			$scrItem->lft = $newpos['l'];
			$scrItem->rgt = $newpos['r'];
			
			$scrItem->save();
			
			return true;
		}
		else
		{
			throw new Zend_Exception("Error moving menu items");
			return false;
		}
		
		return false;
	} 
	
	private function _moveToNextSibling($srcNode, $destNode)
	{
		return $this->_moveSubtree($srcNode, $destNode->rgt+1);
	}
	private function _moveToPreviousSibling($srcNode, $destNode)
	{
		return $this->_moveSubtree($srcNode, $destNode->lft);
	}
	private function _moveToFirstChild($srcNode, $destNode)
	{
		return $this->_moveSubtree($srcNode, $destNode->lft+1);
	}
	private function _moveToLastChild($srcNode, $destNode)
	{
		return $this->_moveSubtree($srcNode, $destNode->rgt);
	}
	private function _moveSubtree($srcNode, $dst)
	{
		$treesize = $srcNode->rgt-$srcNode->lft+1;
	  	
		$this->_shiftRLValues($dst, $treesize, $srcNode->menu_id);
		if($srcNode->lft >= $dst) // src was shifted too?
		{
			$srcNode->lft += $treesize;
			$srcNode->rgt += $treesize;
		}
		
		/* Now there is enough room next to target to move the subtree */
		$newpos =  $this->_shiftRLRange($srcNode->lft, $srcNode->rgt, $dst-$srcNode->lft, $srcNode->menu_id);
		
		/* Correct values after source */
		$this->_shiftRLValues($srcNode->rgt+1, -$treesize, $srcNode->menu_id);
	  	
		if($srcNode->lft <= $dst) // dst was shifted too?
		{
			$newpos['l'] -= $treesize;
			$newpos['r'] -= $treesize;
		}
		
		return $newpos;
	}
	private function _shiftRLValues($first, $delta, $menuId)
	{
		$db = Zend_Registry::get('db');
		
		$db->query("UPDATE ". $this->getTableName()
				 ." SET lft = lft+".$delta
				 ." WHERE menu_id = ".$menuId." AND lft >= ".$first);
				 
		$db->query("UPDATE ". $this->getTableName()
				 ." SET rgt = rgt+".$delta
				 ." WHERE menu_id = ".$menuId." AND rgt >= ".$first);
	}
	
	private function _shiftRLRange($first, $last, $delta, $menuId)
	{
		$db = Zend_Registry::get('db');
		
		$db->query("UPDATE ". $this->getTableName()
				 ." SET lft = lft+".$delta
				 ." WHERE lft >= ".$first." AND menu_id = ".$menuId." AND lft <= ".$last);
				
		$db->query("UPDATE ". $this->getTableName()
				 ." SET rgt = rgt+".$delta
				 ." WHERE rgt >= ".$first." AND menu_id = ".$menuId." AND rgt <= ".$last);
		
		return array('l' => $first+$delta, 'r' => $last+$delta);
	}
	
}