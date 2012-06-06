<?php
class Form_UserprofileForm extends Zend_Form {

 	public function  __construct($options = null) {
		parent::__construct($options);


    $this->setName('User Profile');
    if(Zend_Registry::get('role')=='sa') {
        $plantObj = new Model_DbTable_Plant();
        $plantValue = $plantObj->fetchAll();

        $data = array();
        $data['']='Select an Option';
        foreach($plantValue as $pl) {
            $data[$pl->plantId] = $pl->plantName;
        }

        $plant = new Zend_Form_Element_Select('plantid');
        $plant->setLabel('Plant')
        ->addMultiOptions($data)
        ->setRequired(true)
        ->addFilter('StripTags')
        ->addFilter('StringTrim')
         ->addValidator('NotEmpty');
        $plant->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));
    }

    $firstName = new Zend_Form_Element_Text('firstName');
    $firstName->setLabel('First Name')
    ->setRequired(true)
    ->addValidator('NotEmpty')
    ->addValidator(Model_Validators::alpha())
    ->addFilter('StripTags')
    ->addFilter('StringTrim')
    ->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

    $lastName = new Zend_Form_Element_Text('lastName');
    $lastName->setLabel('Last Name')
    ->setRequired(true)
    ->addValidator('NotEmpty')
    ->addValidator(Model_Validators::alpha())
    ->addFilter('StripTags')
    ->addFilter('StringTrim')
    ->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

    $designation = new Zend_Form_Element_Text('designation');
    $designation->setLabel('Designation')
    ->setRequired(true)
    ->addValidator('NotEmpty')
    ->addValidator(Model_Validators::alpha())
    ->addFilter('StripTags')
    ->addFilter('StringTrim')
    ->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

    $phone = new Zend_Form_Element_Text('phone');
    $phone->setLabel('Phone(Landline)')
    ->setRequired(true)
    ->addValidator('NotEmpty')
	->addValidator(Model_Validators::regex('/[^0-9+-]+/'))
    ->addFilter('StripTags')
    ->addFilter('StringTrim')
    ->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

    $mobile = new Zend_Form_Element_Text('mobile');
    $mobile->setLabel('Mobile')
    ->setRequired(true)
    ->addValidator('NotEmpty')
	->addValidator(Model_Validators::regex('/[^0-9+-]+/'))
    ->addFilter('StripTags')
    ->addFilter('StringTrim')
    ->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

    $email = new Zend_Form_Element_Text('email');
    $email->setLabel('Email')
    ->setRequired(true)
    ->addValidator('NotEmpty')
    ->addValidator(Model_Validators::email())
    ->addFilter('StripTags')
    ->addFilter('StringTrim')

      ->setDecorators(array('ViewHelper',
           array(array('data'=>'HtmlTag'), array('tag' => 'td')),
           array('Label', array('tag' => 'td')),
           array('Errors', array('tag' => 'td')),
           array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
   ));


    $submit = new Zend_Form_Element_Submit('submit');
    $submit->setAttrib('id', 'submitbutton')
			->setAttrib('class','gt-add');
    $submit->setDecorators(array('ViewHelper', 'Description', 'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td',
                    'colspan' => '2', 'align' => 'center')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));
	
    if(Zend_Registry::get('role')=='sa')
        $this->addElement($plant);
    $this->addElements(array($firstName, $lastName, $designation, $phone, $mobile, $email, $submit));
    $this->setDecorators(array('FormElements', array(array('data' => 'HtmlTag'), array('tag' => 'table')), 'Form'));
  }
}

?>
