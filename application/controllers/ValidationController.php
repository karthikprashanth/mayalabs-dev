<?php

class ValidationController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->headTitle('Index Page','PREPEND');
    }
	
	public function validateAction()
	{
		try {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->getHelper('layout')->disableLayout();
			$formname = $this->_getParam('form',"");
			if($formname == 'gasturbine')
			{
            	$form = new Form_GasturbineForm();
			}
			else if($formname == 'plant')
			{
				$form = new Form_PlantForm();
				$form->showForm();
				$formData = $this->getRequest()->getPost();
				$form->partPlant1->isValid($formData);
				$form->partPlant2->isValid($formData);
				$form->partPlant3->isValid($formData);
				$json = $form->partPlant1->getMessages();
	            $json = array_merge($json, $form->partPlant2->getMessages());
	            $json = array_merge($json, $form->partPlant3->getMessages());
			}
			else if($formname == 'gtdata')
			{
				$form = new Form_GTDataForm();
				$formData = $this->getRequest()->getPost();
				$type = $this->_getParam("type","");
				$form->showform($formData['gtid'],0,$type);	
			}
			else if($formname == 'changepassword')
			{
				$form = new Form_ChangePasswordForm();
			}
			else if($formname == 'conference')
			{
				$form = new Form_ConferenceForm();
			}
			else if($formname == 'confpresentation')
			{
				$form = new Form_ConfPresentationForm();
			}
			else if($formname == 'gallery')
			{
				$form = new Form_GalleryForm();
			}
			else if($formname == 'presentation')
			{
				$form = new Form_PresentationForm();
			}
			else if($formname == "schevent")
			{
				$form = new Form_ScheduleEventForm();
			}
			else if($formname == "schedule")
			{
				$form = new Form_ScheduleForm();
			}
			else if($formname == "userprofile")
			{
				$form = new Form_UserprofileForm();
			}
			else if($formname == "registration")
			{	
				$form = new Form_RegistrationForm();
			}
			if($formname != 'plant')
            {
            	$formData = $this->getRequest()->getPost();
	            $form->isValid($formData);
	            $json = $form->getMessages();
			}
			echo Zend_Json::encode($json);
        } 
        catch (Exception $e) {
            echo $e;
        }
	}


}