<?php

class IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    protected $application;

    private function setDb()
	{
		$db = new Zend_Db_Adapter_Mysqli(array(
   			 'host'     => '127.0.0.1',
    		 'username' => 'root',
    		 'password' => 'reason',
    		 'dbname'   => 'hivetest'));
			 
			 return $db;
	}
    
    public function setUp()
	{
		$this->bootstrap = array($this, 'appBootstrap');
		parent::setUp();
		
		$db = $this->setDb();
		Zend_Db_Table_Abstract::setDefaultAdapter($db);
	}
	
	public function appBootstrap()
	{
		$this->application = new Zend_Application(APPLICATION_ENV,
												APPLICATION_PATH . '/configs/application.ini');
	}
	
	public function testIndexAction()
	{
		
	}

}





