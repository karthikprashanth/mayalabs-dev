<?php

class FindingsController extends Zend_Controller_Action {

    public function init() {
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('list', 'json')
                ->initContext();
    }
            
    public function indexAction() 
    {
    	$this->_helper->getHelper('Layout')->disableLayout();
    	$sid = $this->_getParam('id',0);
		$this->view->ssid = $this->_getParam('ssid',0);
    	$subSysModel = new Model_DbTable_Gtsubsystems();
    	$subSysList = $subSysModel->groupSubSystem($sid);
    	$this->view->sslist = $subSysList;
    }

    public function addAction() {
        try {
            $gtid['gtid'] = $this->getRequest()->getPost('gtid');
            $this->view->headTitle('Add New Finding', 'PREPEND');
            $form = new Form_GTDataForm();
            
			$form->showform($gtid['gtid'],0,"finding");            
            $form->submit->setLabel('Add');           	
            $this->view->form = $form;
            
            $sysModel = new Model_DbTable_Gtsystems(Zend_Db_Table_Abstract::getDefaultAdapter(), 0);
            $this->view->subsystems = $sysModel->fetchAll();
            
            $form->populate($gtid);
            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if (isset($formData['title'])) {
                    if ($form->isValid($formData)) {
//                        $userp = new Model_DbTable_Finding();
                        $userp = new Model_DbTable_Gtdata(Zend_Db_Table_Abstract::getDefaultAdapter(), 0);
                        $content = $form->getValues();
						$type = "finding";
						$grpdata = $userp->getList(array('columns' => array('type' => 'finding')));
						foreach($grpdata as $data)
						{
							if($data['title'] == $content['title'])
							{
								$this->view->message = ucfirst($type). " title already exists";
								return;
							}
						}
						if($content['presentationId'] != "")
						{
	                        $temp = '';
	                        foreach ($content['presentationId'] as $pId) {
	                        	if($pId != "")
								{
	                            	$temp = $temp . $pId . ',';
								}
	                        }
	                    }
						
//						$pmodel = new Model_DbTable_Presentation(Zend_Db_Table_Abstract::getDefaultAdapter(), 0);
						$funcs = new Model_Functions();
						
						$filenames = array(
							1 => $form->content1->getFileName(),
							2 => $form->content2->getFileName(),
							3 => $form->content3->getFileName(),
							4 => $form->content4->getFileName(),
							5 => $form->content5->getFileName()
						);
						
						$prestitles = array(
							1 => $content['prestitle1'],
							2 => $content['prestitle2'],
							3 => $content['prestitle3'],
							4 => $content['prestitle4'],
							5 => $content['prestitle5']
						);
						$checked = array(1 => false,2 => false,3 => false,4 => false,5 => false);
						/*-----
						checks for allowed file extensions
						*/
						
						$i=1;
						for($i=1;$i<=5;$i++)
						{
							$pres=file_get_contents($filenames[$i]);
							$filename = $filenames[$i]; 
							$fileext = $funcs->getFileExt($filename);
							if($filename != NULL)
							{
								if(!in_array(strtolower($fileext),array('pdf','doc','ppt','docx','pptx','xls','xlsx','jpeg','jpg','png','gif')))
								{
									$this->view->message = "File Type Not Allowed";
									return;
								}
							}
							/*creating the data array to be inserted into the db */
							$data = array(
								'title' => $prestitles[$i],
								'GTId' => $gtid['gtid'],
								'content' => $pres,
								'filetype' => $fileext,
								'userupdate' => Zend_Auth::getInstance()->getStorage()->read()->id
							);
							
							/* checking for presentation title conflcts */
//							$gtpres = $pmodel->fetchAll('GTId = '. $gtid['gtid']);
                            $pmodel = new Model_DbTable_Presentation(Zend_Db_Table_Abstract::getDefaultAdapter(), 0);
                            
                            $gtpres = Model_DbTable_Presentation::getList($gtid['gtid']);

                            $exists = false;
							foreach($gtpres as $gtp)
							{
								if($gtp['title'] == $prestitles[$i])
								{
									$exists = true;
								}
							}
                            
							if($exists && !$checked[$i])
							{
								$this->view->message = "Presentation title already exists";
								return;
							}
							
							if($prestitles[$i] != "" && $prestitles[$i] != NULL)
							{
//								$p = $pmodel->insert($data);

                                $pmodel->setData($data);
                                $p = $pmodel->save();

                                $checked[$i] = true;
								if($temp == "")
								{
									$temp = $p . ",";
								}
								else {
									$temp = $temp . $p . ",";
								}
							}
						}
						
						//----
						
						$content['presentationId'] = $temp;
						if($content['subSysId'] == 0 || $content['subSysId'] == "")
						{
							$content['subSysId'] = 34;
						}
						$inscontent = array(
							'gtid' => $gtid['gtid'],
							'type' => 'finding',
							'data' => $content['data'],
							'userupdate' => Zend_Auth::getInstance()->getStorage()->read()->id,
							'title' => $content['title'],
							'presentationId' => $temp,
							'sysId' => $content['sysId'],
							'subSysId' => $content['subSysId'],
							'EOH' => $content['EOH'],
							'DOF' => $content['DOF'],
							'TOI' => $content['TOI']
							
						);
//                        $fid = $userp->add($inscontent);
                        
                        $userp->setGTData($inscontent);
                        $fid = $userp->save();

                        $this->_redirect('/findings/view?id=' . $fid);
                    } else {
                    	$x = 1;
						for($x=1;$x<=5;$x++)
						{
							$formData['prestitle' . $x] = "";
						}
                        $form->populate($formData);
                    }
                }
            }
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function editAction() {
    	
        $this->view->headTitle('Edit Finding', 'PREPEND');
        try {
        	$id = $this->_getParam('id', 0);
			$gtdatamodel = new Model_DbTable_Gtdata();
			$gtdata = $gtdatamodel->getData($id);
			$gtid = $gtdata['gtid'];
            $form = new Form_GTDataForm();
			$form->showForm($gtid,$id,"finding");
            $form->submit->setLabel('Save');
			
            if (Zend_Auth::getInstance()->getStorage()->read()->lastlogin == '') {
                $form->submit->setLabel('Save & Continue');
			}           
            $this->view->form = $form;
            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {
                    $finding = new Model_DbTable_Finding();
                    $gtdatamodel = new Model_DbTable_Gtdata();
                    $gtdata = $gtdatamodel->getData($id);
                    $content = $form->getValues();
                    foreach ($content['presentationId'] as $presentations) {
                        $presid = $presid . $presentations . ",";
                    }
					if ($presid == ',')
					{
						$presid = "";
					}
                    $content['presentationId'] = $presid;
					$r = array_diff($content,$gtdata);
					
								
						$filenames = array(
							1 => $form->content1->getFileName(),
							2 => $form->content2->getFileName(),
							3 => $form->content3->getFileName(),
							4 => $form->content4->getFileName(),
							5 => $form->content5->getFileName()
						);
						$prestitles = array(
							1 => $content['prestitle1'],
							2 => $content['prestitle2'],
							3 => $content['prestitle3'],
							4 => $content['prestitle4'],
							5 => $content['prestitle5']
						);
						$checked = array(1 => false,2 => false,3 => false,4 => false,5 => false);
						$pmodel = new Model_DbTable_Presentation();
						$funcs = new Model_Functions();
						$i=1;
						$noPresAdded = 0;
						for($i=1;$i<=5;$i++)
						{
							$pres=file_get_contents($filenames[$i]);
							$filename = $filenames[$i];
							$fileext = $funcs->getFileExt($filename);
							
							if($filename != NULL)
							{
								if(!in_array(strtolower($fileext),array('pdf','doc','ppt','docx','pptx','xls','xlsx','jpeg','jpg','png','gif')))
								{
									$this->view->message = "File Type Not Allowed";
									return;
								}
							}
							
							$p=0;
							if($prestitles[$i] != "")
							{
								$data = array(
									'title' => $prestitles[$i],
									'GTId' => $gtdata['gtid'],
									'content' => $pres,
									'filetype' => $fileext,
									'userupdate' => Zend_Auth::getInstance()->getStorage()->read()->id
								);
								$gtpres = $pmodel->fetchAll('GTId = '. $gtdata['gtid']);
								$exists = false;
								foreach($gtpres as $gtp)
								{
									if($gtp['title'] == $prestitles[$i])
									{
										$exists = true;
									}
								}
								if($exists && !$checked[$i])
								{
									$this->view->message = "Presentation title already exists";
									return;
								}
								$p = $pmodel->insert($data);
								$noPresAdded++;
								$checked[$i] = true;
									
							}
							if($p != 0)
							{
								$temp_p .= $p . ",";
							}
							
						}
						if($presid != "")
						{
							$temp = $presid . $temp_p;
						}
						else {
							$temp = $temp_p;
						}
						$gtdatamodel = new Model_DbTable_Gtdata();
						$gtdata = $gtdatamodel->getData($id);
						$temp = $temp . $gtdata['presentationId'];
						$content['presentationId'] = $temp;
						if($content['subSysId'] == 0 || $content['subSysId'] == "")
						{
							$content['subSysId'] = 34;
						}
						$content = array(
							'id'   => $id,
							'gtid' => $gtdata['gtid'],
							'type' => 'finding',
							'data' => $content['data'],
							'userupdate' => Zend_Auth::getInstance()->getStorage()->read()->id,
							'title' => $content['title'],
							'presentationId' => $temp,
							'sysId' => $content['sysId'],
							'subSysId' => $content['subSysId'],
							'EOH' => $content['EOH'],
							'DOF' => $content['DOF'],
							'TOI' => $content['TOI']
						);
                    	$affRows = $finding->updateFinding($content);
						if($affRows + $noPresAdded > 0)
						{
	                    	$nf = new Model_DbTable_Notification();
	                        $formD = $this->_getParam('id', 0);
                        	$nf->add($formD, 'finding', 0);
						}
                    $this->_redirect('/findings/view?id=' . $id);
                    if (Zend_Auth::getInstance()->getStorage()->read()->lastlogin == '') {
                        $this->_redirect('finding/list');
                    }
                } else {
                	if($formData['DOF'] == "0000-00-00")
					{
						$formData['DOF'] = "";
					}
					if($formData['EOH'] == 0)
					{
						$formData['EOH'] == "";
					}
					$x = 1;
					for($x=1;$x<=5;$x++)
					{
						$formData['prestitle' . $x] = "";
					}
                    $form->populate($formData);
                }
            } else {
                $id = $this->_getParam('id', 0);
                $fin = new Model_DbTable_Finding();
				$fdata = $fin->getFinding($id);
				if($fdata['DOF'] == "0000-00-00")
				{
					$fdata['DOF'] = "";
				}
				if($fdata['EOH'] == 0)
				{
					$fdata['EOH'] = "";	
				}
               	$form->populate($fdata);
				$form->subSysId->setValue($fdata['subSysId']);
				$this->view->gtdata = $fin->getFinding($id);
            }
        } catch (exception $e) {
            echo $e;
        }
    }

    public function listAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->getHelper('Layout')->disableLayout();
        }
        $id = $this->_getParam('id', 0);
		
        $resultSet = new Model_DbTable_Finding();
        $resultSet = $resultSet->listFinding($id);
		
		$uModel = new Model_DbTable_User();
		$this->view->usermodel = $uModel;
		
		$userModel = new Model_DbTable_Userprofile();
		$this->view->umodel = $userModel;
		
		$sModel = new Model_DbTable_Gtsystems();
		$this->view->sysModel = $sModel;
		
		$ssModel = new Model_DbTable_Gtsubsystems();
		$this->view->subSysModel = $ssModel;
		
        $up = new Model_DbTable_Userprofile();
        $up = $up->getUser(Zend_Auth::getInstance()->getStorage()->read()->id);
        $pid = $up['plantId'];
        $gtmodel = new Model_DbTable_Gasturbine();
        $gt = $gtmodel->getGTP($pid);
		
		$gast = $gtmodel->getGT($id);
		$role = Zend_Registry::get("role");
		if((int)$gast['plantId'] == (int)$pid || $role == 'sa')
		{
			$this->view->ubool = true;
		}
		else {
			$this->view->ubool = false;
		}
		
        $this->view->findings = $resultSet;
        $this->view->id = $id;
        $this->view->gt = $gt;
    }

    public function deleteAction() {
        if ($this->getRequest()->isPost()) {
            $del = $this->getRequest()->getPost('del');
            if ($del == 'Delete') {
                $id = $this->getRequest()->getPost('id');
                $user = new Model_DbTable_Finding();
				$gtdatamodel = new Model_DbTable_Gtdata();
				$data = $gtdatamodel->getData($id);
				$gtid = $data['gtid'];
                $user->deleteFinding($id);
				$this->_redirect("/gasturbine/view?id=" .$gtid . "#ui-tabs-2");
				
            }
        }
    }

    public function viewAction() {
        try {
            $id = $this->_getParam('id', 0);
            $result = new Model_DbTable_Finding();
            $result = $result->getFinding($id);
			
            $this->view->headTitle("View Finding - " . $result['title'], 'PREPEND');

            $presentations = new Model_DbTable_Presentation();
			$this->view->presmodel = $presentations;
            $plist = explode(',', $result['presentationId']);
            array_pop(&$plist);

            $ptitle = array();
            foreach ($plist as $pid) {
                $res = $presentations->getPresentation($pid);
                $temp = array();
                $temp[] = array_combine(array($res['presentationId']), array($res['title']));
                ;

                $ptitle = array_merge($ptitle, $temp); //array($res['presentationId'] => $res['title']));
            }

            $gt = new Model_DbTable_Gasturbine();
            $gt = $gt->getGT($result['gtid']);
			
			$sModel = new Model_DbTable_Gtsystems();
			$this->view->sysModel = $sModel;
			
			$ssModel = new Model_DbTable_Gtsubsystems();
			$this->view->subSysModel = $ssModel;
			
            $this->view->finding = $result;
            $this->view->plist = $ptitle;
            $this->view->gt = $gt;
        } catch (Exception $e) {
            echo $e;
        }
    }
    
    
    
    

}