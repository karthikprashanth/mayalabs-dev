<?php

class AttachmentController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {

    }

    public function addAction() {
        try {
            if($this->getRequest()->isXmlHttpRequest())
                $this->_helper->getHelper('Layout')->disableLayout();
			
            $this->view->headTitle('New Attachment', 'PREPEND');
            $form = new Form_AttachmentForm();
            
			if($this->getRequest()->getPost('cid')){
				$data['cid'] = $this->getRequest()->getPost('cid');
				$form->populate($data);
			}
			
            $this->view->form = $form;
            
            if ($this->getRequest()->getPost('title')) {
                $formData = $this->getRequest()->getPost();
				
                if ($form->isValid($formData)) {
                    $userp = new Model_DbTable_Attachment(Zend_Db_Table_Abstract::getDefaultAdapter(), 0);
                    $content = $form->getValues();

                    $pdata = file_get_contents($form->content->getFileName());
                    $funcs = new Model_Functions();

                    $filename = $form->content->getFileName();
                    $fileext = $funcs->getFileExt($filename);
					
                    if (in_array($fileext, array('pdf', 'doc', 'ppt', 'docx', 'pptx', 'xls', 'xlsx', 'jpg', 'jpeg', 'gif', 'png'))) {
                        $content['content'] = $pdata;
                        
                        $columns = array(
                            'title' => $content['title'],
                            'description' => $content['description'],
                            'content' => $pdata,
                            'filetype' => $fileext,
                            'updatedBy' => Zend_Auth::getInstance()->getStorage()->read()->id
                        );

                        $userp->setData($columns);                        
                        $userp->save();
						
						if($this->getRequest()->getPost('cid')){
							$pres = new Model_DbTable_ConferenceAttachment(Zend_Db_Table_Abstract::getDefaultAdapter());
							$data = array('conferenceId' => $this->getRequest()->getPost('cid'),'attachmentId' => $userp->getId());
							$pres->setData($data);
							$pres->save();
							$this->_redirect("/conference/view?id=".$data['conferenceId']."#ui-tabs-2");
						}
												
                    } else {
                        $this->view->message = "File Type Not Allowed";
                        return;
                    }
                }
            }
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function deleteAction() {
        try{
            $id = $this->_getParam('id',0);
            $attachment = new Model_DbTable_Attachment(Zend_Db_Table_Abstract::getDefaultAdapter(),$id);
            $attachment->deleteAttachment();
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
            	$attachments = Model_DbTable_GtdataAttachment::getList(array('columns' => array('gtid' => $id)));
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
            $this->view->attachments = $attachmentList;
            $this->view->mode = $mode;
			$this->view->id = $id;
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
}