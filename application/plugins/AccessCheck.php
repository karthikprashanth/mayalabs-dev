<?php
class Plugin_AccessCheck extends Zend_Controller_Plugin_Abstract {

	private $_acl;

	public function  __construct(Zend_Acl $acl) {

		$this->_acl = $acl;
	}

	public function  preDispatch(Zend_Controller_Request_Abstract $request) {
		
                $resource = $request->getControllerName();
				$action = $request->getActionName();
				$role = Zend_Registry::get('role');

                if($resource=='index' && $action=='index' )
                {
                           $request->setControllerName('authentication');
                           $request->setActionName('login');

                }
                
                if(!$this->_acl->isAllowed($role,$resource,$action)) {
                    if(!Zend_Registry::isRegistered('t')){
                        $pquery = $request->getParams();
                        unset($pquery['module']);
                        unset($pquery['controller']);
                        unset($pquery['action']);
                        $uparams = http_build_query($pquery);
                        $t = $resource.'/'.$action;
                        if($uparams)
                            $t =$t.'?'.$uparams;
                        Zend_Registry::set('t',$t);
                    }
                    $request->setControllerName('authentication');
		    $request->setActionName('login');
		}				
	}
}
?>
