<?php
/**
 * Class untuk form data user.
 * @author Adi Sayoga
 */
class Form_User extends Zend_Form
{
	public function __construct($exclude = null, $options = null)
	{
		parent::__construct($options);
		$this->setExclude($exclude);
	}
	
	/**
	 * Exclude untuk validator
	 * @param string $exclude - Field value yang di-exclude pada validator username
	 */
	public function setExclude($exclude)
	{
		if (!$exclude) return;
		
		$username = $this->getElement("username");
		if (!$username) return;
		
		$username->removeValidator("Zend_Validate_Db_NoRecordExists");
		$usernameValidator = new Zend_Validate_Db_NoRecordExists(array(
			"table" => "users", "field" => "username", 
			"exclude" => array("field" => "username", "value" => $exclude)
		));
		$username->addValidator($usernameValidator);
	}
	
	public function init()
	{
		$this->setAttrib("class", "form");
		
		// Buat elemen baru
		// Id
		$id = new Zend_Form_Element_Hidden("id");
		$id->setDecorators(array("ViewHelper"));
		
		// Username
		$username = new Zend_Form_Element_Text("username");
		$username->setLabel("Nama User:")->setRequired()->setAttrib("size", "50");
		$username->addFilter("StripTags");
		$username->addValidator(new Zend_Validate_Db_NoRecordExists(array(
			"table" => "users", "field" => "username")));
		
		// Display name
		$displayName = new Zend_Form_Element_Text("displayName");
		$displayName->setLabel("Nama Ditampilkan:")->setRequired()->setAttrib("size", "50");
		$displayName->addFilter("StripTags");
		
		// Password
		$password = new Zend_Form_Element_Password("password");
		$password->setLabel("Password:")->setRequired()->setAttrib("size", "50");
		
		// Role
		$role = new Zend_Form_Element_Select("role");
		$role->setLabel("Pilih Role:")->setRequired();
		$role->addMultiOptions(array("user" => "User", "admin" => "Administrator"));
		
		// Submit
		$submit = new Zend_Form_Element_Submit("submit");
		$submit->setLabel("Simpan");
		
		$this->addElements(array($id, $username, $displayName, $password, $role, $submit));
	}
}