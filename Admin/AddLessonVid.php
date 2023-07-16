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
$MM_authorizedUsers = "1";
$MM_donotCheckaccess = "false";

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
    if (($strUsers == "") && false) { 
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
  $insertSQL = sprintf("INSERT INTO mp4 (id, LesNo, title, `file`) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['id'], "int"),
                       GetSQLValueString($_POST['LesNo'], "text"),
                       GetSQLValueString($_POST['title'], "text"),
                       GetSQLValueString($_POST['file'], "text"));

  mysql_select_db($database_system, $system);
  $Result1 = mysql_query($insertSQL, $system) or die(mysql_error());

  $insertGoTo = "/Admin/Lessons.php";
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
$query_u = sprintf("SELECT * FROM `admin` WHERE username = %s", GetSQLValueString($colname_u, "text"));
$u = mysql_query($query_u, $system) or die(mysql_error());
$row_u = mysql_fetch_assoc($u);
$totalRows_u = mysql_num_rows($u);

$colname_Recordset1 = "-1";
if (isset($_GET['id'])) {
  $colname_Recordset1 = $_GET['id'];
}
mysql_select_db($database_system, $system);
$query_Recordset1 = sprintf("SELECT * FROM lesson WHERE id = %s", GetSQLValueString($colname_Recordset1, "int"));
$Recordset1 = mysql_query($query_Recordset1, $system) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

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
<title>Add Lessons</title>

<link rel="icon" href="../image/San Isidro logo.png">


<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
.navbar {
  overflow: hidden;
  background-color:#755EF5  ;
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
</style>
</head>

<body>
<table width="1000" align="center" >
  <tr>
    <td width="996" height="83"><img src="../image/logo.jpg" width="1000" height="200" /></td>
  </tr>
  <tr>
  
    <td height="21" bgcolor="#FFFFFF">
<div class="navbar">
<a href="<?php echo $logoutAction ?>">Logout</a>
  <a href="../Admin/admin.php">Home</a>
  <a href="../Admin/Profile.php">Profile</a>
 
  
  <div class="dropdown">
    <button class="dropbtn"><font color="yellow">Lessons</font> 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="../Admin/AddLessons.php">Add New</a>
      <a href="../Admin/Lessons.php">View Lessons</a>
     
    </div>
  </div> 
  <div class="dropdown">
    <button class="dropbtn">User Account
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="../Admin/AddStudents.php">Add Students</a>
      <a href="../Admin/StudList.php">Add Teacher</a>

    </div>
  </div> 
 <div class="dropdown">
    <button class="dropbtn">Quiz 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="../Admin/AddQuiz.php">Add Quiz</a>
      <a href="../Admin/AddActivities.php">Add Activities</a>
        </div>
  </div> 
   
   <a href="">Welcome  <?php echo $row_u['fn']; ?></a>
    </div></td>
  </tr>
  <tr>
    <td height="515" align="center" bgcolor="#FFFFFF"  background="../image/violet.png" ><p>&nbsp;</p>
      <p>&nbsp;</p>
<p>&nbsp;</p>
    <p>&nbsp;</p>
    <form action="<?php echo $editFormAction; ?>" method="post" name="form2" id="form2">
      Is this the correct lesson your uploading?
      <table align="center">
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">LesNo:</td>
          <td><input type="text" name="LesNo" value="<?php echo $row_Recordset1['LesNo']; ?>" size="32" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Title:</td>
          <td><input type="text" name="title" value="<?php echo $row_Recordset1['title']; ?>" size="32" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">File:</td>
          <td><input name="file" type="text" value="<?php echo $row_Recordset1['file']; ?>" size="32" readonly="readonly" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">&nbsp;</td>
          <td><input type="submit" value="Yes" />
            <input type="reset" name="Cancel2" id="Cancel2" value="No"/ onclick="window.history.go(-1); return false;"></td>
        </tr>
      </table>
      <input type="hidden" name="id" value="" />
      <input type="hidden" name="MM_insert" value="form2" />
    </form>
    <p>&nbsp;</p>
<p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p></td>
  </tr>
  <tr>
    <td height="21" bgcolor="#FFFFFF" background="" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>
</body>
</html>
<script>
document.getElementById("Cancel").onclick = function (){
	location.href="Lessons.php";};
	</script>
<?php
mysql_free_result($u);

mysql_free_result($Recordset1);
?>
