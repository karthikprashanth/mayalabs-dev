<?php
class Form_ChangePasswordForm extends Zend_Form
{
 	public function  __construct($options = null) {
		parent::__construct($options);

    $this->setName('Change Password');
		
    //old password
    $oldPassword = new Zend_Form_Element_Password('oldPassword');
    $oldPassword->setLabel('Old Password')
    ->setRequired(true)
    ->addFilter('StripTags')
    ->addFilter('StringTrim')
    ->addValidator('NotEmpty')
    ->addValidator(Model_Validators::regex('/[^a-zA-Z0-9$_.@]+/'));
	
	
    
    //new password
			
    $newPassword = new Zend_Form_Element_Password('newPassword');
    $newPassword->setLabel('New Password')
    ->addFilter('StripTags')
    ->addFilter('StringTrim')
    ->addValidator('NotEmpty')
    ->addValidator(Model_Validators::regex('/[^a-zA-Z0-9$_.@]+/'))
	->addValidator(Model_Validators::password());
    
    //retype new pass
    $reNewPassword = new Zend_Form_Element_Password('reNewPassword');
    $reNewPassword->setLabel('Confirm Password')
    ->addFilter('StripTags')
    ->addFilter('StringTrim')
    ->addValidator('NotEmpty')
    ->addValidator(Model_Validators::regex('/[^a-zA-Z0-9$_.@]+/'))
	->addValidator(Model_Validators::password());
    //submit
    $submit = new Zend_Form_Element_Submit('submit');
    $submit->setAttrib('id', 'submitbutton')
			->setAttrib('class','user-save');
    $this->addElements(array($oldPassword,$newPassword,$reNewPassword, $submit));
    $this->setMethod('post');
 }
}

?>