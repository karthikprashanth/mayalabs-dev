<?php

class Form_ConfPresentationForm extends Zend_Form{

    public function  __construct($options = null) {
		parent::__construct($options);

                $this->setName('Conference Presentations');
                
                $Title = new Zend_Form_Element_Text('title');
                $Title->setLabel('Attachment Title')
                ->setRequired(true)
                ->addValidator('NotEmpty')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
                
                $plantModel = new Model_DbTable_Plant();
                $plantList = $plantModel->fetchAll();
                
				$data = array();
		        $data['']='Select an Option';
		        foreach($plantList as $pl) {
		        	if($pl['plantId'] != 1)
					{
		            	$data[$pl->plantId] = $pl->plantName;
					}
		        }
                
                $plantName = new Zend_Form_Element_Select('plantId');
                $plantName->setLabel('Plant Name')
                		  ->addMultiOptions($data)
                		  ->setRequired(true)
					      ->addFilter('StripTags')
					      ->addFilter('StringTrim')
					      ->addValidator('NotEmpty');
                
         		$appath = substr(APPLICATION_PATH,0,strlen(APPLICATION_PATH)-12);
				
                $content=new Zend_Form_Element_File('content');
                $content->setLabel('Upload the File')
                        ->setDestination($appath . '/public/uploads');
				
				$info= new Zend_Form_Element_Hidden('info');
    			$info->setLabel("(allowed formats - pdf,ppt,pptx,xls,xlsx,doc,docx)");

                $submit = new Zend_Form_Element_Button('submit');
                $submit->addDecorator('Htmltag',array('tag' => 'p'));
                $submit->setAttrib('id', 'submitbutton')
						->setAttrib('class','gt-add')
                        ->setAttrib('type', 'submit');
                    
                $this->addElements(array($cId,$Title,$plantName,$content,$info,$submit));
    }
}

?>
