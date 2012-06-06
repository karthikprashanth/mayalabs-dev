<?php

class Model_DbTable_LTE extends Zend_Db_Table_Abstract {

    protected $_name = 'gtdata';

    public function getLTE($id) {
        $gtid = (int) $id;
        $row = $this->fetchRow('id = ' . $gtid);
        if (!$row) {
            throw new Exception("Could not find row $gtid");
        }
        return $row->toArray();
    }

    public function add($content) {
        $currentdate = date('Y-m-d H:i:s');
        $data = array_merge($content, array('type' => 'lte', 'userupdate' => Zend_Auth::getInstance()->getStorage()->read()->id, 'updatedate' => $currentdate));
        $this->insert($data);

        $newId = $this->getAdapter()->lastInsertId();
		
		$searchIndex = new Model_SearchIndex();
		$searchIndex->updateIndex("gtdata",$newId);
		
        $nf = new Model_DbTable_Notification();
        $nf->add($newId, 'lte', 1);
		return $newId;
    }

    public function updateLTE($content) {
        return $this->update($content, $this->getAdapter()->quoteInto("id = ?", $content['id']));
    }

    public function deleteLTE($id) {
        $this->delete('id =' . (int) $id);
		$nf = new Model_DbTable_Notification();
		$nf->delete("category = 'lte' AND catid = " . (int)$id);
    }

    public function listLTE($id) {
        $select = $this->select()
					->where("type = 'lte' AND gtid = " . $id)
				   ->order('updatedate DESC');
		$rSet = $this->fetchAll($select);
		return $rSet->toArray();
    }
    
    public function listGTLTE($gtid) {
    	$gtid = (int) $gtid;
    	$row = $this->fetchAll('gtid = ' . $gtid);
    	return $row->toArray();
    }

}

?>