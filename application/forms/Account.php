<?php
/**
 * Class untuk form data account.
 * @author Adi Sayoga
 */
class Form_Account extends Zend_Form
{
	public function __construct($exclude = null, $options = null)
	{
		parent::__construct($options);
		$this->setExclude($exclude);
	}
	
	/**
	 * Exclude untuk validator NoRecordExists
	 * @param string $exclude - Field value yang di-exclude pada validator kodeAccount
	 */
	public function setExclude($exclude)
	{
		if (!$exclude) return;
		
		$kodeAccount = $this->getElement("kodeAccount");
		if (!$kodeAccount) return;
		
		$kodeAccount->removeValidator("Zend_Validate_Db_NoRecordExists");
		$kodeAccountValidator = new Zend_Validate_Db_NoRecordExists(array(
			"table" => "account", "field" => "kodeAccount", 
			"exclude" => array("field" => "kodeAccount", "value" => $exclude)
		));
		$kodeAccount->addValidator($kodeAccountValidator);
	}
	
	public function init()
	{
		$this->setAttrib("class", "form");
		$accountModel = new Model_Account();
		$dataAccount = $accountModel->fetchKeyValue("id", "account", "kodeAccount");
		
		$id = $this->createElement('hidden', 'id');
        $id->setDecorators(array("ViewHelper"));
        
		// Parent
		$parent = new Zend_Form_Element_Select("parent");
		$parent->setLabel("Parent:")->setRequired();
		$parent->addMultiOption(0, "(Tidak Ada)");
		$dataAccount && $parent->addMultiOptions($dataAccount);
		
		// Insert Setelah...
		$after = new Zend_Form_Element_Select("after");
		$after->setLabel("Insert Setelah:")->setRequired();
		$after->addMultiOption(0, "(Paling Awal)");
		$dataAccount && $after->addMultiOptions($dataAccount);
		
		// Kode Account
		$kodeAccount = new Zend_Form_Element_Text("kodeAccount");
		$kodeAccount->setLabel("Kode Akun:")->setRequired()->setAttrib("size", "20");
		$kodeAccount->addValidator(new Zend_Validate_Db_NoRecordExists(
			array("table" => "account", "field" => "kodeAccount")));
		
		// Account
		$account = new Zend_Form_Element_Textarea("account");
		$account->setLabel("Akun:")->setRequired();
		$account->setAttribs(array("cols" => 100, "rows" => 2));
		
		// Normal Pos
		$normalPos = new Zend_Form_Element_Select("normalPos");
		$normalPos->setLabel("Normal Pos:")->setRequired();
		$normalPos->addMultiOptions(array(1 => "Debit", -1 => "Kredit"));
		
		// Kelompok
		$kelompok = new Zend_Form_Element_Select("kelompok");
		$kelompok->setLabel("Kelompok:")->setRequired();
		$kelompok->addMultiOptions(array("N" => "Neraca", "L" => "Laba/Rugi"));
		
		// Submit
		$submit = new Zend_Form_Element_Submit("submit");
		$submit->setLabel("Simpan");
		
		$this->addElements(array($id, $parent, $after, $kodeAccount, $account, $kelompok, 
				$normalPos, $submit));
		
		/*$this->setElementDecorators(array("ViewHelper", 
		    array(array("data" => "HtmlTag"), array("tag" => "td", "class" => "element")),
		    array("label", array("tag" => "td")),
		    array(array("row" => "HtmlTag"), array("tag" => "tr")),
		));
		$submit->setDecorators(array("ViewHelper",
		    array(array("data" => "HtmlTag"), array("tag" => "td", "class" => "element")),
            array(array("emptyrow" => "HtmlTag"), array("tag" => "td", "class" => "element", "placement" => "prepend")),
            array(array("row" => "HtmlTag"), array("tag" => "tr")),
		));
		$this->setDecorators(array("FormElements", array("HtmlTag", array("tag" => "table")), "Form"));*/
	}
}