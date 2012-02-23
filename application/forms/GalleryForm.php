<?php

class Form_GalleryForm extends Zend_Form {

    public function __construct($options = null) {
        parent::__construct($options);

        $this->setName('Presentations');


        $Title = new Zend_Form_Element_Text('tag');
        $Title->setLabel('Photo Tag')
                ->addValidator('NotEmpty')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $content = new Zend_Form_Element_Button('image');
        $content->setLabel('Upload Photo')                
				->setAttrib('class','gt-add');
		
        $info = new Zend_Form_Element_Hidden('info');
        $info->setLabel("(allowed formats - jpg,jpeg)");


        $this->addElements(array($Title, $content, $info));
    }

}

?>
