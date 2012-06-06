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
            
			$bookmark = new Model_DbTable_Bookmark(Zend_Db_Table_Abstract::getDefaultAdapter());
            
            $bookmark->setCatId($this->getRequest()->getPost('id',0));
            $bookmark->setUserId(Zend_Auth::getInstance()->getStorage()->read()->id);
            $bookmark->setCategory($this->getRequest()->getPost('category'));			
            $bookmark->setName($this->getRequest()->getPost('bmName'));
			$bookmark->save();
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
            $cBookmark = new Model_DbTable_Bookmark(Zend_Db_Table_Abstract::getDefaultAdapter(),$id);
            $cBookmark->deleteBookmark();
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
			$uid = Zend_Auth::getInstance()->getStorage()->read()->id;
			
			$list = Model_DbTable_Bookmark::getList(array("limit" => 5,"columns" => array("userId" => $uid)));
            $this->view->list = $list;
        }
        catch(Exception $e){
            echo $e;
        }
    }

    public function longlistAction()
    {
            try{
                $this->view->headTitle('Bookmarks Full List','PREPEND');
                
				$this->view->bookmarks = Model_DbTable_Bookmark::getList();

            }
            catch(Exception $e){
                echo $e;
            }
    }

    public function viewAction()
    {
    	if($this->getRequest()->isXmlHttpRequest()) {
    		$this->view->display = false;
    	}
		else {
			$this->view->display = true;
		}
        $id = $this->_getParam('id', 0);		
        $userid = Zend_Auth::getInstance()->getStorage()->read()->id;
        $controller = $this->getRequest()->getParam('controller');
		
        if($controller=='bookmark')
             $controller = $this->getRequest()->getParam('category');
		
        $bookmarkList = Model_DbTable_Bookmark::getList(array("columns" => array("userId" => $userid,"category" => $controller,"catId" => $id)));		
		
		if(count($bookmarkList)) {
			$val = 1;
			$this->view->bmid = $bookmarkList[0]["bmId"];	
		}
		else {
			$val = 0;
		}
        $this->view->id = $id;
        $this->view->result=$val;
        $this->view->controller=Zend_Registry::get('controller');
    }

}