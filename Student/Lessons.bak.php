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
$query_u = sprintf("SELECT * FROM students WHERE username = %s", GetSQLValueString($colname_u, "text"));
$u = mysql_query($query_u, $system) or die(mysql_error());
$row_u = mysql_fetch_assoc($u);
$totalRows_u = mysql_num_rows($u);

$maxRows_les = 5;
$pageNum_les = 0;
if (isset($_GET['pageNum_les'])) {
  $pageNum_les = $_GET['pageNum_les'];
}
$startRow_les = $pageNum_les * $maxRows_les;



$colname_les = "1";


if (isset($_GET['search'])) {
  $colname_les = $_GET['search'];
}
mysql_select_db($database_system, $system);
$query_les = sprintf("SELECT * FROM lesson WHERE id LIKE %s or teacher  LIKE %s or title  LIKE %s or subject  LIKE %s or file  LIKE %s or date  LIKE %s or LesNo  LIKE %s", GetSQLValueString("%" . $colname_les . "%", "text"),GetSQLValueString("%" . $colname_les . "%", "text"),GetSQLValueString("%" . $colname_les . "%", "text"),GetSQLValueString("%" . $colname_les . "%", "text"),GetSQLValueString("%" . $colname_les . "%", "text"),GetSQLValueString("%" . $colname_les . "%", "text"),GetSQLValueString("%" . $colname_les . "%", "text"));
$query_limit_les = sprintf("%s LIMIT %d, %d", $query_les, $startRow_les, $maxRows_les);
$les = mysql_query($query_limit_les, $system) or die(mysql_error());
$row_les = mysql_fetch_assoc($les);

if (isset($_GET['totalRows_les'])) {
  $totalRows_les = $_GET['totalRows_les'];
} else {
  $all_les = mysql_query($query_les);
  $totalRows_les = mysql_num_rows($all_les);
}
$totalPages_les = ceil($totalRows_les/$maxRows_les)-1;

mysql_select_db($database_system, $system);
$query_qz = "SELECT * FROM quiz";
$qz = mysql_query($query_qz, $system) or die(mysql_error());
$row_qz = mysql_fetch_assoc($qz);
$totalRows_qz = mysql_num_rows($qz);

mysql_select_db($database_system, $system);
$query_qstn = "SELECT * FROM quest";
$qstn = mysql_query($query_qstn, $system) or die(mysql_error());
$row_qstn = mysql_fetch_assoc($qstn);
$totalRows_qstn = mysql_num_rows($qstn);

$queryString_les = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_les") == false && 
        stristr($param, "totalRows_les") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_les = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_les = sprintf("&totalRows_les=%d%s", $totalRows_les, $queryString_les);

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
<title>Lessons</title>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/Student/CSS/Table_NavMainHeaderFooter.css" />
<link rel="stylesheet" href="/Student/CSS/lesson.css" />
<style>
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
<table width="100%" align="center" >
  <tr>
    <td width="100%" height="400" id="tdLogoLs"></td>
  </tr>
  <tr>
     <td height="50" id="NavBar">
     
  <h1 id="name" > Welcome! <?php echo $row_u['fn']; ?> </h1>
  <a id="link"href="StudentPage.php">Home</a>
  <a id="link"href="Profile.php">Profile</a>
  <a id="link"href="lessons.php" style="color:red;cursor:default;"">Lessons</a>
 <a id="link"href="Quiz.php">Quiz</a>
 <a id="link"href="Activities.php">Activities</a>
  
 
   <a href="<?php echo $logoutAction ?>" id="logoutbutton">Logout</a>
      
   
</td>
  </tr>
  <tr>
    <td height="415" align="center" id="tdMain"><p>&nbsp;</p>
      <table width="792" id="tableView">
      <tr>
        <td width="784" height="9" id="tdLessonR"><strong>LIST OF MP4 LESSONS</strong></td>
        </tr>
      <tr>
        <td height="10" id="tdLessonR2">
        <form id="search" name="search" method="get" action="">
          <label for="search"></label>
          <input type="text" name="search2" id="search" class="search-input" placeholder="Search Lesson"/>
          <input type="submit" name="button" id="button" value="Search" class="button-add"/>
           </form></td>
      </tr>
      <tr>
        <td height="54">
        <table width="800" align="center" cellpadding="10" cellspacing="15" >
            <tr>
              <td width="84" align="center" id="tdCatLs" style="padding-right:10px"><strong>Lesson No.</strong></td>
              <td width="274" align="center" id="tdCatLs"><strong>Title</strong></td>
              <td width="148" align="center" id="tdCatLs"><strong>Subject</strong></td>
              <td width="98" align="center" id="tdCatLs"><strong>Date Posted</strong></td>
              <td></td>
              </tr>
            <?php do { ?>
              <tr>
                <td align="center" id="tdLsR"><strong><?php echo $row_les['LesNo']; ?></strong></td>
                <td align="center"id="tdLsR"><?php echo $row_les['title']; ?></td>
                <td align="center"id="tdLsR"><?php echo $row_les['subject']; ?></td>
                <td align="center"id="tdLsR"><?php echo $row_les['date']; ?></td>
                <td align="center"><a href="/Student/Lessons/Lesson1.php?id=<?php echo $row_les['id'];?>">View</a></td>
                
                </tr>
              <?php } while ($row_les = mysql_fetch_assoc($les)); ?>
          </table></td>
        </tr>
      <tr>
        <td height="21">&nbsp;
Records <?php echo ($startRow_les + 1) ?> to <?php echo min($startRow_les + $maxRows_les, $totalRows_les) ?> of <?php echo $totalRows_les ?></td>
        </tr>
      <tr>
        <td height="44"><table border="0">
          <tr>
              <td><?php if ($pageNum_les > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_les=%d%s", $currentPage, 0, $queryString_les); ?>">First</a>
                  <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_les > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_les=%d%s", $currentPage, max(0, $pageNum_les - 1), $queryString_les); ?>">Previous</a>
                  <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_les < $totalPages_les) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_les=%d%s", $currentPage, min($totalPages_les, $pageNum_les + 1), $queryString_les); ?>">Next</a>
                  <?php } // Show if not last page ?></td>
              <td><?php if ($pageNum_les < $totalPages_les) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_les=%d%s", $currentPage, $totalPages_les, $queryString_les); ?>">Last</a>
                  <?php } // Show if not last page ?></td>
            </tr>
        </table></td>
      </tr>
      </table>
      <p>&nbsp;</p>
    <p>&nbsp;</p></td>
  </tr>
  <tr id="trFooter">
    <td height="15" id="tdFooter" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($u);

mysql_free_result($les);

mysql_free_result($qz);

mysql_free_result($qstn);
?>
