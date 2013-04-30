<?php

class AccountController extends Zend_Controller_Action
{

    public function init() 
    {
    	//$this->view->headTitle("Data Akun");
    }

    /**
     * Index.
     */
    public function indexAction()
    {
        // Tambahkan link javascript
    	$this->view->JQuery()->addJavascriptFile("/js/jquery.pagination.js");
    	
    	$accountModel = new Model_Account();
    	$adapter = $accountModel->fetchPaginatorAdapter();
    	$paginator = new Zend_Paginator($adapter);
    	$paginator->setPageRange(5);
    	
    	$page = $this->getRequest()->getParam("page", 1);
    	$paginator->setCurrentPageNumber($page);
    	$this->view->paginator = $paginator;
    }

    /**
     * Halaman untuk menambah data account.
     */
    public function createAction()
    {
    	// Tambahkan link javascript
    	$this->view->JQuery()->addJavascriptFile("/js/jquery.formValidation.js");
    	
        $accountForm = new Form_Account();
        $accountForm->setAction("/account/create");
        $accountForm->setMethod("post");
        
        if ($this->getRequest()->isPost() && $accountForm->isValid($_POST)) {
            $accountModel = new Model_Account();
            
            $result = $accountModel->createNew(array(
            	"parent"      => $accountForm->getValue("parent"), 
            	"after"       => $accountForm->getValue("after"),
                "kodeAccount" => $accountForm->getValue("kodeAccount"), 
                "account"     => $accountForm->getValue("account"),
                "normalPos"   => $accountForm->getValue("normalPos"),
                "kelompok"    => $accountForm->getValue("kelompok")
            ));
            
            // Setelah disimpan, kosongkan kembali formnya
            if ($result) {
	            // Data account telah berubah, update list account
            	$accountData = $accountModel->fetchKeyValue("id", "account", "kodeAccount");
	            if ($accountData) {
		            $accountForm->parent->clearMultiOptions();
		            $accountForm->parent->addMultiOption(0, "(Tidak Ada)");
		            $accountForm->parent->addMultiOptions($accountData);
		            $accountForm->after->clearMultiOptions();
		            $accountForm->after->addMultiOption(0, "(Paling Awal)");
		            $accountForm->after->addMultiOptions($accountData);
	            }
	            
            	$accountForm->kodeAccount->setValue("");
	            $accountForm->account->setValue("");
	            
	            $this->view->message = "Data akun berhasil ditambahkan.";
            } else {
            	$this->view->message = "Data akun gagal ditambahkan, cek kembali inputan!";
            }
        }
        $this->view->form = $accountForm;
    }
    
    /**
     * Halaman untuk mengubah data account.
     */
    public function updateAction()
    {
    	// Tambahkan link javascript
    	$this->view->JQuery()->addJavascriptFile("/js/jquery.formValidation.js");
    	
    	$id = $this->getRequest()->getParam("id");
    	$accountModel = new Model_Account();
    	$account = $accountModel->find($id)->current();
    	if (!$account) throw new Zend_Exception("User tidak ditemukan.");
    	
    	$accountForm = new Form_Account();
    	$accountForm->setAction("/account/update");
    	$accountForm->setMethod("post");
    	$accountForm->removeElement("parent"); // Parent tidak bisa di-update
    	$accountForm->removeElement("after");  // After tidak diperlukan
    	$accountForm->setExclude($account->kodeAccount);
    	
    	if ($this->getRequest()->isPost()) {
    		if ($accountForm->isValid($_POST)) {
	    		$result = $accountModel->updateData($accountForm->getValue("id"), array(
	    			"kodeAccount" => $accountForm->getValue("kodeAccount"),
	    			"account"     => $accountForm->getValue("account"),
	    			"normalPos"   => $accountForm->getValue("normalPos"),
	    			"kelompok"    => $accountForm->getValue("kelompok")
	    		));
	    		return $this->_forward("index");
    		}
    	} else {
	    	$accountForm->populate($account->toArray());
    	}
    	$this->view->form = $accountForm;
    	$this->view->lastKodeAccount = $account->kodeAccount;
    }
    
    /**
     * Halaman untuk menghapus data account.
     */
    public function deleteAction()
    {
    	$accountModel = new Model_Account();
    	$id = $this->getRequest()->getParam("id");
    	$accountModel->deleteData($id);
    	return $this->_forward("index");
    }
}