<?php

class Form_AttachmentForm extends Zend_Form {

    public function __construct($options = null) {
        parent::__construct($options);
		
		$this->setAttrib('enctype','multipart/form-data');
		$this->setAttrib('target','upload_target');
		$this->setAttrib('action','/attachment/add');
		
        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Attachment Title')
                ->setRequired(true)
                ->addValidator('NotEmpty')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
				->setAttrib('id','attachTitle');

        $attach = new Zend_Form_Element_File('attachment');
        $attach->setLabel("File");
			   
		$info = new Zend_Form_Element_Hidden("info");
		$info->setLabel("(only pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,gif,png files)");
		
		$mode = new Zend_Form_Element_Hidden("mode");
		$mode->setAttrib("id","mode");
		$modeId = new Zend_Form_Element_Hidden("modeId");
		$modeId->setAttrib("id","modeId");
		$source = new Zend_Form_Element_Hidden("src");
		
		
		$submit = new Zend_Form_Element_Submit('save-attach');
		$submit->setLabel("Add")
			   ->setAttrib('id','save-attach')
			   ->setAttrib('class','gt-add');

        $this->addElements(array($source,$title,$attach,$info,$submit,$mode,$modeId));
    }

}

?>
