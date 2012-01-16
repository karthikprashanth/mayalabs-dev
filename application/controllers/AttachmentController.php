<?php

class AttachmentController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {

    }

    public function addAction() {
        $this->_helper->getHelper('Layout')->disableLayout();
        try {
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

}