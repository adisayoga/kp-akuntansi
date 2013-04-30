<?php
/**
 * Class untuk login user.
 * @author Adi Sayoga
 */
class Form_Login extends Zend_Form
{
	public function init()
	{
		$this->setAttrib("class", "form");
		
		// Buat elemen baru
		
		// Username
		$username = new Zend_Form_Element_Text("username");
		$username->setLabel("Nama User:")->setRequired()->setAttrib("size", "40");
		$username->addFilter("StripTags");
		
		// Password
		$password = new Zend_Form_Element_Password("password");
		$password->setLabel("Password:")->setRequired()->setAttrib("size", "40");
		
		// Submit
		$submit = new Zend_Form_Element_Submit("submit");
		$submit->setLabel("Login");
		
		$this->addElements(array($username, $password, $submit));
	}
}