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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO quiz (QzID, QzNo, DateIn, DateOut, NoItems, username, Teacher, sy, Lesson) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['QzID'], "int"),
                       GetSQLValueString($_POST['QzNo'], "text"),
                       GetSQLValueString($_POST['DateIn'], "text"),
                       GetSQLValueString($_POST['DateOut'], "text"),
                       GetSQLValueString($_POST['NoItems'], "text"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['Teacher'], "text"),
                       GetSQLValueString($_POST['sy'], "text"),
                       GetSQLValueString($_POST['Lesson'], "text"));

  mysql_select_db($database_system, $system);
  $Result1 = mysql_query($insertSQL, $system) or die(mysql_error());

  $insertGoTo = "AddQuizMsg.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
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
$query_qz = sprintf("SELECT * FROM quiz WHERE username = %s", GetSQLValueString($colname_qz, "text"));
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

mysql_select_db($database_system, $system);
$query_qstn1 = "SELECT * FROM stdqz";
$qstn1 = mysql_query($query_qstn1, $system) or die(mysql_error());
$row_qstn1 = mysql_fetch_assoc($qstn1);
$totalRows_qstn1 = mysql_num_rows($qstn1);

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
<title>Add Quiz Questions</title>


<link rel="stylesheet" type="text/css" href="tcal.css" />
	<script type="text/javascript" src="tcal.js"></script>
	<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
	<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
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
: #FF0;
}
</style>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
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
    <button class="dropbtn">Students 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="AddStudents.php" >Add New</a>
      <a href="StudList.php">Student List</a>
     
    </div>
  </div> 

 <div class="dropdown">
    <button class="dropbtn" style="color:gray; cursor: default;">Quiz 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="AddQuiz.php"style="color:gray; cursor: default;"><img src="/image/icons/PixelMixer/folder.ico" width="16" height="16" />Add Quiz</a>
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
    <td height="500" align="center"id="tdMain" ><p>&nbsp;</p>
      <table width="900">
        <tr>
          <td width="268">&nbsp;</td>
          <td width="620"id="tdLessonR"><b>LIST OF CREATED QUIZ</b></td>
        </tr>
        <tr>
          <td height="79" ><form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
            <table align="center">
              <tr valign="baseline">
                <td width="79" align="right" nowrap="nowrap">&nbsp;</td>
                <td width="500" id="tdLessonR"><B>ADD NEW QUIZ</B></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Quiz No:</td>
                <td><span id="sprytextfield1">
                  <input type="text" name="QzNo" value="" size="10" placeholder="Ex. Quiz 1."/>
                  <span class="textfieldRequiredMsg">*</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Date Start:</td>
                <td><span id="sprytextfield2">
                  <input name="DateIn" type="text"  value="" size="35" placeholder="Ex. June 01, 1999 08:00:00 AM/PM" />
                  <span class="textfieldRequiredMsg">*</span></span></td>
              </tr>
              <tr valign="baseline">
                <td align="right" nowrap="nowrap">Date End:</td>
                <td><span id="sprytextfield3">
                  <input name="DateOut" type="text"  value="" size="35" placeholder="Ex. June 01, 1999 08:00:00 AM/PM" />
                  <span class="textfieldRequiredMsg">*</span></span></td>
              </tr>
              <tr valign="baseline">
                <td align="right" nowrap="nowrap">Lesson Title:</td>
                <td><label for="Lesson"></label>
                  <span id="sprytextfield4">
                  <input name="Lesson" type="text" id="Lesson" size="25" />
                  <span class="textfieldRequiredMsg">*</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">School Year:</td>
                <td><span id="spryselect1">
                  <select name="sy" id="sy">
                    <option value="" <?php if (!(strcmp("", "."))) {echo "selected=\"selected\"";} ?>></option>
                    <option value="2022-2023" <?php if (!(strcmp("2022-2023", "."))) {echo "selected=\"selected\"";} ?>>2022-2023</option>
                    <option value="2023-2024" <?php if (!(strcmp("2023-2024", "."))) {echo "selected=\"selected\"";} ?>>2023-2024</option>
                    <option value="2024-2025" <?php if (!(strcmp("2024-2025", "."))) {echo "selected=\"selected\"";} ?>>2024-2025</option>
                    <option value="2025-2026" <?php if (!(strcmp("2025-2026", "."))) {echo "selected=\"selected\"";} ?>>2025-2026</option>
                    <option value="2026-2027" <?php if (!(strcmp("2026-2027", "."))) {echo "selected=\"selected\"";} ?>>2026-2027</option>
                    <option value="2027-2028" <?php if (!(strcmp("2027-2028", "."))) {echo "selected=\"selected\"";} ?>>2027-2028</option>
                    <option value="2028-2029" <?php if (!(strcmp("2028-2029", "."))) {echo "selected=\"selected\"";} ?>>2028-2029</option>
                    <option value="2029-2030" <?php if (!(strcmp("2029-2030", "."))) {echo "selected=\"selected\"";} ?>>2029-2030</option>
                  </select>
                  <span class="selectRequiredMsg">*</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><label for="sy">
                  <input type="hidden" name="QzID" value="" size="32" />
                  <input type="hidden" name="username" value="<?php echo $row_u['username']; ?>" size="32" />
                  <input type="hidden" name="Teacher" value="<?php echo $row_u['fn']," ", $row_u['mi']," ",$row_u['ln']; ?>" size="32" />
                  <input type="hidden" name="NoItems" value="10" size="10" />
                </label></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><input type="submit" value="Submit" /></td>
              </tr>
            </table>
            <input type="hidden" name="MM_insert" value="form1" />
          </form>
          </td>
          
          
          <td align="center" >&nbsp;
            <table width="492" cellspacing="10" cellpadding="5">
              <tr >
                <td width="100" align="center" id="tdCatLs">Quiz No.</td>
                <td width="200" align="center" id="tdCatLs">Date Start</td>
                <td width="160" align="center" id="tdCatLs">Date End</td>
                <td width="100" align="center" id="tdCatLs">No. Items</td>
                <td width="100" align="center" id="tdCatLs"><font size="2">Add Questions</font></td>
              </tr>
              <?php do { ?>
                <tr>
                  <td align="center" id="tdLsR"><strong><?php $Qznum = $row_qz['QzNo']; echo $Qznum; ?></strong></td>
                  <td align="center" id="tdLsR"><font size="2"><?php echo $row_qz['DateIn']; ?></font></td>
                  <td align="center" id="tdLsR"><font size="2"><?php echo $row_qz['DateOut']; ?></font></td>
                  <td align="center" id="tdLsR"><?php $no = $row_qz['NoItems']; echo $no ;?></td>
                  <td align="center" id="tdLsR"><a id="linkView"href="AddQuestions.php?QzID=<?php echo $row_qz['QzID'];?>"><img src="../image/icons/Pinvoke/sign_plus.png" width="16" height="16" /></a></td>
                </tr>
                <?php } while ($row_qz = mysql_fetch_assoc($qz)); ?>
          </table>
          <p>&nbsp;
Records  <?php echo min($startRow_qz + $maxRows_qz, $totalRows_qz) ?> of <?php echo $totalRows_qz ?>&nbsp;          </p>
          <table border="0">
            <tr>
              <td><?php if ($pageNum_qz > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_qz=%d%s", $currentPage, 0, $queryString_qz); ?>">First</a>
                  <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_qz > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_qz=%d%s", $currentPage, max(0, $pageNum_qz - 1), $queryString_qz); ?>">Previous</a>
                  <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_qz < $totalPages_qz) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_qz=%d%s", $currentPage, min($totalPages_qz, $pageNum_qz + 1), $queryString_qz); ?>">Next</a>
                  <?php } // Show if not last page ?></td>
              <td><?php if ($pageNum_qz < $totalPages_qz) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_qz=%d%s", $currentPage, $totalPages_qz, $queryString_qz); ?>">Last</a>
                  <?php } // Show if not last page ?></td>
            </tr>
        </table>
          </p></td>
        </tr>
      </table>
      <p>&nbsp;</p>
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
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3");
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1");
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4");
</script>
</body>
</html>
<?php
mysql_free_result($u);

mysql_free_result($my);

mysql_free_result($qz);

mysql_free_result($qstn1);
?>
