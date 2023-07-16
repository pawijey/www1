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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO lesson (id, username, teacher, title, subject, `file`, `date`, LesNo) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['id'], "int"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['teacher'], "text"),
                       GetSQLValueString($_POST['title'], "text"),
                       GetSQLValueString($_POST['subject'], "text"),
                       GetSQLValueString($_POST['file'], "text"),
                       GetSQLValueString($_POST['date'], "text"),
                       GetSQLValueString($_POST['LesNo'], "text"));

  mysql_select_db($database_system, $system);
  $Result1 = mysql_query($insertSQL, $system) or die(mysql_error());

  $insertGoTo = "AddMsg.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

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
$query_les = sprintf("SELECT * FROM lesson WHERE username = %s", GetSQLValueString($colname_les, "text"));
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

$maxRows_mp4 = 5;
$pageNum_mp4 = 0;
if (isset($_GET['pageNum_mp4'])) {
  $pageNum_mp4 = $_GET['pageNum_mp4'];
}
$startRow_mp4 = $pageNum_mp4 * $maxRows_mp4;

mysql_select_db($database_system, $system);
$query_mp4 = "SELECT * FROM mp4";
$query_limit_mp4 = sprintf("%s LIMIT %d, %d", $query_mp4, $startRow_mp4, $maxRows_mp4);
$mp4 = mysql_query($query_limit_mp4, $system) or die(mysql_error());
$row_mp4 = mysql_fetch_assoc($mp4);

if (isset($_GET['totalRows_mp4'])) {
  $totalRows_mp4 = $_GET['totalRows_mp4'];
} else {
  $all_mp4 = mysql_query($query_mp4);
  $totalRows_mp4 = mysql_num_rows($all_mp4);
}
$totalPages_mp4 = ceil($totalRows_mp4/$maxRows_mp4)-1;

$colname_mp42 = "-1";
if (isset($_GET['id'])) {
  $colname_mp42 = $_GET['id'];
}
mysql_select_db($database_system, $system);
$query_mp42 = sprintf("SELECT * FROM mp4 WHERE id = %s", GetSQLValueString($colname_mp42, "int"));
$mp42 = mysql_query($query_mp42, $system) or die(mysql_error());
$row_mp42 = mysql_fetch_assoc($mp42);
$totalRows_mp42 = mysql_num_rows($mp42);

$queryString_mp4 = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_mp4") == false && 
        stristr($param, "totalRows_mp4") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_mp4 = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_mp4 = sprintf("&totalRows_mp4=%d%s", $totalRows_mp4, $queryString_mp4);

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
<title>Add Lessons</title>

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
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
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
      <a href="AddLessons.php"style="color:gray; cursor: default;"><img src="/image/icons/PixelMixer/folder.ico" width="16" height="16" />Add New</a>
      <a href="Lessons.php" >View Lessons</a>
     
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
    <td height="100%" align="center" id="tdMain" >
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <table width="900" height="100%">
      <tr>
        <td width="420" height="21" align="center" id="tdLessonR">LIST OF MATH LESSONS:</td>
        <td width="468" align="center"></p></td>
      </tr>
      <tr>
        <td height="228" align="center">
        <table width="600" align="center" cellspacing="10">
          <tr id="actCat">
            <td width="88" align="center" id="tdCatLs">Lesson Number</td>
            <td width="200" align="center" id="tdCatLs">Lesson Title</td>
            <td width="77" align="center" id="tdCatLs">File</td>
            <td width="27" align="center" id="tdCatLs">Add</td>
            </tr>
          <?php do { ?>
            <tr>
              <td align="center" id="tdLsR"><?php echo $row_mp4['LesNo']; ?></td>
              <td align="center" id="tdLsR"><font size="2"><?php echo $row_mp4['title']; ?></font></td>
              <td align="center" id="tdLsR"><font size="2"><video width="150" height="100" controls>
                
                <source src="../MathLessonsMp4/Lessons/<?php echo $row_mp4['file']; ?>" type="video/ogg">
                
              </video></font></td>
              <td align="center" id="tdLsR1"><a id="linkView"href="AddLessons.php?id=<?php echo $row_mp4['id'];?>"><img src="../image/icons/Pinvoke/sign_plus.png" width="16" height="16" /></a></td>
              </tr>
            <?php } while ($row_mp4 = mysql_fetch_assoc($mp4)); ?>
          </table>
          <p>&nbsp;
            Records <?php echo ($startRow_mp4 + 1) ?> to <?php echo min($startRow_mp4 + $maxRows_mp4, $totalRows_mp4) ?> of <?php echo $totalRows_mp4 ?>&nbsp;          </p>
          <table border="0">
            <tr>
              <td><?php if ($pageNum_mp4 > 0) { // Show if not first page ?>
                <a href="<?php printf("%s?pageNum_mp4=%d%s", $currentPage, 0, $queryString_mp4); ?>">First</a>
                <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_mp4 > 0) { // Show if not first page ?>
                <a href="<?php printf("%s?pageNum_mp4=%d%s", $currentPage, max(0, $pageNum_mp4 - 1), $queryString_mp4); ?>">Previous</a>
                <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_mp4 < $totalPages_mp4) { // Show if not last page ?>
                <a href="<?php printf("%s?pageNum_mp4=%d%s", $currentPage, min($totalPages_mp4, $pageNum_mp4 + 1), $queryString_mp4); ?>">Next</a>
                <?php } // Show if not last page ?></td>
              <td><?php if ($pageNum_mp4 < $totalPages_mp4) { // Show if not last page ?>
                <a href="<?php printf("%s?pageNum_mp4=%d%s", $currentPage, $totalPages_mp4, $queryString_mp4); ?>">Last</a>
                <?php } // Show if not last page ?></td>
              </tr>
          </table>
          </td>
          
          
          
        <td width="468" align="center" >&nbsp;
          <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
            <table align="center">
              <tr valign="baseline">
                <td rowspan="2" align="right" nowrap="nowrap">&nbsp;</td>
                <td id="tdLessonR"><strong>Add MP4 Lessons</strong></td>
              </tr>
              <tr valign="baseline">
                <td>&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Title:</td>
                <td><strong><?php echo $row_mp42['title']; ?></strong></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Lesson No.</td>
                <td><strong><font size="5"><?php echo $row_mp42['LesNo']; ?></font></strong></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Subject:</td>
                <td><span id="sprytextfield1">
                  <input type="text" name="subject" value="" size="32" placeholder="Type Subject" />
                  <span class="textfieldRequiredMsg">*</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><input type="hidden" name="username" value="<?php echo $row_u['username']; ?>" size="32" />
                  <input type="hidden" name="teacher" value="<?php echo $row_u['fn']," ",$row_u['mi']," ",$row_u['ln']; ?>" size="32" />
                  <input type="hidden" name="title" value="<?php echo $row_mp42['title']; ?>" size="32" />
                  <input type="hidden" name="file" value="<?php echo $row_mp42['file']; ?>" size="32" />
                  <input type="hidden" name="date" value="<?php date_default_timezone_set('Asia/Manila');echo date("m-d-Y"); ?>" size="32" />
                  <input type="hidden" name="LesNo" value="<?php echo $row_mp42['LesNo']; ?>" size="32" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><input type="submit" value="Add Submit" name="submit"/></td>
              </tr>
            </table>
            <input type="hidden" name="id" value="" />
            <input type="hidden" name="MM_insert" value="form1" />
          </form>
          <p>&nbsp;</p></td>
        </tr>
  </table>
    <p>&nbsp;</p>
    <p>&nbsp;</p></td>
  </tr>
  <tr id="trFooter">
    <td height="21" id="tdFooter" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
hidesub = document.getElementsByName("file");
php = '<?php echo $row_les['file'];?>';

if (hidesub.value == php)
{
	document.getElementsByName("submit").style.visibility = "hidden";
}
else 
{
	document.getElementByName{"submit").style.visibility = "visible";
	}
	
</script>

</body>
</html>
<?php
mysql_free_result($u);

mysql_free_result($my);

mysql_free_result($les);

mysql_free_result($mp4);

mysql_free_result($mp42);
?>
