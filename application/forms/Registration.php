<?php
class Form_Registration extends Zend_Form
{
	public function init()
	{
		$this->setMethod("post");
		
		$name = new Zend_Form_Element_Text("name");
		$name->setLabel("Name:")->setRequired();
		
		$cellPhone = new Acc_Form_Element_Phone("phone");
		$cellPhone->setLabel("Cell Number:")->setRequired();
		$cellPhone->addValidator(new Acc_Validate_CellPhone());
		
		$submit = new Zend_Form_Element_Submit("submit");
		
		$this->addElements(array($name, $cellPhone, $submit));
	}
}