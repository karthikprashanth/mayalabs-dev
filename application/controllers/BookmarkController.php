<?php

class BookmarkController extends Zend_Controller_Action
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
        try {
            $this->_helper->getHelper('layout')->disableLayout();
            $id = $this->getRequest()->getPost('id',0);
            $userid = Zend_Auth::getInstance()->getStorage()->read()->id;
            $controller = $this->getRequest()->getPost('category');
            $bmName = $this->getRequest()->getPost('bmName');
            $updatedtime = new Model_DbTable_Bookmark();
            $updatedtime = $updatedtime->timestamp();

            $content=array();
            $content['bmName']=strip_tags($bmName);
            $content['userId']=$userid;
            $content['category']=$controller;
            $content['catId']=$id;
            $content['updatedtime']=$updatedtime;
			
            $add=new Model_DbTable_Bookmark();
            $add = $add->add($content);
        }
        catch(Exception $e){
            echo $e;
        }
    }

    public function deleteAction()
    {
        try {
            $this->_helper->getHelper('layout')->disableLayout();
            $id = $this->getRequest()->getPost('id',0);
			$mode = $this->getRequest()->getPost('mode',0);
            $userid = Zend_Auth::getInstance()->getStorage()->read()->id;
            $cBookmark = new Model_DbTable_Bookmark();
            $cBookmark->deleteBookmark($id);
        }
        catch(Exception $e){
            echo $e;
        }
    }

    public function listAction()
    {
        try{
            if(!$this->_request->isXmlHttpRequest())
                $this->_helper->viewRenderer->setResponseSegment('bookmark');

            $resultSet = new Model_DbTable_Bookmark();
            $resultSet = $resultSet->longlistBookmark();

            $list=new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($resultSet));
            $list->setItemCountPerPage(5)
                        ->setCurrentPageNumber($this->_getParam('page', 1));

            $this->view->list = $list;
        }
        catch(Exception $e){
            echo $e;
        }
    }

    public function longlistAction()
    {
            try{
                $this->view->headTitle('List All Bookmarks','PREPEND');
                $resultSet = new Model_DbTable_Bookmark();
                $resultSet = $resultSet->longlistBookmark();

                $this->view->result=$val;

                $longList=new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($resultSet));
                $longList->setItemCountPerPage(5)
                            ->setCurrentPageNumber($this->_getParam('page', 1));
         
                $this->view->longList = $longList;

                }
            catch(Exception $e){
                echo $e;
            }
    }

    public function viewAction()
    {
        $id = $this->_getParam('id', 0);
        $userid = Zend_Auth::getInstance()->getStorage()->read()->id;
        $controller = $this->getRequest()->getParam('controller');
        if($controller=='bookmark')
             $controller = $this->getRequest()->getParam('category');
        $result = new Model_DbTable_Bookmark();
        $result = $result->checkBookmark($id, $userid, $controller);

        $this->view->id=$id;

        if($result) {
            $val=1;
            $this->view->id=$result->bmId;
        }
        else
            $val=0;
        
        $this->view->result=$val;
        
        $this->view->controller=Zend_Registry::get('controller');
    }

}