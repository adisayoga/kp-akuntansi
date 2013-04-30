<?php
/**
 * Class untuk manage data journal.
 * @author Adi Sayoga
 */
class Model_Journal extends Zend_Db_Table_Abstract implements Model_Crud
{
	protected $_name = "bukti_transaksi";   // Nama tabel yang digunakan
	protected $_tipeJournal = "";   		// Tipe journal yang digunakan
	protected $_tahun;          			// Periode tahun
	protected $_bulan;          			// Periode bulan
	
	const TIPE_JOURNAL_UMUM = 1;
	const TIPE_JOURNAL_PENYESUAIAN = 2;
	
	/**
	 * Mendapatkan instansi dari objek Zend_Db_Table_Select.
	 * @param array $params
	 * @return Zend_Db_Table_Select
	 */
	private function _getSelect($params)
	{
		$select = new Zend_Db_Select($this->getAdapter());
		$select->from(
				array("b" => "bukti_transaksi"),
				array("id", "noBukti", "tipeJournal",
				 
					"tipeJournalKet" => new Zend_Db_Expr("CASE b.tipeJournal "
				  		. "WHEN " . self::TIPE_JOURNAL_UMUM . " THEN 'Jurnal Umum' " 
				  		. "WHEN " . self::TIPE_JOURNAL_PENYESUAIAN . " THEN 'Jurnal Penyesuaian' "
				  		. "ELSE 'Jurnal' END "), 
				  	
				  	"tanggal" => new Zend_Db_Expr("date_format(from_unixtime(b.tanggal), '%d/%m/%Y')"),
				  	 
				  	"keterangan", "total" => new Zend_Db_Expr("format(total, '#.##0')"), "validatedBy",
				  	
				  	"validatedDate" => new Zend_Db_Expr("date_format(from_unixtime(b.validatedDate), "
				  		. "'%d/%m/%Y')")))
				
			->join(
				array("j" => "journal"), "b.id = j.idBukti",
				array("idAccount", 
					"debit" => new Zend_Db_Expr("format(debit, '#.##0')"), 
					"kredit" => new Zend_Db_Expr("format(kredit, '#.##0')"), 
					"pos" => new Zend_Db_Expr("CASE j.debit WHEN 0 THEN -1 ELSE 1 END"),
					"posKet" => new Zend_Db_Expr("CASE j.debit WHEN 0 THEN 'Kredit' ELSE 'Debit' END")))
				
			->join(array("a" => "account"), "a.id = j.idAccount",
			   	array("kodeAccount", "account"));
		
		// Tipe journal
		$this->_tipeJournal && $select->where("tipeJournal = ?", $this->_tipeJournal);
		
		// Periode
		$this->_tahun && $select->where("year(from_unixtime(tanggal)) = ?", $this->_tahun);
		$this->_bulan && $select->where("month(from_unixtime(tanggal)) = ?", $this->_bulan);
		
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
	 * Mengeset tipe jurnal (jurnal umum/jurnal penyesuaian)
	 * @param int $tipeJournal
	 */
	public function setTipeJournal($tipeJournal) 
	{
		$this->_tipeJournal = $tipeJournal;
	}
	
	/**
	 * Mengeset filter periode (tahun dan bulan)
	 * @param $tahun int
	 * @param $bulan int
	 */
	public function setPeriode($tahun, $bulan)
	{
		$this->_tahun = $tahun;
		$this->_bulan = $bulan;
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
		return $db->fetchAll($select);
	}
	
	/**
	 * Mendapatkan data hanya dari tabel bukti transaksi
	 * @param int $id
	 * @return array
	 */
	public function fetchBukti($id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select();
		$select->from("bukti_transaksi",
			array("id", "noBukti", "tipeJournal",
				"tipeJournalKet" => new Zend_Db_Expr("CASE tipeJournal "
			  		. "WHEN " . self::TIPE_JOURNAL_UMUM . " THEN 'Jurnal Umum' " 
			  		. "WHEN " . self::TIPE_JOURNAL_PENYESUAIAN . " THEN 'Jurnal Penyesuaian' "
			  		. "ELSE 'Jurnal' END "), 
			  	
			  	"tanggal" => new Zend_Db_Expr("date_format(from_unixtime(tanggal), '%d/%m/%Y')"),
			  	 
			  	"keterangan", "total", "validatedBy",
			  	
			  	"validatedDate" => new Zend_Db_Expr("date_format(from_unixtime(validatedDate), "
			  		. "'%d/%m/%Y')")))
			->where("id = ?", $id);
		
		return $db->fetchRow($select);
	}
	
	/**
	 * Mendapatkan semua data journal per bukti transaksi
	 * @param int $id
	 * @return array
	 */
	public function fetchDetails($id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select();
		$select->from(array("j" => "journal"))
			   ->join(array("a" => "account"), "a.id = j.idAccount", array("kodeAccount"))
			   ->where("j.idBukti = ?", $id);
		return $db->fetchAll($select);
	}
	
	public function createNew($data)
	{
		return $this->updateData(0, $data);
	}
	
	/**
	 * @return Primary key dari tabel
	 */
	public function updateData($id, $data)
	{
		//return $data;
		
		$db = $this->getAdapter();
		$db->beginTransaction();
		
		// Cari baris yang cocok dengan id, jika tidak ditemukan maka tambah baru
		$row = $this->find($id)->current();
		$row || $row = $this->createRow();
		
		if (!$row) {
			$db->rollBack();
			throw new Zend_Exception("Tidak dapat menambah atau mengubah data.");
		}
		
		$now = new Zend_Date();
		$tanggal = new Zend_Date($data["tanggal"]);
		$auth = Zend_Auth::getInstance();
		$username = ($auth->hasIdentity())? $auth->getIdentity()->username: "";
        
		// Set data pada baris baru
		$row->tipeJournal = $data["tipeJournal"];
		$row->noBukti = $data["noBukti"];
		$row->tanggal = $tanggal->get(Zend_Date::TIMESTAMP);
		$row->keterangan = $data["keterangan"];
		$row->total = $data["total"];
		$row->validatedBy = $username;
		$row->validatedDate = $now->get(Zend_Date::TIMESTAMP);
		
		// Simpan baris yang di-update
		$id = $row->save();
		
		// Hapus data terlebih dahulu
		$db->delete("journal", "idBukti = $id");
		
		// Kemudian insert detail baru
		foreach ($data["details"] as $detail) {
			$db->insert("journal", array(
				"idBukti" => $id,
				"idAccount" => $detail["idAccount"],
				"debit" => $detail["debit"],
				"kredit" => $detail["kredit"]
			));
		}
		
		$db->commit();
		return $id;
	}
	
	public function deleteData($id)
	{
		// Cari baris yang cocok dengan id
		$row = $this->find($id)->current();
		if (!$row) throw new Zend_Exception("$id Delete gagal, data tidak ditemukan!");
		
		$row->delete();
		return true;
	}
	
	/**
	 * <p>Membuat nomor bukti otomatis sesuai dengan tipe journal dan tanggal dan dengan
	 * format yang ditentukan.</p>
	 * @param Zend_Date $date - Tanggal dengan format timestamp
	 * @param array $pattern - Pola yang digunakan. pattern[prefix], pattern[dateFormat], 
	 * 		  pattern[digitNum]
	 * @return string
	 */
	public function autoNum($date, $pattern)
	{
		$defaultPattern = array("prefix" => "BJU", "dateFormat" => "yyMM", "digitNum" => 4);
		
		// prefix
		$prefix = "";
		if (isset($pattern["dateFormat"])) {
			$pattern["dateFormat"] && $prefix .= $date->toString($pattern["dateFormat"]) . "/";
		} else {
			$prefix .= $date->toString($defaultPattern["dateFormat"]) . "/";
		}
		if (isset($pattern["prefix"])) {
			$pattern["prefix"] && $prefix .= $pattern["prefix"] . "/";
		} else {
			$prefix .= $defaultPattern["prefix"] . "/";
		}
		$lenPrefix = strlen($prefix);
		
		// jumlah digit
		if (isset($pattern["digitNum"])) {
			$pattern["digitNum"] && $digitNum = $pattern["digitNum"];
		} else {
			$digitNum = $defaultPattern["digitNum"];
		}
		
		// sql: SELECT MAX(noBukti) FROM bukti_transaksi WHERE LEFT(noBukti, [lenPrefix]) = [prefix]
		$select = $this->select();
		$select->from("bukti_transaksi", array("noBukti" => new Zend_Db_Expr("MAX(noBukti)")));
		$select->where("LEFT(noBukti, $lenPrefix) = ?", $prefix);
		
		$db = $this->getAdapter();
		$noBukti = $db->fetchOne($select);
		
		// Nomor otomatis
		$no = 1;
		$noBukti && $no = intval(substr($noBukti, $lenPrefix + 1)) + 1;
		
		return $prefix . substr(str_repeat("0", $digitNum) . $no, -$digitNum);
	}
}