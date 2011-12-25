<?php
class AttachmentsController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	
    }

	public function addAction()
	{
		$this->_helper->getHelper('Layout')->disableLayout();
	}

	public function uploadAction()
	{
		include("C:/xampp/htdocs/common.php");
		upload('file',"C:/xampp/htdocs/hive/public/uploads/");
	}

}