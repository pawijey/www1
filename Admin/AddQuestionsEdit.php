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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO quest (id, QzID, QzNo, DateIn, DateOut, username, Teacher, sy, question, answer, itemNo, a, b, c, d) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['id'], "int"),
                       GetSQLValueString($_POST['QzID'], "text"),
                       GetSQLValueString($_POST['QzNo'], "text"),
                       GetSQLValueString($_POST['DateIn'], "text"),
                       GetSQLValueString($_POST['DateOut'], "text"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['Teacher'], "text"),
                       GetSQLValueString($_POST['sy'], "text"),
                       GetSQLValueString($_POST['question'], "text"),
                       GetSQLValueString($_POST['answer'], "text"),
                       GetSQLValueString($_POST['itemNo'], "int"),
                       GetSQLValueString($_POST['a'], "text"),
                       GetSQLValueString($_POST['b'], "text"),
                       GetSQLValueString($_POST['c'], "text"),
                       GetSQLValueString($_POST['d'], "text"));

  mysql_select_db($database_system, $system);
  $Result1 = mysql_query($insertSQL, $system) or die(mysql_error());
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE actanswer SET username=%s, ActID=%s, ActNo=%s, DateIn=%s, DateOut=%s, Teacher=%s, sy=%s, itemNo=%s, answer=%s, a=%s WHERE id=%s",
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['ActID'], "text"),
                       GetSQLValueString($_POST['ActNo'], "text"),
                       GetSQLValueString($_POST['DateIn'], "text"),
                       GetSQLValueString($_POST['DateOut'], "text"),
                       GetSQLValueString($_POST['Teacher'], "text"),
                       GetSQLValueString($_POST['sy'], "text"),
                       GetSQLValueString($_POST['itemNo'], "text"),
                       GetSQLValueString($_POST['answer'], "text"),
                       GetSQLValueString($_POST['a'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_system, $system);
  $Result1 = mysql_query($updateSQL, $system) or die(mysql_error());

  $updateGoTo = "EditMsg.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE quest SET QzID=%s, QzNo=%s, DateIn=%s, DateOut=%s, username=%s, Teacher=%s, sy=%s, question=%s, answer=%s, itemNo=%s, a=%s, b=%s, c=%s, d=%s WHERE id=%s",
                       GetSQLValueString($_POST['QzID'], "text"),
                       GetSQLValueString($_POST['QzNo'], "text"),
                       GetSQLValueString($_POST['DateIn'], "text"),
                       GetSQLValueString($_POST['DateOut'], "text"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['Teacher'], "text"),
                       GetSQLValueString($_POST['sy'], "text"),
                       GetSQLValueString($_POST['question'], "text"),
                       GetSQLValueString($_POST['answer'], "text"),
                       GetSQLValueString($_POST['itemNo'], "int"),
                       GetSQLValueString($_POST['a'], "text"),
                       GetSQLValueString($_POST['b'], "text"),
                       GetSQLValueString($_POST['c'], "text"),
                       GetSQLValueString($_POST['d'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_system, $system);
  $Result1 = mysql_query($updateSQL, $system) or die(mysql_error());

  $updateGoTo = "EditMsg.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
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

$maxRows_qz = 10;
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

$maxRows_quest = 5;
$pageNum_quest = 0;
if (isset($_GET['pageNum_quest'])) {
  $pageNum_quest = $_GET['pageNum_quest'];
}
$startRow_quest = $pageNum_quest * $maxRows_quest;

$colname_quest = "-1";
if (isset($_GET['id'])) {
  $colname_quest = $_GET['id'];
}
mysql_select_db($database_system, $system);
$query_quest = sprintf("SELECT * FROM quest WHERE id = %s", GetSQLValueString($colname_quest, "int"));
$query_limit_quest = sprintf("%s LIMIT %d, %d", $query_quest, $startRow_quest, $maxRows_quest);
$quest = mysql_query($query_limit_quest, $system) or die(mysql_error());
$row_quest = mysql_fetch_assoc($quest);

if (isset($_GET['totalRows_quest'])) {
  $totalRows_quest = $_GET['totalRows_quest'];
} else {
  $all_quest = mysql_query($query_quest);
  $totalRows_quest = mysql_num_rows($all_quest);
}
$totalPages_quest = ceil($totalRows_quest/$maxRows_quest)-1;

$queryString_quest = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_quest") == false && 
        stristr($param, "totalRows_quest") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_quest = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_quest = sprintf("&totalRows_quest=%d%s", $totalRows_quest, $queryString_quest);

$queryString_qz = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_qz") == false && 
        stristr($param, "totalRows_qz") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_qz = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_qz = sprintf("&totalRows_qz=%d%s", $totalRows_qz, $queryString_qz);

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
<title>Add Questions Edit</title>

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
.yw {
	color: #FFFF80;
}
.ygreen {
	color: #80FF00;
}
.yellow {
	color: #FF0;
}
</style>
<link href="../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
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
    <button class="dropbtn">Lessons
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
      <a href="../Admin/AddTeacher.php">Add Teacher</a>

    </div>
  </div> 
 <div class="dropdown">
    <button class="dropbtn"><font color="yellow">Quiz </font>
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
    <td height="422" align="center" bgcolor="#FFFFFF"  background="../image/violet.png" ><table width="42%" align="left">
      <tr>
        <td height="268" colspan="2" align="center"><p></p>
          <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
            <table align="center">
              <tr valign="baseline">
                <td colspan="2" align="center" nowrap="nowrap">EDIT QUESTIONER</td>
                </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">:</td>
                <td><input type="hidden" name="QzID" value="<?php echo htmlentities($row_quest['QzID'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                  <input type="hidden" name="QzNo" value="<?php echo htmlentities($row_quest['QzNo'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                  <input type="hidden" name="DateIn" value="<?php echo htmlentities($row_quest['DateIn'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                  <input type="hidden" name="DateOut" value="<?php echo htmlentities($row_quest['DateOut'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                  <input type="hidden" name="username" value="<?php echo htmlentities($row_quest['username'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                  <input type="hidden" name="Teacher" value="<?php echo htmlentities($row_quest['Teacher'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                  <input type="hidden" name="sy" value="<?php echo htmlentities($row_quest['sy'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                  <input type="hidden" name="itemNo" value="<?php echo htmlentities($row_quest['itemNo'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Question:</td>
                <td><span id="sprytextarea1">
                  <textarea name="question" cols="32"><?php echo htmlentities($row_quest['question'], ENT_COMPAT, 'utf-8'); ?></textarea>
                  <span class="textareaRequiredMsg">*</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Answer:</td>
                <td><span id="sprytextfield1">
                  <input type="text" name="answer" value="<?php echo htmlentities($row_quest['answer'], ENT_COMPAT, 'utf-8'); ?>" size="10" />
                  <span class="textfieldRequiredMsg">*</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Choice A:</td>
                <td><span id="sprytextfield2">
                  <input type="text" name="a" value="<?php echo htmlentities($row_quest['a'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                  <span class="textfieldRequiredMsg">*</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Choice B:</td>
                <td><span id="sprytextfield3">
                  <input type="text" name="b" value="<?php echo htmlentities($row_quest['b'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                  <span class="textfieldRequiredMsg">*</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Choice C:</td>
                <td><span id="sprytextfield4">
                  <input type="text" name="c" value="<?php echo htmlentities($row_quest['c'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                  <span class="textfieldRequiredMsg">*</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Choice D:</td>
                <td><span id="sprytextfield5">
                  <input type="text" name="d" value="<?php echo htmlentities($row_quest['d'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                  <span class="textfieldRequiredMsg">*</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><input type="submit" value="Update record" /></td>
              </tr>
            </table>
            <input type="hidden" name="MM_update" value="form1" />
            <input type="hidden" name="id" value="<?php echo $row_quest['id']; ?>" />
          </form>
          <p>&nbsp;</p>
<p>&nbsp;</p></td>
        </tr>
      <tr>
        <td width="230" height="21" align="center">&nbsp;</td>
        <td width="176" align="center">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="21" bgcolor="#FFFFFF" background="" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>
<script type="text/javascript">
var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1");
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3");
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4");
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5");
</script>
</body>
</html>
<?php
mysql_free_result($u);

mysql_free_result($qz);

mysql_free_result($quest);
?>
