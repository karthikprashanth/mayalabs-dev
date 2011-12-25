<?php

class Plugin_BookmarkLayout extends Zend_Controller_Plugin_Abstract  {

    protected $_view;
    public function  preDispatch(Zend_Controller_Request_Abstract $request) {
        parent::preDispatch($request);
        
    }
    public function  dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        parent::dispatchLoopStartup($request);
		
        if($request->isXmlHttpRequest())
            $valid = false;
        if($request->getActionName()!='view')
            $valid = false;
		else 
			$valid = true;
		if($request->getControllerName() == 'userprofile')
			$valid = false;
		if($request->getControllerName() == 'conference' && $request->getActionName() == 'list')
			$valid = true;
		if($request->getControllerName() == 'schedule' && $request->getActionName() == 'view')
			$valid = false;
		if($request->getControllerName() == 'notification' || $request->getControllerName() == 'search')
			$valid = false;
		if(!$valid)
			return;
        $front = Zend_Controller_Front::getInstance();
        if (!$front->hasPlugin(
                        'Zend_Controller_Plugin_ActionStack')) {
            $actionStack = new
                    Zend_Controller_Plugin_ActionStack();
            $front->registerPlugin($actionStack, 97);
        } else {
            $actionStack = $front->getPlugin(
                            'Zend_Controller_Plugin_ActionStack');
        }
        //Set category to the current controller for bookmarking
        Zend_Registry::set('controller',$request->getControllerName());
        $myBookmarkAction = clone($request);
        $myBookmarkAction->setActionName('view')
                        ->setControllerName('bookmark');
        $actionStack->pushStack($myBookmarkAction);
        
    }
}
?>