<?php

class GasturbineController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        try {
	        $userId = Zend_Auth::getInstance()->getStorage()->read()->id;
            $user = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),$userId);
            $this->_redirect("gasturbine/list?id=".$user->getPlantId());
	    } catch (Exception $e) {
	        echo $e;
	    }
    }

    public function addAction()
    {
        try {
            $this->view->headTitle('Add New Gasturbine', 'PREPEND');
            $form = new Form_GasturbineForm();
            //JQuery Form Enable
            ZendX_JQuery::enableForm($form);
            $form->submit->setLabel('Add');
			$form->submit->setAttrib('class','gt-add');
            $this->view->form = $form;

            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {
                    $content = $form->getValues();

                    $existingGTS = Model_DbTable_Gasturbine::getList(array('columns' => array('GTName' => $content['GTName'])));
                    if(count($existingGTS))
                    {
                        $this->view->errorMessage = "Gasturbine name already exists";
                        return;
                    }
					$gasturbine = new Model_DbTable_Gasturbine(Zend_Db_Table_Abstract::getDefaultAdapter());
                    $gasturbine->setGTData($content);
                    $gasturbine->save();
                    $this->_redirect("gasturbine/view?id=".$gasturbine->getGTId());
                } else {
                    $form->populate($formData);
                }
            }
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function editAction()
    {
        try {
            $gtid = $this->_getParam('id',0);
            if(!$gtid)
            {
                $uid = Zend_Auth::getInstance()->getStorage()->read()->id;
                $user = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(), $uid);
                $this->_redirect("gasturbine/list?id=".$user->getPlantId());
            }
            
            $form = new Form_GasturbineForm();
            $form->submit->setLabel('Save');
            $form->submit->setAttrib('class','user-save');
            $this->view->form = $form;

            $gasturbine = new Model_DbTable_Gasturbine(Zend_Db_Table_Abstract::getDefaultAdapter(),$gtid);
            $gtData = $gasturbine->getGTData();

            $this->view->headTitle($gasturbine->getGTName()." - Edit",'PREPEND');
            
            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {

                    $content = $form->getValues();
                    $gasturbine->setGTData($content);
                    $gasturbine->save();
                    $this->_redirect('/gasturbine/view?id='.$gtid);
                    if (Zend_Auth::getInstance()->getStorage()->read()->lastlogin == '') {
                        $this->_redirect('dashboard/index');
                    }
                } else {
                    $form->populate($formData);
                }
            } else {
                $form->populate($gtData);
                $role = Zend_Registry::get("role");
                if($role == "sa")
                    $form->plantid->setValue($gasturbine->getPlantId());
            }
        } catch (exception $e) {
            echo $e;
        }
    }

    public function viewAction()
    {
        try {
            $gtid = $this->_getParam('id',0);
            
            if(!$gtid){
                $uid = Zend_Auth::getInstance()->getStorage()->read()->id;
                $user = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),$uid);
                $userGT = Model_DbTable_Gasturbine::getList(array('columns' => array('plantId' => $user->getPlantId())));
                if(!count($userGT)){
                    $this->_redirect("gasturbine/list?id=".$user->getPlantId());
                }
                $gtid = $userGT[0]['GTId'];
            }

            $gasturbine = new Model_DbTable_Gasturbine(Zend_Db_Table_Abstract::getDefaultAdapter(),$gtid);
            $this->view->headTitle($gasturbine->getGTName() . " - View",'PREPEND');
            $this->view->gtid = $gtid;
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function detailsAction()
    {
        try {
            if ($this->_request->isXmlHttpRequest()) {
                $this->_helper->getHelper('Layout')->disableLayout();
            }
            $gtid = $this->_getParam('id',0);
            $uid = Zend_Auth::getInstance()->getStorage()->read()->id;
            $user = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),$uid);
           
            $gasturbine = new Model_DbTable_Gasturbine(Zend_Db_Table_Abstract::getDefaultAdapter(),$gtid);
            
            $this->view->GTData = $gasturbine->getGTData();
            $plant = new Model_DbTable_Plant(Zend_Db_Table_Abstract::getDefaultAdapter(),$gasturbine->getPlantId());
            $this->view->plantName = $plant->getPlantName();

            $role = Zend_Registry::get('role');

            if($role == 'sa' || ($user->getPlantId() == $gasturbine->getPlantId()))
            {
                if($role != 'us')
                {
                    $this->view->editValidate = true;
                }
            }
            
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function listAction()
    {
    	try
    	{
	    	$pid = $this->_getParam('id',0);
			if($pid)
			{
	    		$gts = Model_DbTable_Gasturbine::getList(array('columns' => array('plantId' => $pid)));
				$plant = new Model_DbTable_Plant(Zend_Db_Table_Abstract::getDefaultAdapter(),$pid);
				$this->view->headTitle($plant->getPlantName() . " - Gasturbines",'PREPEND');
			}
			else
			{
				$gts = Model_DbTable_Gasturbine::getList();
				$this->view->headTitle("Gasturbines",'PREPEND');
			}
	    	
			for($i=0;$i<count($gts);$i++){			
				$gts[$i]['fcount'] = Model_DbTable_Gtdata::getCount(array('columns' => array('type' => 'finding','gtid' => $gts[$i]['GTId'])));
				$gts[$i]['ucount'] = Model_DbTable_Gtdata::getCount(array('columns' => array('type' => 'upgrade','gtid' => $gts[$i]['GTId'])));
				$gts[$i]['lcount'] = Model_DbTable_Gtdata::getCount(array('columns' => array('type' => 'lte','gtid' => $gts[$i]['GTId'])));
				
			}
	    	$GTdata = new Zend_Paginator(new Zend_Paginator_Adapter_Array($gts));
	        $GTdata->setItemCountPerPage(5)
	               ->setCurrentPageNumber($this->_getParam('page', 1));
	                              
	       	$user = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),Zend_Auth::getInstance()->getStorage()->read()->id);
			
			$this->view->userpid = $user->getPlantId();
	    	$this->view->pid = $pid;		
	    	$this->view->GTData = $GTdata;
			
			$this->view->userRole = Zend_Registry::get('role');
			$this->view->lastlogin = Zend_Auth::getInstance()->getStorage()->read()->lastlogin;
		}
		catch(Exception $e){
			echo $e;
		}
    }
}