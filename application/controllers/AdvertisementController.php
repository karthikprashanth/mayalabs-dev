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
            //JQuery Form Enable
            ZendX_JQuery::enableForm($form);
            $form->submit->setLabel('Add');
            $this->view->form = $form;
            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {
                    $userp = new Model_DbTable_Advertisement();
                    $content = $form->getValues();

                    $time = new Model_DbTable_Advertisement();
                    $time = $time->timeStamp();
                    $content['timeupdate'] = $time;

                    $fdata = file_get_contents($form->advertImage->getFileName());
                    $content['advertImage'] = $fdata;

                    $userp->add($content);
                    //$this->_redirect('index');
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
            //JQuery Form Enable
            ZendX_JQuery::enableForm($form);

            $id['advertId'] = $this->_getParam('advertId', 0);

            $GTVal = new Model_DbTable_Advertisement();
            $form->populate($GTVal->getAdvertisement($id['advertId']));
            $form->submit->setLabel('Save');

            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if (isset($formData['title'])) {
                    if ($form->isValid($formData)) {
                        $GT = new Model_DbTable_Advertisement();
                        $content = $form->getValues();

                        $time = new Model_DbTable_Advertisement();
                        $time = $time->timeStamp();
                        $content['timeupdate'] = $time;

                        $fdata = file_get_contents($form->advertImage->getFileName());
                        $content['advertImage'] = $fdata;
                        $GT->updateAdvertisement($content);
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
            $advert = new Model_DbTable_Advertisement();
            $data = $advert->getAdvertisement($id);
            $img = 'random/abcd';
            file_put_contents($img, $data['advertImage']);
            $this->view->viewImg = $img;
            $this->view->viewData = $data;
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function listAction() {
        try {
            $role = Zend_Registry::get('role');
            $this->view->role = $role;
            $this->view->headTitle('List Advertisement', 'PREPEND');
            $advert = new Model_DbTable_Advertisement();
            $data = $advert->fetchAll();
            $data = $advert->listAds();
            $adList = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($data));
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
			
            $advert = new Model_DbTable_Advertisement();
            $data = $advert->randomAd();
			
            $img = 'random/abc' . rand(0,999999);
            file_put_contents($img, $data['advertImage']);
			
            $this->view->randomAd = $img;
			$this->view->desc = $data['description'];
            $this->view->adTitle = $data;
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function deleteAction() {
        if ($this->getRequest()->isPost()) {
            $del = $this->getRequest()->getPost('del');
            if ($del == 'Delete') {
                echo 'hi';
                $id = $this->getRequest()->getPost('id');
                $user = new Model_DbTable_Advertisement();
                $user->deleteAdvertisement($id);
            }
            $this->_helper->redirector('list');
        }
    }

}