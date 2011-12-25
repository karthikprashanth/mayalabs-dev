<?php
	class Plugin_ForumSearchIndex extends Zend_Controller_Plugin_Abstract  
	{
		public function preDispatch(Zend_Controller_Request_Abstract $request) {
	        parent::preDispatch($request);
	    }
		
		public function  dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        	parent::dispatchLoopStartup($request);
			
			$searchIndexModel = new Model_DbTable_SearchIndex();
			$posts = $searchIndexModel->getPosts();
			if(count($posts))
			{
				$indexModel = new Model_SearchIndex();
				foreach($posts as $post)
				{
					if($post['post_id'] != 0)
						$indexModel->updateIndex("forum",$post['post_id'],"newpost");
					else
						$indexModel->updateIndex("forum",$post['topic_id'],"newtopic");
					
				}
				$searchIndexModel->delete();
			}
			
			
		}
	}
?>