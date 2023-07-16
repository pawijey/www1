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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO chat1 (id, username, username2, name, chat, `date`) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['id'], "int"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['username2'], "text"),
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['chat'], "text"),
                       GetSQLValueString($_POST['date'], "text"));

  mysql_select_db($database_system, $system);
  $Result1 = mysql_query($insertSQL, $system) or die(mysql_error());

  $insertGoTo = "../Teacher/MsgChat.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO chat2 (id, username, username2, name, chat, `date`) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['id'], "int"),
                       GetSQLValueString($_POST['username3'], "text"),
                       GetSQLValueString($_POST['username4'], "text"),
                       GetSQLValueString($_POST['name2'], "text"),
                       GetSQLValueString($_POST['chat'], "text"),
                       GetSQLValueString($_POST['date'], "text"));

  mysql_select_db($database_system, $system);
  $Result1 = mysql_query($insertSQL, $system) or die(mysql_error());

  $insertGoTo = "MsgChat.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$currentPage = $_SERVER["PHP_SELF"];

$colname_u = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_u = $_SESSION['MM_Username'];
}
mysql_select_db($database_system, $system);
$query_u = sprintf("SELECT * FROM teachers WHERE username = %s", GetSQLValueString($colname_u, "text"));
$u = mysql_query($query_u, $system) or die(mysql_error());
$row_u = mysql_fetch_assoc($u);
$colname_u = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_u = $_SESSION['MM_Username'];
}
mysql_select_db($database_system, $system);
$query_u = sprintf("SELECT * FROM students WHERE username = %s", GetSQLValueString($colname_u, "text"));
$u = mysql_query($query_u, $system) or die(mysql_error());
$row_u = mysql_fetch_assoc($u);
$totalRows_u = mysql_num_rows($u);

$colname_st = "-1";
if (isset($_GET['id'])) {
  $colname_st = $_GET['id'];
}
mysql_select_db($database_system, $system);
$query_st = sprintf("SELECT * FROM students WHERE id = %s", GetSQLValueString($colname_st, "int"));
$st = mysql_query($query_st, $system) or die(mysql_error());
$row_st = mysql_fetch_assoc($st);
$totalRows_st = mysql_num_rows($st);

$maxRows_inbox = 5;
$pageNum_inbox = 0;
if (isset($_GET['pageNum_inbox'])) {
  $pageNum_inbox = $_GET['pageNum_inbox'];
}
$startRow_inbox = $pageNum_inbox * $maxRows_inbox;

$colname_inbox = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_inbox = $_SESSION['MM_Username'];
}
mysql_select_db($database_system, $system);
$query_inbox = sprintf("SELECT * FROM chat1 WHERE username = %s", GetSQLValueString($colname_inbox, "text"));
$query_limit_inbox = sprintf("%s LIMIT %d, %d", $query_inbox, $startRow_inbox, $maxRows_inbox);
$inbox = mysql_query($query_limit_inbox, $system) or die(mysql_error());
$row_inbox = mysql_fetch_assoc($inbox);

if (isset($_GET['totalRows_inbox'])) {
  $totalRows_inbox = $_GET['totalRows_inbox'];
} else {
  $all_inbox = mysql_query($query_inbox);
  $totalRows_inbox = mysql_num_rows($all_inbox);
}
$totalPages_inbox = ceil($totalRows_inbox/$maxRows_inbox)-1;

$colname_inbox1 = "-1";
if (isset($_GET['id'])) {
  $colname_inbox1 = $_GET['id'];
}
mysql_select_db($database_system, $system);
$query_inbox1 = sprintf("SELECT * FROM chat1 WHERE id = %s", GetSQLValueString($colname_inbox1, "int"));
$inbox1 = mysql_query($query_inbox1, $system) or die(mysql_error());
$row_inbox1 = mysql_fetch_assoc($inbox1);
$totalRows_inbox1 = mysql_num_rows($inbox1);

$queryString_inbox = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_inbox") == false && 
        stristr($param, "totalRows_inbox") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_inbox = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_inbox = sprintf("&totalRows_inbox=%d%s", $totalRows_inbox, $queryString_inbox);

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
//$queryString_my = sprintf("&totalRows_my=%d%s", $totalRows_my, $queryString_my);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Chat</title>


<script src="../SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
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




<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>



input[type=button], input[type=submit], input[type=reset] {
  background-color: #04AA6D;
  border: none;
  color: white;
  padding: 16px 32px;
  text-decoration: none;
  margin: 4px 2px;
  cursor: pointer;
}





.navbar {
  overflow: hidden;
  background-color: #333;
  font-family: Arial, Helvetica, sans-serif;
}

.navbar a {
  float: left;
  font-size: 16px;
  color: white;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
}

.dropdown {
  float: left;
  overflow: hidden;
}

.dropdown .dropbtn {
  font-size: 16px;  
  border: none;
  outline: none;
  color: white;
  padding: 14px 16px;
  background-color: inherit;
  font-family: inherit;
  margin: 0;
}

.navbar a:hover, .dropdown:hover .dropbtn {
  background-color: red;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f9f9f9;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
}

.dropdown-content a {
  float: none;
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
  text-align: left;
}

.dropdown-content a:hover {
  background-color: #ddd;
}

.dropdown:hover .dropdown-content {
  display: block;
}




<style type="text/css">
body,td,th {
	font-family: Georgia, "Times New Roman", Times, serif;
	font-size: 12px;
}
body {
	background-color: #CCC;
}
.yellow {
	color: #FF0;
}
.yellow {
	color: #FF0;
}
</style>
<link href="../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
</head>

<body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>



<table width="1000" align="center" >
  <tr>
    <td width="996" height="83"><img src="../image/logo.jpg" width="1000" height="200" /></td>
  </tr>
  <tr>
     <td height="21" bgcolor="#FFFFFF">
<div class="navbar">
 <a href="<?php echo $logoutAction ?>">Logout</a>
  <a href="StudentPage.php">Home</a>
  <a href="Profile.php">Profile</a>
 <a href="Lessons.php">Lessons</a>
  <div class="dropdown">
     <button class="dropbtn">Quiz 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="Quiz.php">Take a Quiz</a>
      <a href="Activities.php">Activities</a>
  
    </div>
  </div> 
  <a href="Chat.php">Chat</a>
   
      <a href=""> Welcome: <?php echo $row_u['fn']; ?> </a>
   
    </div></td>
  </tr>
  <tr>
    <td height="421" align="center" bgcolor="#FFFFFF"  ><table width="900">
      <tr>
        <td width="292" height="21"><time id="row2"></time> <time id="row1"></time>
<time id="row2"></time>
<script type="text/javascript">updateClock();</script>
          <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
            <table align="center">
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><strong>Compose Message:</strong></td>
                </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Send From:</td>
                <td><img src="../image/icons/Pinvoke/letter1.png" width="16" height="16" /> <?php echo $row_u['fn']," ",$row_u['ln']; ?>
                  <input type="text" name="username2" value="<?php echo $row_u['username']; ?>" size="32" /></td>
                </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Send For:</td>
                <td><img src="../image/icons/Pinvoke/letter2.png" width="16" height="16" /> <?php echo $row_inbox1['name']; ?><span id="sprytextfield1">
                  <input type="text" name="username" value="<?php echo $row_inbox1['username2']; ?>" size="32" />
                  <span class="textfieldRequiredMsg">*</span></span></td>
                </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><span id="sprytextarea1">
                  <textarea name="chat" cols="32" rows="6"></textarea>
                  <span class="textareaRequiredMsg">*</span></span></td>
                </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td>Type Message</td>
                </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><input type="text" name="id" value="" size="15" />
                  <input type="hidden" name="name" value="<?php echo $row_u['fn'],"  ", $row_u['ln']; ?>" size="32" />
                  <input type="hidden" name="date" value="<?php date_default_timezone_set('Asia/Manila');echo date("m-d-Y h:i:s A"); ?>" size="32" />
                  <input type="text" name="username3" value="<?php echo $row_inbox1['username2']; ?>" size="32" />
                  <input name="username4" type="text" id="username4" value="<?php echo $row_inbox1['username']; ?>" size="32" />
                  <input type="text" name="name2" value="<?php echo $row_inbox1['name']; ?>" size="32" /></td>
                </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><input type="submit" value="Submit"  style="font-size : 20px; width: 50%; height: 50px;"  /></td>
                </tr>
              </table>
            <input type="hidden" name="MM_insert" value="form1" />
            <input type="hidden" name="MM_insert2" value="form2" />
          </form></td>
        <td width="596" align="center"><table width="278">
          <tr>
            <td><strong>INBOX</strong></td>
          </tr>
          <tr>
            <td><table width="498">
              <tr class="yellow">
                <td width="129" align="center" bgcolor="#333333"><img src="../image/icons/Pinvoke/letter1.png" width="16" height="16" /> Message From</td>
                <td width="152" align="center" bgcolor="#333333">Name</td>
                <td width="111" align="center" bgcolor="#333333">Date</td>
              </tr>
              <?php do { ?>
              <tr>
                <td align="center" bgcolor="#999999"><?php echo $row_inbox['username2']; ?></td>
                <td align="center" bgcolor="#999999"><?php echo $row_inbox['name']; ?></td>
                <td align="center" bgcolor="#999999"><?php echo $row_inbox['date']; ?></td>
              </tr>
              <?php } while ($row_inbox = mysql_fetch_assoc($inbox)); ?>
            </table></td>
          </tr>
        </table>
          <p> Records  <?php echo min($startRow_inbox + $maxRows_inbox, $totalRows_inbox) ?> of <?php echo $totalRows_inbox ?> &nbsp;&nbsp;          </p>
          <table border="0">
            <tr>
              <td><?php if ($pageNum_inbox > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_inbox=%d%s", $currentPage, 0, $queryString_inbox); ?>">First</a>
                <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_inbox > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_inbox=%d%s", $currentPage, max(0, $pageNum_inbox - 1), $queryString_inbox); ?>">Previous</a>
                <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_inbox < $totalPages_inbox) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_inbox=%d%s", $currentPage, min($totalPages_inbox, $pageNum_inbox + 1), $queryString_inbox); ?>">Next</a>
                <?php } // Show if not last page ?></td>
              <td><?php if ($pageNum_inbox < $totalPages_inbox) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_inbox=%d%s", $currentPage, $totalPages_inbox, $queryString_inbox); ?>">Last</a>
                <?php } // Show if not last page ?></td>
            </tr>
        </table>
          <p>&nbsp;</p>
          <p>&nbsp;</p>
          </p></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="21" bgcolor="#FFFFFF" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>
<p>&nbsp;</p>
<script type="text/javascript">
var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1");
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
</script>
</body>
</html>
<script>
$(document).ready(function(){
    $("#a").change(function(){
        var chat = $("#a").val();
        $("#b").val(a);
    });
});
</script>



<?php
mysql_free_result($u);

mysql_free_result($st);

mysql_free_result($inbox);

mysql_free_result($inbox1);
?>
