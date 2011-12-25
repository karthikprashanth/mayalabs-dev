<?php

class ScheduleController extends Zend_Controller_Action
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
    	$this->view->headTitle('Add Schedule','PREPEND');
        $form = new Form_ScheduleForm();
   		$this->view->form = $form;
   		$form->submit->setLabel('Save & Continue');
   		$id = $this->_getParam('id',0);
   		$schModel = new Model_DbTable_Schedule();
		$u = new Model_DbTable_User();
		$uid = Zend_Auth::getInstance()->getStorage()->read()->id;
   		if($schModel->schExists($id) || !$u->is_confchair($uid)) {
   			$role = Zend_Registry::get('role');
			if($role != 'sa')
			{
   				$this->_redirect('/conference/list?id='.$this->_getParam('id',0));
			}
   		}
   		if($this->getRequest()->isPost()){
            $formData=$this->getRequest()->getPost();
            if( isset($formData['days']) ) {
            	if($form->isValid($formData)){
            		$data = array(
            						'days' => $form->getValue('days'),
            						'first_day' => $form->getValue('first_day'),
            						'last_day' => $form->getValue('last_day'),
            						'events' => $form->getValue('events'),
            						'cId' => $id
            		);
					$fday = $form->getValue('first_day');
					$lday = $form->getValue('last_day');
					$fint = explode("-",$fday);
					$lint = explode("-",$lday);
					$f = mktime(0,0,0,$fint[1],$fint[2],$fint[0]);
					$l = mktime(0,0,0,$lint[1],$lint[2],$lint[0]);
					if(($l-$f) < 0)
					{
						$this->view->message = "First Day cannot be later than Last Day";
						return;
					}
            		$schModel = $schModel->insert($data);
            		$notModel = new Model_DbTable_Notification();
            		$notModel = $notModel->add($id,'schedule',1);
            		$this->_redirect('/schedule/add-event-list?id='.$id.'&n='.(int)$form->getValue('events'));
            		
            	}
            }
       	}
    }

    public function editAction()
    {
    	$role = Zend_Registry::get('role');
		
		$cid = $this->_getParam('id',0);
		$form = new Form_ScheduleForm();
		$this->view->form = $form;
		$form->submit->setLabel("Save and Continue");
		$schmodel = new Model_DbTable_Schedule();
		if($this->getRequest()->isPost())
		{
			$formdata = $this->getRequest()->getPost();
			if($form->isValid($formdata))
			{
				$content = array();
				$content['days'] = $formdata['days'];
				$content['first_day'] = $formdata['first_day'];
				$content['last_day'] = $formdata['last_day'];
				$content['events'] = $formdata['events'];
				$schmodel->updateSch($cid,$content);
				$this->_redirect("/conference/list?id=".$cid."#ui-tabs-1");
			}
		}
		else
		{
			$conf = $schmodel->fetchRow("cId = " . $cid);
			$sid = $conf['sch_id'];
			$sch = $schmodel->getEventDet($sid);
			$form->populate($sch);	
		}
    }

	public function deleteAction()
	{
		$role = Zend_Registry::get('role');
		
		$cid = $this->_getParam('id',0);
		$schmodel = new Model_DbTable_Schedule();
		$scheventmodel = new Model_DbTable_ScheduleEvent();
		$sch = $schmodel->fetchRow('cId = ' . $cid);
		$sch = $sch->toArray();
		$schlist = $scheventmodel->fetchAll('sch_id = ' . (int)$sch['sch_id']);
		$schlist = $schlist->toArray();
		$this->view->schlist = $schlist;
		$this->view->schid = $sch['sch_id'];
	}
	
	public function delschAction()
	{
		$schid = $this->_getParam('id',0);
		$schmodel = new Model_DbTable_Schedule();
		$scheventmodel = new Model_DbTable_ScheduleEvent();
		$noteModel = new Model_DbTable_Notification();		
		$sch = $schmodel->getEventDet($schid);
		$cid = $sch['cId'];
		$schmodel->delete("sch_id = " . $schid);
		$scheventmodel->delete("sch_id = " . $schid);
		$noteModel->delete("category = 'schedule' AND catid = " . $cid);
		$this->_redirect("/conference/list?id=".$cid."#ui-tabs-1");
	}
	
	public function deleventAction()
	{
		$eventid = $this->_getParam('id',0);
		$schid = $this->_getParam('schid',0);
		$scheventmodel = new Model_DbTable_ScheduleEvent();
		$schmodel = new Model_DbTable_Schedule();
		$sch = $schmodel->getEventDet($schid);
		$cid = $sch['cId'];
		$scheventmodel->delete('event_id = ' . (int)$eventid);
		$this->_redirect("/schedule/delete?id=".$cid);
	}

    public function viewAction()
    {
        if($this->_request->isXmlHttpRequest()) {
                   $this->_helper->getHelper('Layout')->disableLayout();
       	}
    	$id = $this->_getParam('id',0);
    	$this->view->id = $id;
    	$uModel = new Model_DbTable_User();
    	$id = Zend_Auth::getInstance()->getStorage()->read()->id;
    	$iscc = $uModel->is_confchair($id);
    	$this->view->iscc = $iscc;
    	
    	$validcc = $uModel->ccinfo();
    	
    	$schModel = new Model_DbTable_Schedule();
    	$schId = $schModel->getSchId($this->_getParam('id',0));
    	$schId = $schId['sch_id'];
    	
    	$count = $schModel->getCount($schId);
   		$this->view->schCount = $count;
   		
   		if($count != 0 ) {
	   		$eventDet = $schModel->getEventDet($schId);
	   		$this->view->eventDet = $eventDet;
	    	$eventModel = new Model_DbTable_ScheduleEvent();
	    	$eventList = $eventModel->getEventList($this->_getParam('id',0));
	    	$this->view->eventList = $eventList;
	    }
    	
    }

    public function addEventListAction()
    {
    	
    	$this->view->headTitle('Add Event List','PREPEND');
    	$n = $this->_getParam('n',0);
    	$form = new Form_ScheduleEventForm();
    	$form->setmyvar($n);
    	$form->startform();
    	$this->view->form = $form;
    	$schModel = new Model_DbTable_Schedule();
		$u = new Model_DbTable_User();
		$role = Zend_Registry::get('role');
		$uid = Zend_Auth::getInstance()->getStorage()->read()->id;
   		
    	$schId = $schModel->getSchId($this->_getParam('id',0));
    	$schId = $schId['sch_id'];
    	
    	if($this->getRequest()->isPost()){
            $formData=$this->getRequest()->getPost();
            if( isset($formData['event_day0']) ) {
            	if($form->isValid($formData)){
            		$schEve = new Model_DbTable_ScheduleEvent();
            		$existingSch = $schEve->getEventList($this->_getParam('id',0));
					$i = 1;
					if($existingSch == 0)
            			$event_no = 1;
					else 
						$event_no = count($existingSch) + 1;	
            		while( $i <= $n ) {
            			$data = array(
            					'sch_id' => $schId,
            					'cId' => $this->_getParam('id',0),
            					'event_no' => $event_no,
            					'event_date' => $form->getValue('event_day'.($i-1)),
            					'timings' => $form->getValue('timing'.($i-1)),
            					'description' => $form->getValue('desc'.($i-1))
            			);
            					
            			
            			$data = $schEve->put_data($data);
            			$i++;
            		}
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
				    $this->_redirect('/conference/list?id='.$this->_getParam('id',0)."#ui-tabs-1");			
            	}
            }
       	}  	
    } 


}
