<?php
class Model_DbTable_ScheduleEvent extends Zend_Db_Table_Abstract {

	protected $_name = 'schevent';

	public function put_data($data)
	{
		$this->insert($data);
	}
	
	public function getEventList($cid)
	{
		try {
	        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
	        $stmt = $dbAdapter->query('SELECT * FROM schevent WHERE cId = ' . (int)$cid . ' ORDER BY event_no');
	        $row = $stmt->fetchAll();
	        return $row;
        }
        catch(Exception $e) {
            return 0;
        }
	} 
	
}
?>