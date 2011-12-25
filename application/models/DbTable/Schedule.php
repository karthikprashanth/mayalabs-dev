<?php
class Model_DbTable_Schedule extends Zend_Db_Table_Abstract {

	protected $_name = 'schedule';
	
	public function getCount($id)
	{
	
		$id = (int)$id;
		$row = $this->fetchAll('sch_id = ' . $id);
		if (!$row) {
			return 0;
		}
		else {
			$i=0;
			foreach($row as $tblrow) {
				$i++;
			}
			return $i;
		}
		
	}
	
	public function getSchId($cId){
		$cId = (int)$cId;
		$row = $this->fetchRow('cId = ' . $cId);
		if ($row == NULL) {
			return 0;
		}
		else {
			return $row->toArray();
		}
		
	}
	
	public function getEventDet($id)
	{
		$id = (int)$id;
		$row = $this->fetchRow('sch_id = ' . $id);
		return $row->toArray();
	}
	
	public function schExists($id)
	{
		$id = (int) $id;
		$row = $this->fetchRow('cId = ' . $id);
		$row = (array) $row;
		if (count($row) > 0){
			return 1;
		}
		else {
			return 0;
		}
	}
	
	public function updateSch($cid, $content) {
        $where = $this->getAdapter()->quoteInto('cId = ?', $cid);
        $this->update($content, $where);
    }
	
}