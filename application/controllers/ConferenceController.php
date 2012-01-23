<?php

class ConferenceController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	
    	$this->_redirect("conference/list");	    
    }
	
	public function addAction()
    {
    	try{
	        $form = new Form_ConferenceForm();
			$this->view->form = $form;
			$form->submit->setLabel('Add Conference');
			$this->view->headTitle('Add New Conference','PREPEND');
			if($this->getRequest()->isPost())
			{
				$formData = $this->getRequest()->getPost();
				if($form->isValid($formData))
				{
					$conf = new Model_DbTable_Conference(Zend_Db_Table_Abstract::getDefaultAdapter());
					$content = array();
					$content['host'] = $formData['host'];
					$content['year'] = $formData['year'];
					$content['place'] = $formData['place'];
					$content['abstract'] = $formData['abstract'];
					$conf->setConferenceData($content);
					$conf->save();
					$this->_redirect("conference/view?id=".$conf->getConferenceId());
				}
				else
				{
					$form->populate($formData);
				}
			}
		}
		catch(Exception $e){
			echo $e;
		}
    }
	
	public function editAction()
	{
		try{
			$cid = $this->_getParam('id',0);
			if(!$cid){
				$this->_redirect("conference/list");
			}
			$conf = new Model_DbTable_Conference(Zend_Db_Table_Abstract::getDefaultAdapter(),$cid);
			$form = new Form_ConferenceForm();
			$this->view->form = $form;
			$form->submit->setLabel("Save");
			$this->view->headTitle("Edit - " . $conf->getPlace() . " (" . $conf->getYear() . ")","PREPEND");
			if($this->getRequest()->isPost())
			{
				$formData = $this->getRequest()->getPost();
				if($form->isValid($formData))
				{
					$content = array();
					$content['cId'] = $cid;
					$content['host'] = $formData['host'];
					$content['year'] = $formData['year'];
					$content['place'] = $formData['place'];
					$content['abstract'] = $formData['abstract'];
					$conf->setConferenceData($content);
					$conf->save($content);
					$this->_redirect("/conference/view?id=".$cid);
				}
			}
			else {
				$confData = $conf->getConferenceData();				
				$form->populate($confData);
			}
		}
		catch(Exception $e){
			echo $e;
		}
	}

	public function viewAction()
	{
		try{
			
			$cid = $this->_getParam('id',0);
			if(!$cid){
				$this->_redirect("conference/list");
			}
			$conf = new Model_DbTable_Conference(Zend_Db_Table_Abstract::getDefaultAdapter(),$cid);
			$this->view->headTitle($conf->getPlace() . " (" . $conf->getYear() . ")",'PREPEND');
			$this->view->conf = $conf;
		}
		catch(Exception $e){
			echo $e;
		}
	}
	
	public function listAction()
    {
        try{
        	$this->view->headTitle("Conferences",'PREPEND');
			$conferenceList = Model_DbTable_Conference::getList(array('orderby' => 'year'));
			$this->view->conferences = $conferenceList;
			
			$role = Zend_Registry::get('role');
			$uid = Zend_Auth::getInstance()->getStorage()->read()->id;
			$user = new Model_DbTable_User(Zend_Db_Table_Abstract::getDefaultAdapter(),$uid);
			if($role == 'sa' || $user->isConferenceChairman()){
				$this->view->allowed = true;
			}
			else{
				$this->view->allowed  = false;
			}
        }
		catch(Exception $e){
			echo $e;
		}
	}
		
	public function deleteAction()
	{
		try{
			$cid = $this->_getParam('id',0);
			$conf = new Model_DbTable_Conference(Zend_Db_Table_Abstract::getDefaultAdapter(),$cid);
			$conf->deleteConference();
			$this->_redirect("conference/list");
			/*Delete schedules also*/
		}
		catch(Exception $e){
			echo $e;
		}
	}
	
	/*public function viewAction()
    {
    	$this->_helper->getHelper('Layout')->disableLayout();
    	$this->view->headTitle('View Presentation','PREPEND');
        $presModel = new Model_DbTable_ConfPresentation();
        $id = $this->_getParam('id',0);
        $presDet = $presModel->getPres($id);
        $data = $presDet['content'];
		$filename = $presDet['title'] . "_".rand(0,999999).".".$presDet['filetype'];
		$appath = substr(APPLICATION_PATH,0,strlen(APPLICATION_PATH)-12);
		$appath = $appath . "/public/uploads/";
		$file = file_put_contents($appath.$filename,$data);
		$this->view->browserfilename = $filename;
		$this->view->origfilepath = $appath . $filename;
	}
	
	public function galleryAction()
	{
		try{
					
	        $cid = $this->_getParam('id',0);
	        $this->view->headTitle('New Presentation','PREPEND');
	        $form=new Form_GalleryForm();
	        $form->submit->setLabel('Upload Photo');
	        $this->view->form=$form;                    
	        
	        if($this->getRequest()->isPost()){
		        $formData=$this->getRequest()->getPost();
		        if(isset ($formData['tag'])){
			        if($form->isValid($formData)){
					    $userp=new Model_DbTable_Gallery();
					    $content=$form->getValues();
					    $pdata=file_get_contents($form->data->getFileName());
			        	$content['data']=$pdata;
						$content['cId'] = $cid;
						
						$columns = array(
							'tag' => $content['tag'],
							'cId' => $cid,
							'data' => $pdata
						);
			        	$userp->insert($columns);
						$this->_redirect("/conference/list?id=".$cid."#confdata-frag-4");
			        }
		        }
	   		}
       	}
        catch(Exception $e){
            echo $e;
        }
	}


	public function addpresentationAction()
	{
		try{
					
	        $cid = $this->_getParam('id',0);
	        $this->view->headTitle('New Presentation','PREPEND');
	        $form=new Form_ConfPresentationForm();
	        $form->submit->setLabel('Add');
	        $this->view->form=$form;                    
	        
	        if($this->getRequest()->isPost()){
		        $formData=$this->getRequest()->getPost();
		        if(isset ($formData['title'])){
			        if($form->isValid($formData)){
					    $userp=new Model_DbTable_ConfPresentation();
					    $content=$form->getValues();
					    $pdata=file_get_contents($form->content->getFileName());
						$funcs = new Model_Functions();
						$filename = $form->content->getFileName();
						$fileext = $funcs->getFileExt($filename);
						$gtpreslist = $userp->getPresDetail($cid);
						$exists = false;
						foreach($gtpreslist as $p)
						{
							if($p['title'] == $content['title'])
							{
								$exists = true;
								break;
							}
						}
						if($exists)
						{
							$this->view->message = "Presentation title already exists";
							return;
						}
						if(in_array($fileext,array('pdf','doc','ppt','docx','pptx','xls','xlsx','jpg','jpeg','gif','png')))
						{			
				        	$content['content']=$pdata;
							
							$columns = array(
								'title' => $content['title'],
								'cId' => $cid,
								'filetype' => $fileext,
								'plantId' => $content['plantId'],
								'content' => $pdata
							
							);
				        	$userp->insert($columns);
							$this->_redirect('/conference/list?id='.$cid . "#confdata-frag-3");
						}
						else {
							$this->view->message = "File Type Not Allowed";
							return;
				    	}
			        }
		        }
	   		}
       	}
        catch(Exception $e){
            echo $e;
        }
	}	
	
	public function delpresAction()
	{
		if($this->getRequest()->isPost())
		{
			$id = $this->getRequest()->getPost("presid");
			$presmodel = new Model_DbTable_ConfPresentation();
			$pres = $presmodel->getPres($id);
			$cid = $pres['cId'];
			$presmodel->delete("presentationId = " . (int)$id);
			$this->_redirect("/conference/list?id=" . $cid . "#confdata-frag-3");
			
		}
	}
	
	public function delphotoAction()
	{
		$id = $this->_getParam('id',0);
		$confgal = new Model_DbTable_Gallery();
		$gal = $confgal->getPhoto($id);
		$cid = $gal['cId'];
		$confgal->delete("photoId = " . $id);
		$this->_redirect("/conference/list?id=" . $cid . "#confdata-frag-4");
		
	}*/
	
	
}