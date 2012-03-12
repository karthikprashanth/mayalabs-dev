<?php

class Form_GalleryForm extends Zend_Form {

    public function __construct($options = null) {
        parent::__construct($options);

        $this->setName('Presentations');
		$this->setAttrib('enctype','multipart/form-data');
		$this->setAttrib('target','upload_target');
		$this->setAttrib('action','/gallery/add');

        $Title = new Zend_Form_Element_Text('tag');
        $Title->setLabel('Photo Tag')
                ->addValidator('NotEmpty')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $content = new Zend_Form_Element_File('img');
        $content->setLabel('Upload Photo');
		
		$cid = new Zend_Form_Element_Hidden('cid');
		
        $info = new Zend_Form_Element_Hidden('info');
        $info->setLabel("(allowed formats - jpg,jpeg)");
		
		$submit = new Zend_Form_Element_Submit('save-photo');
		$submit->setLabel("Add Photo")
			   ->setAttrib("id","save-photo")
			   ->setAttrib("class","gt-add");

        $this->addElements(array($Title, $content, $info,$submit,$cid));
    }

}

?>
