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

abstract class CMS_Model_Tree extends CMS_Model_MultiLang {

	/*
	 * Attribut gérant le multi arbre (plusieur ROOT dans une même table)
	 * */
	protected $name_tree_field 		= null;
	protected $id_tree 						= null;
	protected $cond_sql_multi_tree 	= null;
	
	public function __construct($name_tree_field = null, $id_tree = null){
		parent::__construct();
		
		// Construction de la chaine de condition pour le multi arbre
		if ($name_tree_field && $id_tree){
			$this->name_tree_field 		= $name_tree_field;
			$this->id_tree 						= $id_tree;			
			$this->cond_sql_multi_tree = " AND ".$this->name_tree_field." = ".$this->id_tree." ";
		}
	}
	
	/*
	 * Fonction permettant d'avoir l'identifiant du parent de l'identifiant passé en param
	 * @return ID si il y a un parent, null sinon
	 * */
	public function getParentId($id)
	{
		if (!$id)
			throw new Exception('No id');
		
		$sql = "SELECT parent." . $this->_primaryKey . "
       				FROM pagespro_activites parent,
                                pagespro_activites node
       				WHERE parent.lft < node.lft AND parent.rgt > node.rgt 
					AND node." . $this->_primaryKey . " = ".$id." ";

		$sql .= $this->cond_sql_multi_tree;		
		
       	$sql .="ORDER BY parent.rgt-node.rgt ASC 
       				LIMIT 1";
		
		$return = $this->getAdapter()->fetchOne($sql);
		
		if(!empty($return))
		return $return;
		else
		return null;
	}
	
	public function getTree($id_lang = null){
	
		$this->_values = array_merge($this->_values, array('parent_id', 'level'));
		
		$return = $this->prepareFieldsForLoad($this->_getDatas($id_lang));

		foreach ($this->_values as $key => $value) {
			if ($value == 'parent_id' || $value == 'level')
				unset($this->_values[$key]);
		}
				
		return $return ? $return : null;
	}
	
	/*
	 * Récupération de l'arbre avec les levels des nodes
	 * */
	protected function _getDatas($id_lang = null)
	{
		
		$sql = "SELECT A.*, B.* , (COUNT(parent." . $this->_primaryKey . ")-1) AS level, (SELECT " . $this->_primaryKey . " 
																															           	FROM  ".$this->_name." t2 
																															           	WHERE t2.lft < A.lft AND t2.rgt > A.rgt    
																															          	ORDER BY t2.rgt-A.rgt ASC 
																															           	LIMIT 1) AS parent_id
					FROM ".$this->_name." AS A,
					     ".$this->_name." AS parent,
					     ".$this->_name."_lang AS B 
					WHERE A.lft BETWEEN parent.lft AND parent.rgt 
					";
		
		$sql .= $this->cond_sql_multi_tree;
		
		$sql 	.= " 	AND B." . $this->_primaryKey . " = A." . $this->_primaryKey . " 
						GROUP BY A." . $this->_primaryKey . ", id_lang
						ORDER BY A.lft;";

		//parent::generateSQL($sql, $where, $order, $limit);
	
		$return = $this->getAdapter()->fetchAll($sql, null, zend_db::FETCH_OBJ);
		
		return $return ? $return : null;
	}
	
	/*
	 * Fonction d'ajout d'un item 
	 * */
	public function addItem($obj)
	{
		$db = $this->getAdapter();
	
		try
		{
			$db->beginTransaction();
			
			$db->query("SELECT @myRight := rgt FROM " . $this->_name . " WHERE ".$this->_primaryKey." = ?", array($obj->parent_id));

			if ($this->id_tree && $this->name_tree_field) {
				$cond_lft 		= array("lft  >= @myRight", $this->name_field_tree." = ".$this->id_tree);
				$cond_rgt 	= array("rgt >= @myRight", $this->name_field_tree." = ".$this->id_tree);
			} else {
				$cond_lft 		= array("lft  >= @myRight");
				$cond_rgt 	= array("rgt >= @myRight");
			}
			
			$db->update(
			$this->_name,
			array( "rgt" => new Zend_Db_Expr("rgt + 2") ),
			$cond_rgt
			);
				
			$db->update(
			$this->_name,
			array( "lft" => new Zend_Db_Expr("lft + 2") ),
			$cond_lft
			);
			
			$obj->lft  	= new Zend_Db_Expr('@myRight');
			$obj->rgt = new Zend_Db_Expr('@myRight+1');
			
			$obj->save();
			
			$db->commit();

		}
		catch (Exception $e)
		{
			$db->rollBack();
			throw new Zend_Db_Exception($e->getMessage(), $e->getCode());
		}
	
		return $obj->id;
	}
	
	/*
	 * Fonction de delete de l'item passé en parametre (identifiant) 
	 * Ses enfants seront supprimés en même temps
	 * */
	public function deleteEntity($param)
	{
		// Si $param est un entier : suppression de l'élement par sa clé primaire
		if(is_int($param))
		{
			if(!$param)
			throw new Zend_Exception(_t('Missing parameter'));
				
			$db = $this->getAdapter();
				
			try
			{
				$db->beginTransaction();
	
				// /!\ Delete children if folder
				$db->query("SELECT @myLeft := lft, @myRight := rgt, @myWidth := rgt - lft + 1 FROM " . $this->_name . " WHERE  ".$this->_primaryKey." = ?", array($param));
				$objs = $db->query("SELECT ".$this->_primaryKey." FROM " . $this->_name . " WHERE lft BETWEEN @myLeft AND @myRight ".$this->cond_sql_multi_tree)->fetchAll(PDO::FETCH_OBJ);
	
				foreach ($objs as $obj){
					parent::delete((int)$obj->id);
				}
				
				// Condition pour le multi tree
				if ($this->id_tree && $this->name_tree_field) {
					$cond_lft 		= array("lft  > @myRight", $this->name_field_tree." = ".$this->id_tree);
					$cond_rgt 	= array("rgt > @myRight", $this->name_field_tree." = ".$this->id_tree);
				} else {
					$cond_lft 		= array("lft  > @myRight");
					$cond_rgt 	= array("rgt > @myRight");
				}
	
				$db->update(
				$this->_name,
				array( "rgt" => new Zend_Db_Expr("rgt - @myWidth") ),
				$cond_rgt
				);
	
				$db->update(
				$this->_name,
				array( "lft" => new Zend_Db_Expr("lft - @myWidth") ),
				$cond_lft
				);
	
				$db->commit();
			}
			catch (Exception $e)
			{
				$db->rollBack();
				throw new Zend_Db_Exception($e->getMessage(), $e->getCode());
			}
				
			return true;
		}

		return false;
	}
	
	/** NESTED TREE **/
	
	public function updateParent($itemId, $parentId)
	{
		$row = $this->find ($itemId)->current();
		if ($row)
		{
			$destItem = $this->find ( $parentId )->current ();
			if($destItem)
			{
				if (($destItem->lft>$row->lft) && ($destItem->rgt<$row->rgt))
				return false;

				$pos = $this->_moveToLastChild($row,$destItem);			
				return true;
			}
		}
	
		return false;
	}
	
	public function moveItemsToPreviousSibling($srcId, $destId)
	{
		$scrItem = $this->find($srcId)->current();
		$destItem = $this->find($destId)->current();
	
		if ($scrItem && $destItem)
		{
			$newpos = $this->_moveToPreviousSibling($scrItem,$destItem);
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
	
		$this->_shiftRLValues($dst, $treesize);
		if($srcNode->lft >= $dst) // src was shifted too?
		{
			$srcNode->lft += $treesize;
			$srcNode->rgt += $treesize;
		}
	
		/* Now there is enough room next to target to move the subtree */
		$newpos =  $this->_shiftRLRange($srcNode->lft, $srcNode->rgt, $dst-$srcNode->lft);
	
		/* Correct values after source */
		$this->_shiftRLValues($srcNode->rgt+1, -$treesize);
	
		if($srcNode->lft <= $dst) // dst was shifted too?
		{
			$newpos['l'] -= $treesize;
			$newpos['r'] -= $treesize;
		}
	
		return $newpos;
	}
	private function _shiftRLValues($first, $delta)
	{
		$db = Zend_Registry::get('db');
	
		$db->query("UPDATE ". $this->_name
		." SET lft = lft+".$delta
		." WHERE lft >= ".$first
		." ".$this->cond_sql_multi_tree);
			
		$db->query("UPDATE ". $this->_name
		." SET rgt = rgt+".$delta
		." WHERE rgt >= ".$first
		." ".$this->cond_sql_multi_tree);
	}
	
	private function _shiftRLRange($first, $last, $delta)
	{
		$db = Zend_Registry::get('db');
	
		$db->query("UPDATE ". $this->_name
		." SET lft = lft+".$delta
		." WHERE lft >= ".$first." AND lft <= ".$last
		." ".$this->cond_sql_multi_tree);
	
		$db->query("UPDATE ". $this->_name
		." SET rgt = rgt+".$delta
		." WHERE rgt >= ".$first." AND rgt <= ".$last
		." ".$this->cond_sql_multi_tree);
	
		return array('l' => $first+$delta, 'r' => $last+$delta);
	}
}