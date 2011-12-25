<?php
	class Model_DbTable_ConfPresentation extends Zend_Db_Table_Abstract {
		protected $_name = 'confpresentation';
		
		public function getPresDetail($id){
			$id = (int)$id;
			$row = $this->fetchAll('cId = ' . $id);
			return $row->toArray();
		}
		
		public function getPres($id)
		{
			$id = (int)$id;
			$row = $this->fetchRow('presentationId = ' . $id);
			return $row->toArray();
		}
		
	}
?>
			
		