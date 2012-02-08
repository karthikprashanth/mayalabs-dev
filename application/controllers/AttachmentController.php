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
            
            $this->view->form = $form;
            
            if ($this->getRequest()->isPost()) {
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
            $gtid = $this->_getParam('gtid',0);
            if($this->getRequest()->isXmlHttpRequest()){
                $this->_helper->getHelper('Layout')->disableLayout();
            }
            $gtdataAttachments = Model_DbTable_GtdataAttachment::getList(array('columns' => array('gtid' => $gtid)));
            $i=0;
            $attachments = array();
            foreach($gtdataAttachments as $gdalist){
                $attachments[$i++] = new Model_DbTable_Attachment(Zend_Db_Table_Abstract::getDefaultAdapter(),$gdalist['attachmentId']);                
            }
            
            $this->view->attachments = $attachments;
            
            $uid = Zend_Auth::getInstance()->getStorage()->read()->id;
            $user = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),$uid);
            $upid = $user->getPlantId();

            $gt = new Model_DbTable_Gasturbine(Zend_Db_Table_Abstract::getDefaultAdapter(), $gtid);
            $gtpid = $gt->getPlantId();

            $role = Zend_Registry::get("role");
            if($upid == $gtpid || $role == 'sa') {
                $this->view->userBelongs = true;
            }
            else {
                $this->view->userBelongs = false;
            }
            
        }
        catch(Exception $e){
            echo $e;
        }
    }

}