<?php

class AttachmentController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {

    }

    public function addAction() {
        try {
        	if($this->getRequest()->isPost()){     
	            //$this->_helper->getHelper('Layout')->disableLayout();
	            $userp = new Model_DbTable_Attachment(Zend_Db_Table_Abstract::getDefaultAdapter());
	            $content = $this->getRequest()->getPost();
				
				$pdata = file_get_contents($_FILES['attachment']['tmp_name']);
				$ext = Model_Functions::getFileExt($_FILES['attachment']['name']);
				
				if($_FILES['attachment']['name'] == "") {
					$this->view->fileEmpty = "true";
					return;
				}
	            $content['content'] = $pdata;
	            if(empty($content['title'])){
	            	$content['title'] = str_replace(".".$ext,"",$_FILES['attachment']['name']); 
	            }
				$this->view->gtdata = $this->getRequest()->getPost('gtdata');
				$this->view->mode = $this->getRequest()->getPost('mode');
				$this->view->id = $this->getRequest()->getPost('modeId');
				$this->view->source = $this->getRequest()->getPost('src');
				//Check for title name clash
				if($this->getRequest()->getPost('mode') == 'conf')
					$existingPres = Model_DbTable_ConferenceAttachment::getList(array("columns" => array("conferenceId" => $this->getRequest()->getPost('modeId'))));
				else 
					$existingPres = Model_DbTable_GtAttachment::getList(array("columns" => array("gtid" => $this->getRequest()->getPost('modeId'))));
				foreach($existingPres as $a){
					$attach = new Model_DbTable_Attachment(Zend_Db_Table_Abstract::getDefaultAdapter(),$a['attachmentId']);
					if($attach->getTitle() == $content['title']){
						$this->view->titleValid = "false";
						return;
					}
				}
				
	            $columns = array(
	                'title' => $content['title'],
	                'content' => $pdata,
	                'filetype' => $ext,
	                'updatedBy' => Zend_Auth::getInstance()->getStorage()->read()->id
	            );
				//Check for title name clash ends
				
	            $userp->setData($columns);                        
	            $userp->save();
				if($this->getRequest()->getPost('mode') == 'conf'){
					$cid = $this->getRequest()->getPost('modeId');
					
					$pres = new Model_DbTable_ConferenceAttachment(Zend_Db_Table_Abstract::getDefaultAdapter());
					$data = array(
									'conferenceId' => $cid,
								  	'attachmentId' => $userp->getId()
								 );
					$pres->setData($data);
					$pres->save();
				}
				else {
					$data = array("gtid" => $this->getRequest()->getPost('modeId'),"attachmentId" => $userp->getId());
					$gtPres = new Model_DbTable_GtAttachment(Zend_Db_Table_Abstract::getDefaultAdapter());
					$gtPres->setData($data);
					$gtPres->save();
					$gtPresId = $gtPres->getId();
					
					if($this->getRequest()->getPost('gtdataid')){
						$gtdataid = $this->getRequest()->getPost('gtdataid');
						$gtdataPres = new Model_DbTable_GtdataAttachment(Zend_Db_Table_Abstract::getDefaultAdapter());
						$data = array("attachmentId" => $userp->getId(),"gtdataId" => $gtdataid);
						$gtdataPres->setData($data);
						$gtdataPres->save();
					}
				}
				
				unlink($content['filePath']);
				
				$up = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),Zend_Auth::getInstance()->getStorage()->read()->id);
				
				$content = $userp->getData();
				$content['updatedBy'] = Model_DbTable_Userprofile::getUserName($content['updatedBy']);
				$content['attachmentId'] = $userp->getId();
				$content['filetype'] = $userp->getFileType();
				$content['date'] = date("Y-m-d H:i:s");
				$content['userplantname'] = $up->getPlantName();
				$content['gtPresId'] = $gtPresId;
				$content['titleValid'] = "true";
				$this->view->fileEmpty = "false";
				$this->view->content = $content;
			}
       
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function deleteAction() {
        try{
			$this->_helper->getHelper('Layout')->disableLayout();
			$id = $this->getRequest()->getPost("attachmentId");			
            if($this->getRequest()->getPost("cid")){            	       	
            	$redirect = "/conference/view?id=".$this->getRequest()->getPost("cid")."#ui-tabs-2";
            	$confAttachments = Model_DbTable_ConferenceAttachment::getList(array("columns" => array("attachmentId" => $id)));				
				foreach($confAttachments as $ca){
					$cAttach = new Model_DbTable_ConferenceAttachment(Zend_Db_Table_Abstract::getDefaultAdapter(),$ca['id']);
					$cAttach->deleteConferenceAttachment();
				}
            }
			else {
				$redirect = "/gasturbine/view?id=".$this->getRequest()->getPost("gtid")."#ui-tabs-5";
				$gdAttachments = Model_DbTable_GtAttachment::getList(array("columns" => array("attachmentId" => $id)));
				foreach($gdAttachments as $gda){
					$gAttach = new Model_DbTable_GtAttachment(Zend_Db_Table_Abstract::getDefaultAdapter(),$gda['id']);
					$gAttach->deleteGtAttachment();
				}
				
				$gtdataAttachments = Model_DbTable_GtdataAttachment::getList(array("columns" => array("attachmentId" => $id)));
				foreach($gtdataAttachments as $gda){
					$gAttach = new Model_DbTable_GtdataAttachment(Zend_Db_Table_Abstract::getDefaultAdapter(),$gda['id']);
					$gAttach->deleteGTdataAttachment();
				}
			}
			$attachment = new Model_DbTable_Attachment(Zend_Db_Table_Abstract::getDefaultAdapter(),$id);
			$attachment->deleteAttachment();
			if(!$this->getRequest()->isXmlHttpRequest())
				$this->_redirect($redirect);
        }
        catch(Exception $e){
            echo $e;
        }
    }

	public function uploadAction(){
		try{
			if($this->getRequest()->isPost()){
				$this->_helper->getHelper('Layout')->disableLayout();
				
			}
			
		}
		catch(Exception $e){
			echo $e;
		}
	}
	

    public function listAction() {
        try{
            $id = $this->_getParam('id',0);
			$mode = $this->_getParam('mode','gt');
            if($this->getRequest()->isXmlHttpRequest()){
                $this->_helper->getHelper('Layout')->disableLayout();
            }
			$role = Zend_Registry::get("role");
			$uid = Zend_Auth::getInstance()->getStorage()->read()->id;
	        $user = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),$uid);
			if($mode == 'gt'){
            	$attachments = Model_DbTable_GtAttachment::getList(array('columns' => array('gtId' => $id)));
	            $upid = $user->getPlantId();
	            $gt = new Model_DbTable_Gasturbine(Zend_Db_Table_Abstract::getDefaultAdapter(), $id);
	            $gtpid = $gt->getPlantId();
	            if($upid == $gtpid || $role == 'sa') {
	                $this->view->allowed = true;
				}
			}
			else if($mode=="conf"){ 
				$attachments = Model_DbTable_ConferenceAttachment::getList(array('columns' => array('conferenceId' => $id)));
				if($role == 'sa' || $user->isConferenceChairman()){
					$this->view->allowed = true;
				}
			}
            $attachmentList = array();
            foreach($attachments as $list){
                $attachmentList[] = new Model_DbTable_Attachment(Zend_Db_Table_Abstract::getDefaultAdapter(),$list['attachmentId']);                
            }
			
			$attachmentForm = new Form_AttachmentForm();
			$fArray = array("mode" => $mode,"modeId" => $id);
			$attachmentForm->populate($fArray);
			
            $this->view->attachments = $attachmentList;
            $this->view->mode = $mode;
			$this->view->id = $id;
			$this->view->attachmentForm = $attachmentForm;
        }
        catch(Exception $e){
            echo $e;
        }
    }

	public function viewAction() {
		try{
			$this->_helper->getHelper('Layout')->disableLayout();
			$id = $this->_getParam('id',0);
	        $pres = new Model_DbTable_Attachment(Zend_Db_Table_Abstract::getDefaultAdapter(),$id);
			$filename = $pres->getTitle() .".".$pres->getFileExtension();
			$appath = substr(APPLICATION_PATH,0,strlen(APPLICATION_PATH)-12);
			$appath = $appath . "/public/uploads/";
			$file = file_put_contents($appath.$filename,$pres->getContent());
			$this->view->browserfilename = $filename;
			$this->view->origfilepath = $appath . $filename;
		}
		catch(Exception $e){
			echo $e;
		}
	}
	
	public function unlinkAction(){
		try{
			$this->_helper->getHelper('Layout')->disableLayout();
			$id = $this->getRequest()->getPost('attachmentId');
			$gtdataid = $this->getRequest()->getPost('gtdataid');
			$attach = new Model_DbTable_Attachment(Zend_Db_Table_Abstract::getDefaultAdapter(),$id);
			
			$gta = Model_DbTable_GtdataAttachment::getList(array("columns" => array("attachmentId" => $id,"gtdataId" => $gtdataid)));			
			$gta = new Model_DbTable_GtdataAttachment(Zend_Db_Table_Abstract::getDefaultAdapter(),$gta[0]['id']);
			$gta->deleteGTdataAttachment();
			
			echo json_encode(array("title" => $attach->getTitle()));
		}
		catch(Exception $e){
			
		}
	}
}