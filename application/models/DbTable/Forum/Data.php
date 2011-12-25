<?php
	class Model_DbTable_Forum_Data extends Zend_Db_Table_Abstract
	{
		protected $_name = 'forum_forums';
		
		public function getForum($id)
		{	
			$row = $this->fetchRow('forum_id = '.$id);
			return $row->toArray();
			
		}
	}