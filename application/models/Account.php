<?php
/**
 * Class untuk manage data account.
 * @author Adi Sayoga
 */
class Model_Account extends Zend_Db_Table_Abstract implements Model_Crud
{
	const DELETE_ALL_CHILD = 1;
	const ALL_CHILD_TO_PARENT = 2;
	const FIRSTCHILD_TO_PARENT = 3;
	
	protected $_name = "account";
	private $_deleteMethod = self::DELETE_ALL_CHILD;
	
	public function fetchData($params = null) {
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
		
		return $this->fetchAll($select);
	}
	
	/**
	 * Mendapatkan semua data dalam bentuk tree, disini menghasilkan tambahan field yaitu 
	 * <code>depth</code>: kedalaman dari node, dan <code>hasChild</code>: ada atau tidaknya child.
	 * @param array $params OPTIONAL - Parameter yang diterima: <code>array filters</code> - filter
	 * 		  pada where clause yang berupa <code>key => value</code>.
	 * @return array
	 */
	public function fetchArray($params = null)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		
		// Filter
		$whereExp = "";
		if (isset($params["filters"])) {
			if ($params["filters"] instanceof Zend_Db_Expr) {
				$whereExp = $params["filter"]->__toString();
			} else {
				foreach ($params["filters"] as $field => $filter) {
					$whereExp += " AND " . ($filter == null)? "node.$field is null": 
							$db->quoteInto("node.$field = ?", $filter);
				}
			}
		}
		
		$sql = "SELECT node.id, node.kodeAccount, node.account, node.normalPos, CASE node.normalPos WHEN 1 "
			 .     "THEN 'Debit' ELSE 'Kredit' END AS normalPosKet, node.kelompok, CASE node.kelompok WHEN 'N' "
			 .     "THEN 'Neraca' ELSE 'Laba/Rugi' END AS kelompokKet, (COUNT(parent.id) - 1) AS depth, "
			 .     "CASE node.rgt - node.lft WHEN 1 THEN true ELSE false END AS hasChild "
			 . "FROM account AS node, account AS parent "
			 . "WHERE node.lft BETWEEN parent.lft AND parent.rgt $whereExp"
			 . "GROUP BY node.id "
			 . "ORDER BY node.lft";
		
		return $db->fetchAll($sql);
	}
	
	/**
	 * Mendapatkan semua data dalam bentuk Zend_Paginator_Adapter_Array.
	 * @return Zend_Paginator_Adapter_Array
	 */
	public function fetchPaginatorAdapter($params = null) {
		$data = $this->fetchArray($params);
		$adapter = new Zend_Paginator_Adapter_Array($data);
		return $adapter;
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
	public function fetchChilds($parent = null)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		
		// Generate sql
		if ($parent == null) {
			// $parent tidak ditentukan, select semua root
			$sql = "SELECT node.id, node.kodeAccount, node.account, node.normalPos, node.kelompok, null as parent, "
				 . "	(COUNT(parent.id) - 1) AS depth, CASE node.rgt - node.lft WHEN 1 THEN true ELSE "
				 . "	false END AS hasChild "
				 . "FROM account AS node, account AS parent "
				 . "WHERE node.lft BETWEEN parent.lft AND parent.rgt "
				 . "GROUP BY node.id "
				 . "HAVING depth = 0 "
				 . "ORDER BY node.lft";
		} else {
			// Select anak dari $parent ini
			$parent = $db->quote($parent);
			$sql = "SELECT node.id, node.kodeAccount, node.account, node.normalPos, node.kelompok, $parent as parent, "
			     . "	CASE node.rgt - node.lft WHEN 1 THEN true ELSE false END AS hasChild, "
			     . "	(COUNT(parent.id) - (subTree.depth + 1)) AS depth "
				 . "FROM account AS node, account AS parent, account AS subParent, "
				 . "( "
				 . "	SELECT node.id, (COUNT(parent.id) - 1) AS depth "
				 . "	FROM account AS node, account AS parent "
				 . "	WHERE node.lft BETWEEN parent.lft AND parent.rgt "
				 . "	AND node.id = $parent "
				 . "	GROUP BY node.id "
				 . "	ORDER BY node.lft "
				 . ") AS subTree "
				 . "WHERE node.lft BETWEEN parent.lft AND parent.rgt "
				 . "	AND node.lft BETWEEN subParent.lft AND subParent.rgt "
				 . "	AND subParent.id = subTree.id "
				 . "GROUP BY node.id "
				 . "HAVING depth = 1 "
				 . "ORDER BY node.lft";
		}
		
		return $db->fetchAll($sql);
	}
	
	/**
	 * Mendapatkan data pasangan key dan value ke dalam array.
	 * 
	 * @param string $fieldKey
	 * @param string $fieldValue
	 * @param string $fieldKode - OPTIONAL
	 * @return array
	 */
	public function fetchKeyValue($fieldKey, $fieldValue, $fieldKode = "")
	{
		$data = array();
		$rows = $this->fetchArray();
		foreach ($rows as $row) {
			$data[$row[$fieldKey]] = str_repeat(". . . . ", $row["depth"]) . (($fieldKode)? 
					" [" . $row[$fieldKode] . "] - ": "") . " " . $row[$fieldValue];
		}
		return $data;
	}
	
	/**
	 * Mendapatkan data account berdasarkan id
	 * @param int idAccount
	 * @return array
	 */
	public function getAccountById($idAccount) 
	{
		$select = $this->select();
		$select->where("id = ?", $idAccount);
		$db = $this->getAdapter();
		return $db->fetchAll($select);
	}
	
	/**
	 * Mendapatkan data account berdasarkan kode
	 * @param string kodeAccount
	 * @return array
	 */
	public function getAccountByKode($kodeAccount)
	{
		$select = $this->select();
		$select->where("kodeAccount = ?", $kodeAccount);
		$db = $this->getAdapter();
		return $db->fetchAll($select);
	}
	
	public function createNew($data) 
	{
		// Menentukan lft/rgt dari node
		if ($data["after"] == 0) { // Insert paling awal
			$rowParent = $this->find($data["parent"])->current();
			$no = ($rowParent)? $rowParent->lft: 0;
		} else {
			$rowParent = $this->find($data["after"])->current();
			$no = ($rowParent)? $rowParent->rgt: 0;
		}
		
		// Update lft dan rgt terlebih dahulu
		$adapter = $this->getAdapter();
		$adapter->beginTransaction();
		$this->update(array("lft" => new Zend_Db_Expr("lft + 2")), $adapter->quoteInto("lft > ?", $no));
		$this->update(array("rgt" => new Zend_Db_Expr("rgt + 2")), $adapter->quoteInto("rgt > ?", $no));
		
		// Buat baris baru pada tabel account
		$row = $this->createRow();
		if (!$row) {
			$adapter->rollBack();
			throw new Zend_Exception("Tidak dapat membuat account baru!");
		}
		
		// Set data pada baris baru
		$row->parent = ($data["parent"])? $data["parent"]: null;
		$row->lft = $no + 1;
		$row->rgt = $no + 2;
		$row->kodeAccount = $data["kodeAccount"];
		$row->account = $data["account"];
		$row->normalPos = $data["normalPos"];
		$row->kelompok = $data["kelompok"];
		
		// Simpan baris baru
		$result = $row->save();
		$adapter->commit();
		return $result;
	}
	
	public function updateData($id, $data)
	{
		// Cari baris yang cocok dengan id
		$row = $this->find($id)->current();
		if (!$row) throw new Zend_Exception("Update gagal, data tidak ditemukan!");
		
		$oldKodeAccount = $row->kodeAccount;
		$lenOldKode = strlen($oldKodeAccount);
		
		// Update data
		$row->kodeAccount = $data["kodeAccount"];
		$row->account = $data["account"];
		$row->normalPos = $data["normalPos"];
		$row->kelompok = $data["kelompok"];
		
		// Simpan baris yang di-update
		$row->save();
		
		// Update child
		$db = $this->getAdapter();
		$db->update("account", array("kodeAccount" => new Zend_Db_Expr("concat('" . $data["kodeAccount"] 
		        . "', SubString(kodeAccount, " . ($lenOldKode + 1) . ")) ")), 
		        "Left(kodeAccount, $lenOldKode) = '$oldKodeAccount'");
		        
		return true;
	}
	
	public function deleteData($id)
	{
		$row = $this->find($id)->current();
		if (!$row) throw new Zend_Exception("Delete gagal, data tidak ditemukan.");
		
		$adapter = $this->getAdapter();
		$adapter->beginTransaction();
		
		$left = $row->lft;
		$right = $row->rgt;
		$width = $row->rgt - $row->lft + 1;
		
		switch ($this->_deleteMethod) {
			case self::DELETE_ALL_CHILD:
				// Hapus beserta anak-anaknya
				$this->delete("lft BETWEEN $left AND $right");
				
				// Update lft dan rgt
				$this->update(array("lft" => new Zend_Db_Expr("lft - $width")), "lft > $right");
				$this->update(array("rgt" => new Zend_Db_Expr("rgt - $width")), "rgt > $right");
				break;
				
			case self::ALL_CHILD_TO_PARENT:
				// Hapus node ini
				$this->delete("lft = $left");
				
				// Update lft dan rgt
				$this->update(array("lft" => new Zend_Db_Expr("lft - 1"), "rgt" => new Zend_Db_Expr("rgt - 1")), 
					"lft BETWEEN $left AND $right");
					
				$this->update(array("lft" => new Zend_Db_Expr("lft - 2")), "lft > $right");
				$this->update(array("rgt" => new Zend_Db_Expr("rgt - 2")), "rgt > $right");
				break;
				
			case self::FIRSTCHILD_TO_PARENT:
				// TODO Menghapus node account kemudian anak pertamanya dijadikan parent
				throw new Zend_Exception("TODO Menghapus node account kemudian anak pertamanya dijadikan parent");
				break;
		}
		
		$adapter->commit();
		return true;
	}
	
	
	/**
	 * Update field lft dan rgt pada tabel berdasarkan field parent.
	 */
	public function rebuildTree()
	{
		// mendapatkan top level tree (tidak mempunyai parent)
		$result = $this->fetchData(array("filters" => array("parent" => null)));
		
		// Rebuild child
		$left = 1; // Nilai awal left = 1
		foreach ($result as $row) {
			$left = $this->_rebuildChild($row["id"], $left);
		}
	}
	
	/**
	 * Fungsi rekursi untuk mengupdate semua anak-anaknya.
	 * 
	 * @param int $parent
	 * @param int $left
	 * @return int
	 */
	private function _rebuildChild($parent, $left) {
		// Nilai right dari node ini adalah nilai left + 1
		$right = $left + 1;
		
		// Mendapatkan semua anak dari node ini
		$child = $this->fetchData(array("filters" => array("parent" => $parent)));
		
		foreach ($child as $row) {
			// Eksekusi rekursi untuk setiap anak dari node ini.
			// $right adalah nilai right saat ini, yang dinaikkan nilainya oleh fungsi ini.
			$right = $this->_rebuildChild($row["id"], $right);
		}
		
		// Kita telah mendapatkan nilai left, dan sekarang kita memproses anak dari node
		// ini, kita juga tahu nilai right
		$$this->update(array("lft" => $left, "rgt" => $right), "id = $parent");

		// Kembalikan nilai right dari node ini + 1
		return $right + 1;
	}
	
}