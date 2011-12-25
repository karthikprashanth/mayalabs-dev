<?php
	$primPath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
	if(strrpos($primPath,"cache") == 0) {
		$primPath = substr($primPath,0,strlen($primPath)-22);
	}
	else {
		$primPath = substr($primPath,0,strlen($primPath)-19);
	}
	$xmlPath  = $primPath . "library" . DIRECTORY_SEPARATOR . "Zend" . DIRECTORY_SEPARATOR . "Config" . DIRECTORY_SEPARATOR . "Xml.php";
	$navPath  = $primPath . "library" . DIRECTORY_SEPARATOR . "Zend" . DIRECTORY_SEPARATOR . "Navigation.php";
	$navFile = $primPath . "application" . DIRECTORY_SEPARATOR . "configs". DIRECTORY_SEPARATOR."navigation.xml";
	$aclFile = $primPath . "application" . DIRECTORY_SEPARATOR . "models" . DIRECTORY_SEPARATOR . "HiveAcl.php";
	$zendAclPath  = $primPath . "library" . DIRECTORY_SEPARATOR . "Zend" . DIRECTORY_SEPARATOR . "Acl.php";
	include_once($xmlPath);
	include_once($navPath);
	echo $zendAclPath;
	/*include_once($zendAclPath);
	include_once($aclFile);
	$acl = new Model_HiveAcl();
	$navContainerConfig = new Zend_Config_Xml($navFile,"adminnav");
	$navContainer=new Zend_Navigation($navContainerConfig);*/
?>