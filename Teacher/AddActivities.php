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
  $insertSQL = sprintf("INSERT INTO activities (ActID, ActNo, DateIn, DateOut, NoItems, username, Teacher, sy, Lesson) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['ActID'], "int"),
                       GetSQLValueString($_POST['ActNo'], "text"),
                       GetSQLValueString($_POST['DateIn'], "text"),
                       GetSQLValueString($_POST['DateOut'], "text"),
                       GetSQLValueString($_POST['NoItems'], "text"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['Teacher'], "text"),
                       GetSQLValueString($_POST['sy'], "text"),
                       GetSQLValueString($_POST['Lesson'], "text"));

  mysql_select_db($database_system, $system);
  $Result1 = mysql_query($insertSQL, $system) or die(mysql_error());

  $insertGoTo = "addActMsg.php";
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

$maxRows_Act = 5;
$pageNum_Act = 0;
if (isset($_GET['pageNum_Act'])) {
  $pageNum_Act = $_GET['pageNum_Act'];
}
$startRow_Act = $pageNum_Act * $maxRows_Act;

$colname_Act = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_Act = $_SESSION['MM_Username'];
}
mysql_select_db($database_system, $system);
$query_Act = sprintf("SELECT * FROM activities WHERE username = %s", GetSQLValueString($colname_Act, "text"));
$query_limit_Act = sprintf("%s LIMIT %d, %d", $query_Act, $startRow_Act, $maxRows_Act);
$Act = mysql_query($query_limit_Act, $system) or die(mysql_error());
$row_Act = mysql_fetch_assoc($Act);

if (isset($_GET['totalRows_Act'])) {
  $totalRows_Act = $_GET['totalRows_Act'];
} else {
  $all_Act = mysql_query($query_Act);
  $totalRows_Act = mysql_num_rows($all_Act);
}
$totalPages_Act = ceil($totalRows_Act/$maxRows_Act)-1;

$queryString_Act = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_Act") == false && 
        stristr($param, "totalRows_Act") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_Act = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_Act = sprintf("&totalRows_Act=%d%s", $totalRows_Act, $queryString_Act);

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
<title>Add Activities</title>

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
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<script src="../SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>
</head>




<body>

<link rel="stylesheet" type="text/css" href="tcal.css" />
	<script type="text/javascript" src="tcal.js"></script> 




<table width="100%" >
  <tr>
    <td width="100%" height="150" id="tdLogo"></td>
  </tr>
  <tr>
    <td height="70" id="NavBar">
    
<div class="navbar">
<h1 id="name1">Welcome, <?php echo $row_u['fn']; ?>!</h1>  <a href="TeacherPage.php" id="link1" >Home</a>
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
      <a href="AddQuiz.php">Add Quiz</a>
      <a href="AddActivities.php"style="color:gray; cursor: default;"><img src="/image/icons/PixelMixer/folder.ico" width="16" height="16" />Add Activities</a>
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
</div>
</td>
  </tr>
  
  
  <tr>
    <td height="800" align="center" id="tdMain" >
    <table width="962">
      <tr>
        <td width="268" height="21">&nbsp;</td>
        <td width="682"id="tdLessonR">LIST OF YOUR CREATED ACTIVITIES</td>
      </tr>
      <tr>
        <td height="21">&nbsp;
          <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
            <table align="left">
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><strong><font size="4">Add New Activity</font></strong></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Activity No:</td>
                <td><span id="sprytextfield1">
                <input type="text" name="ActNo" value="" size="10" />
                <span class="textfieldRequiredMsg">*</span><span class="textfieldInvalidFormatMsg"><font size="1">Number</font></span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">DateStarted:</td>
                <td><span id="sprytextfield2">
                <input type="text" name="DateIn" value="" class="tcal" size="10" placeholder="mm/dd/yyyy"/>
                <span class="textfieldRequiredMsg">*</span><span class="textfieldInvalidFormatMsg"><font size="1">Invalid</font></span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">DateEnd:</td>
                <td><span id="sprytextfield3">
                <input type="text" name="DateOut" value="" class="tcal" size="10" placeholder="mm/dd/yyyy"/>
                <span class="textfieldRequiredMsg">*</span><span class="textfieldInvalidFormatMsg"><font size="1">Invalid</font></span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">No of Items:</td>
                <td><span id="sprytextfield4">
                <input type="text" name="NoItems" value="" size="10" />
                <span class="textfieldRequiredMsg">*</span><span class="textfieldInvalidFormatMsg"><font size="1">Number</font></span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">School Year:</td>
                <td><label for="sy"></label>
                  <span id="spryselect1">
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
                <td nowrap="nowrap" align="right">Lesson Name:</td>
                <td><span id="sprytextarea1">
                  <textarea name="Lesson" cols="20"></textarea>
                  <span class="textareaRequiredMsg">*</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><input type="hidden" name="username" value="<?php echo $row_u['username']; ?>" size="32" />
                  <input type="hidden" name="Teacher" value="<?php echo $row_u['fn']," ",$row_u['mi'],"  ", $row_u['ln']; ?>" size="32" />
                  <input type="hidden" name="ActID" value="" size="32" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><input type="submit" value="Insert record" />
                  <input type="reset" name="Reset" id="button" value="Reset" /></td>
              </tr>
            </table>
            <input type="hidden" name="MM_insert" value="form1" />
          </form>
          <p>&nbsp;</p></td>
        <td align="center"><p><strong></strong></p>
          <table width="800" cellspacing="10">
            <tr class="yellow">
              <td width="49" align="center" id="tdCatLs">Act.ID</td>
              <td width="54" align="center" id="tdCatLs">Act.No</td>
              <td width="87" align="center" id="tdCatLs">Date-Start</td>
              <td width="89" align="center" id="tdCatLs">Date End</td>
              <td width="51" align="center" id="tdCatLs">No. of Items</td>
              <td width="64" align="center" id="tdCatLs">S.Y.</td>
              <td width="126" align="center" id="tdCatLs">Lesson</td>
              <td width="21" align="center" id="tdCatLs"><img src="../image/icons/Pinvoke/paste.png" width="16" height="16" /></td>
            </tr>
            <?php do { ?>
              <tr>
                <td align="center" bgcolor="#CCCCCC"><?php $ActID = $row_Act['ActID']; echo $ActID; ?></td>
                <td align="center" bgcolor="#CCCCCC"><?php $ActNo = $row_Act['ActNo']; echo $ActNo; ?></td>
                <td align="center" bgcolor="#CCCCCC"><font size="2"><?php echo $row_Act['DateIn']; ?></font></td>
                <td align="center" bgcolor="#CCCCCC"><font size="2"><?php echo $row_Act['DateOut']; ?></font></td>
                <td align="center" bgcolor="#CCCCCC"><font size="2"><?php $NoItem = $row_Act['NoItems']; echo $NoItem; ?></font></td>
                <td align="center" bgcolor="#CCCCCC"><font size="2"><?php echo $row_Act['sy']; ?></font></td>
                <td align="center" bgcolor="#CCCCCC"><font size="2"><?php echo $row_Act['Lesson']; ?></font></td>
                <td align="center" bgcolor="#CCCCCC"><a href="AddActQstn.php?ActID=<?php echo $row_Act['ActID'];?>"><img src="../image/icons/Pinvoke/pencil2.png" width="16" height="16" /></a></td>
              </tr>
              <?php } while ($row_Act = mysql_fetch_assoc($Act)); ?>
          </table>
          <p>&nbsp;
Records   <?php echo min($startRow_Act + $maxRows_Act, $totalRows_Act) ?> of <?php echo $totalRows_Act ?> </p>
          <p>&nbsp;</p>
          <a href="<?php printf("%s?pageNum_Act=%d%s", $currentPage, 0, $queryString_Act); ?>">First</a><a href="<?php printf("%s?pageNum_Act=%d%s", $currentPage, min($totalPages_Act, $pageNum_Act + 1), $queryString_Act); ?>">Next</a><a href="<?php printf("%s?pageNum_Act=%d%s", $currentPage, max(0, $pageNum_Act - 1), $queryString_Act); ?>">Previous</a><a href="<?php printf("%s?pageNum_Act=%d%s", $currentPage, $totalPages_Act, $queryString_Act); ?>">Last</a></td>
      </tr>
      <tr>
        <td height="21">&nbsp;</td>
        <td>&nbsp;</td>
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
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "integer");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "date", {format:"mm/dd/yyyy"});
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "date", {format:"mm/dd/yyyy"});
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "integer");
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1");
var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1");
</script>
</body>
</html>
<?php
mysql_free_result($u);

mysql_free_result($my);

mysql_free_result($Act);
?>
