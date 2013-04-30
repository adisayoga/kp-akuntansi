<?php
/**
 * Class untuk form journal.
 * @author Adi Sayoga
 */
class Form_Journal extends Zend_Form
{
	public function __construct($exclude = null, $options = null)
	{
		parent::__construct($options);
		$this->setExclude($exclude);
	}
	
	/**
	 * Exclude untuk validator
	 * @param string $exclude - Field value yang di-exclude pada validator no bukti
	 */
	public function setExclude($exclude)
	{
		if (!$exclude) return;
		
		$noBukti = $this->getElement("noBukti");
		if (!$noBukti) return;
		
		$noBukti->removeValidator("Zend_Validate_Db_NoRecordExists");
		$noBuktiValidator = new Zend_Validate_Db_NoRecordExists(array(
			"table" => "bukti_transaksi", "field" => "noBukti", 
			"exclude" => array("field" => "noBukti", "value" => $exclude)
		));
		$noBukti->addValidator($noBuktiValidator);
	}
	
	public function init()
	{
		$this->setAttrib("class", "form");
		
		// ID
		$id = new Zend_Form_Element_Hidden("id");
		$id->setDecorators(array("ViewHelper"));
		
		// Tipe Journal
		$tipeJournal = new Zend_Form_Element_Hidden("tipeJournal");
		$tipeJournal->setDecorators(array("ViewHelper"));
		
		// No. Bukti
		$noBukti = new Zend_Form_Element_Text("noBukti");
		$noBukti->setLabel("No. Bukti:")->setRequired()->setAttrib("size", "20");
		$noBukti->addValidator(new Zend_Validate_Db_NoRecordExists(array(
			"table" => "bukti_transaksi", "field" => "noBukti")));
		
		// Tanggal
		$tanggal = new ZendX_JQuery_Form_Element_DatePicker("tanggal", 
				array('jQueryParams' => array("dateFormat" => "dd/mm/yy")));
		$tanggal->addValidator(new Zend_Validate_Date(array("locale" => "id")));
		$tanggal->setLabel("Tanggal:")->setRequired()->setAttrib("size", "10");
		
		// Keterangan
		$keterangan = new Zend_Form_Element_Textarea("keterangan");
		$keterangan->setLabel("Keterangan:")->setRequired();
		$keterangan->setAttribs(array("cols" => 100, "rows" => 2));
		
		// Total
		$total = new Zend_Form_Element_Text("total");
		$total->setLabel("Total:")->setRequired()->setValue("0");
		//$total->addValidator(new Zend_Validate_Float()); // TODO bermasalah dengan localization
		
		$this->addElements(array($id, $tipeJournal, $noBukti, $tanggal, $keterangan, $total));
	}
}