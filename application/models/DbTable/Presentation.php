<?php

class Model_DbTable_Presentation extends Zend_Db_Table_Abstract {

	protected $_name = 'presentations';

	public function getPresentation($id)
	{
		$pid = (int)$id;
		$row = $this->fetchRow('presentationId = ' . $pid);
		if (!$row) {
			throw new Exception("Could not find row $pid");
		}
		return $row->toArray();
	}

	public function add($content) {
                $currentdate= date('Y-m-d H:i:s');
                $data = array_merge($content,array('userupdate'=>Zend_Auth::getInstance()->getStorage()->read()->id,'timeupdate'=>$currentdate));
            	$this->insert($data);
	}

	public function deletePresentation($id)
        {
            $this->delete('id =' .(int)$id);
        }

        public function listPresentation($id){
			$select = $this->select()
					->where("GTId = " . $id)
				   ->order('timeupdate DESC');
		$rSet = $this->fetchAll($select);
		return $rSet->toArray();
        }
}

?>