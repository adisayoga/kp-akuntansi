<?php
class Acc_View_Helper_PhoneElement extends Zend_View_Helper_FormElement
{
	protected $html = "";
	
	public function phoneElement($name = null, $value = null, $attribs = null)
	{
		$areanum = $geonum = $localnum = "";
		if ($value) list($areanum, $geonum, $localnum) = split("-", $value);
		
		$helper = new Zend_View_Helper_FormText();
		$helper->setView($this->view);
		
		$this->html .= $helper->formText($name . "[areanum]", $areanum, array("size" => "3", "maxlength" => "3"));
		$this->html .= $helper->formText($name . "[geonum]", $geonum, array("size" => "3", "maxlength" => "3"));
		$this->html .= $helper->formText($name . "[localnum]", $localnum, array("size" => "4", "maxlength" => "4"));
		
		return $this->html;
	}
}