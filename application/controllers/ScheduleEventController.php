<?php

class ScheduleEventController extends Zend_Controller_Action
{
	public function init(){
				
	}	
	
	public function indexAction(){
		
	}
	
	public function addAction()
    {
    	try{
    		if($this->getRequest()->isXmlHttpRequest())
                $this->_helper->getHelper('Layout')->disableLayout();
			
			$data = $this->getRequest()->getPost();
			$schevent = new Model_DbTable_ScheduleEvent(Zend_Db_Table_Abstract::getDefaultAdapter());
			$schevent->setEventData($data);
			$schevent->save();
			$eventData = $schevent->getEventData();
			$eventData['id'] = $schevent->getId();
			echo json_encode($eventData);
    	}
		catch(Exception $e){
			echo $e;
		}
    	
    }

    public function viewAction()
    {
    	try{
	        if($this->getRequest()->isXmlHttpRequest())
	        	$this->_helper->getHelper('Layout')->disableLayout();
			$id = $this->_getParam('id',0);
			$schevent = new Model_DbTable_ScheduleEvent(Zend_Db_Table_Abstract::getDefaultAdapter(),$id);
			echo json_encode($schevent->getEventData());
		}
		catch(Exception $e){
			echo $e;
		}
    }
	
	public function editAction()
	{
		try{
			if($this->getRequest()->isXmlHttpRequest())
	        	$this->_helper->getHelper('Layout')->disableLayout();
			
			$data = $this->getRequest()->getPost();
			$schevent = new Model_DbTable_ScheduleEvent(Zend_Db_Table_Abstract::getDefaultAdapter(),$data['id']);
			$data['event_no'] = $schevent->getEventNo();
			$schevent->setEventData($data);
			$schevent->save();
			echo json_encode($schevent->getEventData());
		}
		catch(Exception $e){
			echo $e;
		}
	}	
	
	public function reorderAction(){
		try{
			if($this->getRequest()->isXmlHttpRequest())
	        	$this->_helper->getHelper('Layout')->disableLayout();
			
			$event_ids = $this->getRequest()->getPost('event_ids');
			$event_ids = substr($event_ids,0,strlen($event_ids)-1);
			$eventsArray = explode(",",$event_ids);
			$id = $this->getRequest()->getPost('id');
			$mEvent = new Model_DbTable_ScheduleEvent(Zend_Db_Table_Abstract::getDefaultAdapter(),$id);
			
			for($i=0;$i<count($eventsArray);$i++){
				$event = new Model_DbTable_ScheduleEvent(Zend_Db_Table_Abstract::getDefaultAdapter(),$eventsArray[$i]);
				if($event->getEventNo() == $mEvent->getEventNo() - 1){
					$x = $event->getEventNo();
					$event->setEventNo($mEvent->getEventNo());
					$event->save();
					$mEvent->setEventNo($x);
					$mEvent->save();
					break;
				}
			}
			
			$eventsList = Model_DbTable_ScheduleEvent::getList(array('orderby' => 'event_no','event_ids' => $event_ids));
			echo json_encode($eventsList);
		}
		catch(Exception $e){
			echo $e;
		}
	}

	public function listAction(){
		try{
			if($this->getRequest()->isXmlHttpRequest())
	        	$this->_helper->getHelper('Layout')->disableLayout();
			
			$uid = Zend_Auth::getInstance()->getStorage()->read()->id;
			$user = new Model_DbTable_User(Zend_Db_Table_Abstract::getDefaultAdapter(),$uid);
			$role = Zend_Registry::get('role');
			
			if($role == 'sa' || $user->isConfChair())
				$this->view->permission = true;
			else 
				$this->view->permission = false;
			
			$cid = $this->_getParam('id',0);
			$schedule = Model_DbTable_Schedule::getList(array('columns' => array('cId' => $cid)));
			$this->view->schExists = true;
			$this->view->cid = $cid;
			$this->view->scheduleId = $schedule[0]['id'];
			if(!count($schedule)){
				$this->view->schExists = false;
				return;
			}			
			$this->view->events = Model_DbTable_ScheduleEvent::getList(array('columns' => array('scheduleId' => $schedule[0]['id']),'orderby' => 'event_no'));
			$this->view->schedule = $schedule[0];
			$this->view->eventForm = new Form_ScheduleEventForm();
		}
		catch(Exception $e){
			echo $e;
		}
	}
	
	public function deleteAction(){
		try{
			if($this->getRequest()->isXmlHttpRequest())
	        	$this->_helper->getHelper('Layout')->disableLayout();
			$id = $this->getRequest()->getPost('id');
			
			$event_ids = $this->getRequest()->getPost('event_ids',0);
			$event_ids = substr($event_ids,0,strlen($event_ids)-1);
			
			$schevent = new Model_DbTable_ScheduleEvent(Zend_Db_Table_Abstract::getDefaultAdapter(),$id);
			$n = $schevent->getEventNo();
			$schevent->deleteEvent();
			if($id == $event_ids){
				$eventsList = array('event_ids' => "");
				echo json_encode($eventsList);
				return;
			}
			$eventsList = Model_DbTable_ScheduleEvent::getList(array('orderby' => 'event_no','event_ids' => $event_ids));
			
			for($i=0;$i<count($eventsList);$i++){
				$event = new Model_DbTable_ScheduleEvent(Zend_Db_Table_Abstract::getDefaultAdapter(),$eventsList[$i]['id']);
				if($event->getEventNo() <= $n) continue;
				$event->setEventNo($event->getEventNo() - 1);
				$event->save();
			}
			
			$event_ids = explode($id.",",$event_ids.",");
			
			$eids = implode("",$event_ids);
			
			$eventsList = Model_DbTable_ScheduleEvent::getList(array('orderby' => 'event_no',
																	 'event_ids' => substr($eids,0,strlen($eids)-1)));
			
			$eventsList = array('event_ids' => $eids,'eventsList'=>$eventsList);
			echo json_encode($eventsList);
		}
		catch(Exception $e){
			echo $e;
		}
	}    
}

/*
  MAIL BODY - Should come in add action
  if($this->_getParam('mode','') != 'nomail')
{
	$confModel = new Model_DbTable_Conference();
	$schModel = new Model_DbTable_Schedule();
	$schedule = $schModel->getSchId($this->_getParam('id',0));
	$conf = $confModel->getConfDetail($this->_getParam('id',0));
	$cid = $this->_getParam('id',0);
	$place = $conf['place'];
	$fromDate = $schedule['first_day'];
	$toDate = $schedule['last_day'];
	$uModel = new Model_DbTable_Userprofile();
	$users = $uModel->fetchAll();
	
	$mailbody = "<div style='width: 100%; '><div style='border-bottom: solid 1px #aaa; margin-bottom: 10px;'>";
    $mailbody = $mailbody . "<a href='http://www.hiveusers.com' style='text-decoration: none;'><span style='font-size: 34px; color: #2e4e68;'><b>hive</b></span>";
    $mailbody = $mailbody . "<span style='font-size: 26px; color: #83ac52; text-decoration:none;'><b>users.com</b></span></a><br/><br/>Conference Notification</div>";
    $mailbody = $mailbody . "<div style='margin-bottom:10px;'><span style='color: #000;'><i>Hello</i>,<br/><br/>A new conference has been added<br/>The conference will be held in $place from $fromDate to $toDate. Please click <a href = 'http://www.hiveusers.com/conference/list?id=$cid'>here</a> to view more details about the conference</span></div>";
    $mailbody = $mailbody . "<div style='border-top: solid 1px #aaa; color:#aaa; padding: 5px;'><center>This is a generated mail, please do not Reply.</center></div></div>";
    $mcon = Zend_Registry::get('mailconfig');
	$config = array('ssl' => $mcon['ssl'], 'port' => $mcon['port'], 'auth' => $mcon['auth'], 'username' => $mcon['username'], 'password' => $mcon['password']);
	$tr = new Zend_Mail_Transport_Smtp($mcon['smtp'],$config);
	Zend_Mail::setDefaultTransport($tr);
   	$mail = new Zend_Mail();
	$mail->setBodyHtml($mailbody);
	$mail->setFrom($mcon['fromadd'], $mcon['fromname']);
	foreach($users as $user)
	{
		$mail->addTo($user['email'],$user['firstName']);
	}
	$mail->setSubject('Conference Notification');
	$mail->send();
}
else
{
	$confModel = new Model_DbTable_Conference();
	$schModel = new Model_DbTable_Schedule();
	$schedule = $schModel->getSchId($this->_getParam('id',0));
	$conf = $confModel->getConfDetail($this->_getParam('id',0));
	$cid = $this->_getParam('id',0);
	$place = $conf['place'];
	$fromDate = $schedule['first_day'];
	$toDate = $schedule['last_day'];
	$uModel = new Model_DbTable_Userprofile();
	$users = $uModel->fetchAll();
	
	$mailbody = "<div style='width: 100%; '><div style='border-bottom: solid 1px #aaa; margin-bottom: 10px;'>";
    $mailbody = $mailbody . "<a href='http://www.hiveusers.com' style='text-decoration: none;'><span style='font-size: 34px; color: #2e4e68;'><b>hive</b></span>";
    $mailbody = $mailbody . "<span style='font-size: 26px; color: #83ac52; text-decoration:none;'><b>users.com</b></span></a><br/><br/>Conference Notification</div>";
    $mailbody = $mailbody . "<div style='margin-bottom:10px;'><span style='color: #000;'><i>Hello</i>,<br/><br/>A new event has been added to the schedule of the $place conference<br/>The conference will be held from $fromDate to $toDate. Click <a href = 'http://www.hiveusers.com/conference/list?id=$cid'>here</a> to view more details about the conference</span></div>";
    $mailbody = $mailbody . "<div style='border-top: solid 1px #aaa; color:#aaa; padding: 5px;'><center>This is a generated mail, please do not Reply.</center></div></div>";
    $mcon = Zend_Registry::get('mailconfig');
	$config = array('ssl' => $mcon['ssl'], 'port' => $mcon['port'], 'auth' => $mcon['auth'], 'username' => $mcon['username'], 'password' => $mcon['password']);
	$tr = new Zend_Mail_Transport_Smtp($mcon['smtp'],$config);
	Zend_Mail::setDefaultTransport($tr);
   	$mail = new Zend_Mail();
	$mail->setBodyHtml($mailbody);
	$mail->setFrom($mcon['fromadd'], $mcon['fromname']);
	foreach($users as $user)
	{
		$mail->addTo($user['email'],$user['firstName']);
	}
	$mail->setSubject('Conference Notification');
	$mail->send();
}
*/