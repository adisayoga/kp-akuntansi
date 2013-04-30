<?php
/**
 * Class untuk manage data user.
 * @author Adi Sayoga
 */
class Model_User extends Zend_Db_Table_Abstract implements Model_Crud
{
	protected $_name = "users";
	
	/**
	 * Mendapatkan instansi dari objek Zend_Db_Table_Select.
	 * @param array $params
	 * @return Zend_Db_Table_Select
	 */
	private function _getSelect($params)
	{
		$select = $this->select();
		
		// Filter
		if (isset($params["filters"])) {
			foreach ($params["filters"] as $field => $filter) {
				if ($filter == null) {
					$select->where("$field is null");
				} else {
					$select->where("$field = ?", $filter);
				}
			}
		}
		
		// Sort
		isset($params["sortFields"]) && $select->order($params["sortFields"]);
		
		return $select;
	}
	
	/**
	 * Login
	 * @param string $username
	 * @param string $password
	 * @return bool
	 */
	public function login($username, $password)
	{
		// Dapatkan default db adapter
		$db = Zend_Db_Table::getDefaultAdapter();
		
		// Ciptakan auth adapter
		$authAdapter = new Zend_Auth_Adapter_DbTable($db, "users", "username", "password");
		
		// Set username dan password
		$authAdapter->setIdentity($username);
		$authAdapter->setCredential(md5($password));
		
		// Authenticate
		$result = $authAdapter->authenticate();
		if (!$result->isValid()) return false;
		
		// Taruh username dan displayName dari user
		$auth = Zend_Auth::getInstance();
		$storage = $auth->getStorage();
		$storage->write($authAdapter->getResultRowObject(array("username", "displayName", "role")));
		return true;
	}
	
	/**
	 * Logout
	 */
	public function logout()
	{
		$authAdapter = Zend_Auth::getInstance();
    	$authAdapter->clearIdentity();
	}
	
	public function fetchData($params = null)
	{
		$select = $this->_getSelect($params);
		return $this->fetchAll($select);
	}
	
	public function fetchPaginatorAdapter($params = null)
	{
		$select = $this->_getSelect($params);
		
		// Ciptakan instansi bari dari paginator adapter
		$adapter = new Zend_Paginator_Adapter_DbSelect($select);
		return $adapter;
	}
	
	public function fetchArray($params = null)
	{
		$select = $this->_getSelect($params);
		$db = $this->getAdapter();
		return $db->fetchAssoc($select);
	}
	
	public function createNew($data)
	{
		// Buat baris baru
		$row = $this->createRow();
		if (!$row) throw new Zend_Exception("Tidak dapat membuat user baru!");
		
		// Update nilai row
		$row->username = $data["username"];
		$row->password = md5($data["password"]);
		$row->displayName = $data["displayName"];
		$row->role = $data["role"];
		
		return $row->save();
	}
	
	public function updateData($id, $data)
	{
		// Cari baris yang cocok dengan id
		$row = $this->find($id)->current();
		if (!$row) throw new Zend_Exception("Update gagal, data tidak ditemukan!");
		
		$row->username = $data["username"];
		$row->displayName = $data["displayName"];
		$row->role = $data["role"];
		
		// Simpan baris yang di-update
		$row->save();
		return true;
	}
	
	/**
	 * Mengupdate hanya password.
	 * 
	 * @param int $id
	 * @param string $password
	 * 
	 * @return bool - True jika update berhasil.
	 * @throws Zend_Exception
	 */
	public function updatePassword($id, $password)
	{
		// Cari baris yang cocok dengan id
		$row = $this->find($id)->current();
		if (!$row) throw new Zend_Exception("Update gagal, data tidak ditemukan!");
		
		$row->password = md5($password);
		$row->save();
		return true;
	}
	
	public function deleteData($id)
	{
		// Cari baris yang cocok dengan id
		$row = $this->find($id)->current();
		if (!$row) throw new Zend_Exception("Delete gagal, data tidak ditemukan!");
		
		$row->delete();
		return true;
	}
}