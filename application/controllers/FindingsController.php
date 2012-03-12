<?php

class FindingsController extends Zend_Controller_Action {

    public function init() {
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('list', 'json')
                ->initContext();
    }
            
    public function indexAction() 
    {
    	
    }

    public function addAction() {
        try {
            $type = "finding";
            $gtid['gtid'] = $this->getRequest()->getPost('gtid');
            $this->view->headTitle('Add New Finding', 'PREPEND');
            $form = new Form_GTDataForm();
            	
			$form->showForm($gtid['gtid'],0,"finding");            
            $form->submit->setLabel('Add');           	
            $this->view->form = $form;
            $form->populate($gtid);
			$this->view->attachForm = new Form_AttachmentForm();
			$this->view->attachForm->populate(array("mode" => "gtdata","modeId" => $gtid['gtid'],"src" => "add"));
			$this->view->gtid = $gtid['gtid'];
            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if (isset($formData['title'])) {
                    if ($form->isValid($formData)) {
                        $userp = new Model_DbTable_Gtdata(Zend_Db_Table_Abstract::getDefaultAdapter());
                        $content = $form->getValues();
						
						$grpdata = Model_DbTable_Gtdata::getList(array('columns' => array('type' => 'finding','gtid' => $gtid['gtid'])));
						foreach($grpdata as $data)
						{
							if($data['title'] == $content['title'])
							{
								$this->view->message = ucfirst($type). " title already exists";
								return;
							}
						}
						if($content['subSysId'] == 0 || $content['subSysId'] == "")
						{
							$content['subSysId'] = 34;
						}
						$inscontent = array(
							'gtid' => $gtid['gtid'],
							'type' => $type,
							'data' => $content['data'],
							'userupdate' => Zend_Auth::getInstance()->getStorage()->read()->id,
							'title' => $content['title'],
							'sysId' => $content['sysId'],
							'subSysId' => $content['subSysId'],
							'EOH' => $content['EOH'],
							'DOF' => $content['DOF'],
							'TOI' => $content['TOI']	
						);
                        $userp->setGTData($inscontent);
                        $userp->save();
						$fid = $userp->getId();
						
						$addedPres = $content['attach_ids'];
						
						if($addedPres != "")
						{
							$addedPres = explode(",",$addedPres);
							foreach($addedPres as $pres){
								if($pres == "" or $pres == 0) continue;
								$attach = new Model_DbTable_GtdataAttachment(Zend_Db_Table_Abstract::getDefaultAdapter());
								$attach->setData(array("attachmentId" => $pres,"gtdataId" => $fid));
								$attach->save();
							}
						}
						
						$chosenPres = $content['presentationId'];
						if(count($chosenPres)){
							foreach($chosenPres as $pres){
								if($pres == "" or $pres == 0) continue;
								$attach = new Model_DbTable_GtdataAttachment(Zend_Db_Table_Abstract::getDefaultAdapter());
								$attach->setData(array("attachmentId" => $pres,"gtdataId" => $fid));
								$attach->save();
							}
						}
						//add notifications
                        $this->_redirect('/findings/view?id=' . $fid);
                    } else {
                        $form->populate($formData);
                    }
                }
            }
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function editAction() {
    	try{
    		$id = $this->_getParam("id",0);
			$gtdata = new Model_DbTable_Gtdata(Zend_Db_Table_Abstract::getDefaultAdapter(),$id);
			$gtdataArray = $gtdata->getData();
			
			$gtdAttach = Model_DbTable_GtdataAttachment::getList(array("columns" => array("gtdataId" => $id)));
			
			foreach($gtdAttach as $gtd){
				$attach_ids .= $gtd['attachmentId'] . ",";
				$gtda[] = new Model_DbTable_Attachment(Zend_Db_Table_Abstract::getDefaultAdapter(),$gtd['attachmentId']);
			}
			
			$form = new Form_GTDataForm();
			
			$form->showForm($gtdata->getGTId(),$id,$gtdata->getType(),explode(",",$attach_ids));
			$form->submit->setLabel("Save & Continue");
			$form->submit->setAttrib("class","user-save");
			
			$this->view->gtid = $gtdata->getGTId();
			$this->view->id = $id;
			$this->view->addedAttachments = $gtda;
			
			$this->view->form = $form;
			$this->view->headTitle("Edit " . $gtdata->getTypeTitle() . " - " . $gtdata->getTitle(),'PREPEND');
			$this->view->userupdate = $gtdata->getUserUpdate();
			$this->view->updatetime = $gtdata->getUpdateTime();
			$this->view->typeTitle  = $gtdata->getTypeTitle();
			$this->view->attachForm = new Form_AttachmentForm();
			$this->view->attachForm->populate(array("mode" => "gtdata","modeId" => $gtdata->getGTId(),"src" => "edit"));
			
			if($this->getRequest()->isPost()){
					
				$existing = Model_DbTable_Gtdata::getList(array("columns" => array("type" => $gtdata->getType(),"gtid" => $gtdata->getGTId())));
				
				foreach($existing as $e){
					if($e['id'] != $id){
						$ed = new Model_DbTable_Gtdata(Zend_Db_Table_Abstract::getDefaultAdapter(),$e['id']);
						if($ed->getTitle() == $this->getRequest()->getPost('title')){
							$this->view->message =  $gtdata->getTypeTitle() . " with the same title already exists";
							$form->populate($this->getRequest()->getPost());
							return;
						}
					}
				}
				
				$content = $this->getRequest()->getPost();
				$chosenPres = $content['presentationId'];
				
				if(count($chosenPres)){
					foreach($chosenPres as $pres){
						if($pres == "" or $pres == 0) continue;
						$attach = new Model_DbTable_GtdataAttachment(Zend_Db_Table_Abstract::getDefaultAdapter());
						$attach->setData(array("attachmentId" => $pres,"gtdataId" => $id));
						$attach->save();
					}
				}
				
				$addedPres = explode(",",$content['attach_ids']);
				if(count($addedPres)){
					foreach($addedPres as $pres){
						if($pres == "" or $pres == 0) continue;
						$attach = new Model_DbTable_GtdataAttachment(Zend_Db_Table_Abstract::getDefaultAdapter());
						$attach->setData(array("attachmentId" => $pres,"gtdataId" => $id));
						$attach->save();
					}
				}
					
				unset($content['submit']);
				unset($content['attach_ids']);		
				unset($content['presentationId']);
				if($content['subSysId'] == ""){
					$content['subSysId'] = 34;
				}
				$content['userupdate'] = Zend_Auth::getInstance()->getStorage()->read()->id;
				$content['updatedate'] = date("Y-m-d H:i:s");		
				$gtdata->setGTData($content);
				$gtdata->save();
				
				//add notifications here
				
				$this->_redirect("/findings/view?id=".$id);
				
			}
			else {
				$formData = $gtdata->getData();
				$formData['attach_ids'] = "";
				$form->populate($formData);
			}
    	}
		catch(Exception $e){
			echo $e;
		}
    }
	public function listAction(){
		try{
			$this->_helper->getHelper('Layout')->disableLayout();
			$gtid = $this->_getParam('id',0);
			
			$findings = Model_DbTable_Gtdata::getList(array("columns" => array("gtid" => $gtid,"type" => "finding")));			
			$findingList = array();
			foreach($findings as $f){
				$findingList[] = new Model_DbTable_Gtdata(Zend_Db_Table_Abstract::getDefaultAdapter(),$f['id']);					
			}
			$this->view->findingList = $findingList;
			
			$uid = Zend_Auth::getInstance()->getStorage()->read()->id;
			$user = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),$uid);
			
			$gt = new Model_DbTable_Gasturbine(Zend_Db_Table_Abstract::getDefaultAdapter(),$gtid);
			
			$role = Zend_Registry::get('role');
			if($user->getPlantId() == $gt->getPlantId() || $role == 'sa'){
				$this->view->allowed = true;
			}
			$this->view->gtid = $gtid;
		}
		catch(Exception $e){
			
		}
	}
	
	public function viewAction() {
        try {
        	$id = $this->_getParam("id",0);
			$finding = new Model_DbTable_Gtdata(Zend_Db_Table_Abstract::getDefaultAdapter(),$id);
			$this->view->headTitle("View Finding - " . $finding->getTitle(),'PREPEND');
			$this->view->finding = $finding;
			
			$uid = Zend_Auth::getInstance()->getStorage()->read()->id;
			$user = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),$uid);
			
			$gt = new Model_DbTable_Gasturbine(Zend_Db_Table_Abstract::getDefaultAdapter(),$finding->getGTId());
			$role = Zend_Registry::get('role');
			if($user->getPlantId() == $gt->getPlantId() || $role == 'sa'){
				$this->view->allowed = true;
			}
			
			$attachments = Model_DbTable_GtdataAttachment::getList(array('columns' => array('gtdataid' => $id)));
			foreach($attachments as $a){
				$attachs[] = new Model_DbTable_Attachment(Zend_Db_Table_Abstract::getDefaultAdapter(),$a['attachmentId']);
			}
			array_multisort($updateAt,SORT_DESC,$attachs);
			$this->view->attachmentList = $attachs;
			
        } 
        catch (Exception $e) {
            echo $e;
        }
    }

    public function deleteAction() {
        try{
        	if($this->getRequest()->isPost()){
        		$id = $this->getRequest()->getPost("id");
				
				$gtdataAttachments = Model_DbTable_GtdataAttachment::getList(array("columns" => array("gtdataId" => $id)));
				
				foreach($gtdataAttachments as $gta){
					$gtdAttach = new Model_DbTable_GtdataAttachment(Zend_Db_Table_Abstract::getDefaultAdapter(),$gta['id']);
					$gtdAttach->deleteGTdataAttachment();
				}
				
				$gtdata = new Model_DbTable_Gtdata(Zend_Db_Table_Abstract::getDefaultAdapter(),$id);
				$gtid = $gtdata->getGTId();
				$gtdata->deleteGtdata();
				
				$this->_redirect("/gasturbine/view?id=".$gtid."#ui-tabs-2");
				//delete notifications
        	}
        }
		catch(Exception $e){
			
		}
    }

}