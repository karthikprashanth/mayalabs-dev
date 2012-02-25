<?php

class GastrubineControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{
		
	protected $application;
	
	/* logs in as System Administrator */
	
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
	
	public function testIndexAction()
	{
		$this->request->setMethod('POST')
    	 			   ->setPost(array(
        	 			'username' => 'admin',
        	 			'password' => 'reason',
        	 			't_url' => '',
    	 ));
		$this->dispatch('/authentication/login');
		$this->assertTrue(Zend_Auth::getInstance()->hasIdentity());
		$userId = Zend_Auth::getInstance()->getStorage()->read()->id;
       	$this->assertEquals(2,$userId);
		Zend_Registry::set('id', Zend_Auth::getInstance()->getStorage()->read()->id);
		$session = new Zend_Session_Namespace('Zend_auth');
		$session->setExpirationSeconds(5);
//		session_write_close();
		//sleep(3);
			 
		$this->dispatch('/dashboard/index');
		$this->assertController('dashboard');

	}
	
}