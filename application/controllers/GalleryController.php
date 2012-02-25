<?php

class GalleryController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        
    }
	
	public function addAction(){
		if($this->getRequest()->isPost()){
			$this->_helper->getHelper('Layout')->disableLayout();
			$thumbPath = urldecode($this->getRequest()->getPost("thumbPath"));
			
			$data = file_get_contents($thumbPath);
			$tag = $this->getRequest()->getPost("tag");
			$cid = $this->getRequest()->getPost("cid");
                        $ext = Model_Functions::getFileExt($thumbPath);
			
			$photo = new Model_DbTable_Gallery(Zend_Db_Table_Abstract::getDefaultAdapter());
			$photo->setPhotoData(array("tag" => $tag,"cid" => $cid,"data"=>$data,"ext" => $ext));
			$photo->save();
			$photoId = $photo->getPhotoId();
			
                        $filename = $this->getRequest()->getPost("imgPath");
                        list($width, $height, $type, $attr) = getimagesize($filename);
                        
                        if($width > 1024 && $height > 768){
                            include("../library/phmagick/phmagick.php");
                            $p = new phmagick($filename,"uploads/gallery/photo_$photoId.$ext");
                            $p->resize(1024,768);
                        }
                        else{
                            $origData = file_get_contents($this->getRequest()->getPost("imgPath"));
                            $origPath = "uploads/gallery/photo_$photoId".".".$ext;
                            file_put_contents($origPath,$origData);
                        }
                        
			unlink($this->getRequest()->getPost("imgPath"));                       
			echo json_encode(array("imgId"=>$photoId,"tag" => $tag,"ext" => $ext,"thumbPath"=>"/" .$thumbPath));
		}
		
	}
	
	public function uploadAction(){
		if ($this->getRequest()->isPost()) {
			$this->_helper->getHelper('Layout')->disableLayout();
		    $file = file_get_contents($_FILES['image']['tmp_name']);
			$filename = "uploads/" . rand(0,999999) . $_FILES['image']['name'];
			file_put_contents($filename,$file);
			$thumbPath = "uploads/th_" . $_FILES['image']['name'];                        
                        
                        include("../library/phmagick/phmagick.php");
                        
                        $p = new phmagick($filename,$thumbPath);
                        $p->resize(200);
                        
			echo json_encode(array("imgPath"=>$filename,"thumbPath"=>$thumbPath));
		}
	}
	
	public function listAction(){
		try{
			$this->_helper->getHelper('Layout')->disableLayout();
			$cid = $this->_getParam('id');
			
			$role = Zend_Registry::get("role");
			$uid = Zend_Auth::getInstance()->getStorage()->read()->id;
	        $user = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),$uid);
			if($role == 'sa' || $user->isConferenceChairman()){
				$this->view->allowed = true;
			}		
			$this->view->cid = $cid;
			$photos = Model_DbTable_Gallery::getList(array('cId' => $cid));
			$this->view->photos = $photos;
			$this->view->galleryForm = new Form_GalleryForm();
		}
		catch(Exception $e){
			echo $e;
		}
	}
	
	public function deleteAction(){
		
	}

}