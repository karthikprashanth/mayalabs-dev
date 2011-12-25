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
	        $up = new Model_DbTable_Userprofile();
	        $up = $up->getUser(Zend_Auth::getInstance()->getStorage()->read()->id);
	        $pid = $up['plantId'];
	        $this->_redirect('/gasturbine/plantlist?id='.$pid);
	    } catch (Exception $e) {
	        echo $e;
	    }
    }

    public function addAction()
    {
        try {
                            $this->view->headTitle('Add GT', 'PREPEND');
                            $form = new Form_GasturbineForm();
                            //JQuery Form Enable
                            ZendX_JQuery::enableForm($form);
                            $form->submit->setLabel('Add');
							$form->submit->setAttrib('class','gt-add');
                            $this->view->form = $form;
                            if ($this->getRequest()->isPost()) {
                                $formData = $this->getRequest()->getPost();
                                if ($form->isValid($formData)) {
                                    $userp = new Model_DbTable_Gasturbine();
                                    $content = $form->getValues();
									$exists = false;
									$role = Zend_Registry::get('role');
									if($role == 'ca')
									{
										$umodel = new Model_DbTable_Userprofile();
										$user = $umodel->getUser(Zend_Auth::getInstance()->getStorage()->read()->id);
										$upid = $user['plantId'];
										$content['plantid'] = $upid;
									}
									$pgt = $userp->listPlantGtArray($content['plantid']);
									foreach($pgt as $p)
									{
										if($p['GTName'] == $content['GTName'])
										{
											$exists = true;
											break;
										}
									}
									if($exists)
									{
										$this->view->message = "Gasturbine name already exists";
										return;
									}
                                    $newgt = $userp->add($content);
									
                                    $this->_redirect('/gasturbine/view?id='.$newgt);
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
        $this->view->headTitle('Edit GT', 'PREPEND');
                        try {
                            $form = new Form_GasturbineForm();
                            $form->submit->setLabel('Save');
							$form->submit->setAttrib('class','user-save');
                            $this->view->form = $form;
                			
                            if ($this->getRequest()->isPost()) {
                                $formData = $this->getRequest()->getPost();
                                if ($form->isValid($formData)) {
                                    $GT = new Model_DbTable_Gasturbine();
                                    $gtDet = $GT->getGT($this->_getParam('id',0));
                                    $content = $form->getValues();
                					if(count(array_diff($content,$gtDet)) > 0)
                                    {
                                    	$GT->updateGT($content);
                                    	$nf = new Model_DbTable_Notification();
	                                    $formData['GTId'] = $this->_getParam('id', 0);
	                                    $nf->add($formData['GTId'], 'gasturbine', 0);	
                                    }
                                    
                                    $this->_redirect('/gasturbine/view?id='.$this->_getParam('id',0));
                                    if (Zend_Auth::getInstance()->getStorage()->read()->lastlogin == '') {
                                        $this->_redirect('dashboard/index');
                                    }
                                } else {                                	
                                    $form->populate($formData);
                                }
                            } else {
                                $id = $this->_getParam('id', 0);
                                $GTVal = new Model_DbTable_Gasturbine();
								$gtdet = $GTVal->getGT($id);
                                $form->populate($gtdet);
								$role = Zend_Registry::get("role");
								if($role == "sa")
									$form->plantid->setValue($gtdet['plantId']);
								
                            }
                        } catch (exception $e) {
                            echo $e;
                        }
    }

    public function viewAction()
    {
        
                        try {
                            $id = $this->_getParam('id', 0);
                            $GTView = new Model_DbTable_Gasturbine();
                            $GTData = $GTView->getGT($id);
                
                            $this->view->headTitle("View GT - " . $GTData['GTName'], 'PREPEND');
                
							
                            $this->view->GTData = $GTData;
							
							
							
                            $this->view->id = $id;
                        } catch (Exception $e) {
                            echo $e;
                        }
    }

    public function listAction() {
        try {
            $this->view->headTitle('List GT', 'PREPEND');
            $resultSet = new Model_DbTable_Gasturbine();
            $resultSet = $resultSet->listGT();

            $up = new Model_DbTable_Userprofile();
            $up = $up->getUser(Zend_Auth::getInstance()->getStorage()->read()->id);
            $pid = $up['plantId'];
            $gt = new Model_DbTable_Gasturbine();
            $gt = $gt->getGTP($pid);

            $GTdata = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($resultSet));
            $GTdata->setItemCountPerPage(5)
                    ->setCurrentPageNumber($this->_getParam('page', 1));

            $this->view->GTdata = $GTdata;
            $this->view->gt = $gt;
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function detailsAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
                            $this->_helper->getHelper('Layout')->disableLayout();
        }
        $this->view->headTitle('View GT', 'PREPEND');

        try {
            $id = $this->_getParam('id', 0);
            $this->view->id = $id;
            $GTView = new Model_DbTable_Gasturbine();
            $GTData = $GTView->getGT($id);
			foreach($GTData as $key=>$value)
			{
				if(is_int($value) && $value == 0)
				{
					$GTData[$key] = "-";		
				}
			}
            $this->view->GTData = $GTData;
            $plantModel = new Model_DbTable_Plant();
            $plant = $plantModel->getPlant($GTData['plantId']);
            $this->view->pname = $plant['plantName'];
        } catch (Exception $e) {
            echo $e;
            }
    }

    public function plantlistAction()
    {
    	$pid = $this->_getParam('id',0);
    	if($pid == 0)
    	{
    		$up = new Model_DbTable_Userprofile();
	        $up = $up->getUser(Zend_Auth::getInstance()->getStorage()->read()->id);
	        $pid = $up['plantId'];
    		$this->_redirect("/gasturbine/plantlist?id=".$pid);
    	}
    	$gtmodel = new Model_DbTable_Gasturbine();
    	$gt = $gtmodel->listPlantGt($pid);
    	
    	$GTdata = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($gt));
                            $GTdata->setItemCountPerPage(5)
                                    ->setCurrentPageNumber($this->_getParam('page', 1));
                                    
       	$up = new Model_DbTable_Userprofile();
        $up = $up->getUser(Zend_Auth::getInstance()->getStorage()->read()->id);
        $upid = $up['plantId'];
        $gt = new Model_DbTable_Gasturbine();		
        $gt = $gt->getGTP($upid);
		
        $this->view->gt = $gt;    
    	$this->view->pid = $pid;
    	$this->view->GTData = $GTdata;
    }


}





