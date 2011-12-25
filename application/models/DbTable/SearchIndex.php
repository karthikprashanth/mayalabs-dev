<?php
	class Model_DbTable_SearchIndex extends Zend_Db_Table_Abstract
	{
		protected $_name = 'search_post_index';
		
		public function getPosts()
		{
			$row = $this->fetchAll();
			
			return $row->toArray();
		}
	}