<?php

class PresentationController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }
	public function unlinkAction()
	{
		$this->_helper->getHelper('Layout')->disableLayout();
		if($this->getRequest()->isPost())
		{
			$id = $this->getRequest()->getPost('id');
			$gtdataid = $this->getRequest()->getPost('gtdataid');
			$presmodel = new Model_DbTable_Presentation();
			$pres = $presmodel->getPresentation($id);
			$gtdatamodel = new Model_DbTable_Gtdata();
			$gtdata = $gtdatamodel->getData($gtdataid);
			$presid = substr($gtdata['presentationId'],0,strlen($gtdata['presentationId'])-1);
			$presarray = explode(",",$presid);
			for($i=0;$i<count($presarray);$i++)
			{
				if($presarray[$i] == $id)
				{
					unset($presarray[$i]);
					break;
				}
			}
			$presid = implode(",",$presarray);
			
			$data['presentationId'] = $presid . ",";
			if($data['presentationId'] == ",")
			{
				$data['presentationId'] = "";
			}
			$where['id = ?'] = $gtdataid;
			$gtdatamodel->update($data,$where);
			
			$gtid = $gtdata['gtid'];
			$gtpres = $presmodel->fetchAll("GTId = " . $gtid);
			$presarray = explode(",",$data['presentationId']);
			echo "<option value = '' label = 'Select an Option'>Select an Option</option>";
			foreach($gtpres as $gtp)
			{
				$exists = false;
				for($i=0;$i<count($presarray);$i++)
				{
					if($presarray[$i] == $gtp['presentationId'])
					{
						$exists = true;
						break;
					}
				}
				if(!$exists)
				{
					echo "<option value = '" . $gtp['presentationId'] . "' label = '" . $gtp['title'] . "'>" . $gtp['title'] . "</option>";
				}
			}
			
		}
	}
    public function addAction()
    {
        try{	
	        $gtid['GTId'] = $this->getRequest()->getPost('gtid');
            $gturbineid = $gtid['GTId'];
            $this->view->headTitle('New Presentation','PREPEND');
            $form=new Form_PresentationForm();
            //JQuery Form Enable
            //ZendX_JQuery::enableForm($form);
            $form->submit->setLabel('Add');
            $this->view->form=$form;
            $form->populate($gtid);
	        if($this->getRequest()->isPost()){
		        $formData=$this->getRequest()->getPost();
				
		        if(isset ($formData['title'])){
			        if($form->isValid($formData)){
					    $userp=new Model_DbTable_Presentation();
					    $content=$form->getValues();
					    $pdata=file_get_contents($form->content->getFileName());
						$funcs = new Model_Functions();
						$filename = $form->content->getFileName();
						$fileext = $funcs->getFileExt($filename);
						$gtpreslist = $userp->fetchAll("GTId = " . $formData['GTId']);
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
								'GTId' => $formData['GTId'],
								'filetype' => $fileext,
								'userupdate' => Zend_Auth::getInstance()->getStorage()->read()->id, 
								'content' => $pdata
							);
				        	$userp->insert($columns);
							$this->_redirect('/gasturbine/view?id='.$formData['GTId'].'#ui-tabs-5');
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

    public function listAction()
    {
        if($this->_request->isXmlHttpRequest()) {
                            $this->_helper->getHelper('Layout')->disableLayout();
                        }

                        try{
                        $this->view->headTitle('List Presentations','PREPEND');
                        $id = $this->_getParam('id', 0);
                        $result=new Model_DbTable_Presentation();
                        $result=$result->listPresentation($id);

                        $this->view->presentations = $result;
                        $this->view->id=$id;
                        }

                        catch(Exception $e){
                            echo $e;
                        }
    }

    public function viewAction()
    {
        $this->_helper->getHelper('Layout')->disableLayout();
    	$this->view->headTitle('View Presentation','PREPEND');
        $presModel = new Model_DbTable_Presentation();
        $id = $this->_getParam('id',0);
        $presDet = $presModel->getPresentation($id);
        $data = $presDet['content'];
		$filename = $presDet['title'] . "_".rand(0,999999).".".$presDet['filetype'];
		$appath = substr(APPLICATION_PATH,0,strlen(APPLICATION_PATH)-12);
		$appath = $appath . "/public/uploads/";
		$file = file_put_contents($appath.$filename,$data);
		$this->view->browserfilename = $filename;
		$this->view->origfilepath = $appath . $filename;
    }
	
	public function deleteAction()
	{
		if($this->getRequest()->isPost())
		{
			$id = $this->getRequest()->getPost('id');
			$gtdatamodel = new Model_DbTable_Gtdata();
			$gtdata = $gtdatamodel->fetchAll("presentationId like '%" . $id . "%'");
			foreach($gtdata as $d)
			{
				$presid = substr($d['presentationId'],0,strlen($d['presentationId'])-1);
				$pres_arr = explode(",",$presid);
				for($i=0;$i<count($pres_arr);$i++)
				{
					if($pres_arr[$i] == $id)
					{
						unset($pres_arr[$i]);
						break;
					}
				}
				$data = array();
				$data['presentationId'] = implode(",",$pres_arr) . ",";
				if($data['presentationId'] == ",")
				{
					$data['presentationId'] = "";
				}
				$where = array();
				$where['id = ?'] = $d['id'];
				$gtdatamodel->update($data,$where);
			}
			$presmodel = new Model_DbTable_Presentation();
			$pres = $presmodel->getPresentation($id);
			$gtid = $pres['GTId'];
			$presmodel->delete('presentationId =' . (int) $id);
			$this->_redirect("/gasturbine/view?id=".$gtid."#ui-tabs-5");
		}
	}


}

