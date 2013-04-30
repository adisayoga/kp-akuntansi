/**
 * Copyright 2011, Adi Sayoga
 */
var api, listAccount = "";
// No bukti ini akan di-set melalui kode php (melalui controller), disini tidak bisa disisipkan 
// melalui kode php. 
// TODO Ada cara lebih baik?
var lastNoBukti;

$(document).ready(function() {
	updateUI();
	
	api = jQuery.Zend.jsonrpc({url: '/api/v1.0/jsonrpc.php', async: true });

	// Validasi form
	$(".form").validate({
		smd: api,
		functionName: "validateJournalForm",
		toValidate: ".form #noBukti, .form #tanggal, .form #keterangan, .form #total",
		exclude: lastNoBukti,
		onSubmit: function() { 
			// Jika form di-submit dan data valid, maka update data
			validateTotal() && updateDataJournal($(".form #id").val(), generateData());
		}
	});
	
	// Update no bukti otomatis jika tanggal transaksi diubah
	$("#tanggal").change(function() { autoNum(); });
});

/**
 * No bukti otomatis
 */
function autoNum() {
	var prefix = ($(".form #tipeJournal").val() == "1")? "BJU": "BJP";
	api.journalAutoNum($("#tanggal").val(), { prefix: prefix }, {
		success : function(result, id, method) {
			$("#noBukti").val(result);
		}
	});
}

/**
 * Update UI saat javasipt enable 
 */
function updateUI() {
	// Tombol tambah baru hanya diperlukan jika javascript di-disable
	$(".noJavascript").remove();
	// Tambahkan tombol untuk menambah baris detail
	$(".addRow").html('<input type="button" onClick="addRow()" value="Tambah Baris Baru" />');
	
	// detail
	$(".form table tbody tr").each(function() {
		// Action - Hapus checkbox dan tambahkan tombol delete
		var $deleteButton = $('<a href="#" class="delete"></a>');
		$(this).find(".include").remove();
		$(this).find(".action").append($deleteButton);
		$deleteButton.click(function() { deleteRow(this); });
		
		// Account
		var $kodeAccount = $(this).find(".kodeAccount");
		$kodeAccount.attr("disabled", false);
		var $account = $(this).find(".account");
		if (!$kodeAccount.val()) $account.val(null);
		
		$kodeAccount.blur(function() { displayAccount($(this), $account); });
		$account.change(function() { displayKodeAccount($(this), $kodeAccount); });

		// List account hanya diisi sekali
		if (!listAccount) listAccount = $account.html();

		// Debit/kredit
		$(this).find(".debit").blur(function() { updateTotal(); });
		$(this).find(".kredit").blur(function() { updateTotal(); });
	});
}

/**
 * Mengosongkan UI untuk menambah data baru
 */
function clearUI() {
	autoNum();
	$(".form #id").val("");
	$(".form #keterangan").val("");
	$(".form #total").val("0");
	
	$(".form table tbody tr").empty();
	addRow();
	updateTotal();
}

/**
 * Menampilkan data account pada comboBox account
 * @param object $_self
 * @param object $_toDisplay
 */
function displayAccount($_self, $toDisplay) {
	if (!$_self.val()) { // Jika kode account kosong, maka kosongkan juga comboBox account
		$toDisplay.val(null);
		return;
	}
	
	api.getAccountByKode($_self.val(), { success: function(data, id, method) {
		if (data[0]) {
			$toDisplay.val(data[0].id);
		} else {
			alert("Kode Akun tidak ditemukan.");
			$_self.val("");
			$toDisplay.val(null);
		}
	}});
}

/** 
 * Menampilkan kode account berdasarkan id account
 * @param object $_self
 * @param object $_toDisplay
 */
function displayKodeAccount($_self, $toDisplay) {
	api.getAccountById($_self.val(), { success: function(data, id, method) {
		if (data[0]) {
			$toDisplay.val(data[0].kodeAccount);
		} else {
			$toDisplay.val("");
		}
	}});
}

/** 
 * Menghapus baris detail
 * @param object _self
 */
function deleteRow(_self) {
	if (!$(_self).parent().next().find(".kodeAccount").val() || confirm("Yakin menghapus data ini?")) {
		
		$(_self).parent().parent().remove();
		updateTotal();
	}
}

/** 
 * Menambah baris baru 
 */
function addRow() {
	// Tombol delete
	var $deleteButton = $('<a href="#" class="delete"></a>');
	$deleteButton.click(function() { deleteRow(this); });

	// Account
	var $kodeAccount = $('<input type="text" name="kodeAccount[]" class="kodeAccount" style="width: 100px;" />');
	var $account = $('<select name="account[]" class="account" style="width: 99%; "></select>');

	$kodeAccount.blur(function() { displayAccount($(this), $account); });
	$account.change(function() { displayKodeAccount($(this), $kodeAccount); });
	$account.append(listAccount);

	// Debit/kredit
	var $debit = $('<input type="text" name="debit[]" class="debit" value="0" style="width: 100px;" />');
	var $kredit = $('<input type="text" name="kredit[]" class="kredit" value="0" style="width: 100px;" />');
	$debit.blur(function() { updateTotal(); });
	$kredit.blur(function() { updateTotal(); });
	
	var $tdAction = $('<td class="action"></td>'); $tdAction.append($deleteButton);
	var $tdKodeAccount = $("<td>"); $tdKodeAccount.append($kodeAccount);
	var $tdAccount = $("<td>"); $tdAccount.append($account);
	var $tdDebit = $("<td>"); $tdDebit.append($debit);
	var $tdKredit = $("<td>"); $tdKredit.append($kredit);

	var $row = $("<tr>");
	$row.append($tdAction).append($tdKodeAccount).append($tdAccount).append($tdDebit).append($tdKredit);
	$(".form table tbody").append($row);
	
	$account.val(null); // Kosongkan comboBox account
}

/** 
 * Validasi total transaksi
 */
function validateTotal() {
	var total = parseFloat($("#total").val());
	var totalDetails = updateTotal();

	$("#total").removeClass("errors-element");
	
	// Total debit dan kredit harus sama (balance)
	if (totalDetails.debit != totalDetails.kredit) {
		displayMessage("Total debit/kredit tidak balance!");
		$("#total").addClass("errors-element");
		return false;
	}
	
	// Total transaksi harus sama dengan total debit/kredit
	if (total != totalDetails.debit) {
		displayMessage("Total tidak sama dengan total debit/kredit");
		$("#total").addClass("errors-element");
		return false;
	}

	return true;
}

/** 
 * Update total debit/kredit 
 */
function updateTotal() {
	var totalDebit = 0, totalKredit = 0;

	$(".form table tbody tr").each(function() {
		// Total hanya dihitung jika kode account tidak kosong
		if ($(this).find(".kodeAccount").val()) {
			totalDebit += parseFloat($(this).find(".debit").val());
			totalKredit += parseFloat($(this).find(".kredit").val()); 
		}
	});
	
	// Update total
	$(".form table tfoot .totalDebit").text(totalDebit);
	$(".form table tfoot .totalKredit").text(totalKredit);

	return { debit: totalDebit, kredit: totalKredit };
}

/** 
 * Mendapatkan data 
 */
function generateData() {
	var details = new Array();
	$(".form table tbody tr").each(function() {
		if (!$(this).find(".kodeAccount").val()) return; // Account yang kosong dilewati
		
		details.push({
			"kodeAccount": $(this).find(".kodeAccount").val(),
			"idAccount": $(this).find(".account").val(),
			"debit": $(this).find(".debit").val(),
			"kredit": $(this).find(".kredit").val()
		});
	});
	
	var data = {
		"id": $("#id").val(),
		"tipeJournal": $("#tipeJournal").val(),
		"noBukti": $("#noBukti").val(),
		"tanggal": $("#tanggal").val(),
		"keterangan": $("#keterangan").val(),
		"total": $("#total").val(),
		"details": details
	};

	console.log(data);
	return data;
}

/** 
 * Menyimpan data ke database
 * @param json data
 */
function updateDataJournal(id, data) {
	api.updateJournal(id, data, { success: function(result) {
		if (result) {
			displayMessage("Data berhasil di-update.", "info");
			
			if (!id) { // Id tidak ditentukan, diasumsikan menambah data baru
				clearUI(); // Kosongkan untuk menambah data lainnya
			} else {
				// redirect ke daftar journal
				// TODO Hard code, seharusnya ada cara yang lebih balik
				var url = "";
				switch ($("#tipeJournal").val()) {
					case "1": url = "/journal"; break;
					case "2": url = "/journal-penyesuaian"; break;
				}
				window.location = url;
			}
		} else {
			displayMessage("Data gagal di-update", "error");
		}
	}});
}