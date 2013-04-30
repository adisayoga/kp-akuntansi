<?php
/**
 * Class untuk form laporan.
 * @author Adi Sayoga
 */
class Form_Laporan extends Zend_Form
{
	public function init()
	{
		$this->setAttrib("class", "form");
		
		// Periode dalam 1 bulan
		$tglAwalValue = new Zend_Date();
		$tglAwalValue->set("1", Zend_Date::DAY);
		
		$tglAkhirValue = new Zend_Date();
		$tglAkhirValue->set("1", Zend_Date::DAY);
		$tglAkhirValue->add("1", Zend_Date::MONTH);
		$tglAkhirValue->sub("1", Zend_Date::DAY);
		
		// Tanggal awal
		$tglAwal = new ZendX_JQuery_Form_Element_DatePicker("tglAwal", 
				array('jQueryParams' => array("dateFormat" => "dd/mm/yy")));
		$tglAwal->addValidator(new Zend_Validate_Date(array("locale" => "id")));
		$tglAwal->setLabel("Tanggal Awal:")->setRequired()->setAttrib("size", "15");
		$tglAwal->setValue($tglAwalValue->get("dd/MM/yyyy"));
		
		// Tanggal akhir
		$tglAkhir = new ZendX_JQuery_Form_Element_DatePicker("tglAkhir", 
				array('jQueryParams' => array("dateFormat" => "dd/mm/yy")));
		$tglAkhir->addValidator(new Zend_Validate_Date(array("locale" => "id")));
		$tglAkhir->setLabel("Tanggal Akhir:")->setRequired()->setAttrib("size", "15");
		$tglAkhir->setValue($tglAkhirValue->get("dd/MM/yyyy"));
		
		// Account
		$account = new Zend_Form_Element_Select("idAccount");
		$account->setLabel("Akun:")->setRequired();
		$account->setTranslator();
		
		$accountModel = new Model_Account();
		$dataAccount = $accountModel->fetchKeyValue("id", "account", "kodeAccount");
		if ($dataAccount) $account->addMultiOptions($dataAccount);
		
		// Submit
		$submit = new Zend_Form_Element_Submit("submit");
		$submit->setLabel("OK");
		
		$this->addElements(array($tglAwal, $tglAkhir, $account, $submit));
	}
}