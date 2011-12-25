<?php
class Model_DbTable_GTResource extends Zend_Controller_Action implements Zend_Acl_Resource_Interface  {
    public $ownername = null;
    public $resource_id = null;

    public function  __construct() {
        $resource_id = $this->getRequest()->getParam('controller');
    }


    public function  getResourceId() {
        return $this->resource_id;
    }
}
?>
