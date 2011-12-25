<?php

class ConferenceController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	
    	$this->view->headTitle('Conference','PREPEND');
        $conf_table = new Model_DbTable_Conference();
	    $yearList = $conf_table->getConfList();
	    $this->view->yrList = $yearList;
	    $bUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
	    $this->view->baseUrl = $bUrl;
	    
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
		
	}
    public function addAction()
    {
    	
        $form = new Form_ConferenceForm();
		$this->view->form = $form;
		$form->submit->setLabel('Add Conference');
		if($this->getRequest()->isPost())
		{
			$formData = $this->getRequest()->getPost();
			if($form->isValid($formData))
			{
				
				$conf = new Model_DbTable_Conference();
				$content = array();
				$content['host'] = $formData['host'];
				$content['year'] = $formData['year'];
				$content['place'] = $formData['place'];
				$content['abstract'] = $formData['abstract'];
				$newcid = $conf->insert($content);
				$this->_redirect("conference/list?id=".$newcid);
			}
			else
			{
				$form->populate($formData);
			}
		}
    }
	
	public function editAction()
	{
		
		$cid = $this->_getParam('id',0);
		$confmodel = new Model_DbTable_Conference();
		$form = new Form_ConferenceForm();
		$this->view->form = $form;
		$form->submit->setLabel("Save");
		if($this->getRequest()->isPost())
		{
			$formData = $this->getRequest()->getPost();
			if($form->isValid($formData))
			{
				$content = array();
				$content['host'] = $formData['host'];
				$content['year'] = $formData['year'];
				$content['place'] = $formData['place'];
				$content['abstract'] = $formData['abstract'];
				$confmodel->updateConf($cid,$content);
				$this->_redirect("/conference/list?id=".$cid);
			}
		}
		else {
			$conf = $confmodel->getConfDetail($cid);
			$form->populate($conf);
		}
	}
	
	public function deleteAction()
	{
		$cid = $this->_getParam('id',0);
		$confmodel = new Model_DbTable_Conference();
		$noteModel = new Model_DbTable_Notification();
		$confmodel->delete('cId = ' . (int)$cid);
		$schmodel = new Model_DbTable_Schedule();
		$sch = $schmodel->getSchId($cid);
		$schid = $sch['sch_id'];
		echo $schid;
		$schmodel->delete('cId = ' . (int)$cid);
		$nf = new Model_DbTable_Notification();
		$nf->delete('catId = ' . (int)$cid);
		$scheventmodel = new Model_DbTable_ScheduleEvent();
		$scheventmodel->delete('sch_id = ' . (int)$schid);
		$noteModel->delete("category = 'schedule' AND catid = " . $cid);
		$this->_redirect("/conference/index");
	}
	
	
   	public function listAction()
    {
        try {
        	$this->view->headTitle('View Conference','PREPEND');
	        $id = $this->_getParam('id',0);
	        $this->view->id = $id;
	
	        $confModel = new Model_DbTable_Conference();
	        $confRow = $confModel->getConfDetail($id);
	        $this->view->confDet = $confRow;
	
	        $bUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
	        $this->view->baseUrl = $bUrl;
	
	        $galModel = new Model_DbTable_Gallery();
	        $galList = $galModel->getGallery($id);
	        $this->view->confGallery = $galList;
	
	        $presModel = new Model_DbTable_ConfPresentation();
	        $presList = $presModel->getPresDetail($id);
	        $this->view->presList = $presList;
	
	        $plantDet = new Model_DbTable_Plant();
	        $this->view->plantDet = $plantDet;
			
			$uid = Zend_Auth::getInstance()->getStorage()->read()->id;
			$uModel = new Model_DbTable_User();
			$iscc = $uModel->is_confchair($uid);
    		$this->view->iscc = $iscc;
        }
        catch(Exception $e){
            echo $e;
        }
	}	
	
    public function viewAction()
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
	
	
}