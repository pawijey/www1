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
  $insertSQL = sprintf("INSERT INTO actqst (id, ActID, ActNo, username, DateIn, DateOut, Teacher, sy, itemNo, question, answer, a, b, c, d) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['id'], "int"),
                       GetSQLValueString($_POST['ActID'], "text"),
                       GetSQLValueString($_POST['ActNo'], "text"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['DateIn'], "text"),
                       GetSQLValueString($_POST['DateOut'], "text"),
                       GetSQLValueString($_POST['Teacher'], "text"),
                       GetSQLValueString($_POST['sy'], "text"),
                       GetSQLValueString($_POST['itemNo'], "text"),
                       GetSQLValueString($_POST['question'], "text"),
                       GetSQLValueString($_POST['answer'], "text"),
                       GetSQLValueString($_POST['a'], "text"),
                       GetSQLValueString($_POST['b'], "text"),
                       GetSQLValueString($_POST['c'], "text"),
                       GetSQLValueString($_POST['d'], "text"));

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

$maxRows_act = 5;
$pageNum_act = 0;
if (isset($_GET['pageNum_act'])) {
  $pageNum_act = $_GET['pageNum_act'];
}
$startRow_act = $pageNum_act * $maxRows_act;

$colname_act = "-1";
if (isset($_GET['ActID'])) {
  $colname_act = $_GET['ActID'];
}
mysql_select_db($database_system, $system);
$query_act = sprintf("SELECT * FROM activities WHERE ActID = %s ORDER BY ActID ASC", GetSQLValueString($colname_act, "int"));
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

$maxRows_actQ = 5;
$pageNum_actQ = 0;
if (isset($_GET['pageNum_actQ'])) {
  $pageNum_actQ = $_GET['pageNum_actQ'];
}
$startRow_actQ = $pageNum_actQ * $maxRows_actQ;

$colname_actQ = "-1";
if (isset($_GET['ActID'])) {
  $colname_actQ = $_GET['ActID'];
}
mysql_select_db($database_system, $system);
$query_actQ = sprintf("SELECT * FROM actqst WHERE ActID = %s", GetSQLValueString($colname_actQ, "text"));
$query_limit_actQ = sprintf("%s LIMIT %d, %d", $query_actQ, $startRow_actQ, $maxRows_actQ);
$actQ = mysql_query($query_limit_actQ, $system) or die(mysql_error());
$row_actQ = mysql_fetch_assoc($actQ);

if (isset($_GET['totalRows_actQ'])) {
  $totalRows_actQ = $_GET['totalRows_actQ'];
} else {
  $all_actQ = mysql_query($query_actQ);
  $totalRows_actQ = mysql_num_rows($all_actQ);
}
$totalPages_actQ = ceil($totalRows_actQ/$maxRows_actQ)-1;

$colname_Activties = "-1";
if (isset($_GET['ActID'])) {
  $colname_Activties = $_GET['ActID'];
}
mysql_select_db($database_system, $system);
$query_Activties = sprintf("SELECT * FROM activities WHERE ActID = %s", GetSQLValueString($colname_Activties, "int"));
$Activties = mysql_query($query_Activties, $system) or die(mysql_error());
$row_Activties = mysql_fetch_assoc($Activties);
$totalRows_Activties = mysql_num_rows($Activties);

$colname_actQ2 = "-1";
if (isset($_GET['ActID'])) {
  $colname_actQ2 = $_GET['ActID'];
}
mysql_select_db($database_system, $system);
$query_actQ2 = sprintf("SELECT *, count(ActNo) as Tot FROM actqst WHERE ActID = %s", GetSQLValueString($colname_actQ2, "text"));
$actQ2 = mysql_query($query_actQ2, $system) or die(mysql_error());
$row_actQ2 = mysql_fetch_assoc($actQ2);
$totalRows_actQ2 = mysql_num_rows($actQ2);

$queryString_actQ = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_actQ") == false && 
        stristr($param, "totalRows_actQ") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_actQ = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_actQ = sprintf("&totalRows_actQ=%d%s", $totalRows_actQ, $queryString_actQ);

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
<title>Add Activities Questioner</title>

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
.yellow {
	color: #FFFF80;
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
    <td height="347" align="center" bgcolor="#FFFFFF"  background="../image/violet.png" ><table width="950">
      <tr>
        <td width="268" height="268" align="center"><form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
            <table align="center">
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><input type="hidden" name="id" value="" size="32" />
                  <input type="hidden" name="ActID" value="<?php echo $row_act['ActID']; ?>" size="32" />
                  <input type="hidden" name="ActNo" value="<?php echo $row_act['ActNo']; ?>" size="32" />
                  <input type="hidden" name="username" value="<?php echo $row_act['username']; ?>" size="32" />
                  <input type="hidden" name="DateIn" value="<?php echo $row_act['DateIn']; ?>" size="32" />
                  <input type="hidden" name="DateOut" value="<?php echo $row_act['DateOut']; ?>" size="32" />
                  <input type="hidden" name="Teacher" value="<?php echo $row_act['Teacher']; ?>" size="32" />
                  <input type="hidden" name="sy" value="<?php echo $row_act['sy']; ?>" size="32" />
                  <input type="hidden" name="itemNo" value="<?php echo $row_act['NoItems']; ?>" size="32" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><strong>Add 10 Activity Questioner</strong></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Question:</td>
                <td><textarea name="question" cols="25"></textarea></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Answer:</td>
                <td><input type="text" name="answer" value="" size="5" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Choices A:</td>
                <td><textarea name="a" cols="25"></textarea></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Choices B:</td>
                <td><textarea name="b" cols="25"></textarea></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Choices C:</td>
                <td><textarea name="c" cols="25"></textarea></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Choices D:</td>
                <td><textarea name="d" cols="25"></textarea></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td>
               
                <?php if ($row_act['NoItems'] = $row_actQ2['Tot']) echo '<input type="submit" value="Insert record" disabled="disabled"/>'; else  echo '<input type="submit" value="Insert record" />'; ?>
                </td>
              </tr>
            </table>
            <input type="hidden" name="MM_insert" value="form1" />
          </form></td>
        <td width="670" align="center"><p><strong><font size="5"><?php echo $row_act['Lesson']; ?></font></strong></p>
          <p>LIST OF QUESTIONER&nbsp;
          </p>
          <table width="600">
            <tr class="yellow">
              <td align="center" valign="middle" bgcolor="#333333">Questions</td>
              <td align="center" valign="middle" bgcolor="#333333">Answer</td>
              <td align="center" valign="middle" bgcolor="#333333">Choices A</td>
              <td align="center" valign="middle" bgcolor="#333333">Choices B</td>
              <td align="center" valign="middle" bgcolor="#333333">Choices C</td>
              <td align="center" valign="middle" bgcolor="#333333">Choices D</td>
            </tr>
            <?php do { ?>
              <tr>
                <td align="left" valign="middle" bgcolor="#CCCCCC"><?php echo $row_actQ['question']; ?></font>
                
<a href="http://localhost" 
target="popup" 
onclick="window.open('http://localhost/Admin/AddQActivitiesEdit.php?id=<?php echo $row_actQ['id'];?>','popup','width=600,height=600'); return false;">
<img src="../image/icons/IcoJoy/reply.png" width="16" height="16" /><font size="1">edit</font></a>
                
                
                </td>
                <td align="center" valign="middle" bgcolor="#CCCCCC"><?php $ans = $row_actQ['answer']; echo $ans; ?></td>
                <td align="center" valign="middle" bgcolor="#CCCCCC"><font size="2"><?php echo $row_actQ['a']; ?></font></td>
                <td align="center" valign="middle" bgcolor="#CCCCCC"><font size="2"><?php echo $row_actQ['b']; ?></font></td>
                <td align="center" valign="middle" bgcolor="#CCCCCC"><font size="2"><?php echo $row_actQ['c']; ?></font></td>
                <td align="center" valign="middle" bgcolor="#CCCCCC"><font size="2"><?php echo $row_actQ['d']; ?></font></td>
              </tr>
              <tr>
                <td align="center" valign="middle">&nbsp;</td>
                <td align="center" valign="middle">&nbsp;</td>
                <td align="center" valign="middle">&nbsp;</td>
                <td align="center" valign="middle">&nbsp;</td>
                <td align="center" valign="middle">&nbsp;</td>
                <td align="center" valign="middle">&nbsp;</td>
              </tr>
              <?php } while ($row_actQ = mysql_fetch_assoc($actQ)); ?>
        </table>
          <p>&nbsp;
Records  <?php echo min($startRow_actQ + $maxRows_actQ, $totalRows_actQ) ?> of <?php echo $totalRows_actQ ?>&nbsp;          </p>
          <table border="0">
            <tr>
              <td><?php if ($pageNum_actQ > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_actQ=%d%s", $currentPage, 0, $queryString_actQ); ?>">First</a>
                <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_actQ > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_actQ=%d%s", $currentPage, max(0, $pageNum_actQ - 1), $queryString_actQ); ?>">Previous</a>
                <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_actQ < $totalPages_actQ) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_actQ=%d%s", $currentPage, min($totalPages_actQ, $pageNum_actQ + 1), $queryString_actQ); ?>">Next</a>
                <?php } // Show if not last page ?></td>
              <td><?php if ($pageNum_actQ < $totalPages_actQ) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_actQ=%d%s", $currentPage, $totalPages_actQ, $queryString_actQ); ?>">Last</a>
                <?php } // Show if not last page ?></td>
            </tr>
        </table>
          </p></td>
      </tr>
    </table></td>
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

mysql_free_result($act);

mysql_free_result($actQ);

mysql_free_result($Activties);

mysql_free_result($actQ2);
?>
