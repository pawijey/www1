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

$maxRows_st = 10;
$pageNum_st = 0;
if (isset($_GET['pageNum_st'])) {
  $pageNum_st = $_GET['pageNum_st'];
}
$startRow_st = $pageNum_st * $maxRows_st;

$colname_st = "0";

if (isset($_GET['search'])) {
  $colname_st = $_GET['search'];
}
mysql_select_db($database_system, $system);
$query_st = sprintf("SELECT * FROM students WHERE grade LIKE %s or fn  LIKE %s or ln  LIKE %s or grade  LIKE %s or id LIKE %s or adviser LIKE %s or teacher LIKE %s or adviser LIKE %s or sy LIKE %s", GetSQLValueString("%" . $colname_st . "%", "text"),GetSQLValueString("%" . $colname_st . "%", "text"),GetSQLValueString("%" . $colname_st . "%", "text"),GetSQLValueString("%" . $colname_st . "%", "text"),GetSQLValueString("%" . $colname_st . "%", "text"),GetSQLValueString("%" . $colname_st . "%", "text"),GetSQLValueString("%" . $colname_st . "%", "text"),GetSQLValueString("%" . $colname_st . "%", "text"),GetSQLValueString("%" . $colname_st . "%", "text"),GetSQLValueString("%" . $colname_st . "%", "text"),GetSQLValueString("%" . $colname_st . "%", "text"));
$query_limit_st = sprintf("%s LIMIT %d, %d", $query_st, $startRow_st, $maxRows_st);
$st = mysql_query($query_limit_st, $system) or die(mysql_error());
$row_st = mysql_fetch_assoc($st);

if (isset($_GET['totalRows_st'])) {
  $totalRows_st = $_GET['totalRows_st'];
} else {
  $all_st = mysql_query($query_st);
  $totalRows_st = mysql_num_rows($all_st);
}
$totalPages_st = ceil($totalRows_st/$maxRows_st)-1;

$queryString_st = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_st") == false && 
        stristr($param, "totalRows_st") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_st = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_st = sprintf("&totalRows_st=%d%s", $totalRows_st, $queryString_st);

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
<title>Add Students</title>

<link rel="icon" href="../image/San Isidro logo.png">

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="/Teacher/CSS/mainCSS.css">
<link rel="stylesheet" type="text/css" href="/Teacher/CSS/tableCSS.css">
<link rel="stylesheet" type="text/css" href="/Teacher/CSS/TableContent.css">
<link rel="stylesheet" type="text/css" href="/Teacher/CSS/lesson.css" />
<style>

body {
	background-color: #CCC;
}

</style>
</head>

<body>
<table width="100%" >
  <tr>
    <td width="100%" height="150" id="tdLogo"></td>
  </tr>
  <tr>
    <td height="70" id="NavBar">
    
<div class="navbar">
<h1 id="name1">Welcome, <?php echo $row_u['fn']; ?>!</h1>
  <a href="TeacherPage.php" id="link1" >Home</a>
  <a href="Profile.php" id="link1">Profile</a>
 
  
  <div class="dropdown">
    <button class="dropbtn" >Lessons 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="AddLessons.php">Add New</a>
      <a href="Lessons.php" >View Lessons</a>
     
    </div>
  </div> 
  
  <div class="dropdown">
    <button class="dropbtn" style="color:gray; cursor: default;">Students 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="AddStudents.php"style="color:gray; cursor: default;"><img src="/image/icons/PixelMixer/folder.ico" width="16" height="16" />Add New</a>
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
    <td height="449" align="center"id="tdMain"><p>&nbsp;</p>
      <table width="850">
      <tr>
        <td width="690" id="tdLessonR"><B>STUDENT DATABASE</B></td>
      </tr>
      <tr>
        <td>
        <form id="search" name="search" method="get" action="">
          <label for="search"></label>
          <input type="text" name="search" id="search" class="search-input"/>
          <input type="submit" name="button" id="button" value="Search" class="button-add"/>
        </form>
        </td>
      </tr>
      <tr>
        <td>
        <table width="850" cellspacing="10">
            <tr id="actCat">
              <td width="307" align="center" id="tdCatLs">Student Name</td>
              <td width="221" align="center" id="tdCatLs">Adviser</td>
              <td width="88" align="center" id="tdCatLs">Profile</td>

            </tr>
            <?php do { ?>
              <tr>
                <td align="center" id="tdLsR"><?php echo $row_st['fn']," ", $row_st['ln']," ", $row_st['mi']; ?></td>
                <td align="center" id="tdLsR"><?php echo $row_st['adviser']; ?></td>
                <td align="center" id="tdLsR"><img src="../Student/image/<?php echo $row_st['picture']; ?>" width="88" height="87" /></td>
                <td align="center" id="tdLsR"><a id="linkView"href=" AddStudentsMsg.php?id=<?php echo $row_st['id'];?>"><img src="../image/icons/Pinvoke/sign_plus.png" width="16" height="16" /></a></td>
              </tr>
              <?php } while ($row_st = mysql_fetch_assoc($st)); ?>
          </table></td>
      </tr>
      <tr>
        <td>&nbsp;
Records <?php echo ($startRow_st + 1) ?> to <?php echo min($startRow_st + $maxRows_st, $totalRows_st) ?> of <?php echo $totalRows_st ?></td>
      </tr>
      <tr>
        <td height="40"><table border="0">
          <tr>
              <td><?php if ($pageNum_st > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_st=%d%s", $currentPage, 0, $queryString_st); ?>">First</a>
                  <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_st > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_st=%d%s", $currentPage, max(0, $pageNum_st - 1), $queryString_st); ?>">Previous</a>
                  <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_st < $totalPages_st) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_st=%d%s", $currentPage, min($totalPages_st, $pageNum_st + 1), $queryString_st); ?>">Next</a>
                  <?php } // Show if not last page ?></td>
              <td><?php if ($pageNum_st < $totalPages_st) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_st=%d%s", $currentPage, $totalPages_st, $queryString_st); ?>">Last</a>
                  <?php } // Show if not last page ?></td>
            </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr id="trFooter">
    <td height="21" id="tdFooter">Allrights resserved @ SIES 2023</td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($u);

mysql_free_result($st);
?>
