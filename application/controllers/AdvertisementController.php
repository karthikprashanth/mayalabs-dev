<?php

class AdvertisementController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {

    }

    public function addAction() {
        try {
            $this->view->headTitle('Add New Advertisement', 'PREPEND');
            $form = new Form_AdvertisementForm();
            $form->submit->setLabel('Add');
			$form->submit->setAttrib('class','gt-add');
            $this->view->form = $form;
            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {
                    $advert = new Model_DbTable_Advertisement(Zend_Db_Table_Abstract::getDefaultAdapter());
                    
                    $content = $form->getValues();
                    $fdata = file_get_contents($form->advertImage->getFileName());
                    $content['advertImage'] = $fdata;
					
					$advert->setAdvertData($content);
					$advert->save();
                    $this->_redirect("/advertisement/view?id=".$advert->getId());
                } else {
                    $form->populate($formData);
                }
            }
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function editAction() {
        $this->view->headTitle('Edit Advertisement', 'PREPEND');
        try {
            $form = new Form_AdvertisementForm();
            $id['advertId'] = $this->_getParam('id', 0);

            $advert = new Model_DbTable_Advertisement(Zend_Db_Table_Abstract::getDefaultAdapter(),$id['advertId']);
            $form->populate($advert->getAdvertData());
            $form->submit->setLabel('Save');
			$form->submit->setAttrib('class','user-save');

            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if (isset($formData['title'])) {
                    if ($form->isValid($formData)) {
                        $content = $form->getValues();
						
                        $fdata = file_get_contents($form->advertImage->getFileName());
                        $content['advertImage'] = $fdata;
                        $advert->setAdvertData($content);
						$advert->save();
                        $this->_helper->redirector('list');
                    }
                } else {
                    $form->populate($formData);
                }
            }
            $this->view->form = $form;
            $form->populate($id);
        } catch (exception $e) {
            echo $e;
        }
    }

    public function viewAction() {
        try {
            $this->view->headTitle('View Advertisement', 'PREPEND');
            $id = $this->_getParam('id', 0);
            $advert = new Model_DbTable_Advertisement(Zend_Db_Table_Abstract::getDefaultAdapter(),$id);
            $data = $advert->getAdvertData();			
            $img = 'uploads/'.$advert->getTitle();
            file_put_contents($img, $data['advertImage']);
            $this->view->viewImg = $img;
            $this->view->viewData = $data;
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function listAction() {
        try {
            $this->view->headTitle('List Advertisement', 'PREPEND');
            $advertList = Model_DbTable_Advertisement::getList();
            
            $adList = new Zend_Paginator(new Zend_Paginator_Adapter_Array($advertList));
            $adList->setItemCountPerPage(5)
                    ->setCurrentPageNumber($this->_getParam('page', 1));
            $this->view->adList = $adList;
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function randomadAction() {
        try {
            if (!$this->_request->isXmlHttpRequest())
                $this->_helper->viewRenderer->setResponseSegment('advert');
			
            $advert = Model_DbTable_Advertisement::getRandomAd();			
            $img = 'uploads/' . $advert['title'];
            file_put_contents($img, $advert['advertImage']);			
            $this->view->randomAd = $img;			
            $this->view->id = $advert['advertId'];
			$this->view->title = $advert['title'];
            
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function deleteAction() {
        if ($this->getRequest()->isPost()) {
            $del = $this->getRequest()->getPost('del');
            if ($del == 'Delete') {                
                $advert = new Model_DbTable_Advertisement(Zend_Db_Table_Abstract::getDefaultAdapter(),$this->getRequest()->getPost('id'));
				$advert->deleteAdvertisement();
            }
            $this->_helper->redirector('list');
        }
    }

}