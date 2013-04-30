<?php
/**
 * Class untuk manage laporan.
 * @author Adi Sayoga
 */
class Model_Laporan
{
	const ROOT_AKTIVA = "1";
	const ROOT_PASIVA = "2";
	const ROOT_MODAL = "3";
	
	const ROOT_PENDAPATAN = "4";
	const ROOT_BIAYA = "5";
	const ROOT_PENDAPATAN_NON_OPERASIONAL = "6.01.01";
	const ROOT_BIAYA_NON_OPRASIONAL = "6.02.01";

    const LABA_DITAHAN = "3.02";
    const LABARUGI_BERJALAN = "3.03";
    
    const DEFAULT_DISPLAY_LEVEL = 2;
    
    /**
     * Implode array
     * @param string $glue
     * @param array $pieces
     * @return string - data yang sudah di-escape
     */
    private function _implode($glue, $pieces) {
    	$db = Zend_Db_Table::getDefaultAdapter();
		
    	$result = "";
    	foreach ($pieces as $piece) {
    		if ($result) $result .= $glue;
    		$result .= $db->quote($piece);
    	}
    	return $result;
    }
    
	/**
	 * Mendapatkan data account (id, lft, rgt) berdasarkan kode account
	 * @param string $kodeAccount
	 * @return Zend_Db_Table_Row_Abstract
	 */
	private function _fetchRowAccount($kodeAccount)
	{
		$accountModel = new Model_Account();
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$result = $accountModel->fetchRow($db->quoteInto("kodeAccount = ?", $kodeAccount));
		if (!$result) {
			return $result;
		} else {
			throw new  Zend_Exception("Kode akun tidak ditemukan.");
		}
	}
	
	/**
	 * Mendapatkan nilai saldo journal (debit - kredit) berdasarkan account (beserta anak-anaknya).
	 * Jika tanggal awal = 0, maka akan dihitung dari paling awal sampai tanggal akhir.
	 * @param string|array $root - Kode root account
	 * @param int $tglAwal - Periode tanggal awal
	 * @param int $tglAkhir - Periode tanggal akhir
	 * @return money
	 */
	private function _getTotalTrans($root, $tglAwal, $tglAkhir)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		
		// TODO Kode tidak selevel apakah menghasilkan data yang benar?
		$rootList = (is_array($root)) ? $this->_implode(", ", $root): $root;
		
		$root = $db->quote($root);
		$tglAwal = $db->quote($tglAwal);
		$tglAkhir = $db->quote($tglAkhir);
		$periode = ($tglAwal == "0") ? "b.tanggal <= $tglAkhir": "b.tanggal between $tglAwal and $tglAkhir";
		
		$sql = "select ifnull(sum(j.saldo), 0) as jumlah "
			 . "from account as node "
			 .     "join ( "
			 .         "select id, lft, rgt from account where kodeAccount in($rootList) "
			 .     ") as parent "
			 .     "join ( "
			 .         "select j.idAccount, sum(j.debit - j.kredit) as saldo "
			 .         "from bukti_transaksi as b "
			 .             "inner join journal as j on b.id = j.idBukti "
			 .             "inner join account as a on a.id = j.idAccount "
			 .         "where $periode "
			 .         "group by j.idAccount "
			 .     ") as j "
			 . "where node.lft between parent.lft and parent.rgt and node.id = j.idAccount ";
		
		$result = $db->fetchOne($sql);
		if (!$result) $result = 0;
		return $result;
	}
	
	/**
	 * Mendapatkan nilai laba/rugi.
	 * Jika tanggal awal = 0, maka akan dihitung dari paling awal sampai tanggal akhir.
	 * @param int $tglAwal - Periode tanggal awal
	 * @param int $tglAkhir - Periode tanggal akhir
	 * @return money
	 */
	private function _getLabaRugi($tglAwal, $tglAkhir)
	{
		$result = $this->_getTotalTrans(array(self::ROOT_PENDAPATAN, self::ROOT_BIAYA,
			self::ROOT_PENDAPATAN_NON_OPERASIONAL, self::ROOT_BIAYA_NON_OPRASIONAL), $tglAwal, $tglAkhir);
		
		return $result;
	}
	
	/**
	 * Mendapatkan jumlah journal (debit, kredit) berdasarkan account per periode awal s/d akhir.
	 * Jika tanggal awal = 0, maka akan dihitung dari paling awal sampai tanggal akhir.
	 * @param int $root - Kode root account
	 * @param int $tglAwal - Periode tanggal awal
	 * @param int $tglAkhir - Periode tanggal akhir
	 * @param int $maxLevel - Maksimum level yang ditampilkan
	 * @return array - Berupa data account (tree) dan nilai debit/kreditnya
	 */
	private function _fetchTransPeriode($root, $tglAwal, $tglAkhir, $maxLevel)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$root = $db->quote($root);
		$tglAwal = $db->quote($tglAwal);
		$tglAkhir = $db->quote($tglAkhir);
		$maxLevel = $db->quote($maxLevel);
		$periode = ($tglAwal == "'0'") ? "b.tanggal <= $tglAkhir": "b.tanggal between $tglAwal and $tglAkhir";
		
		$sql = "select node.id, node.kodeAccount, node.account, count(parent.id) - (subTree.lvl + 1) "
			 . 	   "as lvl, ifnull(j.debit, 0) as debit, ifnull(j.kredit, 0) as kredit, ifnull(j.saldo, 0) "
			 .     "as saldo, case when (node.lft = node.rgt - 1) or (count(parent.id) - (subTree.lvl + 1) = "
			 .     "$maxLevel) then true else false end as isLeaf "
               // Node
             . "from account as node "
               // Parent
             . "join account as parent "
               // Sub Parent
             . "join account as subParent "
               // Sub Tree
             . "join ( "
             .     "select node.id, (count(parent.id) - 1) as lvl "
             .     "from account node join account parent "
             .     "where node.lft between parent.lft and parent.rgt and node.kodeAccount = $root "
             .     "group by node.id "
             .     "order by node.lft "
             . ") as subTree "
               // Total Transaksi
             . "left join ( "
             .     "select parent.id as idAccount, sum(j.debit) as debit, sum(j.kredit) as kredit, "
             .         "sum(j.debit - j.kredit) * parent.normalPos as saldo "
             .     "from account as node "
             .         "join account as parent "
             .         "join ( "
             .             "select j.idAccount, j.debit, j.kredit "
             .             "from bukti_transaksi b "
             .                 "inner join journal j on b.id = j.idBukti "
             .             "where $periode "
             .         ") as j "
             .     "where (node.lft between parent.lft and parent.rgt) and (node.id = j.idAccount) "
             .     "group by parent.id "
             .     "order by parent.lft "
             . ") as j on node.id = j.idAccount "
             . "where node.lft between parent.lft and parent.rgt "
             . "and node.lft between subParent.lft and subParent.rgt "
             . "and subParent.id = subTree.id "
             . "group by node.id "
             . "having lvl <= $maxLevel "
             . "order by node.lft";
             
		$result = $db->fetchAll($sql);
		return $this->_insertTotal($result);
	}
	
	/**
	 * Menyisipkan total pada baris data
	 * @param array $data
	 * @return array - $data yang sudah di-insertkan total
	 */
	private function _insertTotal($data)
	{
		$oldLevel = 0;
		$oldHasChild = false;
		
		$oldKode    = array("1" => "", "2" => "", "3" => "");
		$oldAccount = array("1" => "", "2" => "", "3" => "");
		$oldDebit   = array("1" => 0,  "2" => 0,  "3" => 0);
		$oldKredit  = array("1" => 0,  "2" => 0,  "3" => 0);
		$oldTotal   = array("1" => 0,  "2" => 0,  "3" => 0);
		
		$result = array();
		
		foreach ($data as $row) {
			// Mengecek apakah mempunyai anak/tidak. Jika ya, tandai induk has_child = 1
            // Level saat ini lebih besar dari level sebelumnya, ini artinya loop memasuki level
            // anaknya
			if ($row["lvl"] > $oldLevel) { $oldHasChild = true; }
			
			// Mengecek subtotal
			if ($row["lvl"] < $oldLevel) {
				// Akhir dari level anak. Dengan kata lain, loop kembali ke induknya
				
				// Level 2
				if ($row["lvl"] <= 2 && $oldLevel > 2) { 
					$result[] = array(
						"id" 		  => 0,
						"kodeAccount" => $oldKode["2"] . "-TOTAL",
						"account" 	  => "Total " . $oldAccount["2"],
						"lvl" 		  => 2,
						"debit" 	  => $oldDebit["2"],
						"kredit" 	  => $oldKredit["2"],
						"saldo" 	  => $oldTotal["2"],
						"isLeaf" 	  => 0,
					);
				}
				
				// Level 1
				if ($row["lvl"] <= 1 && $oldLevel > 1) {
					$result[] = array(
						"id" 		  => 0,
						"kodeAccount" => $oldKode["1"] . "-TOTAL",
						"account" 	  => "Total " . $oldAccount["1"],
						"lvl" 		  => 1,
						"debit" 	  => $oldDebit["1"],
						"kredit" 	  => $oldKredit["1"],
						"saldo" 	  => $oldTotal["1"],
						"isLeaf" 	  => 0,
					);
				}
				
				// Level 0
				if ($row["lvl"] <= 0 && $oldLevel > 0) {
					$result[] = array(
						"id" 		  => 0,
						"kodeAccount" => $oldKode["0"] . "-TOTAL",
						"account" 	  => "Total " . $oldAccount["0"],
						"lvl" 		  => 0,
						"debit" 	  => $oldDebit["0"],
						"kredit" 	  => $oldKredit["0"],
						"saldo" 	  => $oldTotal["0"],
						"isLeaf" 	  => 0,
					);
				}
			}
			
			if ($row["lvl"] == 2) {
				$oldKode["2"] = $row["kodeAccount"];
				$oldAccount["2"] = $row["account"];
				$oldDebit["2"] = $row["debit"];
				$oldKredit["2"] = $row["kredit"];
				$oldTotal["2"] = $row["saldo"];
			}
			
			if ($row["lvl"] == 1) {
				$oldKode["1"] = $row["kodeAccount"];
				$oldAccount["1"] = $row["account"];
				$oldDebit["1"] = $row["debit"];
				$oldKredit["1"] = $row["kredit"];
				$oldTotal["1"] = $row["saldo"];
			}
			
			if ($row["lvl"] == 0) {
				$oldKode["0"] = $row["kodeAccount"];
				$oldAccount["0"] = $row["account"];
				$oldDebit["0"] = $row["debit"];
				$oldKredit["0"] = $row["kredit"];
				$oldTotal["0"] = $row["saldo"];
			}
			
			$oldLevel = $row["lvl"];
			$oldHasChild = false; // reset hasChild
			
			$result[] = $row;
		}
		
		// Tambahkan total untuk diakhir
		// Level 2
		if ($oldLevel > 2) { 
			$result[] = array(
				"id" 		  => 0,
				"kodeAccount" => $oldKode["2"] . "-TOTAL",
				"account" 	  => "Total " . $oldAccount["2"],
				"lvl" 		  => 2,
				"debit" 	  => $oldDebit["2"],
				"kredit" 	  => $oldKredit["2"],
				"saldo" 	  => $oldTotal["2"],
				"isLeaf" 	  => 0,
			);
		}
		
		// Level 1
		if ($oldLevel > 1) {
			$result[] = array(
				"id" 		  => 0,
				"kodeAccount" => $oldKode["1"] . "-TOTAL",
				"account" 	  => "Total " . $oldAccount["1"],
				"lvl" 		  => 1,
				"debit" 	  => $oldDebit["1"],
				"kredit" 	  => $oldKredit["1"],
				"saldo" 	  => $oldTotal["1"],
				"isLeaf" 	  => 0,
			);
		}
		
		// Level 0
		if ($oldLevel > 0) {
			$result[] = array(
				"id" 		  => 0,
				"kodeAccount" => $oldKode["0"] . "-TOTAL",
				"account" 	  => "Total " . $oldAccount["0"],
				"lvl" 		  => 0,
				"debit" 	  => $oldDebit["0"],
				"kredit" 	  => $oldKredit["0"],
				"saldo" 	  => $oldTotal["0"],
				"isLeaf" 	  => 0,
			);
		}
		
		return $result;
	}
	
	/**
	 * Mendapatkan laporan journal umum
	 * @param int $tglAwal
	 * @param int $tglAkhir
	 * @return array
	 */
	public function fetchJournalUmum($tglAwal, $tglAkhir)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = new Zend_Db_Select($db);
		$select->from(array("b" => "bukti_transaksi"), 
				array("tipeJournal", "noBukti", "keterangan", "tanggal" => new Zend_Db_Expr(
					"date_format(from_unixtime(b.tanggal), '%d/%m/%Y')")))
			
			->join(array("j" => "journal"), "b.id = j.idBukti", 
				array("debit", "kredit", "idAccount", "pos" => new Zend_Db_Expr(
					"CASE j.debit WHEN 0 THEN -1 ELSE 1 END")))
			
			->join(array("a" => "account"), "j.idAccount = a.id", 
				array("kodeAccount", "account"))
		
			->where("b.tanggal >= ?", $tglAwal)
			->where("b.tanggal <= ?", $tglAkhir)
		
			->order(array("b.tanggal", "b.noBukti", "pos DESC"));
		
		return $db->fetchAll($select);
	}
	
	/**
	 * Mendapatkan laporan buku besar.
	 * @param int $tglAwal
	 * @param int $tglAkhir
	 * @param int $idAccount
	 * @return array
	 */
	public function fetchBukubesar($tglAwal, $tglAkhir, $idAccount)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		
		// Saldo awal
		$selectSaldo = new Zend_Db_Select($db);
		$selectSaldo->from(array("b" => "bukti_transaksi"), 
				new Zend_Db_Expr("SUM((j.debit - j.kredit) * a.normalPos)"))
			->join(array("j" => "journal"), "b.id = j.idBukti", null)
			->join(array("a" => "account"), "a.id = j.idAccount", null)
			->where("j.idAccount = ?", $idAccount)
			->where("b.tanggal < ?", $tglAwal);
		
		$saldoAwal = ($db->fetchOne($selectSaldo))? $db->fetchOne($selectSaldo): 0;
		
		// Data journal
		$selectJournal = new Zend_Db_Select($db);
		$selectJournal->from(array("b" => "bukti_transaksi"), 
				array("noBukti", "keterangan", "tanggal" => new Zend_Db_Expr(
					"date_format(from_unixtime(b.tanggal), '%d/%m/%Y')")))
			
			->join(array("j" => "journal"), "b.id = j.idBukti",
				array("idAccount", "debit", "kredit", 
				new Zend_Db_Expr("(j.debit - j.kredit) * a.normalPos AS saldo"),
				new Zend_Db_Expr("CASE j.debit WHEN 0 THEN -1 ELSE 1 END AS pos")))
			
			->join(array("a" => "account"), "j.idAccount = a.id", 
				array("kodeAccount", "account", "normalPos"))
			
			->where("j.idAccount = ?", $idAccount)
			->where("b.tanggal >= ?", $tglAwal)
			->where("b.tanggal <= ?", $tglAkhir)
			
			->order(array("b.tanggal", "b.noBukti"));
			
		$dataJournal = $db->fetchAll($selectJournal);
		
		// Generate Laporan
		$result = array();
		
		// Tambahkan saldo awal
		$tglSaldo = new Zend_Date($tglAwal);
		$tglSaldo->sub(1, Zend_Date::DAY);
		$result[] = array(
			"tanggal" 	 => $tglSaldo->toString("dd/MM/yyyy"), 
			"noBukti" 	 => "--", 
			"keterangan" => "Saldo Awal", 
			"debit" 	 => 0, 
			"kredit" 	 => 0, 
			"saldo" 	 => $saldoAwal, 
			"pos" 	  	 => "1"
		);
		
		// Tambahkan dari journal
		$saldo = $saldoAwal;
		foreach ($dataJournal as $row) {
			$saldo += $row["saldo"];
			$result[] = array(
				"tanggal" 	 => $row["tanggal"], 
				"noBukti" 	 => $row["noBukti"], 
				"keterangan" => $row["keterangan"], 
				"debit" 	 => $row["debit"], 
				"kredit" 	 => $row["kredit"], 
				"saldo" 	 => $saldo, 
				"pos" 	  	 => $row["pos"]
			);
		}
		
		return $result;
	}
	
	/**
	 * Mendapatkan laporan laba/rugi.
	 * @param int $tglAwal
	 * @param int $tglAkhir
	 * @return array
	 */
	public function fetchLabarugi($tglAwal, $tglAkhir)
	{
		$result = array();
		
		// Pendapatan
		$result[self::ROOT_PENDAPATAN] = $this->_fetchTransPeriode(
			self::ROOT_PENDAPATAN, $tglAwal, $tglAkhir, self::DEFAULT_DISPLAY_LEVEL);
		
		// Biaya
		$result[self::ROOT_BIAYA] = $this->_fetchTransPeriode(
			self::ROOT_BIAYA, $tglAwal, $tglAkhir, self::DEFAULT_DISPLAY_LEVEL);
		
		// Pendapatan Non Operasional
		$result[self::ROOT_PENDAPATAN_NON_OPERASIONAL] = $this->_fetchTransPeriode(
			self::ROOT_PENDAPATAN_NON_OPERASIONAL, $tglAwal, $tglAkhir, self::DEFAULT_DISPLAY_LEVEL);
		
		// Biaya Non Operasional
		$result[self::ROOT_BIAYA_NON_OPRASIONAL] = $this->_fetchTransPeriode(
			self::ROOT_BIAYA_NON_OPRASIONAL, $tglAwal, $tglAkhir, self::DEFAULT_DISPLAY_LEVEL);
		
		return $result;
	}
	
	/**
	 * Mendapatkan laporan neraca
	 * @param int $tglAwal
	 * @param int $tglAkhir
	 */
	public function fetchNeraca($tglAwal, $tglAkhir)
	{
		// Laba ditahan: total dari awal sampai tanggal awal - 1
		$tglAkhirDitahan = new Zend_Date($tglAwal, Zend_Date::TIMESTAMP);
		$tglAkhirDitahan->sub("1", Zend_Date::DAY); // Kurangi 1 hari
		$labaDitahan = $this->_getLabaRugi(0, $tglAkhirDitahan->get(Zend_Date::TIMESTAMP));
		
		// Laba/rugi periode berjalan: total dari tanggal awal sampai tanggal akhir
		$labarugiBerjalan = $this->_getLabaRugi($tglAwal, $tglAkhir);
		
		// Fetch data
		$result = array();
		
		// Aktiva
		$result[self::ROOT_AKTIVA] = $this->_fetchTransPeriode(self::ROOT_AKTIVA, 0, $tglAkhir, 
			self::DEFAULT_DISPLAY_LEVEL);
		
		// Pasiva
		$result[self::ROOT_PASIVA] = $this->_fetchTransPeriode(self::ROOT_PASIVA, 0, $tglAkhir, 
			self::DEFAULT_DISPLAY_LEVEL);
		
		// Modal
		$modal = $this->_fetchTransPeriode(self::ROOT_MODAL, 0, $tglAkhir, self::DEFAULT_DISPLAY_LEVEL);

		// Update laba ditahan dan laba/rugi periode berjalan
		// Laba ditahan dan laba/rugi periode berjalan berada pada akun modal (tepat anaknya modal)
		// jadi cuma update laba ditahan dan laba/rugi periode berjalan saja + update modal. 
		// TODO Ada yang lebih baik?
		foreach ($modal as $key => $rowModal) {
			switch ($rowModal["kodeAccount"]) {
				case self::ROOT_MODAL:
				case self::ROOT_MODAL . "-TOTAL":
					$labarugi = $labaDitahan + $labarugiBerjalan;
					if ($labarugi >= 0) {
						$modal[$key]["debit"] += $labarugi;
					} else {
						$modal[$key]["kredit"] -= $labarugi;
					}
					$modal[$key]["saldo"] -= $labarugi;
					break;
				
				case self::ROOT_MODAL . ".02": // laba ditahan
					if ($labaDitahan >= 0) {
						$modal[$key]["debit"] += $labaDitahan;
					} else {
						$modal[$key]["kredit"] -= $labaDitahan;
					}
					$modal[$key]["saldo"] -= $labaDitahan;
					break;
					
				case self::ROOT_MODAL . ".03": // laba ditahan
					if ($labarugiBerjalan >= 0) {
						$modal[$key]["debit"] += $labarugiBerjalan;
					} else {
						$modal[$key]["kredit"] -= $labarugiBerjalan;
					}
					$modal[$key]["saldo"] -= $labarugiBerjalan;
					break;
			}
		}
		$result[self::ROOT_MODAL] = $modal;
		
		return $result;
	}
}