<?php
class Model_JsonRpc
{
	/* -----------------------------------------------------------------------------------------
	 * Account
	 * -----------------------------------------------------------------------------------------*/
    
    /**
     * Validasi form account.
     * @param array $data
     * @param string $exclude
     * @return array
     */
    public function validateAccountForm($data, $exclude = null)
    {
    	$accountForm = new Form_Account($exclude);
    	
    	foreach ($accountForm->getElements() as $element) {
    		$elementId = $element->getId();
    		
    		if ($element->getType() != "Zend_Form_Element_Hidden" && !key_exists($elementId, $data)) {
    			// Hapus element yang tidak diikutsertakan dalam validasi
    			$accountForm->removeElement($elementId);
    		}
    	}
    	
    	$accountForm->isValid($data);
    	$messages = $accountForm->getMessages();
    	return $messages;
    }
    
    /**
     * Mendapatkan semua data account.
     * @return array
     */
    public function fetchAccount()
    {
    	$accountModel = new Model_Account();
    	return $accountModel->fetchArray();
    }
    
    /**
     * Mendapatkan data account per halaman.
     * @param int $page
     * @return returns the items for a given page.
     */
    public function fetchAccountPaginator($page)
    {
    	$accountModel = new Model_Account();
    	$adapter = $accountModel->fetchPaginatorAdapter();
    	$paginator = new Zend_Paginator($adapter);
    	$paginator->setPageRange(5);
    	
    	$page || $page = 1;
    	$paginator->setCurrentPageNumber($page);
    	
    	$result = array(
    		"pagination" => $paginator->getPages("sliding"),
    		"data" => $paginator->getCurrentItems()
    	);
    	return $result;
    }
    
	/**
	 * Mendapatkan data pasangan key dan value ke dalam array.
	 * @return array
	 */
    public function fetchAccountKeyValue()
    {
    	$accountModel = new Model_Account();
    	return $accountModel->fetchKeyValue("id", "account", "kodeAccount");
    }
    
    /**
	 * Mendapatkan semua anak sesuai dengan id parent yang dilewatkan (tidak termasuk anak dari anaknnya), 
	 * disini menghasilkan tambahan field yaitu 
	 * <code>depth</code>: kedalaman dari node, dan <code>hasChild</code>: ada atau tidaknya child.
	 * 
	 * @param int $parent OPTIONAL - Jika tidak dimasukkan, akan mengembalikan semua data yang tidak
	 *        mempunyai parent (root).
	 * @return array
	 */
    public function fetchAccountChilds($parent = null)
    {
    	$accountModel = new Model_Account();
    	return $accountModel->fetchChilds($parent);
    }
    
    /**
     * Mendapatkan data account berdasarkan id account
     * @param int $idAccount
     * @return array
     */
    public function getAccountById($idAccount) 
    {
    	$accountModel = new Model_Account();
    	return $accountModel->getAccountById($idAccount);
    }
    
    /**
     * Mendapatkan data account berdasarkan kode account
     * @param string $kodeAccount
     * @return array
     */
    public function getAccountByKode($kodeAccount)
    {
    	$accountModel = new Model_Account();
    	return $accountModel->getAccountByKode($kodeAccount);
    }
    
    /**
     * Menambah data account
     * @param array $data
     * @return Primary key dari tabel yang baru saja ditambahkan.
     */
    public function addAccount($data)
    {
    	$accountModel = new Model_Account();
    	return $accountModel->createNew($data);
    }
    
    /**
     * Mengubah data account
     * @param int $id
     * @param array $data
     * @return bool - true jika update berhasil
     */
    public function updateAccount($id, $data) 
    {
    	$accountModel = new Model_Account();
    	return $accountModel->updateData($id, $data);
    }
    
    /**
     * Menghapus data account
     * @param int $id
     * @return bool - True jika delete berhasil
     */
    public function deleteAccount($id)
    {
    	$accountModel = new Model_Account();
    	return $accountModel->deleteData($id);
    }
    
    /* -----------------------------------------------------------------------------------------
	 * Journal
	 * -----------------------------------------------------------------------------------------*/
    
    /**
     * Validasi form journal.
     * @param array $data
     * @param string $exclude
     * @return array
     */
    public function validateJournalForm($data, $exclude = null)
    {
    	$journalForm = new Form_Journal($exclude);
    	
    	foreach ($journalForm->getElements() as $element) {
    		$elementId = $element->getId();
    		
    		if ($element->getType() != "Zend_Form_Element_Hidden" && !key_exists($elementId, $data)) {
    			// Hapus element yang tidak diikutsertakan dalam validasi
    			$journalForm->removeElement($elementId);
    		}
    	}
    	
    	$journalForm->isValid($data);
    	$messages = $journalForm->getMessages();
    	return $messages;
    }
    
    /**
     * Mendapatkan data journal per halaman.
     * @param int $page
     * @param int $tipeJournal
     * @param int $tahun
     * @param int $bulan
     * @return returns the items for a given page.
     */
    public function fetchJournalPaginator($page, $tipeJournal, $tahun, $bulan)
    {
    	$journalModel = new Model_Journal();
    	$journalModel->setTipeJournal($tipeJournal);
    	$journalModel->setPeriode($tahun, $bulan);
    	
    	$adapter = $journalModel->fetchPaginatorAdapter();
    	$paginator = new Zend_Paginator($adapter);
    	$paginator->setPageRange(5);
    	
    	$page || $page = 1;
    	$paginator->setCurrentPageNumber($page);
    	
    	$result = array(
    		"pagination" => $paginator->getPages("sliding"),
    		"data" => $paginator->getCurrentItems()
    	);
    	
    	return $result;
    }
    
    /**
     * <p>Membuat nomor bukti otomatis sesuai dengan tipe journal dan tanggal dan dengan
	 * format yang ditentukan.</p>
	 * @param Zend_Date $date - Tanggal dengan format timestamp
	 * @param array $pattern - Pola yang digunakan. pattern[prefix], pattern[dateFormat], 
	 * 		  pattern[digitNum]
	 * @return string
     */
    public function journalAutoNum($date, $pattern)
    {
    	$journalModel = new Model_Journal();
    	return $journalModel->autoNum(new Zend_Date($date), $pattern);
    }
    
    /**
     * Menambah data journal
     * @param array $data
     * @return Primary key dari tabel yang baru saja ditambahkan.
     */
    public function addJournal($data)
    {
    	$journalModel = new Model_Journal();
    	return $journalModel->createNew($data);
    }
    
    /**
     * Mengupdate data journal berdasarkan id bukti transaksi
     * @param int $id
     * @param array $data
     */
    public function updateJournal($id, $data)
    {
    	$journalModel = new Model_Journal();
    	return $journalModel->updateData($id, $data);
    }
    
    /**
     * Menghapus data journal
     * @param int $id
     * @return bool - True jika delete berhasil
     */
    public function deleteJournal($id)
    {
    	$journalModel = new Model_Journal();
    	return $journalModel->deleteData($id);
    }
    
    /* -----------------------------------------------------------------------------------------
	 * User
	 * -----------------------------------------------------------------------------------------*/
    
    /**
     * Validasi form user.
     * @param array $data
     * @param string $eclude
     * @return array
     */
    public function validateUserForm($data, $exclude = null)
    {
    	$userForm = new Form_User($exclude);
    	
    	foreach ($userForm->getElements() as $element) {
    		$elementId = $element->getId();
    		
    		// Hapus element yang tidak diikutsertakan dalam validasi kecuali hidden element
    		if ($element->getType() != "Zend_Form_Element_Hidden" && !key_exists($elementId, $data)) {
    			$userForm->removeElement($elementId);
    		}
    	}
    	
    	$userForm->isValid($data);
    	$messages = $userForm->getMessages();
    	return $messages;
    }
    
    /**
     * Mendapatkan data user per halaman.
     * @param int $page
     * @return returns the items for a given page.
     */
    public function fetchUserPaginator($page)
    {
    	$userModel = new Model_User();
    	
    	$adapter = $userModel->fetchPaginatorAdapter();
    	$paginator = new Zend_Paginator($adapter);
    	$paginator->setPageRange(5);
    	
    	$page || $page = 1;
    	$paginator->setCurrentPageNumber($page);
    	
    	$result = array(
    		"pagination" => $paginator->getPages("sliding"),
    		"data" => $paginator->getCurrentItems()
    	);
    	return $result;
    }
    
    /**
     * Menambah data user
     * @param array $data
     * @return Primary key dari tabel yang baru saja ditambahkan.
     */
    public function addUser($data)
    {
    	$userModel = new Model_User();
    	return $userModel->createNew($data);
    }
    
    /**
     * Mengupdate data user
     * @param int $id
     * @param array $data
     * @return bool - true jika berhasil diupdate, false lainnya
     */
    public function updateUser($id, $data) 
    {
    	$userModel = new Model_User();
    	return $userModel->updateData($id, $data);
    }
    
    /**
     * Mengubah password
     * @param int $id
     * @param string $password
     * @return bool - true jika update berhasil
     */
    public function updatePassword($id, $password) 
    {
    	$userModel = new Model_User();
    	return $userModel->updatePassword($id, $password);
    }
    
    /**
     * Menghapus data user
     * @param int $id
     * @return bool - True jika delete berhasil
     */
    public function deleteUser($id)
    {
    	$userModel = new Model_User();
    	return $userModel->deleteData($id);
    }
    
	/**
	 * Login
	 * @param string $username
	 * @param string $password
	 */
	public function login($username, $password)
	{
		$userModel = new Model_User();
		return $userModel->login($username, $password);
	}
	
}
