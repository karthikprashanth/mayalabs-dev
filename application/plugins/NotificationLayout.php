<?php

class Plugin_NotificationLayout extends Zend_Controller_Plugin_Abstract  {

    protected $_view;
    public function  preDispatch(Zend_Controller_Request_Abstract $request) {
        parent::preDispatch($request);
        

    }
    public function  dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        parent::dispatchLoopStartup($request);
        if($request->isXmlHttpRequest())
            return;
        if($request->getActionName()!='edit')
            return;
        $front = Zend_Controller_Front::getInstance();
        if (!$front->hasPlugin(
                        'Zend_Controller_Plugin_ActionStack')) {
            $actionStack = new
                    Zend_Controller_Plugin_ActionStack();
            $front->registerPlugin($actionStack, 98);
        } else {
            $actionStack = $front->getPlugin(
                            'Zend_Controller_Plugin_ActionStack');
        }

        //Set category to the current controller for bookmarking

        Zend_Registry::set('controller',$request->getControllerName());
        /*$myNotificationAction = clone($request);
        $myNotificationAction->setActionName('view')
                        ->setControllerName('notification');
        $actionStack->pushStack($myNotificationAction);*/

    }
}

//class Plugin_NotificationLayout extends Zend_Form_Decorator_Abstract{
//
//    protected $_placement = "PREPEND";
//
//    public function render($content)
//    {
//        $element = $this->getElement();
//        echo $element;
//
//    }
//
//}

?>
