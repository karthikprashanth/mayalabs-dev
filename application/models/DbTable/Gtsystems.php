<?php
	class Model_DbTable_Gtsystems extends Zend_Db_Table_Abstract 
	{

    	protected $_name = 'gtsystems';
    	
    	public function getSystem($sid)
    	{
    		$sid = (int)$sid;
    		$row = $this->fetchRow('sysId = ' . $sid);
    		return $row->toArray();
    	}
    }