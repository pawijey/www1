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

  $insertGoTo = "MsgChat.php";
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO chat1 (id, username, username2, name, chat, `date`, email) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['id'], "int"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['username2'], "text"),
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['chat'], "text"),
                       GetSQLValueString($_POST['date'], "text"),
                       GetSQLValueString($_POST['email'], "text"));

  mysql_select_db($database_system, $system);
  $Result1 = mysql_query($insertSQL, $system) or die(mysql_error());

  $insertGoTo = "ChatMsg.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO chat1 (id, username, username2, name, chat, `date`, email, title) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['id'], "int"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['username2'], "text"),
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['chat'], "text"),
                       GetSQLValueString($_POST['date'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['title'], "text"));

  mysql_select_db($database_system, $system);
  $Result1 = mysql_query($insertSQL, $system) or die(mysql_error());

  $insertGoTo = "ChatMsg.php";
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
$totalRows_u = mysql_num_rows($u);

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
  <a href="TeacherPage.php">Home</a>
  <a href="Profile.php">Profile</a>
  <a href="Chat.php">Chat</a>
  
  <div class="dropdown">
    <button class="dropbtn">Lessons 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="AddLessons.php">Add New</a>
      <a href="Lessons.php">View Lessons</a>
     
    </div>
  </div> 
  <div class="dropdown">
    <button class="dropbtn">Students 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="AddStudents.php">Add New</a>
      <a href="StudList.php">Student List</a>
     
    </div>
  </div> 

 <div class="dropdown">
    <button class="dropbtn">Quiz 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="AddQuiz.php">Add Quiz</a>
      <a href="AddActivities.php">Add Activities</a>
      <a href="ViewQuiz.php">View Quiz</a>
   </div>
  </div> 
   <a href="">Welcome  <?php echo $row_u['fn']; ?></a>
    </div></td>
  </tr>
  <tr>
    <td height="365" align="center" bgcolor="#FFFFFF"  ><table width="957">
      <tr>
        <td width="145" height="122">&nbsp;
          <p>&nbsp;</p>
          <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
            <table align="center">
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Id:</td>
                <td><input type="text" name="id" value="" size="32" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Username:</td>
                <td><input type="text" name="username" value="" size="32" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Username2:</td>
                <td><input type="text" name="username2" value="" size="32" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Name:</td>
                <td><input type="text" name="name" value="" size="32" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Chat:</td>
                <td><input type="text" name="chat" value="" size="32" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Date:</td>
                <td><input type="text" name="date" value="" size="32" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Email:</td>
                <td><input type="text" name="email" value="" size="32" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Title:</td>
                <td><input type="text" name="title" value="" size="32" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><input type="submit" value="Insert record" /></td>
              </tr>
            </table>
            <input type="hidden" name="MM_insert" value="form1" />
          </form>
          <p>&nbsp;</p></td>
        <td width="145">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="21" bgcolor="#FFFFFF" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>
<p>&nbsp;</p>
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
?>
