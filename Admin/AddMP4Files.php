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
  $insertSQL = sprintf("INSERT INTO mp4 (id, LesNo, title, `file`) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['id'], "int"),
                       GetSQLValueString($_POST['LesNo'], "text"),
                       GetSQLValueString($_POST['title'], "text"),
                       GetSQLValueString($_POST['file'], "text"));

  mysql_select_db($database_system, $system);
  $Result1 = mysql_query($insertSQL, $system) or die(mysql_error());
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
<title>Add Activities</title>

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
.yellow {
	color: #FF0;
}
</style>
</head>

<body>
<table width="1000" align="center" >
  <tr>
    <td width="996" height="83"><img src="../image/logo.png" width="1000" height="200" /></td>
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
      <a href="../Admin/Folder.php">Upload MP4 File</a>
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
    <td height="347" align="center" bgcolor="#FFFFFF"  background="../image/violet.png" ><table width="100%">
      <tr>
        <td width="407" height="21">&nbsp;</td>
        <td width="1197">&nbsp;</td>
      </tr>
      <tr>
        <td height="21">&nbsp;
          <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
            <table align="center">
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><input type="hidden" name="id" value="" size="32" />
                  <strong>Add New MP4 Lessons</strong></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Lesson No.:</td>
                <td><input type="text" name="LesNo" value="" size="25" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Title of Lesson:</td>
                <td><input type="text" name="title" value="" size="32" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Name of File:</td>
                <td><input type="text" name="file" value="" size="32" placeholder="ex. File.mp4"/></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><input type="submit" value="Insert record" /></td>
              </tr>
            </table>
            <p>
              <input type="hidden" name="MM_insert" value="form1" />
            </p>
          </form></td>
        <td align="center">&nbsp;LIST OF MP4 LESSONS
          <table width="600">
            <tr class="yellow">
              <td width="57" align="center" bgcolor="#333333">ID</td>
              <td width="125" align="center" bgcolor="#333333">Lesson No.</td>
              <td width="192" align="center" bgcolor="#333333">Title of Lesson</td>
              <td width="160" align="center" bgcolor="#333333">File Name</td>
              <td width="42" align="center" bgcolor="#333333">Del</td>
            </tr>
            <?php do { ?>
              <tr>
                <td align="center" bgcolor="#CCCCCC"><?php echo $row_mp4['id']; ?></td>
                <td align="center" bgcolor="#CCCCCC"><font size="2"><?php echo $row_mp4['LesNo']; ?></font></td>
                <td align="center" bgcolor="#CCCCCC"><font size="2"><?php echo $row_mp4['title']; ?></font></td>
                <td align="center" bgcolor="#CCCCCC"><font size="2"><?php echo $row_mp4['file']; ?></font></td>
                <td align="center" bgcolor="#CCCCCC"><img src="../image/icons/IcoJoy/action_delete.png" width="16" height="16" /></td>
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
          </p></td>
      </tr>
      <tr>
        <td height="21" bgcolor="#CCCCCC"><?php
echo "<b><u>Here are MP4 files (Copy paste to Name of File)<u></b>";
echo '<br><br>';
$path = "../MathLessonsMp4";
$dh = opendir($path);
$i=1;
while (($file = readdir($dh)) !== false) {
    if($file != "." && $file != ".." && $file != "index.php" && $file != ".htaccess" && $file != "error_log" && $file != "cgi-bin") {
        echo "<a href='$path/$file'> $file</a><br /><br />";
        $i++;
    }
}
closedir($dh);
?> </td>
        <td>&nbsp;</td>
      </tr>
    </table>
    <p>&nbsp;</p></td>
  </tr>
  <tr>
    <td height="21" bgcolor="#FFFFFF" background="" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($u);

mysql_free_result($mp4);
?>
