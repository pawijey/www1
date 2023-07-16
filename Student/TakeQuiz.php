<?php require_once('../Connections/system.php'); ?>
<?php
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
	
  $logoutGoTo = "../index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
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

$MM_restrictGoTo = "../error.php";
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
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$currentPage = $_SERVER["PHP_SELF"];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO stdqz (id, QzID, QzNo, username, DateIn, Teacher, sy, a, itemNo, answer) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['id'], "int"),
                       GetSQLValueString($_POST['QzID'], "text"),
                       GetSQLValueString($_POST['QzNo'], "text"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['DateIn'], "text"),
                       GetSQLValueString($_POST['Teacher'], "text"),
                       GetSQLValueString($_POST['sy'], "text"),
                       GetSQLValueString($_POST['a'], "text"),
                       GetSQLValueString($_POST['itemNo'], "text"),
                       GetSQLValueString($_POST['answer'], "text"));

  mysql_select_db($database_system, $system);
  $Result1 = mysql_query($insertSQL, $system) or die(mysql_error());
}

$colname_u = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_u = $_SESSION['MM_Username'];
}
mysql_select_db($database_system, $system);
$query_u = sprintf("SELECT * FROM students WHERE username = %s", GetSQLValueString($colname_u, "text"));
$u = mysql_query($query_u, $system) or die(mysql_error());
$row_u = mysql_fetch_assoc($u);
$totalRows_u = mysql_num_rows($u);

$maxRows_qz = 1;
$pageNum_qz = 0;
if (isset($_GET['pageNum_qz'])) {
  $pageNum_qz = $_GET['pageNum_qz'];
}
$startRow_qz = $pageNum_qz * $maxRows_qz;

$colname_qz = "-1";
if (isset($_GET['QzNo'])) {
  $colname_qz = $_GET['QzNo'];
}
mysql_select_db($database_system, $system);
$query_qz = sprintf("SELECT * FROM quiz WHERE QzNo = %s", GetSQLValueString($colname_qz, "text"));
$query_limit_qz = sprintf("%s LIMIT %d, %d", $query_qz, $startRow_qz, $maxRows_qz);
$qz = mysql_query($query_limit_qz, $system) or die(mysql_error());
$row_qz = mysql_fetch_assoc($qz);

if (isset($_GET['totalRows_qz'])) {
  $totalRows_qz = $_GET['totalRows_qz'];
} else {
  $all_qz = mysql_query($query_qz);
  $totalRows_qz = mysql_num_rows($all_qz);
}
$totalPages_qz = ceil($totalRows_qz/$maxRows_qz)-1;

$maxRows_qz1 = 1;
$pageNum_qz1 = 0;
if (isset($_GET['pageNum_qz1'])) {
  $pageNum_qz1 = $_GET['pageNum_qz1'];
}
$startRow_qz1 = $pageNum_qz1 * $maxRows_qz1;

$colname_qz1 = "-1";
if (isset($_GET['QzNo'])) {
  $colname_qz1 = $_GET['QzNo'];
}
mysql_select_db($database_system, $system);
$query_qz1 = sprintf("SELECT * FROM quest WHERE QzNo = %s", GetSQLValueString($colname_qz1, "text"));
$query_limit_qz1 = sprintf("%s LIMIT %d, %d", $query_qz1, $startRow_qz1, $maxRows_qz1);
$qz1 = mysql_query($query_limit_qz1, $system) or die(mysql_error());
$row_qz1 = mysql_fetch_assoc($qz1);

if (isset($_GET['totalRows_qz1'])) {
  $totalRows_qz1 = $_GET['totalRows_qz1'];
} else {
  $all_qz1 = mysql_query($query_qz1);
  $totalRows_qz1 = mysql_num_rows($all_qz1);
}
$totalPages_qz1 = ceil($totalRows_qz1/$maxRows_qz1)-1;

$maxRows_ans = 1;
$pageNum_ans = 0;
if (isset($_GET['pageNum_ans'])) {
  $pageNum_ans = $_GET['pageNum_ans'];
}
$startRow_ans = $pageNum_ans * $maxRows_ans;

$colname_ans = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_ans = $_SESSION['MM_Username'];
}
$colname2_ans = "-1";
if (isset($_GET['QzNo'])) {
  $colname2_ans = $_GET['QzNo'];
}
mysql_select_db($database_system, $system);
$query_ans = sprintf("SELECT * , count(QzNo) as total FROM stdqz WHERE username = %s and QzNo = %s", GetSQLValueString($colname_ans, "text"),GetSQLValueString($colname2_ans, "text"));
$query_limit_ans = sprintf("%s LIMIT %d, %d", $query_ans, $startRow_ans, $maxRows_ans);
$ans = mysql_query($query_limit_ans, $system) or die(mysql_error());
$row_ans = mysql_fetch_assoc($ans);

if (isset($_GET['totalRows_ans'])) {
  $totalRows_ans = $_GET['totalRows_ans'];
} else {
  $all_ans = mysql_query($query_ans);
  $totalRows_ans = mysql_num_rows($all_ans);
}
$totalPages_ans = ceil($totalRows_ans/$maxRows_ans)-1;

$queryString_qz1 = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_qz1") == false && 
        stristr($param, "totalRows_qz1") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_qz1 = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_qz1 = sprintf("&totalRows_qz1=%d%s", $totalRows_qz1, $queryString_qz1);

$queryString_my = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_my") == false && 
        stristr($param, "totalRows_my") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_my = "&" . htmlentities(implode("&", $newParams));
  }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="icon" href="../image/San Isidro logo.png">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Take Quiz</title>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="/Student/CSS/Table_NavMainHeaderFooter.css" />
<link rel="stylesheet" type="text/css" href="/Student/CSS/TableContent.css" />
<link rel="stylesheet" type="text/css" href='/Student/CSS/takequizcss.css' />

<script type="text/javascript">;
function updateClock() {
//© OBH 2015 - www.oliverboorman.biz 

var days = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
var months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

var now = new Date();
var day = now.getDay();
var date = now.getDate();
var month = now.getMonth();
var year = now.getFullYear();
var hour = now.getHours();
var minute = now.getMinutes();
var second = now.getSeconds();
var AMorPM = " AM";

if(hour>=12) AMorPM = " PM";
if(hour>12) hour -= 12;

if(hour<10) hour = "0" + hour;
if(minute<10) minute = "0" + minute;
if(second<10) second = "0" + second;

var firstRow = hour + ":" + minute + ":" + second + AMorPM + '';
document.getElementById("row1").innerHTML = firstRow;
var secondRow = days[day] + "&nbsp;" + date + "/" + months[month] + "/" + year + '';
document.getElementById("row2").innerHTML = secondRow;

setTimeout("updateClock()",1000);
} 
</script>





<style>

p {
	text-align: center;
	font-size: 48px;
	margin-top: 0px;
}


input[type=button], input[type=submit], input[type=reset] {
  background-color: #04AA6D;
  border: none;
  color: white;
  padding: 16px 32px;
  text-decoration: none;
  margin: 4px 2px;
  cursor: pointer;
}
body,td,th {
	font-family: Georgia, "Times New Roman", Times, serif;
	font-size: 12px;
}
body {
	background-color: #CCC;
	overflow-x: hidden;
}
#qzSec {
	padding-right:15%;
}
</style>
<link href="../SpryAssets/SpryValidationRadio.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationRadio.js" type="text/javascript"></script>
</head>

<body>




<script>
  function myFunction() {
    window.location.href="<?php printf("%s?pageNum_qz1=%d%s", $currentPage, min($totalPages_qz1, $pageNum_qz1 + 1), $queryString_qz1); ?>";
  }
 </script>



<table width="100%" align="center" disable="disable">
  <tr>
    <td width="100%" height="50" id="tdLogoQz"></td>
  </tr>
  <tr>
    <td height="21" id="NavBar" align="center">
    
   <time id="row2"></time><time id="row1"></time>
<script type="text/javascript">updateClock();</script>
</td>
  </tr>
  <tr>
    <td height="100%" align="center" id="tdMain"  >
      <table width="100%" height="100%" style="margin-left: 5%;margin-right:5%;  ">
      <tr>
        	<td width="106" id="name1">Name: <?php echo $row_u['fn'] ,"  ", $row_u['mi'], "  ",$row_u['ln']; ?> </td>
        </tr>
      <tr>
        <td height="10" id="qzno"><strong>Quiz No. <?php echo $row_qz1['QzNo']; ?></strong></td>
      </tr>
      <tr>
        <td width="300" height="3" id="DateI">Date Started: <?php echo $row_qz['DateIn']; ?></td>
        </tr>
        <tr>
            <td width="1228" id="DateO">Date End: <font color="red"><?php echo $row_qz['DateOut']; ?></font></td>
         </tr>
      <tr>
        <td height="100%" width="100%"align="left" id="qzSec">
          <form action="<?php printf("%s?pageNum_qz1=%d%s", $currentPage, min($totalPages_qz1, $pageNum_qz1 + 1), $queryString_qz1); ?>" method="post" name="form1" id="form1">
            <input type="hidden" name="id" value="" />
            <input type="hidden" name="MM_insert" value="form1" /><br /><hr /><br />


            <table width="75%" align="center">
            	<tr>
                	<td width="100%" valign="middle" id="Note">Direction: Read the questions carefully and answer it with your best. You can take this quiz once.</td>
                </tr>
              <?php do { ?>
                <tr>
                  <td colspan="5" id="tdQst" width="50%"><font size="6">
                    
                    <?php echo $row_qz1['question']; ?> </font></td>
                  </tr>
                <tr>
                  <td width="94" align="center">&nbsp;</td>
                  <td width="211">&nbsp;</td>
                  <td width="94">&nbsp;</td>
                  <td width="94">&nbsp;</td>
                  <td width="94">&nbsp;</td>
                  </tr>
                <tr>
                  <td colspan="5" align="left"><span id="spryradio1">
                    <label class="container">
                      <input type="radio" name="a" value="A" id="a_0" />
                      <font size="5">  A</font>
                      <strong><font size="5">&nbsp;&nbsp;<?php echo $row_qz1['a']; ?></font></strong><br />
                      <span class="checkmark"></span>
                      </label>
                    
                    <label class="container">
                      <input type="radio" name="a" value="B" id="a_1" />
                      <font size="5">   B</font>
                      <strong><font size="5">&nbsp;&nbsp;<?php echo $row_qz1['b']; ?></font></strong><br />
                      <span class="checkmark"></span>
                      </label>
                    
                    <label class="container">
                      <input type="radio" name="a" value="C" id="a_2" />
                      <font size="5"> C</font>
                      <strong><font size="5">&nbsp;&nbsp;<?php echo $row_qz1['c']; ?></font></strong><br />
                      <span class="checkmark"></span>
                      </label>
                    
                    <label class="container">
                      <input type="radio" name="a" value="D" id="a_3" />
                      <font size="5">   D</font>
                      <strong><font size="5">&nbsp;&nbsp;<?php echo $row_qz1['d']; ?></font></strong><br />
                      <span class="checkmark"></span>
                      </label>
                    
                    <span class="radioRequiredMsg">Please make a selection.</span></span></td>
                  </tr>
                <tr>
                  <td height="21" colspan="5" align="center">&nbsp;</td>
                  </tr>
                <tr>
                  <td height="21" colspan="5" align="center"><?php 
				$time = date_default_timezone_set('Asia/Manila'); date("M d, Y h:i:s A");
				 
				 if ($row_ans['total'] == $row_qz['NoItems']) echo '<input type="submit" value="Submit"  style="font-size : 20px; width: 50%; height: 50px;"  disabled="diabled"/>';
				 else echo '<input type="submit" value="Submit"  style="font-size : 20px; width: 50%; height: 50px;"   />'
				 ?>
  &nbsp;</td>
                  </tr>
                <tr>
                  <td height="21" colspan="5" align="center"><input type="hidden" name="QzID" value="<?php echo $row_qz1['id']; ?>" size="32" />
                    <input type="hidden" name="QzNo" value="<?php echo $row_qz1['QzNo']; ?>" size="32" />
                    <input type="hidden" name="username" value="<?php echo $row_u['username']; ?>" size="32" />
                    <input type="hidden" name="DateIn" value="<?php echo $row_qz1['DateIn']; ?>" size="32" />
                    <input type="hidden" name="Teacher" value="<?php echo $row_qz1['Teacher']; ?>" size="32" />
                    <input type="hidden" name="sy" value="<?php echo $row_qz1['sy']; ?>" size="32" />
                    <input type="hidden" name="itemNo" value="<?php echo $row_qz1['itemNo']; ?>" size="32" />
                    <input type="hidden" name="answer" value="<?php echo $row_qz1['answer']; ?>" size="32" />
                    <font color="#FF0000">
                      <?php if($row_ans['total'] == $row_qz1['itemNo'] & $row_ans['itemNo'] == $row_qz1['itemNo'] ) echo  "You finished the quiz!"; else "";  ?>
                      </font>
                    
                    
                    </td>
                  </tr>
                <tr>
                  <td height="20" colspan="5" align="center">
                    <a href="Results.php?QzNo=<?php echo $row_ans['QzNo'];?>">
                      <?php if($row_ans['total'] == $row_qz1['itemNo'] & $row_ans['itemNo'] == $row_qz1['itemNo']) echo  "View Score";	
				  else echo "";?>
                    </a></td>
                  </tr>
                <?php } while ($row_qz1 = mysql_fetch_assoc($qz1)); ?>
              </table>
          </form></td>
          <td width="0"></td>
      </tr>
      </table>
    <p></p></td>
  </tr>
  <tr id="trFooter">
    <td height="15" id="tdFooter" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>
<script type="text/javascript">
var spryradio1 = new Spry.Widget.ValidationRadio("spryradio1");
</script>




</body>
</html>
<?php
mysql_free_result($u);

mysql_free_result($qz1);

mysql_free_result($ans);

mysql_free_result($qz);


?>
