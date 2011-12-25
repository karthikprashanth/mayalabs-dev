<?php
	class Model_DbTable_Gtsubsystems extends Zend_Db_Table_Abstract 
	{

    	protected $_name = 'gtsubsystems';
    	
    	public function getSubSystem($ssid)
    	{
    		$ssid = (int)$ssid;
    		$row = $this->fetchRow('id = ' . $ssid);
    		return $row->toArray();
    	}
    	
    	public function groupSubSystem($sid)
    	{
    		$row = $this->fetchAll("sysId = " . $sid);
			return $row->toArray();
    	}
    }