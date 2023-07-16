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
if (isset($_SESSION['MM_Username'])) {
  $colname_my = $_SESSION['MM_Username'];
}
mysql_select_db($database_system, $system);
$query_my = sprintf("SELECT * FROM mystudents WHERE username = %s ORDER BY ln ASC", GetSQLValueString($colname_my, "text"));
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

$colname_studentsacc = "-1";
if (isset($_GET['MM_Username'])) {
  $colname_studentsacc = $_GET['MM_Username'];
}
mysql_select_db($database_system, $system);
$query_studentsacc = sprintf("SELECT * FROM students WHERE username = %s ORDER BY id ASC", GetSQLValueString($colname_studentsacc, "text"));
$studentsacc = mysql_query($query_studentsacc, $system) or die(mysql_error());
$row_studentsacc = mysql_fetch_assoc($studentsacc);
$totalRows_studentsacc = mysql_num_rows($studentsacc);

$colname_studact = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_studact = $_SESSION['MM_Username'];
}
mysql_select_db($database_system, $system);
$query_studact = sprintf("SELECT * FROM mystudents WHERE username = %s", GetSQLValueString($colname_studact, "text"));
$studact = mysql_query($query_studact, $system) or die(mysql_error());
$row_studact = mysql_fetch_assoc($studact);
$totalRows_studact = mysql_num_rows($studact);

$colname_studqz = "-1";
if (isset($_SESSION['id'])) {
  $colname_studqz = $_SESSION['id'];
}
mysql_select_db($database_system, $system);
$query_studqz = sprintf("SELECT * FROM stdqz WHERE id = %s", GetSQLValueString($colname_studqz, "int"));
$studqz = mysql_query($query_studqz, $system) or die(mysql_error());
$row_studqz = mysql_fetch_assoc($studqz);
$totalRows_studqz = mysql_num_rows($studqz);

$colname_act = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_act = $_SESSION['MM_Username'];
}
mysql_select_db($database_system, $system);
$query_act = sprintf("SELECT *,  sum(IF (a = answer, '1', '0')) as A FROM actanswer WHERE username = %s GROUP BY ActNo", GetSQLValueString($colname_act, "text"));
$act = mysql_query($query_act, $system) or die(mysql_error());
$row_act = mysql_fetch_assoc($act);
$totalRows_act = mysql_num_rows($act);

$colname_Recordset1 = "-1";
if (isset($_GET['id'])) {
  $colname_Recordset1 = $_GET['id'];
}
mysql_select_db($database_system, $system);
$query_Recordset1 = sprintf("SELECT mystudents.id, actanswer.username FROM mystudents  INNER JOIN actanswer  ON mystudents.id = actanswer.username WHERE mystudents.id = %s ", GetSQLValueString($colname_Recordset1, "text"));
$Recordset1 = mysql_query($query_Recordset1, $system) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

$colname_Recordset2 = "-1";
if (isset($_SESSION['username'])) {
  $colname_Recordset2 = $_SESSION['username'];
}
mysql_select_db($database_system, $system);
$query_Recordset2 = sprintf("SELECT actanswer.*, mystudents.fn FROM actanswer LEFT JOIN mystudents ON actanswer.username = mystudents.fn WHERE actanswer.username = %s", GetSQLValueString($colname_Recordset2, "text"));
$Recordset2 = mysql_query($query_Recordset2, $system) or die(mysql_error());
$row_Recordset2 = mysql_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysql_num_rows($Recordset2);

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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Home</title>

<link rel="icon" href="../image/San Isidro logo.png">


<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="/Teacher/CSS/mainCSS.css">
<link rel="stylesheet" type="text/css" href="/Teacher/CSS/tableCSS.css">
<link rel="stylesheet" type="text/css" href="/Teacher/CSS/TableContent.css">

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
}
#tdContent{
	
}
.space {
	width:25%;
	height:50%;
	background-color:red;
	position:absolute;	
}
</style>
</head>

<body>
<table width="100%" height="100%" >
  <tr>
    <td width="100%" height="150" id="tdLogo"></td>
  </tr>
  <tr>
    <td height="70" id="NavBar">
    
<div class="navbar">
<h1 id="name1">Welcome, <?php echo $row_u['fn']; ?>!</h1>
  <a href="TeacherPage.php" id="link1"><font color="red">Home</font></a>
  <a href="Profile.php" id="link1">Profile</a>
 
  
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
      <a href="AddStudents.php" >Add New</a>
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
    <button class="dropbtn">Grades 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="ViewStudQz.php">Quizzes</a>
      <a href="ViewStudAct.php">Activities</a>
   </div>
  </div> 
   
   <a href="<?php echo $logoutAction ?>" id="logoutbutton1">Logout</a>
    </div>
  </div> 
</div></td>
  </tr>
  
  <tr>
    <td height="800"align="center" valign="top" id="tdMain" ><p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <table width="100%"align="center" cellpadding="5" cellspacing="10">
    
      <tr id="actCat">
      
        <td width="176" height="21" id="tdHeader">FULLNAME</td>
        <td width="133" id="tdHeader">Email</td>
                </tr>
        <?php do { ?>
      <tr id="trContent">
        <td height="21"><?php echo $row_my['fn']; ?><?php echo $row_my['mi']; ?><?php echo $row_my['ln']; ?></td>
        <td><?php echo $row_my['email']; ?></td>
                </tr>
      <?php } while ($row_my = mysql_fetch_assoc($my)); ?>
    </table>
   
      </td>
  </tr>
  <tr id="trFooter">
    <td height="21"id="tdFooter" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($u);

mysql_free_result($my);

mysql_free_result($studentsacc);

mysql_free_result($studact);

mysql_free_result($studqz);

mysql_free_result($act);

mysql_free_result($Recordset1);

mysql_free_result($Recordset2);
?>
