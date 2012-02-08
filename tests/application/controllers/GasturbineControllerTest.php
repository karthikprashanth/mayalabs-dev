<?php

class GastrubineControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{
	protected $application;
	
	public function setUp()
	{
		$this->bootstrap = array($this, 'appBootstrap');
		parent::setUp();
		$this->logIn();
		
	}
	
	public function appBootstrap()
	{
		$this->application = new Zend_Application(APPLICATION_ENV,
												APPLICATION_PATH . '/configs/application.ini');
												
		$this->application->bootstrap();
	}
	
	protected function logIn()
	{
    	
		 $this->request->setMethod('POST')
    	 			   ->setPost(array(
        	 			'username' => 'admin',
        	 			'password' => 'reason',
        	 			't_url' => '',
    	 ));
		$this->dispatch('/authentication/login');
		$this->assertTrue(Zend_Auth::getInstance()->hasIdentity());
	}
	
	public function testIndexAction()
	{
		$this->dispatch('/gasturbine/index');
		
	}
	
}