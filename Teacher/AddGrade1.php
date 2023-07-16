<?php require_once('../Connections/system.php'); ?>
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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE actup SET username=%s, actid=%s, actno=%s, DateIn=%s, DateOut=%s, teacher=%s, upfile=%s, score=%s WHERE id=%s",
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['actid'], "text"),
                       GetSQLValueString($_POST['actno'], "text"),
                       GetSQLValueString($_POST['DateIn'], "text"),
                       GetSQLValueString($_POST['DateOut'], "text"),
                       GetSQLValueString($_POST['teacher'], "text"),
                       GetSQLValueString($_POST['upfile'], "text"),
                       GetSQLValueString($_POST['score'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_system, $system);
  $Result1 = mysql_query($updateSQL, $system) or die(mysql_error());

  $updateGoTo = "/Teacher/AddGrade1.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

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

$colname_u = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_u = $_SESSION['MM_Username'];
}
mysql_select_db($database_system, $system);
$query_u = sprintf("SELECT * FROM teachers WHERE username = %s", GetSQLValueString($colname_u, "text"));
$u = mysql_query($query_u, $system) or die(mysql_error());
$row_u = mysql_fetch_assoc($u);
$totalRows_u = mysql_num_rows($u);

$maxRows_my = 10;
$pageNum_my = 0;
if (isset($_GET['pageNum_my'])) {
  $pageNum_my = $_GET['pageNum_my'];
}
$startRow_my = $pageNum_my * $maxRows_my;

$colname_my = "-1";
if (isset($_GET['username'])) {
  $colname_my = $_GET['username'];
}
mysql_select_db($database_system, $system);
$query_my = sprintf("SELECT * FROM mystudents WHERE username = %s", GetSQLValueString($colname_my, "text"));
$query_limit_my = sprintf("%s LIMIT %d, %d", $query_my, $startRow_my, $maxRows_my);
$my = mysql_query($query_limit_my, $system) or die(mysql_error());
$row_my = mysql_fetch_assoc($my);

if (isset($_GET['totalRows_my'])) {
  $totalRows_my = $_GET['totalRows_my'];
} else {
  $all_my = mysql_query($query_my);
  $totalRows_my = mysql_num_rows($all_my);
}
$totalPages_my = ceil($totalRows_my/$maxRows_my)-1;

$colname_act = "-1";
if (isset($_GET['ActNo'])) {
  $colname_act = $_GET['ActNo'];
}
mysql_select_db($database_system, $system);
$query_act = sprintf("SELECT * FROM activities WHERE ActNo = %s ", GetSQLValueString($colname_act, "text"));
$act = mysql_query($query_act, $system) or die(mysql_error());
$row_act = mysql_fetch_assoc($act);
$totalRows_act = mysql_num_rows($act);

$colname_actans = "-1";
if (isset($_GET['ActNo'])) {
  $colname_actans = $_GET['ActNo'];
}
mysql_select_db($database_system, $system);
$query_actans = sprintf("SELECT *,  sum(IF (a = answer, '1', '0')) as A FROM actanswer WHERE ActNo = %s GROUP BY ActNo ORDER BY username ASC", GetSQLValueString($colname_actans, "text"));
$actans = mysql_query($query_actans, $system) or die(mysql_error());
$row_actans = mysql_fetch_assoc($actans);
$totalRows_actans = mysql_num_rows($actans);

mysql_select_db($database_system, $system);
$query_actextra = "SELECT * FROM actextra";
$actextra = mysql_query($query_actextra, $system) or die(mysql_error());
$row_actextra = mysql_fetch_assoc($actextra);
$totalRows_actextra = mysql_num_rows($actextra);

mysql_select_db($database_system, $system);
$query_Recordset1 = "SELECT * FROM actsub";
$Recordset1 = mysql_query($query_Recordset1, $system) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

$colname_score = "-1";
if (isset($_GET['actno'])) {
  $colname_score = $_GET['actno'];
}
mysql_select_db($database_system, $system);
$query_score = sprintf("SELECT * FROM actup WHERE actno = %s GROUP BY username ORDER BY username ASC", GetSQLValueString($colname_score, "text"));
$score = mysql_query($query_score, $system) or die(mysql_error());
$row_score = mysql_fetch_assoc($score);
$totalRows_score = mysql_num_rows($score);

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
$queryString_my = sprintf("&totalRows_my=%d%s", $totalRows_my, $queryString_my);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<link rel="icon" href="../image/San Isidro logo.png">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Quiz Progress | Student</title>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="/Teacher/CSS/mainCSS.css">
<link rel="stylesheet" type="text/css" href="/Teacher/CSS/Table_NavMainHeaderFooter.css">
<link rel="stylesheet" type="text/css" href="/Teacher/CSS/TableContent.css">
<link rel="stylesheet" type="text/css" href="/Teacher/CSS/lesson.css" />
<style>
body {
	background-color: #CCC;
}
#tdHeader{
	background-color: white;
	font-family: Arial, Gadget, sans-serif;
	font-weight: bolder;
	font-size:24px;
	text-align: center;
	border-radius:10px;
}
#trContent{
	font-family:"Arial Black", Gadget, sans-serif;
	letter-spacing:1px;
	font-size:18px;	
	text-align:center;
	border-bottom: 1pt solid black;
}
#back{
	padding-left:45%;
	padding-bottom:10%;
	text-align:center;
}
</style>
<link href="/SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="/SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
</head>

<body>
<table width="100%" height="100%">
  <tr>
    <td width="100%" height="150" id="tdLogo"></td>
  </tr>
  <tr>
    <td height="70" id="NavBar">
    
<div class="navbar">
<h1 id="name1">Welcome,  <?php echo $row_u['fn']; ?>!</h1>
  <a href="TeacherPage.php" id="link1" >Home</a>
  <a href="Profile.php" id="link1">Profile</a>
 
  
  <div class="dropdown">
    <button class="dropbtn">Lessons 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="AddLessons.php">Add New</a>
      <a href="Lessons.php" >View Lessons</a>
     
    </div>
  </div> 
  
  <div class="dropdown">
    <button class="dropbtn" >Students 
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
      <a href="ViewAct.php">View Activities</a>
   </div>
  </div> 
  <div class="dropdown">
    <button class="dropbtn" style="color:gray; cursor: default;">Grades 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="ViewStudQz.php">Quizzes</a>
      <a href="ViewStudAct.php" style="color:gray; cursor: default;"><img src="/image/icons/PixelMixer/folder.ico" width="16" height="16" />Activities</a>
   </div>
  </div> 
   
   <a href="<?php echo $logoutAction ?>" id="logoutbutton1">Logout</a>
    </div>
  </div> 
</div></td>
  </tr>
  
   
  <tr>
    <td height="90%" align="center" id="tdMain" >
    
    <h1 style="text-align:center; font-size:36px;">Activity <?php echo $row_actextra['actno']; ?>: <?php echo $row_actextra['actname']; ?></h1>
   
    <table width="80%"  cellpadding="10" cellspacing="10" align="center" style="margin-left:10%;">
      <tr id="actCat">
        <td width="200" id="tdHeader">Name</td>
        <td width="200" id="tdHeader">File</td>
        <td width="250" id="tdHeader">Date In</td>
        <td width="100"	id="tdHeader">Score</td>
        
      </tr> 
      <?php do { ?>
      <tr id="trContent">
        <td><?php echo $row_score['username']; ?></td>
        <td><a href="/Student/upload/<?php echo $row_score['upfile']; ?>" download><?php echo $row_score['upfile']; ?></td></td>
        <td><?php echo $row_score['DateIn']; ?></td>

        <td><form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
          <table align="center">
            <tr valign="baseline">
              <td nowrap="nowrap" align="right">Score:</td>
              <td><input type="text" name="score" value="<?php echo $row_score['score']; ?>" size="32" placeholder="Enter your student's grade"/></td>
            </tr>
          </table>
          
        <td><input type="submit" name="submit" value="Save" /></td>
<input type="hidden" name="id" value="<?php echo $row_score['id']; ?>" />
          <input type="hidden" name="username" value="<?php echo $row_score['username']; ?>" />
          <input type="hidden" name="actid" value="<?php echo $row_score['actid']; ?>" />
          <input type="hidden" name="actno" value="<?php echo $row_score['actno']; ?>" />
          <input type="hidden" name="DateIn" value="<?php echo $row_score['DateIn']; ?>" />
          <input type="hidden" name="DateOut" value="<?php echo $row_score['DateOut']; ?>" />
          <input type="hidden" name="teacher" value="<?php echo $row_score['teacher']; ?>" />
          <input type="hidden" name="upfile" value="<?php echo $row_score['upfile']; ?>" />
          <input type="hidden" name="MM_update" value="form1" />
          <input type="hidden" name="username" value="<?php echo $row_score['username']; ?>" />
        </form>
         
        
      </tr><?php } while ($row_score = mysql_fetch_assoc($score));?>
      </table>
      
     </td>
    
  </tr>
  <tr align="center">
        <td align="center" id="back"><a  href="javascript:history.back()">Go Back</a></td>
        </tr>
  
        
  <tr id="trFooter">
    <td height="21" id="tdFooter" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>

</body>
</html>
<?php
mysql_free_result($u);

mysql_free_result($my);

mysql_free_result($act);

mysql_free_result($actans);

mysql_free_result($actextra);

mysql_free_result($Recordset1);

mysql_free_result($score);
?>
