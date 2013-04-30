<?php
class Form_AccountListTools_ extends Zend_Form
{
	public function init()
	{
		$this->setAttrib("class", "search");
		
		$filter = new Zend_Form_Element_Text("filter");
		$filter->setLabel("Cari:");
		$filter->setAttrib("size", 40);
		
		$submit = new Zend_Form_Element_Submit("submit");
		$submit->setLabel("Cari");
		
		$this->addElements(array($filter, $submit));
	}
}