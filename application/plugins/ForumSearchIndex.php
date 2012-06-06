<?php
	class Plugin_ForumSearchIndex extends Zend_Controller_Plugin_Abstract  
	{
		public function preDispatch(Zend_Controller_Request_Abstract $request) {
	        parent::preDispatch($request);
	    }
		
		public function  dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        	parent::dispatchLoopStartup($request);
			
			if($request->getControllerName() == "dashboard" && $request->getControllerName == "showmenu") {
				return;
			}
			
			if(Model_DbTable_SearchIndex::getCount()){				
				$indices = Model_DbTable_SearchIndex::getList();
				
				foreach($indices as $spIndex) {					
					$sp = new Model_DbTable_SearchIndex(Zend_Db_Table_Abstract::getDefaultAdapter(),$spIndex['id']);
					
					if($sp->getType() == "add" || $sp->getType() == "edit") {
						Model_SearchIndex::updateIndex("forum",$sp->getPostId());
					}
					else {
						Model_SearchIndex::deleteForumIndices($sp->getPostId());
					}
					$sp->deleteIndexData();
				}
			}
		}
	}