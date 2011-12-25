<?php
class Form_AdvertisementForm extends Zend_Form {

 	public function  __construct($options = null) {
		parent::__construct($options);


    $this->setName('Advertisements');
    $this->addElementPrefixPath('Hive_Form_Decorators','Hive/Form/Decorators','decorator');

    $adid= new Zend_Form_Element_Hidden('advertId');
    $adid->setAttrib('value', 'TESTING');

    $ts= new Zend_Form_Element_Hidden('timeupdate');
    $ts->setAttrib('value', 'TESTING');

    $advertTitle= new Zend_Form_Element_Text('title');
    $advertTitle->setLabel('Advertisement Title')
    ->setRequired(true)
    ->addDecorator('Htmltag',array('tag' => 'br'))
    ->addValidator('NotEmpty')
    ->addFilter('StripTags')
    ->addFilter('StringTrim')
    ->addValidator(Model_Validators::alnum());

    $GTModelNum = new Zend_Form_Element_Select('GTModel');
    $GTModelNum->setLabel('GT Model Number')
    ->setRequired(true)
    ->addDecorator('Htmltag',array('tag' => 'br'))
    ->addMultiOptions(array('v93.4A' => 'v93.4A'))
    ->addValidator('NotEmpty')
    ->addFilter('StripTags')
    ->addFilter('StringTrim');

    $description = new Zend_Form_Element_Textarea('description');
    $description->setLabel('Description')
            ->setAttrib('rows', '8')
            ->setAttrib('cols', '40')
    ->addDecorator('Htmltag',array('tag' => 'br'))
    ->setRequired(true)
    ->addValidator('NotEmpty')
    ->addFilter('StripTags')
    ->addFilter('StringTrim');

	$appath = substr(APPLICATION_PATH,0,strlen(APPLICATION_PATH)-12);

    $adImg = new Zend_Form_Element_File('advertImage');
    $adImg->setLabel('Upload the Advertisement')
		  ->setDestination($appath . '/public/uploads')
          ->addDecorator('Htmltag',array('tag' => 'br'));

    $submit = new Zend_Form_Element_Button('submit');
    $submit->addDecorator('Htmltag',array('tag' => 'p'));
    $submit->setAttrib('id', 'submitbutton')
            ->setAttrib('type', 'submit');

    $this->addElements(array($adid,$ts,$advertTitle, $GTModelNum, $description, $adImg, $submit));
  }
}


?>
