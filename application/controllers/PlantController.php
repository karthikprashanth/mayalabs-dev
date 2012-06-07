<?php

class PlantController extends Zend_Controller_Action {

    public function init() {
        
    }

    public function indexAction() {
        try {
            $uid = Zend_Auth::getInstance()->getStorage()->read()->id;
            $uprofileModel = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),$uid);
			$this->_redirect("/plant/view?id=".$uprofileModel->getPlantId());
        } 
        catch (Exception $e) {
            echo $e;
        }
    }

    public function addAction() {
        try {
        	
            $this->view->headTitle('Add New Plant', 'PREPEND');
            
            $form = new Form_PlantForm();
            $form->setMode("add");
            $form->showForm();
            $form->partPlant3->submit->setLabel('Add');
			
            //JQuery Form Enable
            ZendX_JQuery::enableForm($form);
            $this->view->form = $form;
            
            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {
                    $content = $form->getValues();
                    $content = array_merge($content['partPlant1'], $content['partPlant2'], $content['partPlant3']);
					
                   	$existingPlants = Model_DbTable_Plant::getList(array('columns' => array('corporateName' => $content['corporateName'])));
					$corpExists = count($existingPlants);
                    
					if($corpExists)
					{
						$existingPlants = Model_DbTable_Plant::getList(array('columns' => array('plantName' => $content['plantName'])));
						if(count($existingPlants))
                        {
							$this->view->errorMessage = "Plant name already exists";
                            return;
                        }

					}
					$content['lastupdateuser'] = Zend_Auth::getInstance()->getStorage()->read()->id;
                    $pModel = new Model_DbTable_Plant(Zend_Db_Table_Abstract::getDefaultAdapter());
					$pModel->setPlantData($content);
					$pModel->save();    
                    $this->_redirect('plant/view?id=' . $pModel->getPlantId());
                } 
                else {
                    $form->populate($formData);
                }
            }
        } 
        catch (Exception $e) {
            echo $e;
        }
    }

    public function viewAction() {
        try {
            $plantId = $this->_getParam('id',0);
			$userId = Zend_Auth::getInstance()->getStorage()->read()->id;
            
			$user = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),$userId);
			if(!$plantId){	
				$plantId = $user->getPlantId();
			}
			$plant = new Model_DbTable_Plant(Zend_Db_Table_Abstract::getDefaultAdapter(),$plantId);
            $this->view->headTitle($plant->getPlantname() . '- View', 'PREPEND');
			$this->view->plantData = $plant->getPlantData();

			$role = Zend_Registry::get('role');
			if(($role == 'sa' && $role != 'us') && $user->getPlantId() == $plantId){
				$this->view->editValid = true;
			}
			else{
				$this->view->editValid = false;
			}
			
			$this->view->lastlogin = Zend_Auth::getInstance()->getStorage()->read()->lastlogin;
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function editAction() {
        try{
            $form = new Form_PlantForm();
            $form->setMode("edit");
            $form->showForm();
            $form->partPlant3->submit->setLabel('Save');

            $form->partPlant3->submit->setAttrib('class', 'user-save');
            $plantId = $this->_getParam('id',0);
            $userId = Zend_Auth::getInstance()->getStorage()->read()->id;
            if(!$plantId)
            {
                $user = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(), $userId);
                $plantId = $user->getPlantId();
            }
            $plant = new Model_DbTable_Plant(Zend_Db_Table_Abstract::getDefaultAdapter(),$plantId);
            $plantData = $plant->getPlantData();
            $this->view->headTitle($plant->getPlantName() . " - Edit",'PREPEND');
            if($this->getRequest()->isPost())
            {
                $formData = $this->getRequest()->getPost();
                if($form->isValid($formData))
                {
                    $content = array_merge($form->partPlant1->getValues(), $form->partPlant2->getValues(),
                                                                $form->partPlant3->getValues());
                    $content['plantId'] = $plantId;
					$content['lastupdateuser'] = $userId;
                    unset($content['modeselect']);
                    $plant->setPlantData($content);
                    $plant->save();
                    
                    if ($this->getRequest()->getPost("modeselect") == "redirect") {
                            $this->_redirect('plant/view?id=' . $plantId);
                    }
                    if ($this->getRequest()->getPost("modeselect") == "go2") {
                        $this->_redirect('plant/edit?id=' . $plantId . '#tabContainer-frag-2');
                    }
                    if ($this->getRequest()->getPost("modeselect") == "go3") {
                        $this->_redirect('plant/edit?id=' . $plantId . '#tabContainer-frag-3');
                    }
                    if ($this->getRequest()->getPost("modeselect") == "stay2") {
                        $this->_redirect('plant/edit?id=' . $plantId . '#tabContainer-frag-2');
                    }
					if ($this->getRequest()->getPost("modeselect") == "stay3") {
                        $this->_redirect('plant/edit?id=' . $plantId . '#tabContainer-frag-3');
                    }                                        

                }
                else
                {
                    $form->populate($formData);
                }
            }
            else {
                $form->populate($plantData);
            }

            $this->view->form = $form;
            $this->view->userId = $userId;
            $this->view->lastlogin = Zend_Auth::getInstance()->getStorage()->read()->lastlogin;
        }
        catch(Exception $e){
            echo $e;
        }
    }


    public function listAction() 
    {
    	try{
	 		$plantModel = new Model_DbTable_Plant();
			$plants = $plantModel::getList();
			
			$plantNames = array();
			
			$i=0;
			foreach($plants as $plant){
				if($plant['plantId'] != 1){
					$plantNames[$i++] = $plant['plantName'];
				}	
			}
				
			$this->view->plantNames = $plantNames;
		}
		catch(Exception $e){
			echo $e;
		}
    }

    public function resultsAction() 
    {
    	try{
	        $this->_helper->getHelper('layout')->disableLayout();
	        
			//Plant Name typed in the Auto Complete textbox
	        $term = $this->_getParam('term');
			
			//Lower Limit and Upper Limit for Pagination purpose
	        $ll = $this->_getParam('ll');
	        $ul = $this->_getParam('ul');
			
	        $plantModel = new Model_DbTable_Plant(Zend_Db_Table_Abstract::getDefaultAdapter());
	        $results = $plantModel::getList(array('likeColumn' => 'plantName','likeTerm' => $term,'orderby' => 'plantName'));
	        
	        if ($term == NULL) {
	            $results = $plantModel::getList(array('orderby' => 'plantName'));
	        }
	
	        foreach($results as $result)
	        {
	            $userlist[$result['plantId']] = Model_DbTable_Userprofile::getList(array('columns' => array('plantId' => $result['plantId'])));
	        }
	        
			$this->view->results = $results;
	        $this->view->userlist = $userlist;
	        $this->view->resultcount = $plantModel::getCount();
			$this->view->term = $term;
	        $this->view->ll = $ll;
	        $this->view->ul = $ul;
	  }
      catch(Exception $e){
      	echo $e;
      }
    }

}

