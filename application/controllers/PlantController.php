<?php

class PlantController extends Zend_Controller_Action {

    public function init() {
        
    }

    public function indexAction() {
        try {
            $uprofileModel = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),Zend_Registry::get('id'));
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
					
                   	
                   	$existingPlants = Model_DbTable_Plant::getList(array('corporateName' => $content['corporateName']));
					$corpExists = count($existingPlants);
					
					if($corpExists)
					{
						$existingPlants = Model_DbTable_Plant::getList(array('plantName' => $content['plantName']));
						if(count($existingPlants))
							$this->view->errorMessage = "Plant name already exists";
					}	
                    $pModel = new Model_DbTable_Plant();
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
			$user = Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),$userId);
			if(!$plantId){	
				$this->_redirect("/plant/view?id=".$user->getPlantId());
			}
			$plant = Model_DbTable_Plant(Zend_Db_Table_Abstract::getDefaultAdapter(),$plantId);
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
        $this->view->headTitle('Edit Plant', 'PREPEND');
        try {

            $form = new Form_PlantForm();
            $form->setMode("edit");
            $form->showForm();
            //JQuery Form Enable
            ZendX_JQuery::enableForm($form);
            $form->partPlant3->submit->setLabel('Save');
            $form->partPlant3->submit->setAttrib('class', 'user-save');

            $this->view->form = $form;
            $this->view->plantId = $this->_getParam('id', 0);

            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {
                    $GT = new Model_DbTable_Plant();
                    $plantDet = $GT->getPlant($this->_getParam('id', 0));
                    $plantId = $this->_getParam('id', 0);
                    $this->view->plantId = $plantId;
                    $content = array_merge($form->partPlant1->getValues(), $form->partPlant2->getValues(), $form->partPlant3->getValues());
                    if (count(array_diff($content, $plantDet)) > 0) {
                        $nf = new Model_DbTable_Notification();
                        $nf->add($plantId, 'plant', 0);
                        $GT->updatePlant($plantId, $content);
                    }
                    if ($this->getRequest()->getPost("modeselect") == "redirect") {
                        $this->_redirect('plant/view?id=' . $plantId);
                    }
                    if ($this->getRequest()->getPost("modeselect") == "stay1") {
                        $this->_redirect('plant/edit?id=' . $plantId . '#tabContainer-frag-1');
                    }
                    if ($this->getRequest()->getPost("modeselect") == "stay2") {
                        $this->_redirect('plant/edit?id=' . $plantId . '#tabContainer-frag-2');
                    }
                    if ($this->getRequest()->getPost("modeselect") == "stay3") {
                        $this->_redirect('plant/edit?id=' . $plantId . '#tabContainer-frag-3');
                    }
                } else {
                    $form->populate($formData);
                }
            } else {
                $plantId = $this->_getParam('id', 0);
                $PlantVal = new Model_DbTable_Plant();
                $form->populate($PlantVal->getPlant($plantId));
            }
        } catch (exception $e) {
            echo $e;
        }
    }

    public function listAction() {
        try {
            $role = Zend_Registry::get('role');

            $this->view->headTitle('List Plants', 'PREPEND');
            $resultSet = new Model_DbTable_Plant();
            $resultSet = $resultSet->listPlants();

            $up = new Model_DbTable_Userprofile();
            $up = $up->getUser(Zend_Auth::getInstance()->getStorage()->read()->id);
            $pid = $up['plantId'];

            $Pdata = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($resultSet));
            $Pdata->setItemCountPerPage(5)
                    ->setCurrentPageNumber($this->_getParam('page', 1));

            $this->view->Pdata = $Pdata;
            $this->view->pid = $pid;
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function adminAction() {
        try {
            $resultSet = new Model_DbTable_Plant();
            $resultSet = $resultSet->listPlants();

            $plants = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($resultSet));
            $plants->setItemCountPerPage(5)
                    ->setCurrentPageNumber($this->_getParam('page', 1));

            $this->view->plants = $plants;
        } catch (Exception $exc) {
            echo $exc;
        }
    }

    public function editvalidateAction() {
        try {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->getHelper('layout')->disableLayout();
            $form = new Form_PlantForm();
            $formData = $this->getRequest()->getPost();
            $form->isValid($formData);
            $json = $form->getMessages();
            echo Zend_Json::encode($json);
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function addvalidateAction() 
    {
        try {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->getHelper('layout')->disableLayout();
            $form = new Form_PlantForm();
            $formData = $this->getRequest()->getPost();
            $form->isValid($formData);
            $json = $form->partPlant1->getMessages();
            $json = array_merge($json, $form->partPlant2->getMessages());
            $json = array_merge($json, $form->partPlant3->getMessages());
            echo Zend_Json::encode($json);
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function clistAction() 
    {
    	
 		/*$plantModel = new Model_DbTable_Plant(Zend_Db_Table_Abstract::getDefaultAdapter());
		$plants = $plantModel::getList("plantName");
		
		$plantNames = array();
		
		$i=0;
		foreach($plants as $plant){
			if($plant['plantId'] != 1){
				$plantNames[$i++] = $plant['plantName'];
			}	
		}
			
		$this->view->plantNames = $plantNames;*/
    }

    public function resultsAction() 
    {
        $this->_helper->getHelper('layout')->disableLayout();
        
		//Plant Name typed in the Auto Complete textbox
        $term = $this->_getParam('term');
		
		//Lower Limit and Upper Limit for Pagination purpose
        $ll = $this->_getParam('ll');
        $ul = $this->_getParam('ul');
		
        $plantModel = new Model_DbTable_Plant(Zend_Db_Table_Abstract::getDefaultAdapter());
        $results = $plantModel::getList("plantName","plantName",$term);
        
        if ($term == NULL) {
            $results = $plantModel::getList("plantName");
        }
		
        $umodel = new Model_DbTable_Userprofile();
        $this->view->usermodel = $umodel;
		
		$this->view->results = $results;
        $this->view->resultcount = $plantModel::getCount();
		$this->view->term = $term;
        $this->view->ll = $ll;
        $this->view->ul = $ul;
    }

}

