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
		try{
			if($this->getRequest()->isPost()){
				//$this->_helper->getHelper('Layout')->disableLayout();
				$thumbPath = urldecode($this->getRequest()->getPost("thumbPath"));
				
				$file = file_get_contents($_FILES['img']['tmp_name']);
				$filename = "uploads/" . rand(0,999999) . $_FILES['img']['name'];
				file_put_contents($filename,$file);
				$thumbPath = "uploads/th_" . $_FILES['img']['name'];
	            include("../library/phMagick/phMagick.php");
	            
	            $phMagick = new \phMagick\Core\Runner();
		
				$resizeAction = new \phMagick\Action\Resize\Proportional($filename,$thumbPath);
				$resizeAction->setWidth(200);
				
				$phMagick->run($resizeAction);
				
				$data = file_get_contents($thumbPath);
				$tag = $this->getRequest()->getPost("tag");
				$cid = $this->getRequest()->getPost("cid");
				
	           	$ext = strtolower(substr($thumbPath,strpos($thumbPath,".")+1));
				
				$photo = new Model_DbTable_Gallery(Zend_Db_Table_Abstract::getDefaultAdapter());
				$photo->setPhotoData(array("tag" => $tag,"cid" => $cid,"data"=>$data,"ext" => $ext));
				$photo->save();
				$photoId = $photo->getPhotoId();
				
	            list($width, $height, $type, $attr) = getimagesize($filename);
	            
	            if($width > 1024 && $height > 768){
					$phMagick = new \phMagick\Core\Runner();
					$resizeAction = new \phMagick\Action\Resize\Proportional($filename,"uploads/gallery/photo_$photoId.$ext");
					$resizeAction->setWidth(1024);
					$resizeAction->setHeight(768);
					$phMagick->run($resizeAction);                
	            }
	            else{
	                $origData = file_get_contents($filename);
	                $origPath = "uploads/gallery/photo_$photoId".".".$ext;
	                file_put_contents($origPath,$origData);
	            }
	            $this->view->thumbPath = $thumbPath;
				$this->view->id = $photoId;
				$this->view->tag = $tag;
				unlink($filename);
				
			}
		}
		catch(Exception $e){
			echo $e;	
		}
		
	}
	
	/*public function uploadAction(){
		if ($this->getRequest()->isPost()) {
			$this->_helper->getHelper('Layout')->disableLayout();
		    $file = file_get_contents($_FILES['image']['tmp_name']);
			$filename = "uploads/" . rand(0,999999) . $_FILES['image']['name'];
			file_put_contents($filename,$file);
			$thumbPath = "uploads/th_" . $_FILES['image']['name'];
            include("../library/phMagick/phMagick.php");
            
            $phMagick = new \phMagick\Core\Runner();
	
			$resizeAction = new \phMagick\Action\Resize\Proportional($filename,$thumbPath);
			$resizeAction->setWidth(200);
			
			$phMagick->run($resizeAction);
            if(!file_exists($thumbPath)){
            	echo json_encode(array("error" => "1"));
				unlink($filename);
            }
			else {
				echo json_encode(array("imgPath"=>$filename,"thumbPath"=>$thumbPath,"error" => "0","filename" => $_FILES['image']['name']));
			}
		}
	}*/
	
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
			
			$galForm = new Form_GalleryForm();
			$galForm->populate(array("cid" => $cid));
			$this->view->galleryForm = $galForm;
		}
		catch(Exception $e){
			echo $e;
		}
	}
	
	public function deleteAction(){
		try{
			$photoId = $this->getRequest()->getPost('photoId');
			$photo = new Model_DbTable_Gallery(Zend_Db_Table_Abstract::getDefaultAdapter(),$photoId);
			$ext = $photo->getExtension();
			unlink("uploads/gallery/photo_".$photo->getPhotoId().".".$ext);
			$cid = $photo->getConferenceId();
			$photo->deletePhoto();
			$this->_redirect("/conference/view?id=".$cid."#ui-tabs-3");
		}
		catch(Exception $e){
			echo $e;
		}
	}

}