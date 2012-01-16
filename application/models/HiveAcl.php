<?php
class Model_HiveAcl extends Zend_Acl {
	public function  __construct() {

		$this->add(new Zend_Acl_Resource('index'));
		$this->add(new Zend_Acl_Resource('error'));
		

		$this->add(new Zend_Acl_Resource('authentication'));
		$this->add(new Zend_Acl_Resource('authentication:login'),'authentication');
		$this->add(new Zend_Acl_Resource('authentication:extlogout'),'authentication');
		$this->add(new Zend_Acl_Resource('authentication:logout'),'authentication');
        $this->add(new Zend_Acl_Resource('authentication:forgotpassword'),'authentication');

		$this->add(new Zend_Acl_Resource('dashboard'));
		$this->add(new Zend_Acl_Resource('dashboard:index'), 'dashboard');
		$this->add(new Zend_Acl_Resource('dashboard:showmenu'), 'dashboard');
		
		$this->add(new Zend_Acl_Resource('reports'));
		$this->add(new Zend_Acl_Resource('attachments'));
		
		$this->add(new Zend_Acl_Resource('gasturbine'));
		$this->add(new Zend_Acl_Resource('gasturbine:add'), 'gasturbine');
		$this->add(new Zend_Acl_Resource('gasturbine:edit'), 'gasturbine');
		$this->add(new Zend_Acl_Resource('gasturbine:view'), 'gasturbine');
		$this->add(new Zend_Acl_Resource('gasturbine:list'), 'gasturbine');
        $this->add(new Zend_Acl_Resource('gasturbine:index'), 'gasturbine');
        $this->add(new Zend_Acl_Resource('gasturbine:editvalidate'), 'gasturbine');
        $this->add(new Zend_Acl_Resource('gasturbine:addvalidate'), 'gasturbine');
        $this->add(new Zend_Acl_Resource('gasturbine:details'), 'gasturbine');
                
		$this->add(new Zend_Acl_Resource('validation'));

		$this->add(new Zend_Acl_Resource('plant'));
		$this->add(new Zend_Acl_Resource('plant:index'), 'plant');
		$this->add(new Zend_Acl_Resource('plant:add'), 'plant');
		$this->add(new Zend_Acl_Resource('plant:edit'), 'plant');
		$this->add(new Zend_Acl_Resource('plant:view'), 'plant');
		$this->add(new Zend_Acl_Resource('plant:list'),'plant');
		$this->add(new Zend_Acl_Resource('plant:results'),'plant');
        $this->add(new Zend_Acl_Resource('plant:editvalidate'), 'plant');
        $this->add(new Zend_Acl_Resource('plant:addvalidate'), 'plant');
		
		$this->add(new Zend_Acl_Resource('search'));
		$this->add(new Zend_Acl_Resource('search:searchindex'), 'search');
		$this->add(new Zend_Acl_Resource('search:view'), 'search');
		
		$this->add(new Zend_Acl_Resource('administration'));
        $this->add(new Zend_Acl_Resource('list'),'administration');
		$this->add(new Zend_Acl_Resource('deleteacc'),'administration');
		$this->add(new Zend_Acl_Resource('resetpassword'),'administration');

		$this->add(new Zend_Acl_Resource('userprofile'));
		        
        $this->add(new Zend_Acl_Resource('findings'));
        
        $this->add(new Zend_Acl_Resource('upgrades'));
        
        $this->add(new Zend_Acl_Resource('lte'));
        

        $this->add(new Zend_Acl_Resource('presentation'));

        $this->add(new Zend_Acl_Resource('advertisement'));
        $this->add(new Zend_Acl_Resource('advertisement:add'),'advertisement');
        $this->add(new Zend_Acl_Resource('advertisement:edit'),'advertisement');
        $this->add(new Zend_Acl_Resource('advertisement:view'),'advertisement');
        $this->add(new Zend_Acl_Resource('advertisement:list'),'advertisement');
        $this->add(new Zend_Acl_Resource('advertisement:randomad'),'advertisement');
        $this->add(new Zend_Acl_Resource('advertisement:delete'),'advertisement');

        $this->add(new Zend_Acl_Resource('bookmark'));
        $this->add(new Zend_Acl_Resource('bookmark:add'),'bookmark');
        $this->add(new Zend_Acl_Resource('bookmark:delete'),'bookmark');
        $this->add(new Zend_Acl_Resource('bookmark:list'),'bookmark');
        $this->add(new Zend_Acl_Resource('bookmark:longlist'),'bookmark');

        $this->add(new Zend_Acl_Resource('conference'));
		
        $this->add(new Zend_Acl_Resource('schedule'));
		$this->add(new Zend_Acl_Resource('schedule:add'),'schedule');
		$this->add(new Zend_Acl_Resource('schedule:add-event-list'),'schedule');
		$this->add(new Zend_Acl_Resource('schedule:view'),'schedule');
		$this->add(new Zend_Acl_Resource('schedule:edit'),'schedule');
		$this->add(new Zend_Acl_Resource('schedule:delete'),'schedule');
		$this->add(new Zend_Acl_Resource('schedule:delevent'),'schedule');
		$this->add(new Zend_Acl_Resource('schedule:delsch'),'schedule');
		
        $this->add(new Zend_Acl_Resource('notification'));

       	$this->add(new Zend_Acl_Resource('myprofile'));

        $this->add(new Zend_Acl_Resource('attachment'));
        $this->add(new Zend_Acl_Resource('attachment:add'),'attachment');

		$this->addRole(new Zend_Acl_Role('guest'));
		$this->addRole(new Zend_Acl_Role('us'), 'guest');
		$this->addRole(new Zend_Acl_Role('ed'), 'us');
		$this->addRole(new Zend_Acl_Role('ad'), 'us');
        $this->addRole(new Zend_Acl_Role('ca'), 'ed');
		$this->addRole(new Zend_Acl_Role('sa'), array('ca','ad'));

		
//		access privilage for guest

		$this->allow('guest','index');
		$this->allow('guest','error');
		$this->allow('guest','authentication',array('login','forgotpassword','extlogout'));
		$this->allow('guest','search',array('view'));
		
//		access privilages for user
		
		$this->allow('us','userprofile');
		$this->deny('us','userprofile','add');
		$this->allow('us','gasturbine',array('index','view','details','list'));
		$this->allow('us','plant',array('view','index','list','results'));
		$this->allow('us','authentication','logout');
		$this->allow('us','dashboard');
		$this->allow('us','reports');
        $this->allow('us','myprofile');
        $this->allow('us','findings');
        $this->allow('us','upgrades');
        $this->allow('us','lte');
        $this->allow('us','presentation');
        $this->allow('us','advertisement',array('view','randomad','list'));
        $this->allow('us','bookmark');
        $this->allow('us','conference');
        $this->allow('us','schedule',array('delevent','edit','view','add','add-event-list','delete','delsch'));
        $this->allow('us','notification');
		$this->allow('us','attachments');
        $this->allow('us','search');
		$this->allow('us','validation');
		$this->deny('us','search','searchindex');

        $this->allow('us','attachment');
		
		
//		access privilages for editor
//
		$this->allow('us','gasturbine',array('edit','editvalidate'));
		$this->allow('us','plant',array('edit','editvalidate'));
		
		$this->allow('ed','gasturbine',array('edit','editvalidate'));
		$this->allow('ed','plant',array('edit','editvalidate'));
//              access privilages for ad

        $this->allow('ad','advertisement',array('add','edit','list'));

//		access privilages for ca
		
		$this->allow('ca','gasturbine',array('add','addvalidate'));
		$this->allow('sa','userprofile');
		$this->allow('ca','administration',array('list','deleteacc','resetpassword'));
        $this->allow('sa','plant');
		$this->allow('sa','gasturbine');
		$this->allow('sa','search');
		$this->allow('sa','administration');

	}
}

?>