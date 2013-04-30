<?php
/**
 * Interface untuk Create, Read, Update, Delete data.
 * @author Adi Sayoga
 */
interface Model_Crud
{
	/**
	 * Menambah data baru.
	 * @param array $data
	 * @return Primary key dari tabel yang baru saja ditambahkan.
	 * @throws Zend_Exception
	 */
	public function createNew($data);
	
	/**
	 * Mendapatkan semua data.
	 * @param array $params OPTIONAL - Parameter yang diterima: <code>array filters</code> - filter
	 * 		  pada where clause yang berupa <code>key => value</code>; <code>string sortFields
	 * 		  </code> - field untuk pengurutan.
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function fetchData($params = null);
	
	/**
	 * Mendapatkan semua data dalam bentuk Zend_Paginator_Adapter_DbSelect.
	 * @param array $params OPTIONAL - Parameter yang diterima: <code>array filters</code> - filter
	 * 		  pada where clause yang berupa <code>key => value</code>; <code>string sortFields
	 * 		  </code> - field untuk pengurutan.
	 * @return Zend_Paginator_Adapter_DbSelect
	 */
	public function fetchPaginatorAdapter($params = null);
	
	/**
	 * Mendapatkan semua data dalam bentuk array.
	 * @param array $params OPTIONAL - Parameter yang diterima: <code>array filters</code> - filter
	 * 		  pada where clause yang berupa <code>key => value</code>; <code>string sortFields
	 * 		  </code> - field untuk pengurutan.
	 * @return array
	 */
	public function fetchArray($params = null);
	
	/**
	 * Mengupdate data berdasarkan id tabel.
	 * @param int $id
	 * @param array $data
	 * @return bool - True jika update berhasil.
	 * @throws Zend_Exception
	 */
	public function updateData($id, $data);
	
	/**
	 * Menghapus data berdasarkan id tabel.
	 * @param int $id
	 * @return bool - True jika delete berhasil.
	 * @throws Zend_Exception
	 */
	public function deleteData($id);
}