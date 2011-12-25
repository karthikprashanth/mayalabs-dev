<?php

class Model_DbTable_Notification extends Zend_Db_Table_Abstract {

    protected $_name = 'notification';

    public function add($id, $controller, $edit) {
        $currentdate = date('Y-m-d H:i:s');
        $uid = Zend_Auth::getInstance()->getStorage()->read()->id;
        $data['userupdate'] = $uid;
        $data['category'] = $controller;
        $data['catId'] = $id;
        $data['timeupdate'] = $currentdate;
        $data['edit'] = $edit; // add-> 1 ; edit-> 0
        $this->insert($data);
    }

    public function getNotifications($ul) {

        try {
    	if($ul == 0)
		{
			$ul = 9;
		}
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $stmt = $dbAdapter->query("SELECT * FROM notification WHERE category != 'gasturbine' AND NOT(category = 'plant' AND edit = 0) ORDER BY timeupdate DESC LIMIT 0," . $ul);
		
        $row = $stmt->fetchAll();
        return $row;
        }
        catch(Exception $e) {
            echo $e;
        }
    }

}

?>
