<?php require_once('../Connections/system.php'); ?>
<?php require_once('../Connections/system.php'); ?>
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

$maxRows_qz = 5;
$pageNum_qz = 0;
if (isset($_GET['pageNum_qz'])) {
  $pageNum_qz = $_GET['pageNum_qz'];
}
$startRow_qz = $pageNum_qz * $maxRows_qz;

$colname_qz = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_qz = $_SESSION['MM_Username'];
}
mysql_select_db($database_system, $system);
$query_qz = sprintf("SELECT *, sum(IF (a = answer, '1', '0')) as A FROM stdqz WHERE username = %s GROUP BY QzNo", GetSQLValueString($colname_qz, "text"));
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

$colname_QzBar = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_QzBar = $_SESSION['MM_Username'];
}
mysql_select_db($database_system, $system);
$query_QzBar = sprintf("SELECT count(QzNo) as Qz, username FROM stdqz WHERE username = %s ", GetSQLValueString($colname_QzBar, "text"));
$QzBar = mysql_query($query_QzBar, $system) or die(mysql_error());
$row_QzBar = mysql_fetch_assoc($QzBar);
$totalRows_QzBar = mysql_num_rows($QzBar);

$maxRows_AcrBar = 5;
$pageNum_AcrBar = 0;
if (isset($_GET['pageNum_AcrBar'])) {
  $pageNum_AcrBar = $_GET['pageNum_AcrBar'];
}
$startRow_AcrBar = $pageNum_AcrBar * $maxRows_AcrBar;

$colname_AcrBar = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_AcrBar = $_SESSION['MM_Username'];
}
mysql_select_db($database_system, $system);
$query_AcrBar = sprintf("SELECT count(ActNo) as Act, username FROM actanswer WHERE username = %s ", GetSQLValueString($colname_AcrBar, "text"));
$query_limit_AcrBar = sprintf("%s LIMIT %d, %d", $query_AcrBar, $startRow_AcrBar, $maxRows_AcrBar);
$AcrBar = mysql_query($query_limit_AcrBar, $system) or die(mysql_error());
$row_AcrBar = mysql_fetch_assoc($AcrBar);

if (isset($_GET['totalRows_AcrBar'])) {
  $totalRows_AcrBar = $_GET['totalRows_AcrBar'];
} else {
  $all_AcrBar = mysql_query($query_AcrBar);
  $totalRows_AcrBar = mysql_num_rows($all_AcrBar);
}
$totalPages_AcrBar = ceil($totalRows_AcrBar/$maxRows_AcrBar)-1;

$maxRows_act = 11;
$pageNum_act = 0;
if (isset($_GET['pageNum_act'])) {
  $pageNum_act = $_GET['pageNum_act'];
}
$startRow_act = $pageNum_act * $maxRows_act;

$colname_act = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_act = $_SESSION['MM_Username'];
}
mysql_select_db($database_system, $system);
$query_act = sprintf("SELECT *,  sum(IF (a = answer, '1', '0')) as A FROM actanswer WHERE username = %s GROUP BY ActNo", GetSQLValueString($colname_act, "text"));
$query_limit_act = sprintf("%s LIMIT %d, %d", $query_act, $startRow_act, $maxRows_act);
$act = mysql_query($query_limit_act, $system) or die(mysql_error());
$row_act = mysql_fetch_assoc($act);

if (isset($_GET['totalRows_act'])) {
  $totalRows_act = $_GET['totalRows_act'];
} else {
  $all_act = mysql_query($query_act);
  $totalRows_act = mysql_num_rows($all_act);
}
$totalPages_act = ceil($totalRows_act/$maxRows_act)-1;

$colname_ans = "-1";
if (isset($_GET['QzNo'])) {
  $colname_ans = $_GET['QzNo'];
}
mysql_select_db($database_system, $system);
$query_ans = sprintf("SELECT * FROM stdqz WHERE QzNo = %s", GetSQLValueString($colname_ans, "text"));
$ans = mysql_query($query_ans, $system) or die(mysql_error());
$row_ans = mysql_fetch_assoc($ans);
$totalRows_ans = mysql_num_rows($ans);

$colname_ansact = "-1";
if (isset($_GET['ActNo'])) {
  $colname_ansact = $_GET['ActNo'];
}
mysql_select_db($database_system, $system);
$query_ansact = sprintf("SELECT * FROM actanswer WHERE ActNo = %s", GetSQLValueString($colname_ansact, "text"));
$ansact = mysql_query($query_ansact, $system) or die(mysql_error());
$row_ansact = mysql_fetch_assoc($ansact);
$totalRows_ansact = mysql_num_rows($ansact);

$colname_eactbar = "-1";
if (isset($_SESSION['Username'])) {
  $colname_eactbar = $_SESSION['Username'];
}
mysql_select_db($database_system, $system);
$query_eactbar = sprintf("SELECT count(actno) as Act, username FROM actup WHERE username = %s", GetSQLValueString($colname_eactbar, "text"));
$eactbar = mysql_query($query_eactbar, $system) or die(mysql_error());
$row_eactbar = mysql_fetch_assoc($eactbar);
$totalRows_eactbar = mysql_num_rows($eactbar);

$colname_eact = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_eact = $_SESSION['MM_Username'];
}
mysql_select_db($database_system, $system);
$query_eact = sprintf("SELECT * FROM actup WHERE username = %s", GetSQLValueString($colname_eact, "text"));
$eact = mysql_query($query_eact, $system) or die(mysql_error());
$row_eact = mysql_fetch_assoc($eact);
$totalRows_eact = mysql_num_rows($eact);

mysql_select_db($database_system, $system);
$query_actex = "SELECT * FROM actextra";
$actex = mysql_query($query_actex, $system) or die(mysql_error());
$row_actex = mysql_fetch_assoc($actex);
$totalRows_actex = mysql_num_rows($actex);

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

$queryString_act = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_act") == false && 
        stristr($param, "totalRows_act") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_act = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_act = sprintf("&totalRows_act=%d%s", $totalRows_act, $queryString_act);

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
$totalImg = 5;
$ActProg = ($row_AcrBar['Act'] / $totalImg);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="icon" href="../image/San Isidro logo.png">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Student | Home</title>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="/Student/CSS/Table_NavMainHeaderFooter.css" />
<link rel="stylesheet" type="text/css" href="/Student/CSS/TableContent.css" />
<style>





<style type="text/css">
body,td,th {
	font-family: Georgia, "Times New Roman", Times, serif;
	font-size: 12px;
}
body {
	background-color: #CCC;
}
</style>
</head>

<body>
<table width="100%" height="100%"align="center" >
<!-- Navbar starts here -->
  <tr>
    <td width="100%" height="150" id="tdLogo"></td>
  </tr>
  <tr>
    <td height="50" bgcolor="#FFFFFF" id="NavBar">

 <h1 id="name" > Welcome, <?php echo $row_u['fn']; ?> !</h1>
  <a href="StudentPage.php" id="link" style="color:red;cursor:default;">Home</a>
  <a href="Profile.php" id="link">Profile</a>
  <a href="Lessons.php" id="link">Lessons</a>
 <a href="Quiz.php" id="link">Quiz</a>
 <a href="Activities.php" id="link">Activities</a>
  
 
   <a href="<?php echo $logoutAction ?>" id="logoutbutton">Logout</a>
      
   

    </td>
  </tr>
<!-- Navbar ends here -->
  <tr>
  	<!-- Table main starts here -->
    <td width="100%" height="auto" align="center" id="tdMain">
    <!-----------------------------------------------------------------------Table Quizzes starts here ------------------------------------------------------------>
      <table width="40%" height="100%"id="tableQuiz">
        <tr>
          <td align="center" bordercolor="black">
          
		  <?php if ($row_QzBar['Qz'] == 0) echo '<img src="../image/starusbar/bar0.png" width="276" height="27" />';
		  elseif($row_QzBar['Qz'] <= 10) echo '<img src="../image/starusbar/bar1.png" width="276" height="27" />'; 
		  elseif ($row_QzBar['Qz'] <= 15)   echo '<img src="../image/starusbar/bar2.png" width="276" height="27" />' ; 
		  elseif ($row_QzBar['Qz'] <= 20)  echo '<img src="../image/starusbar/bar3.png" width="276" height="27" />' ; 
		  elseif ($row_QzBar['Qz'] <= 30)  echo '<img src="../image/starusbar/bar4.png" width="276" height="27" />';  
		  else echo '<img src="../image/starusbar/bar0.png" width="276" height="27" />'; ?>
          </td>
        </tr>
        <tr>
          <td width="596" align="left" id="tdQuizR"><strong>QUIZZES RESULTS</strong></td>
        </tr>
        <tr>
          <td height="20">
          <table width="100%" align="center" cellspacing="10" cellpadding="10">
              <tr id="qzCat">
                <td width="200" align="center" id="tdQzC">Quiz Number</td>
                <td width="276" align="center" id="tdQzC">Date Taken</td>
                <td width="200" align="center"id="tdQzC">Total Items</td>
                <td width="53" align="center"id="tdQzC">Scores</td>
                <td width="98" align="center"id="tdQzC">Remarks</td>
              </tr>
              <?php do { ?>
                <tr id="qzCatRd">
                  <td align="center"><strong><?php echo $row_qz['QzNo']; ?></strong></td>
                  <td align="center"><?php echo $row_qz['DateIn']; ?></td>
                  <td align="center"><?php echo $row_qz['itemNo']; ?></td>
                  <td align="center"><a href="Results.php?QzNo=<?php echo $row_qz['QzNo'];?>"><?php echo $row_qz['A']; ?></a></td>
                  <td align="center"><?php if($row_qz['A'] == 10) echo "Exellent" ; 
				  elseif ($row_qz['A'] >= 7) echo "Very Good" ;
				  elseif ($row_qz['A'] >= 5) echo "Good" ; 
				  elseif ($row_qz['A'] >= 3) echo "Fair" ; 
				  elseif ($row_qz['A'] >= 1) echo "Poor" ;
				  elseif ($row_qz['A'] == 0) echo "" 
				  
				  ;?></td>
                </tr>
                <?php } while ($row_qz = mysql_fetch_assoc($qz)); ?>
          </table></td>
        </tr>


      </table>
<!-----------------------------------------------------------------------Table Quizzes ends here ------------------------------------------------------------>
<!-----------------------------------------------------------------------Table Activities starts here ------------------------------------------------------------>
      <table width="40%" cellspacing="5" id="tableAct">
        <tr>
          <td align="center">
		  <?php if ($row_AcrBar['Act'] == 0) echo '<img src="../image/starusbar/bar0.png" width="276" height="27" />'; 
		  elseif ($row_AcrBar['Act'] <= 20) echo '<img src="../image/starusbar/bar1.png" width="276" height="27" />'; 
		  elseif ($row_AcrBar['Act'] <= 40)   echo '<img src="../image/starusbar/bar2.png" width="276" height="27" />' ; 
		  elseif ($row_AcrBar['Act'] <= 60)  echo '<img src="../image/starusbar/bar3.png" width="276" height="27" />'; 
		  elseif ($row_AcrBar['Act'] <= 81)  echo '<img src="../image/starusbar/bar4.png" width="276" height="27" />' ;  
		  else echo '<img src="../image/starusbar/bar0.png" width="276" height="27" />' ; ?></td>
        </tr>
        <tr>
          <td align="left" id="tdActR" ><strong>ACTIVITIES RESULTS</strong></td>
        </tr>
        <tr>
          <td>
          <table width="100%" align="center" cellspacing="10" cellpadding="10">
            <tr id="actCat">
              <td width="300" align="center" id="tdActC">Activity Number</td>
              <td width="200" align="center"id="tdActC">Date Taken</td>
              <td width="200" align="center" id="tdActC">Total Items</td>
              <td width="49" align="center" id="tdActC">Scores</td>
              <td width="97" align="center" id="tdActC">Remarks</td>
            </tr>
           <?php do { ?>
            <tr id="actCatRd">
              <td width="100" align="center" >
                  <?php echo $row_act['ActNo']; ?>
                  </td>
              <td align="center" ><?php echo $row_act['DateIn']; ?></td>
              <td align="center" ><?php echo $row_act['itemNo']; ?></td>
              <td align="center" >
              <a href="Results2.php?ActNo=<?php echo $row_act['ActNo'];?>"><?php echo $row_act['A']; ?></a>
              </td>
              <td align="center" ><?php if($row_act['A'] == 10) echo "Exellent"; 
			  elseif ($row_act['A'] >= 7) echo "Very Good"; 
			  elseif ($row_act['A'] >= 5) echo "Good";
			   elseif ($row_act['A'] >= 3) echo "Fair"; 
			   elseif ($row_act['A'] >= 1) echo "Poor"; 
			   elseif ($row_act['A'] < "") echo "";?></td>
            </tr>
           <?php } while ($row_act = mysql_fetch_assoc($act)); ?>
          </table></td>
        </tr>

       
      </table>
      <!-----------------------------------------------------------------------Table Quizzes ends here ------------------------------------------------------------>
      <p>&nbsp;</p>
    </td>
  	<!-- Table main ends here -->
  </tr>
  <tr id="trFooter">
    <td height="21" id="tdFooter">Allrights resserved @ SIES 2023</td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($u);

mysql_free_result($qz);

mysql_free_result($QzBar);

mysql_free_result($AcrBar);

mysql_free_result($act);

mysql_free_result($ans);

mysql_free_result($ansact);

mysql_free_result($eactbar);

mysql_free_result($eact);

mysql_free_result($actex);
?>
