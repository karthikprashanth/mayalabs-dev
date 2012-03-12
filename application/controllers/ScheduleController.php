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
    	try{
			$this->view->headTitle('Add Schedule','PREPEND');
	        $form = new Form_ScheduleForm();
	   		$this->view->form = $form;
	   		$eventForm = new Form_ScheduleEventForm();
			$this->view->eventForm = $eventForm;
	   		$id = $this->getRequest()->getPost('cId');
			$data['cId'] = $id;
			$form->populate($data);
	    }
		catch(Exception $e){
			echo $e;
		}
    }

	public function saveAction()
	{
		try{
			if($this->getRequest()->isXmlHttpRequest())
	        	$this->_helper->getHelper('Layout')->disableLayout();
			
			$fday = $this->getRequest()->getPost('first_day');
			$lday = $this->getRequest()->getPost('last_day');
			//$notModel = new Model_DbTable_Notification();
    		//$notModel = $notModel->add($id,'schedule',1);
    		$fint = explode("-",$fday);
			$lint = explode("-",$lday);
			$f = mktime(0,0,0,$fint[1],$fint[2],$fint[0]);
			$l = mktime(0,0,0,$lint[1],$lint[2],$lint[0]);
			if(($l-$f) < 0)
			{
				echo "date error";
				return;
			}
			
			$data = array(
				'first_day' => $this->getRequest()->getPost('first_day'),
				'last_day' => $this->getRequest()->getPost('last_day'),
				'cId' => $this->getRequest()->getPost('cid')
    		);
    		$schedule = new Model_DbTable_Schedule(Zend_Db_Table_Abstract::getDefaultAdapter());
			$schedule->setScheduleData($data);
			$schedule->save();
			
			$event_ids = $this->getRequest()->getPost('event_ids');
			$event_ids = substr($event_ids,0,strlen($event_ids)-1);
			$event_ids = explode(",",$event_ids);
			
			foreach($event_ids as $eid){
				$event = new Model_DbTable_ScheduleEvent(Zend_Db_Table_Abstract::getDefaultAdapter(),$eid);
				$event->setScheduleId($schedule->getId());
				$event->save();
			}
			
		}
		catch(Exception $e){
			echo $e;
		}
	}

    public function editAction()
    {
    	try{
	    	$id = $this->_getParam('id',0);
			$form = new Form_ScheduleForm();
			
			$form->submit->setLabel("Save & Continue");
			$this->view->form = $form;
			$schedule = new Model_DbTable_Schedule(Zend_Db_Table_Abstract::getDefaultAdapter(),$id);
			$cid = $schedule->getConferenceId();
			$conf = new Model_DbTable_Conference(Zend_Db_Table_Abstract::getDefaultAdapter(),$cid);
			$this->view->headTitle("Edit Schedule - " . $conf->getPlace() . "(" . $conf->getYear() . ")",'PREPEND');
			if($this->getRequest()->isPost()){
				$formData = $this->getRequest()->getPost();
				if($form->isValid($formData)){
					$data = array(
						'id' => $id,
						'days' => $form->getValue('days'),
						'first_day' => $form->getValue('first_day'),
						'last_day' => $form->getValue('last_day'),
						'events' => $form->getValue('events'),
						'cId' => $cid
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
					$schedule->setScheduleData($data);
					$schedule->save();
					$this->_redirect("/conference/view?id=".$cid."#confdata-frag-2");
				}
				else {
					$form->populate($formData);
				}
			}
			else{
				$form->populate($schedule->getScheduleData());
			}
		}
		catch(Exception $e){
			echo $e;	
		}
    }

	public function deleteAction()
	{
		try{
			$cid = $this->getRequest()->getPost('cId');
			$schedule = Model_DbTable_Schedule::getList(array('columns' => array('cId' => $cid)));
			$schedule = $schedule[0];
			$events = Model_DbTable_ScheduleEvent::getList(array('columns' => array('scheduleId' => $schedule['id'])));
			foreach($events as $ev){
				$event = new Model_DbTable_ScheduleEvent(Zend_Db_Table_Abstract::getDefaultAdapter(),$ev['id']);
				$event->deleteEvent();
			}
			$schedule = new Model_DbTable_Schedule(Zend_Db_Table_Abstract::getDefaultAdapter(),$schedule['id']);
			$schedule->deleteSchedule();
			$this->_redirect("/conference/view?id=".$cid);
		}
		catch(Exception $e){
			echo $e;
		}
	}
	 
}
