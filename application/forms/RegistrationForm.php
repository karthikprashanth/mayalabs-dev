<?php
class Form_RegistrationForm extends Zend_Form
{
 	public function  __construct($options = null) {
		parent::__construct($options);


    $this->setName('registration');

    $username = new Zend_Form_Element_Text('username');
    $username->setLabel('Username')
    ->setRequired(true)
    ->addFilter('StripTags')
    ->addFilter('StringTrim')
    ->addValidator('NotEmpty')
    ->addValidator(Model_Validators::alnum());

    $role = new Zend_Form_Element_Select('role');
    $role->setLabel('Role')
    ->addMultiOptions(array( ''=>'Enter Value','sa'=> 'System Administrator','ca' => 'Company Administrator','ed' => 'Editor','us' => 'User'))
    ->setRequired(true)
    ->addFilter('StripTags')
    ->addFilter('StringTrim')
    ->addValidator('NotEmpty');

    $submit = new Zend_Form_Element_Submit('submit');
    $submit->setAttrib('id', 'submitbutton')
            ->addDecorator('Htmltag',array('tag' => 'br'));
    $this->addElements(array($username, $role, $submit));
  }
}
?>
