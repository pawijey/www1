<?php require_once('../Connections/system.php'); ?>
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
$query_u = sprintf("SELECT * FROM students WHERE username = %s", GetSQLValueString($colname_u, "text"));
$u = mysql_query($query_u, $system) or die(mysql_error());
$row_u = mysql_fetch_assoc($u);
$totalRows_u = mysql_num_rows($u);

$colname_qz1 = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_qz1 = $_SESSION['MM_Username'];
}
$colname2_qz1 = "-1";
if (isset($_GET['ActNo'])) {
  $colname2_qz1 = $_GET['ActNo'];
}
mysql_select_db($database_system, $system);
$query_qz1 = sprintf("SELECT * FROM activities WHERE username = %s and ActNo = %s ", GetSQLValueString($colname_qz1, "text"),GetSQLValueString($colname2_qz1, "text"));
$qz1 = mysql_query($query_qz1, $system) or die(mysql_error());
$row_qz1 = mysql_fetch_assoc($qz1);
$totalRows_qz1 = mysql_num_rows($qz1);

$maxRows_ans = 10;
$pageNum_ans = 0;
if (isset($_GET['pageNum_ans'])) {
  $pageNum_ans = $_GET['pageNum_ans'];
}
$startRow_ans = $pageNum_ans * $maxRows_ans;

$colname_ans = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_ans = $_SESSION['MM_Username'];
}
$colname1_ans = "-1";
if (isset($_GET['ActNo'])) {
  $colname1_ans = $_GET['ActNo'];
}
mysql_select_db($database_system, $system);
$query_ans = sprintf("SELECT *, IF (a = answer, '1', '0') as A FROM actanswer WHERE username = %s and ActNo = %s", GetSQLValueString($colname_ans, "text"),GetSQLValueString($colname1_ans, "text"));
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

$maxRows_ans2 = 1;
$pageNum_ans2 = 0;
if (isset($_GET['pageNum_ans2'])) {
  $pageNum_ans2 = $_GET['pageNum_ans2'];
}
$startRow_ans2 = $pageNum_ans2 * $maxRows_ans2;

$colname_ans2 = "-1";
if (isset($_GET['ActNo'])) {
  $colname_ans2 = $_GET['ActNo'];
}
$colname2_ans2 = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname2_ans2 = $_SESSION['MM_Username'];
}
mysql_select_db($database_system, $system);
$query_ans2 = sprintf("SELECT *,  sum(IF (a = answer, '1', '0')) as A FROM actanswer WHERE ActNo = %s and username = %s", GetSQLValueString($colname_ans2, "text"),GetSQLValueString($colname2_ans2, "text"));
$query_limit_ans2 = sprintf("%s LIMIT %d, %d", $query_ans2, $startRow_ans2, $maxRows_ans2);
$ans2 = mysql_query($query_limit_ans2, $system) or die(mysql_error());
$row_ans2 = mysql_fetch_assoc($ans2);

if (isset($_GET['totalRows_ans2'])) {
  $totalRows_ans2 = $_GET['totalRows_ans2'];
} else {
  $all_ans2 = mysql_query($query_ans2);
  $totalRows_ans2 = mysql_num_rows($all_ans2);
}
$totalPages_ans2 = ceil($totalRows_ans2/$maxRows_ans2)-1;

$colname_Recordset1 = "-1";
if (isset($_GET['ActNo'])) {
  $colname_Recordset1 = $_GET['ActNo'];
}
mysql_select_db($database_system, $system);
$query_Recordset1 = sprintf("SELECT * FROM actqst WHERE ActNo = %s", GetSQLValueString($colname_Recordset1, "text"));
$Recordset1 = mysql_query($query_Recordset1, $system) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

mysql_select_db($database_system, $system);
$query_les = "SELECT * FROM lesson";
$les = mysql_query($query_les, $system) or die(mysql_error());
$row_les = mysql_fetch_assoc($les);
$totalRows_les = mysql_num_rows($les);

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
<link rel="icon" href="../image/San Isidro logo.png">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Activity Results</title>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="/Student/CSS/Table_NavMainHeaderFooter.css" />
<link rel="stylesheet" type="text/css" href="/Student/CSS/TableContent.css" />
<style>

body,td,th {
	font-family: Georgia, "Times New Roman", Times, serif;
	font-size: 12px;
}
body {
	background-color: #CCC;
}
#trActCat{
	border-bottom: 1px solid green;
}

</style>
</head>

<body>
<table width="100%" align="center" >
<!-- Navbar starts here -->
  <tr>
    <td width="100%" height="150" id="tdLogo"></td>
  </tr>
  <tr>
    <td height="50"  id="NavBar">

 <h1 id="name" > Welcome, <?php echo $row_u['fn']; ?> !</h1>
  <a href="StudentPage.php" id="link" >Home</a>
  <a href="Profile.php" id="link">Profile</a>
  <a href="Lessons.php" id="link">Lessons</a>
 <a href="Quiz.php" id="link">Quiz</a>
 <a href="Activities.php" id="link" style="color:white;">Activities</a>
  
 
   <a href="<?php echo $logoutAction ?>" id="logoutbutton">Logout</a>
      </td>
  </tr>
  <tr>
    <td width="100%" height="100%" align="center" id="tdMain"><p>&nbsp;</p>
      <table width="90%">
        <tr>
        <td width="337" height="3" align="center" id="tdQuizR">ACTIVITY #<?php echo $row_ans['ActNo']; ?> RESULT</td>
        </tr>
      <tr>
        <td height="21">Date Started: <strong><?php echo $row_ans['DateIn']; ?></strong></td>
      </tr>
      <tr>
        <td height="21" width="100%">
        
        <table width="100%" align="center" cellspacing="20" cellpadding="5" border="0">
            <tr id="actCat">
              <td width="1000" align="center" id="tdResult">Question Number</td>
              <td align="center" id="tdResult">Your Answer</td>
              <td align="center" id="tdResult">Remarks</td>
              </tr>
            <?php do { ?>
              <tr>
                <td width="1000"align="center" id="qzCatRd"><?php echo $row_Recordset1['question']; ?></td>
                <td width="50" align="center" id="ans"><?php echo $row_ans['answer']; ?></td>
                <td width="60" align="center" id="remarks"><?php if($row_ans['A'] == '1') echo '<img src="../image/icons/Pinvoke/sign_check.png" width="16" height="16" />'; else echo '<img src="../image/icons/Pinvoke/sign_cross.png" width="16" height="16" />'; ?>                  </td>
                </tr>
              <?php } while ($row_ans = mysql_fetch_assoc($ans) and $row_Recordset1 = mysql_fetch_assoc($Recordset1)); ?>
          </table>
          </td>
        </tr>
      <tr>
        <td height="29">
        <table width="100%" align="center" style="text-align:center;font-size: 24px;">
            <?php do { ?>
              <tr id="actCat">
                <td width="50%" id="tdActC">Total Score:<strong>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $row_ans2['A']; ?> / <?php echo $row_ans2['itemNo']?></strong></td>
                <td width="50%" id="tdActC">Remarks:<strong>&nbsp;&nbsp;&nbsp;<?php if($row_ans2['A']<5) echo "Failed"; elseif ($row_ans2['A']>=5) echo "Passed";?>
                </strong></td>
              </tr>
              <?php } while ($row_ans2 = mysql_fetch_assoc($ans2)); ?>
          </table></td>
        </tr>
  </table>
    <a href="StudentPage.php")">Go Home</a> &nbsp;&nbsp;<a href="/Student/Lessons.php">Lessons</a></td>
  </tr>
  <
  <tr id="trFooter">
    <td height="21" id="tdFooter" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($u);

mysql_free_result($ans);

mysql_free_result($ans2);

mysql_free_result($Recordset1);

mysql_free_result($les);

mysql_free_result($qz1);
?>
