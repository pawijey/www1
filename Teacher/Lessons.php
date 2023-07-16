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

$maxRows_les = 5;
$pageNum_les = 0;
if (isset($_GET['pageNum_les'])) {
  $pageNum_les = $_GET['pageNum_les'];
}
$startRow_les = $pageNum_les * $maxRows_les;

$colname_les = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_les = $_SESSION['MM_Username'];
}
mysql_select_db($database_system, $system);
$query_les = sprintf("SELECT * FROM lesson WHERE username = %s ORDER BY LesNo ASC", GetSQLValueString($colname_les, "text"));
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
$queryString_my = sprintf("&totalRows_my=%d%s", $totalRows_my, $queryString_my);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="icon" href="../image/San Isidro logo.png">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Lessons</title>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="/Teacher/CSS/mainCSS.css">
<link rel="stylesheet" type="text/css" href="/Teacher/CSS/tableCSS.css">
<link rel="stylesheet" type="text/css" href="/Teacher/CSS/TableContent.css">
<link rel="stylesheet" type="text/css" href="/Teacher/CSS/lesson.css" />
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
    <button class="dropbtn" style="color:gray; cursor: default;">Lessons 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="AddLessons.php">Add New</a>
      <a href="Lessons.php" style="color:gray; cursor: default;"><img src="/image/icons/PixelMixer/folder.ico" width="16" height="16" />View Lessons</a>
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
    <td height="399" align="center" id="tdMain"><p>&nbsp;</p>
      <table width="729">
        <tr>
          <td width="721" id="tdLessonR"><b>LIST OF ADDED LESSONS:</b></td>
        </tr>
        <tr>
          <td height="133">
          <table width="725" align="center" cellspacing="10" style="border-collapse: collapse;">
              <tr id="actCat">
                <td width="203" align="center" id="tdCatLs" >Title</td>
                <td width="200" align="center" bgcolor="#333333" id="tdCatLs">Subject</td>
                <td width="112" align="center" bgcolor="#333333" id="tdCatLs">Lesson No.</td>
                <td width="153" align="center" bgcolor="#333333" id="tdCatLs">MP4</td>
                <td width="33" align="center" bgcolor="#333333" id="tdCatLs">Del</td>
              </tr>
              <?php do { ?>
                <tr style="border-bottom:1pt solid black;">
                  <td align="center" id="tdLsR"><?php echo $row_les['title']; ?></td>
                  <td align="center" id="tdLsR"><?php echo $row_les['subject']; ?></td>
                  <td align="center" id="tdLsR"><?php echo $row_les['LesNo']; ?></td>
                  <td align="center" id="tdLsR"><video width="150" height="100" controls>
                
                <source src="../MathLessonsMp4/Lessons/<?php echo $row_les['file']; ?>" type="video/ogg">
                
  </video></td>
                  <td align="center" id="tdLsR"><a href="/Teacher/DeleteLesson.php?id=<?php echo $row_les['id']; ?>" id="linkView"><img src="../image/icons/Pinvoke/sign_cross.png" width="16" height="16" /></a></td>
                </tr>
                <?php } while ($row_les = mysql_fetch_assoc($les)); ?>
          </table></td>
        </tr>
        <tr>
          <td>&nbsp;
Records  <?php echo min($startRow_les + $maxRows_les, $totalRows_les) ?> of <?php echo $totalRows_les ?></td>
        </tr>
        <tr>
          <td height="40"><table border="0">
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
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p></td>
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

mysql_free_result($les);
?>
