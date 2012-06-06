<?php
class Form_LoginForm extends Zend_Form {
	public function  __construct($options = null) {
		parent::__construct($options);

	$this->setName('login');
        $this->addElementPrefixPath('Hive_Form_Decorators','Hive/Form/Decorators','decorator');
        
        //
        $target_url = new Zend_Form_Element_Hidden('t_url');
        $target_url->setValue($options['t']);
		
		
        //username
	$username=new Zend_Form_Element_Text('username');
        $username->setLabel('Username :')
                 ->setAttribs(array('type'  => 'text'));
        $username->setDecorators(array('HiveFormLogin',array('HtmlTag',array('tag' => 'div', 'class' => 'loginform'))));
	$username->setRequired()
			 ->addValidator(Model_Validators::regex('/[^A-Za-z0-9_.]+/'))
			 ->addFilter('StripTags')
			 ->addFilter('StringTrim');
        $username->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        //password
	$password=new Zend_Form_Element_Password('password');
        $password->setLabel('Password :')
                 ->setAttribs(array('type'  => 'password'));
        $password->setDecorators(array('HiveFormLogin',array('HtmlTag',array('tag' => 'div', 'class' => 'loginform'))));
	$password->setRequired(true);
        $password->addValidator(Model_Validators::regex('/[^a-zA-Z0-9$_.@]+/'));
        $password->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        //submit
	$login = new Zend_Form_Element_Image('login');
        $login->setImage('/images/sign_in.gif')
              ->setAttrib('id', 'submitbutton')
              ->setAttrib('type', 'submit')
              ->addDecorator('Htmltag',array('tag' => 'p'));
        $login->setDecorators(array('ViewHelper', 'Description', 'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td',
                    'colspan' => '2', 'align' => 'center')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

	$this->addElements(array($username,$password,$login,$target_url));
	$this->setMethod('post');
	$this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl().'/authentication/login');
        $this->setDecorators(array('FormElements', array(array('data' => 'HtmlTag'), array('tag' => 'table')), 'Form'));
	}
}

?>