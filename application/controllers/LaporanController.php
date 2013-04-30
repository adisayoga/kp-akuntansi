<?php

class LaporanController extends Zend_Controller_Action
{
	public function init() {}
	
	public function accountAction()
	{
		// Laporan tidak memerlukan layout
        $this->view->layout()->disableLayout();
        
        $accountModel = new Model_Account();
    	$account = $accountModel->fetchArray();
    	$this->view->account = $account;
	}
	
	public function journalUmumAction()
	{
		$laporanForm = new Form_Laporan();
    	$laporanForm->setAction("/laporan/journal-umum");
    	$laporanForm->setMethod("post");
    	$laporanForm->removeElement("idAccount");
    	
    	if ($this->getRequest()->isPost() && $laporanForm->isValid($_POST)) {
    		$this->view->layout()->disableLayout();
    		return $this->_forward("view-journal-umum");
    	} else {
	    	$this->view->form = $laporanForm;
    	}
	}
	
	public function viewJournalUmumAction()
	{
		$tglAwal = new Zend_Date($this->_request->getParam("tglAwal"));
    	$tglAkhir = new Zend_Date($this->_request->getParam("tglAkhir"));
    	
    	$lapModel = new Model_Laporan();
    	$journal = $lapModel->fetchJournalUmum($tglAwal->get(Zend_Date::TIMESTAMP), 
    		$tglAkhir->get(Zend_Date::TIMESTAMP));
    	
    	$this->view->tglAwal = $tglAwal;
    	$this->view->tglAkhir = $tglAkhir;
    	$this->view->journal = $journal;
	}
	
	public function bukubesarAction()
	{
	$laporanForm = new Form_Laporan();
    	$laporanForm->setAction("/laporan/bukubesar");
    	$laporanForm->setMethod("post");
    	
    	if ($this->getRequest()->isPost() && $laporanForm->isValid($_POST)) {
    		$this->view->layout()->disableLayout();
    		return $this->_forward("view-bukubesar");
    	} else {
	    	$this->view->form = $laporanForm;
    	}
	}
	
	public function viewBukubesarAction()
	{
		$tglAwal = new Zend_Date($this->_request->getParam("tglAwal"));
    	$tglAkhir = new Zend_Date($this->_request->getParam("tglAkhir"));
    	$idAccount = $this->_request->getParam("idAccount");
    	
    	$accountModel = new Model_Account();
    	$accountRow = $accountModel->fetchRow("id = $idAccount");
    	
    	$lapModel = new Model_Laporan();
    	$bukubesar = $lapModel->fetchBukubesar($tglAwal->get(Zend_Date::TIMESTAMP), 
    		$tglAkhir->get(Zend_Date::TIMESTAMP), $idAccount);
    	
    	$this->view->tglAwal = $tglAwal;
    	$this->view->tglAkhir = $tglAkhir;
    	$this->view->account = $accountRow;
    	$this->view->bukubesar = $bukubesar;
	}
	
	public function labarugiAction()
	{
		$laporanForm = new Form_Laporan();
    	$laporanForm->removeElement("idAccount");
    	$laporanForm->setAction("/laporan/labarugi");
    	$laporanForm->setMethod("post");
    	
    	if ($this->getRequest()->isPost() && $laporanForm->isValid($_POST)) {
    		$this->view->layout()->disableLayout();
    		return $this->_forward("view-labarugi");
    	} else {
	    	$this->view->form = $laporanForm;
    	}
	}
	
	public function viewLabarugiAction()
	{
		$tglAwal = new Zend_Date($this->_request->getParam("tglAwal"));
    	$tglAkhir = new Zend_Date($this->_request->getParam("tglAkhir"));
    	
    	$lapModel = new Model_Laporan();
    	$labarugi = $lapModel->fetchLabarugi($tglAwal->get(Zend_Date::TIMESTAMP), 
    		$tglAkhir->get(Zend_Date::TIMESTAMP));
    	
    	$this->view->tglAwal = $tglAwal;
    	$this->view->tglAkhir = $tglAkhir;
    	$this->view->labarugi = $labarugi;
	}
	
	public function neracaAction()
	{
		$laporanForm = new Form_Laporan();
    	$laporanForm->removeElement("idAccount");
    	$laporanForm->setAction("/laporan/neraca");
    	$laporanForm->setMethod("post");
    	
    	if ($this->getRequest()->isPost() && $laporanForm->isValid($_POST)) {
    		$this->view->layout()->disableLayout();
    		return $this->_forward("view-neraca");
    	} else {
	    	$this->view->form = $laporanForm;
    	}
	}
	
	public function viewNeracaAction()
	{
		$tglAwal = new Zend_Date($this->_request->getParam("tglAwal"));
    	$tglAkhir = new Zend_Date($this->_request->getParam("tglAkhir"));
    	
    	$lapModel = new Model_Laporan();
    	$neraca = $lapModel->fetchNeraca($tglAwal->get(Zend_Date::TIMESTAMP), 
    		$tglAkhir->get(Zend_Date::TIMESTAMP));
    	
    	$this->view->tglAwal = $tglAwal;
    	$this->view->tglAkhir = $tglAkhir;
    	$this->view->neraca = $neraca;
	}
}