<?php
	class Model_DbTable_Forum_Posts extends Zend_Db_Table_Abstract
	{
		protected $_name = 'forum_posts';
		
		public function getPost($id)
		{
			try
			{	
				$row = $this->fetchRow('post_id = '.$id);
				if(!$row)
				{
					$row = array();
					return $row;
				}
				return $row->toArray();
			}
			catch(Exception $e)
			{
				return 0;
			}
			
		}
		
		public function getFieldsForSearch()
		{
			$row = $this->fetchAll();
			return $row->toArray();
		}
	}