<?php

/**
 *  Journal penyesuaian ini sama dengan journal umum dengan perbedaan tipe journalnya
 */
include_once APPLICATION_PATH . "/controllers/JournalController.php";

class JournalPenyesuaianController extends JournalController
{
	protected $_tipeJournal = Model_Journal::TIPE_JOURNAL_PENYESUAIAN;
	protected $_prefixAutoNum = "BJP";
}

