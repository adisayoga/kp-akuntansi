<?php

class JournalController extends Zend_Controller_Action
{
	protected $_tipeJournal = Model_Journal::TIPE_JOURNAL_UMUM;
	protected $_prefixAutoNum = "BJU";
	
	/**
	 * Data account untuk comboBox
	 * @return array
	 */
	private function _getAccountList() 
	{
		$accountModel = new Model_Account();
        $accountList = $accountModel->fetchKeyValue("id", "account");
		return $accountList;
	}
	
	/**
	 * Action untuk create/update
	 */
	private function _actionCreateUpdate()
	{
		// Tambahkan link javascript
    	$this->view->JQuery()->addJavascriptFile("/js/dateFormat.js");
    	$this->view->JQuery()->addJavascriptFile("/js/jquery.formValidation.js");
    	$this->view->JQuery()->addJavascriptFile("/js/journal.js");
    	
    	$journalModel = new Model_Journal();
    	$dateNow = new Zend_Date();
    	
    	$id = $this->getRequest()->getParam("id");
    	$bukti = null;
    	$id && $bukti = $journalModel->fetchBukti($id);
    	if ($bukti) {
    		$lastNoBukti = $bukti["noBukti"];
    	} else {
    		$lastNoBukti = $journalModel->autoNum($dateNow, array("prefix" => $this->_prefixAutoNum));
    	}
    	
		$journalForm = new Form_Journal();
        $journalForm->setExclude($lastNoBukti);
        
        // detail
        $details = array();
        $totalDebit = 0;
        $totalKredit = 0;
        
        if ($this->getRequest()->isPost()) {
        	$kodeAccount = array();
	        $idAccount = $this->getRequest()->getParam("account");
	        $debit = $this->getRequest()->getParam("debit");
	        $kredit = $this->getRequest()->getParam("kredit");
	        
	        $accountModel = new Model_Account();
	        for ($i = 0; $i < count($idAccount); $i++) {
	        	if (!($debit[$i] || $kredit[$i]) || !$this->getRequest()->getParam("include$i")) continue;
	        	
		        // Tampilkan kode account berdasarkan id account
		        $account = $accountModel->find($idAccount[$i])->current();
		        if ($account) $kodeAccount[$i] = $account->kodeAccount;
		        
	        	$details[] = array(
	        		"idAccount" => $idAccount[$i],
	        		"kodeAccount" => $kodeAccount[$i],
	        		"debit" => ($debit[$i])? $debit[$i]: 0,
	        		"kredit" => ($kredit[$i])? $kredit[$i]: 0,
	        	);
	        	
	        	$totalDebit += $debit[$i];
	        	$totalKredit += $kredit[$i];
	        	
	        	// validasi debit dan kredit harus balance, total harus sama dengan debit/kredit
				$isValid = true;
				$uiState = "ui-state-error";
				$uiIcon = "ui-icon-alert";
				$message = "";
				if ($totalDebit != $totalKredit) {
					$message .= "<li>Debit dan kredit tidak balance</li>";
					$isValid = false;
				}
				if ($this->getRequest()->getParam("total") != $totalDebit || $this->getRequest()->getParam("total") != $totalKredit) {
					$message .= "<li>Total transaksi tidak sama dengan total debit/kredit</li>";
					$isValid = false;
				}
				
				// form di-submit
	        	if ($journalForm->isValid($_POST)) {
			        if ($isValid) {
		        		// generate data
						$data = array(
							"noBukti" => $this->getRequest()->getParam("noBukti"),
							"tipeJournal" => $this->getRequest()->getParam("tipeJournal"),
							"tanggal" => $this->getRequest()->getParam("tanggal"),
							"keterangan" => $this->getRequest()->getParam("keterangan"),
							"total" => $this->getRequest()->getParam("total"),
							"details" => $details,
						);
						
		        		// Simpan perubahan
		        		if ($journalModel->updateData($this->getRequest()->getParam("id"), $data)) {
				        	$uiState = "ui-state-highlight";
				        	$uiIcon = "ui-icon-info";
				        	$message = "disimpan";
		        		}
			        }
		        } else {
		        	$message .= "<li>Data tidak valid, Periksa kembali inputan!</li>";
		        }
		        
		        // tampilkan message
		        $message && $message = "<ul style='list-style: none; margin: 0; padding: 0; '>$message</ul>";
		        $this->view->uiState = $uiState;
		        $this->view->uiIcon = $uiIcon;
				$this->view->message = $message;
	        }
        } else {
        	if (!$bukti) { // id tidak ada, diasumsikan menambah data baru
        		// set data default
		        $journalForm->getElement("noBukti")->setValue($lastNoBukti);
		        $journalForm->getElement("tipeJournal")->setValue($this->_tipeJournal);
		        $journalForm->getElement("tanggal")->setValue($dateNow->toString("dd/MM/yyyy"));
		        
        	} else {
	        	$journalForm->populate($bukti);
	        	$this->view->user = array("name" => $bukti["validatedBy"], "date" => $bukti["validatedDate"]);
	        	
	        	$details = $journalModel->fetchDetails($id);
	        	foreach ($details as $detail) {
	        		$totalDebit += $detail["debit"];
		        	$totalKredit += $detail["kredit"];
	        	}
        	}
        }
        
        $this->view->form = $journalForm;
        $this->view->details = $details;
        $this->view->totalDebit = $totalDebit;
        $this->view->totalKredit = $totalKredit;
	    
		// data account untuk comboBox
		$this->view->accountList = $this->_getAccountList();
		
		// TODO Ada cara lebih baik?
		$this->view->headScript()->captureStart(); ?>
		lastNoBukti = "<?php echo $lastNoBukti; ?>";
		<?php $this->view->headScript()->captureEnd();
	}
	
    public function init() 
    {
    	//$this->view->headTitle("Transaksi Jurnal");
    }
	
	/**
     * Index.
     */
    public function indexAction()
    {
    	// Tambahkan link javascript
    	$this->view->JQuery()->addJavascriptFile("/js/jquery.pagination.js");
    	
    	// filter bulan
    	$tahun = $this->getRequest()->getParam("tahun");
    	$bulan = $this->getRequest()->getParam("bulan");
    	
    	$tanggal = getdate();
    	$tahun || $tahun = $tanggal["year"];
    	$bulan || $bulan = $tanggal["mon"];
    	
    	$journalModel = new Model_JournalUmum();
    	$journalModel->setTipeJournal($this->_tipeJournal);
    	$journalModel->setPeriode($tahun, $bulan);
    	
    	$adapter = $journalModel->fetchPaginatorAdapter();
    	$paginator = new Zend_Paginator($adapter);
    	$paginator->setPageRange(5);
    	
    	$page = $this->getRequest()->getParam("page", 1);
    	$paginator->setCurrentPageNumber($page);
    	
    	$this->view->paginator = $paginator;
    	$this->view->bulan = $bulan;
    	$this->view->tahun = $tahun;
    }

    /**
     * Halaman untuk menambah data journal.
     */
    public function createAction() {
    	$this->_actionCreateUpdate();
    }
    
    /**
     * Halaman untuk mengubah data journal.
     */
    public function updateAction()
    {
    	$this->_actionCreateUpdate();
    }
    
    /**
     * Halaman untuk menghapus data journal.
     */
    public function deleteAction()
    {
    	$journalModel = new Model_JournalUmum();
    	$id = $this->getRequest()->getParam("id");
    	$journalModel->deleteData($id);
    	return $this->_forward("index");
    }
}

