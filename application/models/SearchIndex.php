<?php
	class Model_SearchIndex
	{
		/**
		 * Updates the gtdata index file
		 * 
		 * @param Integer - The id of the gtdata
		 */
		public static function updateIndex($type,$id)
		{
			$doc = new Zend_Search_Lucene_Document();
			$index = Model_SearchIndex::getIndex($type);
			if($type == "gtdata") {
				
				$gtdata = new Model_DbTable_Gtdata(Zend_Db_Table_Abstract::getDefaultAdapter(),$id);
				$gtdataArray = $gtdata->getData();
				
				/* Deleting existing index with the same id */
				Model_SearchIndex::deleteGTDataIndices($gtdata->getTitle(),$gtdata->getType());
				
				$user = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),$gtdata->getUserUpdate());
				$username = $user->getFullName();				
				$uplantname = $user->getPlantName();
				
				$sysname = $gtdata->getSystemName();
				
				if($gtdataArray['subSysId'] != 34)
				{					
					$subsysname = $gtdata->getSubSystemName();
				}
				else {
					$subsysname = "-";
				}
				
				$doc->addField(Zend_Search_Lucene_Field::UnIndexed('dataid',$id));
				$doc->addField(Zend_Search_Lucene_Field::UnStored('title',$gtdata->getTitle()));
				$doc->addField(Zend_Search_Lucene_Field::UnStored('data',$gtdataArray['data']));
				$doc->addField(Zend_Search_Lucene_Field::UnStored('type',$gtdata->getType()));
				$doc->addField(Zend_Search_Lucene_Field::UnStored('sysname',$sysname));  
				$doc->addField(Zend_Search_Lucene_Field::UnStored('subsysname',$subsysname));
				$doc->addField(Zend_Search_Lucene_Field::Keyword('eoh',$gtdataArray['EOH']));
				$doc->addField(Zend_Search_Lucene_Field::UnStored('toi',$gtdataArray['TOI']));
				$doc->addField(Zend_Search_Lucene_Field::UnStored('userplantname',$uplantname));
				
			}
			else if($type == "forum") {
								
				$post = new Model_DbTable_Forum_Posts(Zend_Db_Table_Abstract::getDefaultAdapter(),$id);
				$postData = $post->getPostData();
				
				$topic = new Model_DbTable_Forum_Topics(Zend_Db_Table_Abstract::getDefaultAdapter(),$postData['topic_id']);
				$topicData = $topic->getTopicData();
				
				$forum = new Model_DbTable_Forum_Forums(Zend_Db_Table_Abstract::getDefaultAdapter(),$postData['forum_id']);
				$forumData = $forum->getForumData();
				
				//Delete existing indices with similar parameters
				Model_SearchIndex::deleteForumIndices($id);
				
				$user = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),$postData['poster_id']);
				$uplantname = $user->getPlantName();
				
				$doc->addField(Zend_Search_Lucene_Field::Keyword('post_id',$postData['post_id']));
				$doc->addField(Zend_Search_Lucene_Field::UnStored('post_subject',$postData['post_subject']));
				$doc->addField(Zend_Search_Lucene_Field::UnStored('post_text',$postData['post_text']));
				$doc->addField(Zend_Search_Lucene_Field::UnStored('topicname',$topicData['topic_title']));
				$doc->addField(Zend_Search_Lucene_Field::UnStored('forumname',$forumData['forum_name']));
				$doc->addField(Zend_Search_Lucene_Field::UnStored('userplantname',$uplantname));
			}
			
			$index->addDocument($doc);
			$index->commit();
			$index->optimize();
		}

		/**
		 * Returns the Zend_Lucene_Search_Index object based on the type
		 * 
		 * @param String - `gtdata` or `forum` 
		 * @return Zend_Search_Lucene_Index
		 */		
		public static function getIndex($type){
			$appath = substr(APPLICATION_PATH,0,strlen(APPLICATION_PATH)-12);
			$path = $appath . DIRECTORY_SEPARATOR . "search" . DIRECTORY_SEPARATOR . $type;	
			$index = Zend_Search_Lucene::open($path);
			return $index;
		}
		
		/**
		 * Deletes gtdata indices which has the given query
		 * 
		 * @param String - The Title of the gtdata
		 * @param String - The type - finding/upgrade/lte
		 */
		public static function deleteGTDataIndices($title,$type){		 	
		 	$index = Model_SearchIndex::getIndex("gtdata");			
		 	$hits = $index->find("title:$title AND type:$type");
			foreach($hits as $hit){
				$index->delete($hit->id);
			}
			$index->commit();
			$index->optimize();
		 }
		 
		 /**
		  * Deletes forum indices which has the given query
		  * 
		  * @param String - Post Text
		  * @param String - Post Subject
		  * @param String - Topic Name
		  * @param String - Forum Name
		  */
		 public static function deleteForumIndices($id) {		 				
		 	$index = Model_SearchIndex::getIndex("forum");			
		 	$hits = $index->find("post_id:$id");
			foreach($hits as $hit){
				$index->delete($hit->id);
			}
			$index->commit();
			$index->optimize();
		 } 
	}