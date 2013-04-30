<?php
/**
 * Class untuk manage tree.
 * @author Adi Sayoga
 */
class Model_Tree_Will_Deleted
{
	/** 
	 * Zend_Db_Adapter_Abstract object. 
	 * @var Zend_Db_Adapter_Abstract 
	 */
    protected $_db;
    
	/**
	 * Nama dari tabel yang digunakan.
	 * @var string
	 */
	protected $_name;
	
	/**
	 * Field id tabel. Default = "id".
	 * @var string
	 */
	protected $_id = "id";
	
	/**
	 * Field Left. Default = "lft".
	 * @var string
	 */
	protected $_lft = "lft";
	
	/**
	 * Field Right. Default = "rgt".
	 * @var string
	 */
	protected $_rgt = "rgt";
	
	/**
	 * Field parent. Default = "parent".
	 * @var string
	 */
	protected $_parent = "parent";
	
	/**
	 * Daftar nama field dari database.
	 * @var array
	 */
	protected $_fields;
	
	/**
	 * Constructor
	 * @param string $tableName - Nama dari tabel yang digunakan.
	 * @param array $fields - Daftar nama field dari database.
	 * @throws Exception
	 */
	public function __construct($tableName, $fields)
	{
		// Validasi
		if (!$tableName) throw new Exception("Nama tabel harus ditentukan");
		if (!$fields || !is_array($fields)) throw new Exception("\$field harus ditentukan dan berupa array");
		
		$this->_db = Zend_Db_Table::getDefaultAdapter();
		$this->_name = $tableName;
		$this->_fields = $fields;
	}
	
	/**
	 * Implode array field menjadi string.
	 * @param array $fields
	 * @param string $tablePrefix - OPTIONAL Nama tabel prefix, default = node
	 * @return string
	 */
	public function implodeField($fields, $tablePrefix = "node")
	{
		$result = "";
		$prefix = ($tablePrefix)? "$tablePrefix.": "";
		
		foreach ($fields as $field) {
			if ($field) {
				if ($result) $result .= ", ";
				if ($field instanceof Zend_Db_Expr) {
					//$result .= $field;
				} else {
					$result .= $prefix . $field;
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * Update field lft dan rgt pada tabel berdasarkan field parent.
	 */
	public function rebuildTree()
	{
		// mendapatkan top level tree (tidak mempunyai parent)
		$result = $this->fetchData(array("filters" => array($this->_parent => null)));
		
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
		$child = $this->fetchData(array("filters" => array($this->_parent => $parent)));
		
		foreach ($child as $row) {
			// Eksekusi rekursi dari fungsi ini untuk setiap anak dari node ini
			// $right adalah nilai right saat ini, yang dinaikkan nilainya oleh fungsi
			// rebuildTree ini.
			$right = $this->_rebuildChild($row["id"], $right);
		}
		
		// Kita telah mendapatkan nilai left, dan sekarang kita memproses anak dari node
		// ini, kita juga tahu nilai right
		$this->_db->update($this->_name, array($this->_lft => $left, $this->_rgt => $right), 
				$this->_db->quoteInto($this->_id . " = ?", $parent));

		// Kembalikan nilai right dari node ini + 1
		return $right + 1;
	}
	
	/**
	 * Mendapatkan semua data tabel ini
	 * @param array $params OPTIONAL - Parameter yang diterima: <code>array filters</code> - filter
	 * 		  pada where clause yang berupa <code>key => value</code>; <code>string sortFields
	 * 		  </code> - field untuk pengurutan.
	 * @return array
	 */
	public function fetchData($params = null)
	{
		$select = $this->_db->select();
		$select->from($this->_name);
		
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
		
		return $this->_db->fetchAll($select);
	}
	
	/**
	 * Mendapatkan semua data dalam bentuk tree, disini menghasilkan tambahan field yaitu 
	 * <code>depth</code>: kedalaman dari node, dan <code>hasChild</code>: ada atau tidaknya child.
	 * @param array $selectFields OPTINAL - Daftar field yang akan ditampilkan. Jika tidak dilewatkan
	 * 		  maka field pada constructor yang digunakan.
	 * @param array $params OPTIONAL - Parameter yang diterima: <code>array filters</code> - filter
	 * 		  pada where clause yang berupa <code>key => value</code>; <code>string sortFields
	 * 		  </code> - field untuk pengurutan.
	 * @return array
	 */
	public function fetchTree($selectFields = null, $params = null)
	{
		if ($selectFields == null) $selectFields = $this->_fields;
		$fields = $this->implodeField($selectFields, "node");
		
		$tableName = $this->_name;
		$idField = $this->_id;
		$lftField = $this->_lft;
		$rgtField = $this->_rgt;
		
		// Filter
		$whereExp = "";
		if (isset($params["filters"])) {
			if ($params["filters"] instanceof Zend_Db_Expr) {
				$whereExp = $params["filter"]->__toString();
			} else {
				foreach ($params["filters"] as $field => $filter) {
					$whereExp += " AND " . ($filter == null)? "node.$field is null": 
							$this->_db->quoteInto("node.$field = ?", $filter);
				}
			}
		}
		
		// Sort
		$sortOrder = (isset($params["sortFields"]))? $params["sortFields"] . ", ": "";
		
		$sql = "SELECT $fields, (COUNT(parent.$idField) - 1) AS depth, "
			 . "	CASE node.$rgtField - node.$lftField WHEN 1 THEN true ELSE false END AS hasChild "
			 . "FROM $tableName AS node, $tableName AS parent "
			 . "WHERE node.$lftField BETWEEN parent.$lftField AND parent.$rgtField $whereExp"
			 . "GROUP BY node.$idField "
			 . "ORDER BY $sortOrder node.$lftField";
		
		return $this->_db->fetchAll($sql);
	}
	
	/**
	 * Mendapatkan semua anak sesuai dengan id parent yang dilewatkan (tidak termasuk anak dari anaknnya), 
	 * disini menghasilkan tambahan field yaitu 
	 * <code>depth</code>: kedalaman dari node, dan <code>hasChild</code>: ada atau tidaknya child.
	 * 
	 * @param int $parent OPTIONAL - Jika tidak dimasukkan, akan mengembalikan semua data yang tidak
	 *        mempunyai parent (root).
	 * @param array $selectFields OPTIONAL - Daftar field yang akan ditampilkan. Jika tidak dilewatkan
	 * 		  maka field pada constructor yang digunakan.
	 * @return array
	 */
	public function fetchChilds($parent = null, $selectFields = null)
	{
		if ($selectFields == null) $selectFields = $this->_fields;
		$fields = $this->implodeField($selectFields, "node");
		
		$tableName = $this->_name;
		$idField = $this->_id;
		$lftField = $this->_lft;
		$rgtField = $this->_rgt;
		
		// Generate sql
		if ($parent == null) {
			// $parent tidak ditentukan, select semua root
			$sql = "SELECT $fields, null as parent, (COUNT(parent.$idField) - 1) AS depth, "
				 . "	CASE node.$rgtField - node.$lftField WHEN 1 THEN true ELSE false END AS hasChild "
				 . "FROM $tableName AS node, $tableName AS parent "
				 . "WHERE node.$lftField BETWEEN parent.$lftField AND parent.$rgtField "
				 . "GROUP BY node.$idField "
				 . "HAVING depth = 0 "
				 . "ORDER BY node.$lftField";
		} else {
			// Select anak dari $parent ini
			$parent = $this->_db->quote($parent);
			$sql = "SELECT $fields, $parent as parent, CASE node.$rgtField - node.$lftField WHEN 1 THEN true "
				 . "	ELSE false END AS hasChild, (COUNT(parent.$idField) - (subTree.depth + 1)) "
				 . "	AS depth "
				 . "FROM $tableName AS node, $tableName AS parent, $tableName AS subParent, "
				 . "( "
				 . "	SELECT node.$idField, (COUNT(parent.$idField) - 1) AS depth "
				 . "	FROM $tableName AS node, $tableName AS parent "
				 . "	WHERE node.$lftField BETWEEN parent.$lftField AND parent.$rgtField "
				 . "	AND node.$idField = $parent "
				 . "	GROUP BY node.$idField "
				 . "	ORDER BY node.$lftField "
				 . ") AS subTree "
				 . "WHERE node.$lftField BETWEEN parent.$lftField AND parent.$rgtField "
				 . "	AND node.$lftField BETWEEN subParent.$lftField AND subParent.$rgtField "
				 . "	AND subParent.$idField = subTree.$idField "
				 . "GROUP BY node.$idField "
				 . "HAVING depth = 1 "
				 . "ORDER BY node.$lftField";
		}
		
		return $this->_db->fetchAll($sql);
	}
	
	/**
	 * Mengecek apakah node ini mempunyai anak/tidak.
	 * @param int $id
	 * @return bool
	 */
	public function isLeaf($id)
	{
		$select = $this->_db->select();
		$select->from($this->_name, $this->_id);
		$select->where($this->_id . " = ?", $id);
		$select->where("rgt = lft + 1");
		return $this->_db->fetchOne($select) == $id;
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
		$data = "";
		$rows = $this->fetchTree(array($fieldKey, $fieldValue, $fieldKode));
		foreach ($rows as $row) {
			$data[$row[$fieldKey]] = str_repeat(". . . . ", $row["depth"]) 
					. (($fieldKode)? " [" . $row[$fieldKode] . "] - ": "") . " " . $row[$fieldValue];
		}
		return $data;
	}
	
	/**
	 * Mengembalikan data tree berupa nested array.
	 * @param string $whichField - Field yang mana yang akan ditampilkan
	 * @return array
	 */
	public function toArray($whichField)
	{
		// TODO Nested array
		return array("Nested array");
	}
	
	/* *
	 * Menghapus data berdasarkan id tabel.
	 * 
	 * @param int $id - Id dari table.
	 * @param int $method - OPTIONAL Metode yang digunakan: <b>hapus beserta semua anak-anaknya</b>|
	 * 		semua anak menjadi parent|anak pertama dipromosikan menjadi parent
	 * @return bool - True jika delete berhasil.
	 * @throws Zend_Exception
	 */
	/*public function deleteData($id, $method = Model_Tree::DELETE_ALL_CHILD)
	{
		$tableName = $this->_name;
		$idField = $this->_id;
		$lftField = $this->_lft;
		$rgtField = $this->_rgt;
		
		$db = $this->_db;
		$select = $db->select();
		$select->from($tableName, array($lftField, $rgtField));
		$select->where("$idField = ?", $id);
		
		$row = $db->fetchCol($select);
		if (!$row) throw new Zend_Exception("Fungsi delete gagal, data tidak ditemukan.");
		
		$db->beginTransaction();
		
		$left  = $row[$lftField];
		$right = $row[$rgtField];
		$width = $row[$lftField] - $row[$rgtField] + 1;
		
		switch ($method) {
			case Model_Tree::DELETE_ALL_CHILD:
				// Hapus beserta anak-anaknya
				$db->delete($tableName, "$lftField BETWEEN $left AND $right");
				
				// Update lft dan rgt
				$db->update($tableName, array($lftField => new Zend_Db_Expr("$lftField - $width")), "$lftField > $right");
				$db->update($tableName, array($rgtField => new Zend_Db_Expr("$rgtField - $width")), "$rgtField > $right");
				break;
				
			case Model_Tree::ALL_CHILD_TO_PARENT:
				// Hapus node ini
				$db->delete($tableName, "$lftField = $left");
				
				// Update lft dan rgt
				$db->update($tableName, array($lftField => new Zend_Db_Expr("$lftField - 1"), 
											  $rgtField => new Zend_Db_Expr("$rgtField - 1")), 
					"$lftField BETWEEN $left AND $right");
					
				$db->update($tableName, array("$lftField" => new Zend_Db_Expr("$lftField - 2")), "$lftField > $right");
				$db->update($tableName, array("$rgtField" => new Zend_Db_Expr("$rgtField - 2")), "$rgtField > $right");
				break;
				
			case Model_Tree::FIRSTCHILD_TO_PARENT:
				// TO DO Menghapus node account kemudian anak pertamanya dijadikan parent
				throw new Zend_Exception("TO DO Menghapus node account kemudian anak pertamanya dijadikan parent");
				break;
		}
		
		$db->commit();
		return true;
	}*/
}