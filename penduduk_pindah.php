<?php require_once('Connections/koneksi.php');

//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "login.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}

if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "eror_user.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}

mysql_select_db($database_koneksi, $koneksi);
$query_n = "SELECT * FROM pendudukpindah ORDER BY nama ASC";
$n = mysql_query($query_n, $koneksi) or die(mysql_error());
$row_n = mysql_fetch_assoc($n);
$totalRows_n = mysql_num_rows($n);

$maxRows_n = 10;
$pageNum_n = 0;
if (isset($_GET['pageNum_n'])) {
  $pageNum_n = $_GET['pageNum_n'];
}
$startRow_n = $pageNum_n * $maxRows_n;

mysql_select_db($database_koneksi, $koneksi);
$query_n = "SELECT * FROM pendudukpindah";
$query_limit_n = sprintf("%s LIMIT %d, %d", $query_n, $startRow_n, $maxRows_n);
$n = mysql_query($query_limit_n, $koneksi) or die(mysql_error());
$row_n = mysql_fetch_assoc($n);

if (isset($_GET['totalRows_n'])) {
  $totalRows_n = $_GET['totalRows_n'];
} else {
  $all_n = mysql_query($query_n);
  $totalRows_n = mysql_num_rows($all_n);
}
$totalPages_n = ceil($totalRows_n/$maxRows_n)-1;

ini_set('display_errors', 1); ini_set('error_reporting', E_ERROR);
$cari = $_GET['cari'];
$currentPage = $_SERVER["PHP_SELF"];

if ($cari != "")
	$query_n .= " WHERE NSKP LIKE '%$cari%'";

$query_limit_n = sprintf("%s LIMIT %d, %d", $query_n, $startRow_n, $maxRows_n);
$n = mysql_query($query_limit_n, $koneksi) or die(mysql_error());
$row_n = mysql_fetch_assoc($n);

$totalPages_n = ceil($totalRows_n/$maxRows_n)-1;

$queryString_n = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_n") == false && 
        stristr($param, "totalRows_n") == false) {
      array_push($newParams, $param);
    }
  }

}
$totalPages_n = ceil($totalRows_n/$maxRows_n)-1;
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--
Template Name: Modular Business
Author: <a href="http://www.os-templates.com/">OS Templates</a>
Author URI: http://www.os-templates.com/
Licence: Free to use under our free template licence terms
Licence URI: http://www.os-templates.com/template-terms
-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>


<script type="text/javascript">
// 1 detik = 1000
window.setTimeout("waktu()",1000);  
function waktu() {   
var tanggal = new Date();  
setTimeout("waktu()",1000);  
document.getElementById("output").innerHTML =tanggal.getHours()+":"+tanggal.getMinutes()+":"+tanggal.getSeconds();
 }
</script>


<title>Penduduk pindah</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="layout/styles/layout.css" type="text/css" />
<script type="text/javascript" src="layout/scripts/jquery.min.js"></script>
<script type="text/javascript" src="layout/scripts/jquery.jcarousel.pack.js"></script>
<script type="text/javascript" src="layout/scripts/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="layout/scripts/jquery.jcarousel.setup.js"></script>
<style type="text/css">
<!--
.style1 {color: #FFFFFF}
.style2 {font-size: 15px}
.style3 {color: #CCCCCC}
-->
</style>
<script type="text/JavaScript">
<!--
function MM_popupMsg(msg) { //v1.0
  alert(msg);
}
//-->
</script>
</head>
<body id="top">
<div class="wrapper col1">
  <div id="topbar">
    <p>Desa Cimareme | Telepon : 6867005 | User</p>
	<ul class="style2">
	  <li><a href="kontak.php" class="style3">Kontak </a></li> <li><a href="<?php echo $logoutAction ?>" class="style3">Logout</a></li> 
	</ul>
    <br class="clear" />
  </div>
</div>
<!-- ####################################################################################################### -->
<div class="wrapper col2">
  <div id="header">
    <div class="fl_left">
      <h1><img src="images/Header.png" width="345" height="88" /></h1>
    </div>
    <div class="fl_right"> 
      <div align="right"><a href="#"><img src="images/Logo Bandung Barat.png" width="127" height="91" /></a> </div>
    </div>
    <br class="clear" />
  </div>
  <marquee>
  <div align="center" class="style2">- Wilujeng Sumping di Aplikasi Sensus Penduduk Desa Cimareme -</div>
  </marquee>
  </p>
</div>
<!-- ####################################################################################################### -->
<div class="wrapper col3">
  <div id="topnav">
    <ul>
      <li><a href="home.php">Desa Cimareme</a><span>Profil Desa</span>
        <ul>
          <li><a href="sejarah.php">Sejarah singkat</a></li>
          <li><a href="visimisi.php">Visi & Misi</a></li>
          <li><a href="struktur.php">Struktur Organisasi</a></li>
		  <li><a href="geografis.php">Wilayah Geografis</a></li>
		  <li><a href="perangkatdesa.php">Perangkat Desa</a></li>
        </ul>
      </li>
	  <li><a href="">Data Master</a><span>data</span>
        <ul>
          <li><a href="penduduk.php">Penduduk</a></li>
          <li><a href="keluarga.php">Keluarga</a></li>
        </ul>
      </li>
	  <li><a href="">Pertumbuhan penduduk</a><span>data</span>
        <ul>
          <li><a href="kelahiran_penduduk.php">Kelahiran penduduk</a></li>
          <li><a href="kematian_penduduk.php">Kematian penduduk</a></li>
        </ul>
      </li>
	  <li><a href="">Perpindahan penduduk</a><span>data</span>
        <ul>
          <li><a href="penduduk_datang.php">Penduduk datang</a></li>
          <li><a href="penduduk_pindah.php">Penduduk pindah</a></li>
        </ul>
      </li>
	  <li><a href="grafik_kolom.php">Grafik</a></li>
    </ul>
    <br class="clear" />
  </div>
</div>
<!-- ####################################################################################################### -->
<div class="wrapper col4">
  <div id="featured_slide">
    <div id="featured_content">
      <ul>
        <li><a href="#"><img src="images/Kantor Kepala Desa Cimareme.jpg" alt="" /></a></li>
        <li><a href="#"><img src="images/Balai Desa Cimareme sekarang.jpg" alt="" /></a></li>
        <li><a href="#"><img src="images/Balai Desa Cimareme tempo dulu.jpg" alt="" /></a></li>
        <li><a href="#"><img src="images/Perangkat Desa.jpg" alt="" /></a></li>
        <li><a href="#"><img src="images/Warga Desa.jpg" alt="" /></a></li>
        <li><a href="#"><img src="images/Ibu - ibu.jpg" alt="" /></a></li>
        <li><a href="#"><img src="images/Ibu - ibu 2.jpg" alt="" /></a></li>
		<li><a href="#"><img src="images/Anak - anak.jpg" alt="" /></a></li>
        <li><a href="#"><img src="images/Rapat.jpg" alt="" /></a></li>
        <li><a href="#"><img src="images/Desa.jpg" alt="" /></a></li>
      </ul>
    </div>
    <a href="javascript:void(0);" id="featured-item-prev"><img src="layout/images/prev.png" alt="" /></a> <a href="javascript:void(0);" id="featured-item-next"><img src="layout/images/next.png" alt="" /></a> </div>
</div>
<!-- ####################################################################################################### -->
<div class="wrapper col3">
  <div id="topnav">
    <ul>
      <li><a href="penduduk_pindah.php">Penduduk Pindah</a><span>data</span>
        <ul>
          <li><a href="tambahpenduduk_pindah.php">Tambah penduduk pindah</a></li>
          <li><a href="pendudukpindah_cetak.php">Cetak data</a></li>
        </ul>
    </ul>
    <br class="clear" />
  </div>
</div>
<!-- ####################################################################################################### -->
<div class="wrapper col5">
<body bgcolor="black" text="black" onload="waktu()">
<table align=center bgcolor="#CCCCCC" class="col5" style="border:1px solid black">
<tr><td>
<div id="output">
</div>
</td></tr>
</table>
  <div id="container">
    <div id="content">
      <h2>Penduduk pindah</h2>
      <img src="images/Migrasi.jpg" width="162" height="192" />
      <p align="justify">Penduduk pindah adalah proses pergerakan penduduk yang menyebabkan berkurangnya jumlah penduduk di Desa. Penduduk pindah dapat juga disebut dengan Urbanisasi. Urbansasi adalah perpindahan penduduk dari suatu daerah ( desa ) ke daerah lain yang padat penduduk( kota ) di dalam wilayah Indonesia. Orang yang melakukan urbanisasi disebut urban.</p>
    <div align="justify">
    </div>
    </div>
	<div id="column">
      <div class="holder">
        <h2 class="title"><img src="images/cari1.png" width="44" height="46" />Grafik</h2>
        <p>Grafik adalah adalah penjelasan dari suatu keadaan yang divisualisasikan dalam bentuk . . .</p>
        <p class="readmore">
          <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0" width="147" height="34">
            <param name="movie" value="button_grafikkolom.swf" />
            <param name="quality" value="high" />
            <embed src="button_grafikkolom.swf" quality="high" pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="147" height="34" ></embed>
          </object>
        </p>
      </div>
    </div>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<form>
      <div align="right"><img src="images/cari1.png" width="44" height="28" border="0" />
          <input name="cari" type="text" placeholder="NSKP" />
          <input name="submit" type="submit" class="col1" value="Cari">
      </div>
    </form>
	<p></p>
	<table border="1" cellpadding="0" cellspacing="3">
      <tr class="col1">
        <td><div align="center">NSKP</div></td>
        <td><div align="center">NKK</div></td>
        <td><div align="center">NIK</div></td>
        <td><div align="center">Nama</div></td>
        <td><div align="center">Tempat lahir</div></td>
        <td><div align="center">Tanggal lahir</div></td>
        <td><div align="center">Jenis kelamin</div></td>
        <td><div align="center">Alamat</div></td>
		<td><div align="center">Hubungan keluarga</div></td>
        <td><div align="center">Alasan pindah</div></td>
        <td><div align="center">Alamat pindah</div></td>
        <td><div align="center">Tanggal pindah</div></td>
        <td><div align="center">Klasifikasi pindah</div></td>
		<td colspan="2"><div align="center">Action</div></td>
      </tr>
      <?php do { ?>
        <tr>
          <td><div align="center"><?php echo $row_n['NSKP']; ?></div></td>
          <td><div align="center"><?php echo $row_n['NKK']; ?></div></td>
          <td><div align="center"><?php echo $row_n['NIK']; ?></div></td>
          <td><div align="center"><?php echo $row_n['nama']; ?></div></td>
          <td><div align="center"><?php echo $row_n['tempat_lahir']; ?></div></td>
          <td><div align="center"><?php echo $row_n['tanggal_lahir']; ?></div></td>
          <td><div align="center"><?php echo $row_n['jenis_kelamin']; ?></div></td>
          <td><div align="center"><?php echo $row_n['alamat']; ?></div></td>
		  <td><div align="center"><?php echo $row_n['hubungan_keluarga']; ?></div></td>
          <td><div align="center"><?php echo $row_n['alasan_pindah']; ?></div></td>
          <td><div align="center"><?php echo $row_n['alamat_pindah']; ?></div></td>
          <td><div align="center"><?php echo $row_n['tanggal_pindah']; ?></div></td>
          <td><div align="center"><?php echo $row_n['klasifikasi_pindah']; ?></div></td>
		  <td width="5%"><div align="center"><a href="editpenduduk_pindah.php?id=<?php echo $row_n['nomor']; ?>">Edit<img src="images/edit2.png" width="16" height="26" border="0"></a></div></td>
          <td width="5%"><div align="center" onclick="MM_popupMsg('Data penduduk pindah dihapus')"><a href="hapuspenduduk_pindah.php?id=<?php echo $row_n['NSKP']; ?>">Delete<img src="images/dell2.png" width="16" height="16" border="0"></a></div></td>
        </tr>
        <?php } while ($row_n = mysql_fetch_assoc($n)); ?>
    </table>
  </div>
    </div>
</div>
<!-- ####################################################################################################### -->
<div align="center">
  <p>
  </p>
  <hr width="71%" />
  <p>
    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="960" height="125">
      <param name="movie" value="images/bawah.swf" />
      <param name="quality" value="high" />
      <embed src="images/bawah.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="960" height="125"></embed>
    </object>
</p>
  <hr width="71%" />
  <p>&nbsp;</p>
</div>
<!-- ####################################################################################################### -->
<div class="wrapper col6">
  <div id="footer">
    <div class="footbox">
      <h2><a href="">Perhitungan penduduk</a></h2>
      <ul>
        <li><a href="PPPT.php">Perhitungan pertumbuhan penduduk total</a></li>
        <li><a href="PPPA.php">Penduduk pertumbuhan penduduk alami</a></li>
		<li><a href="PPP.php">Penduduk pertumbuhan penduduk</a></li>
		<li><a href="PPRP.php">Penduduk perpindahan penduduk</a></li>
		<li><a href="PKP.php">Penduduk kepadatan penduduk</a></li>
      </ul>
    </div>
    <div class="footbox">
      <h2><a href="">Perpindahan penduduk </a></h2>
      <ul>
        <li><a href="penduduk_datang.php">Penduduk datang</a></li>
        <li><a href="penduduk_pindah.php">Penduduk pindah</a></li>
      </ul>
    </div>
    <div class="footbox">
      <h2 class="style4 style3">Pertumbuhan penduduk</h2>
      <ul>
        <li><a href="kelahiran_penduduk.php">Kelahiran penduduk</a></li>
        <li><a href="kematian_penduduk.php">Kematian penduduk</a></li>
      </ul>
    </div>
    <div class="footbox">
      <h2 class="style4 style3">Data Master</h2>
      <ul>
        <li><a href="penduduk.php">Penduduk</a></li>
        <li><a href="keluarga.php">Keluarga</a></li>
	  </ul>
    </div>
    <div class="footbox">
	<h2>&nbsp;</h2>
	<h2>&nbsp;</h2>
      <h2><a href="home.php">Desa Cimareme</a></h2>
      <ul>
        <li><a href="sejarah.php">Sejarah singkat</a></li>
        <li><a href="visimisi.php">Visi dan Misi</a></li>
        <li><a href="struktur.php">Struktur Organisasi</a></li>
        <li><a href="geografis.php">Wilayah Geografis</a></li>
		<li><a href="perangkatdesa.php">Perangkat Desa</a></li>
      </ul>
    </div>
    <br class="clear" />
  </div>
</div>
<!-- ####################################################################################################### -->
<div class="col1">
  <div id="copyright">
    <p class="fl_left style1">Copyright &copy; Desa Cimareme 2015</p>
    <br class="clear" />
  </div>
</div>
</body>
</html>
<?php
mysql_free_result($n);
?>