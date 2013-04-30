/*
SQLyog Community v10.2 
MySQL - 5.1.50-community : Database - accounting
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`accounting` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `accounting`;

/*Table structure for table `account` */

DROP TABLE IF EXISTS `account`;

CREATE TABLE `account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `kodeAccount` varchar(20) NOT NULL,
  `account` varchar(100) NOT NULL,
  `normalPos` tinyint(4) NOT NULL,
  `kelompok` varchar(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IX_ACCOUNT_KODE` (`kodeAccount`),
  KEY `IX_ACCOUNT_TREE` (`lft`,`rgt`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=207 DEFAULT CHARSET=latin1;

/*Data for the table `account` */

insert  into `account`(`id`,`lft`,`rgt`,`parent`,`kodeAccount`,`account`,`normalPos`,`kelompok`) values (1,1,86,NULL,'1','Aktiva',1,'N'),(2,2,71,1,'1.01','Aktiva Lancar',1,'N'),(3,3,8,2,'1.01.11','Kas',1,'N'),(4,4,5,3,'1.01.11.01','Kas Kasir',1,'N'),(5,6,7,3,'1.01.11.02','Kas Outstanding',1,'N'),(6,9,18,2,'1.01.21','Bank',1,'N'),(7,10,11,6,'1.01.21.01','BNI',1,'N'),(8,12,13,6,'1.01.21.02','Bank Danamon',1,'N'),(9,14,15,6,'1.01.21.03','BRI',1,'N'),(10,16,17,6,'1.01.21.04','BPD',1,'N'),(11,19,26,2,'1.01.03','Piutang Usaha',1,'N'),(12,20,21,11,'1.01.03.01','Piutang Usaha',1,'N'),(13,22,23,11,'1.01.03.02','Piutang Guna Sejahtera',1,'N'),(14,24,25,11,'1.01.03.03','Piutang Pembuatan Rek Kary.Yantek Mataram',1,'N'),(15,27,66,2,'1.01.04','Piutang Karyawan',1,'N'),(16,28,29,15,'1.01.04.01','Piutang Adi Putra',1,'N'),(18,30,31,15,'1.01.04.03','Piutang Ayu Ratna',1,'N'),(19,32,33,15,'1.01.04.04','Piutang Alit Sumastha',1,'N'),(20,34,35,15,'1.01.04.05','Piutang Sambegana',1,'N'),(21,36,37,15,'1.01.04.06','Piutang IB',1,'N'),(22,38,39,15,'1.01.04.07','Piutang Novan sastrawan',1,'N'),(23,40,41,15,'1.01.04.08','Piutang Gung Adi Krisna',1,'N'),(24,42,43,15,'1.01.04.09','Piutang Alit Sastrawan',1,'N'),(25,44,45,15,'1.01.04.10','Piutang Sang Nyoman',1,'N'),(26,46,47,15,'1.01.04.11','Piutang Arif Haryanto Edon',1,'N'),(27,48,49,15,'1.01.04.12','Piutang Darmika',1,'N'),(28,50,51,15,'1.01.04.13','Piutang SIM',1,'N'),(29,52,53,15,'1.01.04.14','Pak ketut suardana',1,'N'),(30,54,55,15,'1.01.04.15','Piutang pak gung raka',1,'N'),(31,56,57,15,'1.01.04.16','Piutang A.A Wismaniti dewi',1,'N'),(32,58,59,15,'1.01.04.17','Piutang ibu YULI',1,'N'),(33,60,61,15,'1.01.04.18','Piutang diah galuh',1,'N'),(34,62,63,15,'1.01.04.19','Pinjaman I Wayan Arsana',1,'N'),(35,64,65,15,'1.01.04.20','Piutang pak Suada',1,'N'),(36,67,70,2,'1.01.05','Sewa Dibayar Dimuka',1,'N'),(37,68,69,36,'1.01.05.01','Sewa Gedung',1,'N'),(38,72,85,1,'1.02','Aktiva Tetap',1,'N'),(39,73,74,38,'1.02.01','Mesin dan Peralatan',1,'N'),(40,75,76,38,'1.02.02','Ak. Peny. Mesin dan Peralatan',1,'N'),(41,77,78,38,'1.02.03','Kendaraan',1,'N'),(42,79,80,38,'1.02.04','Ak. Peny.Kendaraan',1,'N'),(43,81,82,38,'1.02.05','Inventaris Kantor',1,'N'),(44,83,84,38,'1.02.06','Ak. Peny. Inventaris Kantor',1,'N'),(45,87,164,NULL,'2','Pasiva',-1,'N'),(46,88,163,45,'2.01','Hutang Lancar',-1,'N'),(47,89,124,46,'2.01.01','Hutang Usaha',-1,'N'),(48,90,91,47,'2.01.01.01','Hutang Gaji',-1,'N'),(49,92,93,47,'2.01.01.02','Hutang Koperasi Purnalis',-1,'N'),(50,94,95,47,'2.01.01.03','Hutang BRI',-1,'N'),(51,96,97,47,'2.01.01.04','Hutang Elba',-1,'N'),(52,98,99,47,'2.01.01.05','Hutang Biaya',-1,'N'),(53,100,101,47,'2.01.01.06','Hutang KDA Proyek Yantek Tabanan',-1,'N'),(54,102,103,47,'2.01.01.07','Hutang Proyek Sewa Komputer',-1,'N'),(55,104,105,47,'2.01.01.08','Hutang CV. Bali Tulen',-1,'N'),(56,106,107,47,'2.01.01.09','Hutang Bp.Suroto',-1,'N'),(57,108,109,47,'2.01.01.10','Hutang Jamsostek',-1,'N'),(58,110,111,47,'2.01.01.11','Hutang PT.Loka',-1,'N'),(59,112,113,47,'2.01.01.12','Hutang KDA Proyek AP klungkung',-1,'N'),(60,114,115,47,'2.01.01.13','Hutang Sewa Kendaraan',-1,'N'),(61,116,117,47,'2.01.01.14','Hutang Pendapatan',-1,'N'),(62,118,119,47,'2.01.01.15','Hutang Wahana Wirawan',-1,'N'),(63,120,121,47,'2.01.01.16','Hutang BPR. Adiartha Udiana',-1,'N'),(64,122,123,47,'2.01.01.17','Hutang Adira Finance',-1,'N'),(65,125,134,46,'2.01.02','Hutang Pajak',-1,'N'),(66,126,127,65,'2.01.02.01','PPN',-1,'N'),(67,128,129,65,'2.01.02.02','PPh 21',-1,'N'),(68,130,131,65,'2.01.02.03','PPh 23',-1,'N'),(69,132,133,65,'2.01.02.04','PPh 25',-1,'N'),(70,135,162,46,'2.01.03','Hutang Pinjaman',-1,'N'),(71,136,137,70,'2.01.03.01','Pinjaman Pk',-1,'N'),(72,138,139,70,'2.01.03.02','Pinjaman Dody Arya',-1,'N'),(73,140,141,70,'2.01.03.03','Pinjaman IB',-1,'N'),(74,142,143,70,'2.01.03.04','Pinjaman Adi Krisna',-1,'N'),(75,144,145,70,'2.01.03.05','Pinjaman S. Nym',-1,'N'),(76,146,147,70,'2.01.03.06','Pinjaman Alit',-1,'N'),(77,148,149,70,'2.01.03.07','Pinjaman Bayu',-1,'N'),(78,150,151,70,'2.01.03.08','Pinjaman KSS',-1,'N'),(79,152,153,70,'2.01.03.09','Pinjaman Ibu Dyah',-1,'N'),(80,154,155,70,'2.01.03.10','Pinjaman Megaputra',-1,'N'),(81,156,157,70,'2.01.03.11','Pinjaman Arjana',-1,'N'),(82,158,159,70,'2.01.03.12','Pinjaman Yuli',-1,'N'),(83,160,161,70,'2.01.03.13','Pinjaman diah',-1,'N'),(84,165,182,NULL,'3','Modal',-1,'N'),(85,166,171,84,'3.01','Modal Disetor',-1,'N'),(86,167,168,85,'3.01.01','Modal Awal',-1,'N'),(87,169,170,85,'3.01.02','Agio Saham',-1,'N'),(88,172,173,84,'3.02','Laba Ditahan',-1,'N'),(89,174,175,84,'3.03','Laba/Rugi Periode Berjalan',-1,'N'),(90,176,181,84,'3.04','Koreksi Laba',-1,'N'),(91,177,178,90,'3.04.01','Koreksi Laba Ditahan',-1,'N'),(92,179,180,90,'3.04.02','Koreksi Laba/Rugi Periode Berjalan',-1,'N'),(93,183,226,NULL,'4','Pendapatan',-1,'L'),(94,184,225,93,'4.01','Pendapatan Proyek',-1,'L'),(95,185,186,94,'4.01.01','Pendapatan TUL',-1,'L'),(96,187,188,94,'4.01.02','Pendapatan Pengembangan Data',-1,'L'),(97,189,190,94,'4.01.03','Pendapatan Dinas Gangguan',-1,'L'),(98,191,192,94,'4.01.04','Pendapatan AMR',-1,'L'),(99,193,194,94,'4.01.05','Pendapatan Sewa Komputer',-1,'L'),(100,195,196,94,'4.01.06','Pendapatan Yantek Tabanan',-1,'L'),(101,197,198,94,'4.01.07','Pendapatan ULC',-1,'L'),(102,199,200,94,'4.01.08','Pendapatan P2TL',-1,'L'),(103,201,202,94,'4.01.09','Pendapatan TUL Denpasar, Mengwi',-1,'L'),(104,203,204,94,'4.01.10','Pendapatan Yantek Denpasar, Mengwi',-1,'L'),(105,205,206,94,'4.01.11','Pendapatan TUL Kuta Tabanan',-1,'L'),(106,207,208,94,'4.01.12','Pendapatan AP Klungkung',-1,'L'),(107,209,210,94,'4.01.13','Pendapatan Dinas Kelautan dan Perikanan',-1,'L'),(108,211,212,94,'4.01.14','Pendapatan Dinas Kebersihan dan Pertamanan',-1,'L'),(109,213,214,94,'4.01.15','Pendapatan Proyek PDAM',-1,'L'),(110,215,216,94,'4.01.16','Pendapatan Yantek Klungkung',-1,'L'),(111,217,218,94,'4.01.17','Pendapatan Yantek Karangasem',-1,'L'),(112,219,220,94,'4.01.18','Pendapatan Yantek Negara',-1,'L'),(113,221,222,94,'4.01.19','Pendapatan Yantek Negara',-1,'L'),(114,223,224,94,'4.01.20','Pendapatan Yantek Mataram',-1,'L'),(115,227,382,NULL,'5','Biaya Operasional',1,'L'),(116,228,293,115,'5.01','Biaya Operasi',1,'L'),(117,229,272,116,'5.01.01','Biaya Proyek',1,'L'),(118,230,231,117,'5.01.01.01','Biaya TUL',1,'L'),(119,232,233,117,'5.01.01.02','Biaya PJTK Pengembangan Data',1,'L'),(120,234,235,117,'5.01.01.03','Biaya Dinas Gangguan',1,'L'),(121,236,237,117,'5.01.01.04','Biaya AMR',1,'L'),(122,238,239,117,'5.01.01.05','Biaya Sewa Komputer',1,'L'),(123,240,241,117,'5.01.01.06','Biaya P2TL',1,'L'),(124,242,243,117,'5.01.01.07','Biaya Yantek Tabanan',1,'L'),(125,244,245,117,'5.01.01.08','Biaya ULC',1,'L'),(126,246,247,117,'5.01.01.09','Biaya Yantek Denpasar Mengwi',1,'L'),(127,248,249,117,'5.01.01.10','Proyek Kebersihan dan Pertamanan',1,'L'),(128,250,251,117,'5.01.01.11','Biaya AP Klungkung',1,'L'),(129,252,253,117,'5.01.01.12','Biaya Dinas kelautan dan Perikanan',1,'L'),(130,254,255,117,'5.01.01.13','Biaya TUL Denpasar, Mengwi',1,'L'),(131,256,257,117,'5.01.01.14','Biaya TUL Kuta, Tabanan',1,'L'),(132,258,259,117,'5.01.01.15','Biaya Proyek PDAM',1,'L'),(133,260,261,117,'5.01.01.16','Biaya Yantek Mojokerto',1,'L'),(134,262,263,117,'5.01.01.20','Biaya YAntek AJ Klungkung',1,'L'),(135,264,265,117,'5.01.01.21','Biaya Yantek UJ Karangasem',1,'L'),(136,266,267,117,'5.01.01.23','Biaya proyek Mataram',1,'L'),(137,268,269,117,'5.01.01.24','Biaya Yantek Negara',1,'L'),(138,270,271,117,'5.01.01.25','Biaya Bapeda Gianyar',1,'L'),(139,273,282,116,'5.01.02','Biaya Overhead Proyek',1,'L'),(140,274,275,139,'5.01.02.01','Biaya Tinta',1,'L'),(141,276,277,139,'5.01.02.02','Biaya Kertas',1,'L'),(142,278,279,139,'5.01.02.03','Biaya Cetak/Jilid',1,'L'),(143,280,281,139,'5.01.02.04','Biaya Overhead Lainnya',1,'L'),(144,283,292,116,'5.01.03','Biaya Marketing',1,'L'),(145,284,285,144,'5.01.03.01','Biaya Entertaiment',1,'L'),(146,286,287,144,'5.01.03.02','Biaya Kit Marketing',1,'L'),(147,288,289,144,'5.01.03.03','Biaya Perjalanan Dinas',1,'L'),(148,290,291,144,'5.01.03.04','Biaya Proposal',1,'L'),(149,294,381,115,'5.02','Biaya Administrasi',1,'L'),(150,295,304,149,'5.02.01','Biaya Pegawai',1,'L'),(151,296,297,150,'5.02.01.01','Biaya Gaji',1,'L'),(152,298,299,150,'5.02.01.02','Biaya THR & Bonus',1,'L'),(153,300,301,150,'5.02.01.03','Biaya Pakaian Seragam',1,'L'),(154,302,303,150,'5.02.01.04','Biaya Tunjangan Asuransi',1,'L'),(155,305,310,149,'5.02.02','Biaya Transportasi',1,'L'),(156,306,307,155,'5.02.02.01','Biaya Bensin',1,'L'),(157,308,309,155,'5.02.02.02','Biaya Sewa Kendaraan',1,'L'),(158,311,324,149,'5.02.03','Biaya Komunikasi dan Listrik',1,'L'),(159,312,313,158,'5.02.03.01','Biaya Telpon',1,'L'),(160,314,315,158,'5.02.03.02','Biaya Listrik',1,'L'),(161,316,317,158,'5.02.03.03','Biaya web',1,'L'),(162,318,319,158,'5.02.03.04','Biaya Yellow Pages',1,'L'),(163,320,321,158,'5.02.03.05','Biaya Koran',1,'L'),(164,322,323,158,'5.02.03.06','Biaya PDAM',1,'L'),(165,325,334,149,'5.02.04','Biaya Pemeliharaan Aset',1,'L'),(166,326,327,165,'5.02.04.01','Biaya Pemeliharaan Gedung',1,'L'),(167,328,329,165,'5.02.04.02','Biaya Pemeliharaan Kendaraan',1,'L'),(168,330,331,165,'5.02.04.03','Biaya Pemeliharaan Mesin dan Peralatan',1,'L'),(169,332,333,165,'5.02.04.04','Biaya Pemeliharaan Inventaris',1,'L'),(170,335,340,149,'5.02.05','Biaya Peralatan Administrasi',1,'L'),(171,336,337,170,'5.02.05.01','Biaya Alat Tulis',1,'L'),(172,338,339,170,'5.02.05.02','Biaya Materai',1,'L'),(173,341,346,149,'5.02.06','Biaya Perijinan',1,'L'),(174,342,343,173,'5.02.06.01','Biaya Perijinan Perusahaan',1,'L'),(175,344,345,173,'5.02.06.02','Biaya Perijinan Aset',1,'L'),(176,347,362,149,'5.02.07','Biaya Perpajakan',1,'L'),(177,348,349,176,'5.02.07.01','Biaya PPh 21',1,'L'),(178,350,351,176,'5.02.07.02','Biaya PPh 25',1,'L'),(179,352,353,176,'5.02.07.03','Biaya Konsultan Pajak',1,'L'),(180,354,355,176,'5.02.07.04','Denda pajak',1,'L'),(181,356,357,176,'5.02.07.05','Biaya PPh Vendor',1,'L'),(182,358,359,176,'5.02.07.06','Biaya Pph Psl 23',1,'L'),(183,360,361,176,'5.02.07.07','Biaya SPT Tahunan',1,'L'),(184,363,366,149,'5.02.08','Biaya Konsumsi',1,'L'),(185,364,365,184,'5.02.08.01','Biaya Konsumsi Karyawan',1,'L'),(186,367,372,149,'5.02.09','Biaya Pendidikan dan Pelatihan',1,'L'),(187,368,369,186,'5.02.09.01','Biaya Seminar',1,'L'),(188,370,371,186,'5.02.09.02','Biaya Training',1,'L'),(189,373,380,149,'5.02.99','Biaya Administrasi Lainnya',1,'L'),(190,374,375,189,'5.02.99.01','Biaya Alat Sembahyang',1,'L'),(191,376,377,189,'5.02.99.02','Biaya Administrasi Lainnya',1,'L'),(192,378,379,189,'5.02.99.03','Biaya Perabasan Pohon',1,'L'),(193,383,410,NULL,'6','Pendapatan dan Biaya Non Operasional',-1,'L'),(194,384,393,193,'6.01','Pendapatan Non Operasional',-1,'L'),(195,385,392,194,'6.01.01','Pendapatan Non Operasional',-1,'L'),(196,386,387,195,'6.01.01.01','Pendapatan Jasa Giro',-1,'L'),(197,388,389,195,'6.01.01.02','Pendapatan Bunga Pinjaman kredit',-1,'L'),(198,390,391,195,'6.01.01.03','Pendapatan Jasa Bendera',-1,'L'),(199,394,409,193,'6.02','Biaya Non Operasional',1,'L'),(200,395,408,199,'6.02.01','Biaya Non Operasional',1,'L'),(201,396,397,200,'6.02.01.01','Biaya Penyusutan Aset',1,'L'),(202,398,399,200,'6.02.01.02','Biaya Sewa Gedung',1,'L'),(203,400,401,200,'6.02.01.03','Biaya Bunga Pinjaman',1,'L'),(204,402,403,200,'6.02.01.04','Biaya Administrasi bank',1,'L'),(205,404,405,200,'6.02.01.05','Biaya Deviden',1,'L'),(206,406,407,200,'6.02.01.06','Biaya Administrasi pinjaman',1,'L');

/*Table structure for table `bukti_transaksi` */

DROP TABLE IF EXISTS `bukti_transaksi`;

CREATE TABLE `bukti_transaksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipeJournal` tinyint(4) NOT NULL,
  `noBukti` varchar(30) NOT NULL,
  `tanggal` int(11) NOT NULL,
  `keterangan` varchar(125) NOT NULL,
  `validatedBy` varchar(25) NOT NULL,
  `validatedDate` int(11) NOT NULL,
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `IX_BUKTI_NO` (`noBukti`)
) ENGINE=InnoDB AUTO_INCREMENT=444 DEFAULT CHARSET=latin1;

/*Data for the table `bukti_transaksi` */

insert  into `bukti_transaksi`(`id`,`tipeJournal`,`noBukti`,`tanggal`,`keterangan`,`validatedBy`,`validatedDate`,`total`) values (414,1,'1101/BJU/0001',1293984000,'Tarik cek danamon','admin',1303909573,'120000.00'),(415,1,'1101/BJU/0002',1294070400,'Pelunasan hutang gaji','admin',1303914286,'20000000.00'),(416,1,'1101/BJU/0003',1294070400,'Sewa kendaraan AMR','admin',1303909891,'3000000.00'),(417,1,'1101/BJU/0004',1294156800,'Pendapatan bank','admin',1303912275,'170000.00'),(418,1,'1101/BJU/0005',1294243200,'Bunga pinjaman KSS','admin',1303912514,'15000000.00'),(419,1,'1101/BJU/0006',1294588800,'Pendapatan bunga pinjaman','admin',1303912615,'150000.00'),(420,1,'1101/BJU/0007',1294588800,'Pendapatan TUL','admin',1303912675,'54000000.00'),(421,1,'1101/BJU/0008',1294675200,'Pendapatan yantek tabanan','admin',1303912884,'200000000.00'),(422,1,'1101/BJU/0009',1294675200,'Tarik cek danamon','admin',1303913043,'100000000.00'),(423,1,'1101/BJU/0010',1294675200,'Air accu','admin',1303913215,'18000.00'),(424,1,'1101/BJU/0011',1294848000,'Terima pinjaman','admin',1303913436,'10000000.00'),(425,1,'1101/BJU/0012',1294934400,'Pelunasan pot. pinjaman karyawan','admin',1303914207,'7000000.00'),(426,1,'1101/BJU/0013',1295193600,'Hutang gaji','admin',1303914792,'2000000.00'),(427,1,'1101/BJU/0014',1295193600,'Potongan pinjaman purnalis','admin',1303915034,'30000000.00'),(428,1,'1102/BJU/0001',1296489600,'Pendapatan pengembangan data','admin',1303916179,'100000000.00'),(429,1,'1102/BJU/0002',1296576000,'Pendapatan TUL','admin',1306652067,'80000000.00'),(430,1,'1102/BJU/0003',1296576000,'Pendapatan Sewa Komputer','admin',1303916326,'20000000.00'),(431,1,'1102/BJU/0004',1296748800,'Angsuran piutang karyawan','admin',1303916603,'200000.00'),(432,1,'1102/BJU/0005',1297008000,'Piutang karyawan','admin',1303916715,'500000.00'),(433,1,'1102/BJU/0006',1297094400,'Ak. Peny. Mesin & Peralatan','admin',1303916877,'1200000.00'),(434,1,'1102/BJU/0007',1297094400,'Biaya bensin','admin',1303917188,'20000.00'),(435,1,'1102/BJU/0008',1297094400,'Pembayaran DP pemb. mobil','admin',1303917359,'25000000.00'),(436,1,'1102/BJU/0009',1297267200,'Gaji','admin',1303917531,'10000000.00'),(437,1,'1102/BJU/0010',1297267200,'Kertas utk di suli','admin',1303917685,'100000.00'),(438,1,'1103/BJU/0001',1298908800,'Amortisasi sewa kantor','admin',1304337336,'1500000.00'),(439,1,'1103/BJU/0002',1299081600,'Angsuran bunga KMK','admin',1304337555,'8000000.00'),(440,1,'1103/BJU/0003',1299427200,'Pendapatan yantek tabanan','admin',1304339371,'200000000.00'),(441,1,'1103/BJU/0004',1299427200,'PPh 21','admin',1304339669,'2000000.00'),(442,1,'1103/BJU/0005',1300032000,'Biaya pinjaman sertifikasi BP IB','admin',1304340155,'1000000.00'),(443,1,'1105/BJU/0001',1305302400,'tes','admin',1305341473,'1000.00');

/*Table structure for table `journal` */

DROP TABLE IF EXISTS `journal`;

CREATE TABLE `journal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idBukti` int(11) NOT NULL DEFAULT '0',
  `idAccount` int(11) NOT NULL DEFAULT '0',
  `debit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `kredit` decimal(12,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `FK_JOURNAL_BUKTI` (`idBukti`),
  KEY `FK_JOURNAL_ACCOUNT` (`idAccount`),
  CONSTRAINT `FK_JOURNAL_ACCOUNT` FOREIGN KEY (`idAccount`) REFERENCES `account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_JOURNAL_BUKTI` FOREIGN KEY (`idBukti`) REFERENCES `bukti_transaksi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2585 DEFAULT CHARSET=latin1;

/*Data for the table `journal` */

insert  into `journal`(`id`,`idBukti`,`idAccount`,`debit`,`kredit`) values (2509,414,4,'120000.00','0.00'),(2510,414,8,'0.00','120000.00'),(2513,416,121,'3000000.00','0.00'),(2514,416,4,'0.00','3000000.00'),(2515,417,7,'120000.00','0.00'),(2516,417,9,'50000.00','0.00'),(2517,417,196,'0.00','170000.00'),(2518,418,203,'15000000.00','0.00'),(2519,418,4,'0.00','15000000.00'),(2520,419,4,'150000.00','0.00'),(2521,419,197,'0.00','150000.00'),(2522,420,8,'54000000.00','0.00'),(2523,420,95,'0.00','54000000.00'),(2524,421,9,'200000000.00','0.00'),(2525,421,100,'0.00','200000000.00'),(2526,422,4,'100000000.00','0.00'),(2527,422,9,'0.00','100000000.00'),(2528,423,167,'18000.00','0.00'),(2529,423,4,'0.00','18000.00'),(2530,424,4,'10000000.00','0.00'),(2531,424,76,'0.00','6000000.00'),(2532,424,77,'0.00','4000000.00'),(2537,425,49,'7000000.00','0.00'),(2538,425,4,'0.00','7000000.00'),(2539,415,48,'20000000.00','0.00'),(2540,415,4,'0.00','20000000.00'),(2543,426,4,'2000000.00','0.00'),(2544,426,48,'0.00','2000000.00'),(2545,427,4,'30000000.00','0.00'),(2546,427,49,'0.00','30000000.00'),(2547,428,4,'100000000.00','0.00'),(2548,428,96,'0.00','100000000.00'),(2551,430,4,'20000000.00','0.00'),(2552,430,99,'0.00','20000000.00'),(2555,431,4,'200000.00','0.00'),(2556,431,16,'0.00','200000.00'),(2557,432,18,'500000.00','0.00'),(2558,432,4,'0.00','500000.00'),(2559,433,138,'1200000.00','0.00'),(2560,433,40,'0.00','1200000.00'),(2561,434,156,'20000.00','0.00'),(2562,434,4,'0.00','20000.00'),(2563,435,41,'25000000.00','0.00'),(2564,435,4,'0.00','25000000.00'),(2565,436,151,'10000000.00','0.00'),(2566,436,4,'0.00','10000000.00'),(2567,437,141,'100000.00','0.00'),(2568,437,4,'0.00','100000.00'),(2569,438,202,'1500000.00','0.00'),(2570,438,37,'0.00','1500000.00'),(2571,439,203,'8000000.00','0.00'),(2572,439,9,'0.00','8000000.00'),(2573,440,12,'200000000.00','0.00'),(2574,440,100,'0.00','200000000.00'),(2575,441,177,'2000000.00','0.00'),(2576,441,67,'0.00','2000000.00'),(2579,442,203,'1000000.00','0.00'),(2580,442,4,'0.00','1000000.00'),(2581,443,4,'1000.00','0.00'),(2582,443,7,'0.00','1000.00'),(2583,429,4,'80000000.00','0.00'),(2584,429,95,'0.00','80000000.00');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) NOT NULL,
  `displayName` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `role` varchar(25) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IX_USERS_USERNAME` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COMMENT='Data User';

/*Data for the table `users` */

insert  into `users`(`id`,`username`,`displayName`,`password`,`role`) values (1,'admin','Administrator','21232f297a57a5a743894a0e4a801fc3','admin'),(7,'user','User','ee11cbb19052e40b07aac0ca060c23ee','user'),(11,'ipsum','Ipsum','87d4eeb7dec7686410748d174c0e0a11','user'),(12,'lorem','Lorem','d2e16e6ef52a45b7468f1da56bba1953','admin'),(13,'dolor','dolor','568a7bd05c09b03d57f0e614eb55f58f','admin');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
