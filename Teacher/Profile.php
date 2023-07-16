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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE teachers SET username=%s, password=%s, fn=%s, ln=%s, mi=%s, bday=%s, contact=%s, address=%s, pic=%s, subject=%s, sy=%s, UserType=%s, status=%s, dateReg=%s WHERE id=%s",
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['fn'], "text"),
                       GetSQLValueString($_POST['ln'], "text"),
                       GetSQLValueString($_POST['mi'], "text"),
                       GetSQLValueString($_POST['bday'], "text"),
                       GetSQLValueString($_POST['contact'], "text"),
                       GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString($_POST['pic'], "text"),
                       GetSQLValueString($_POST['subject'], "text"),
                       GetSQLValueString($_POST['sy'], "text"),
                       GetSQLValueString($_POST['UserType'], "text"),
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($_POST['dateReg'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_system, $system);
  $Result1 = mysql_query($updateSQL, $system) or die(mysql_error());

  $updateGoTo = "MsgUpdate.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
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
<title>Profile</title>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="/Teacher/CSS/mainCSS.css">
<link rel="stylesheet" type="text/css" href="/Teacher/CSS/tableCSS.css">
<link rel="stylesheet" type="text/css" href="/Teacher/CSS/updateCSS.css">
<style>

body {
	background-color: #CCC;
}

</style>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
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
<h1 id="name1">Welcome,  <?php echo $row_u['fn']; ?>!</h1>
  <a href="TeacherPage.php" id="link1">Home</a>
  <a href="Profile.php" id="link1"><font color="red">Profile</font></a>
 
  
  <div class="dropdown">
    <button class="dropbtn">Lessons 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="AddLessons.php">Add New</a>
      <a href="Lessons.php">View Lessons</a>
     
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
    <td height="100%" align="center" id="tdMain"><p>&nbsp;</p>
      <table width="100%" height="100%">
        <tr>
          <td height="100%" align="center" id="updateform">&nbsp;
            <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
              <table align="center" height="100%">
                <tr valign="baseline">
                  <td width="95" height="75" align="right" nowrap="nowrap">:</td>
                  <td width="304"><img src="image/<?php echo $row_u['pic']; ?>" width="119" height="118" /></td>
                </tr>
                <tr valign="baseline">
                  <td width="95" align="right" nowrap="nowrap" id="tdCat">Username:</td>
                  <td><input name="username" type="text" value="<?php echo htmlentities($row_u['username'], ENT_COMPAT, 'utf-8'); ?>" size="32" readonly="readonly" />
                  <img src="../image/icons/Pinvoke/lock.png" width="16" height="16" /></td>
                </tr>
                <tr valign="baseline">
                  <td rowspan="2" align="right" nowrap="nowrap" id="tdCat">Password:</td>
                  <td><span id="sprytextfield1">
                    <input type="password" name="password" value="<?php echo htmlentities($row_u['password'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                  <span class="textfieldRequiredMsg">A value is required.</span></span></td>
                </tr>
                <tr valign="baseline">
                  <td>&nbsp;</td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap="nowrap" id="tdCat">First Name:</td>
                  <td><span id="sprytextfield2">
                    <input type="text" name="fn" value="<?php echo htmlentities($row_u['fn'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                  <span class="textfieldRequiredMsg">*</span></span></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap="nowrap" id="tdCat">Last Name:</td>
                  <td><span id="sprytextfield3">
                    <input type="text" name="ln" value="<?php echo htmlentities($row_u['ln'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                  <span class="textfieldRequiredMsg">*</span></span></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap="nowrap" id="tdCat">Middle Initial:</td>
                  <td><span id="sprytextfield4">
                    <input type="text" name="mi" value="<?php echo htmlentities($row_u['mi'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                  <span class="textfieldRequiredMsg">*</span></span></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap="nowrap" id="tdCat">Birthday:</td>
                  <td><span id="sprytextfield5">
                   <input name="bday" type="text" class="tcal" value="<?php echo $row_u['bday']; ?>" size="10" readonly="readonly" />
                  <span class="textfieldRequiredMsg">*</span></span></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap="nowrap" id="tdCat">Contact No:</td>
                  <td><span id="sprytextfield6">
                    <input type="text" name="contact" value="<?php echo htmlentities($row_u['contact'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                  <span class="textfieldRequiredMsg">*</span></span></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap="nowrap" id="tdCat">Home Address:</td>
                  <td><span id="sprytextarea1">
                    <textarea name="address" cols="32"><?php echo htmlentities($row_u['address'], ENT_COMPAT, 'utf-8'); ?></textarea>
                  <span class="textareaRequiredMsg">*</span></span></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">&nbsp;</td>
                  <td><input type="hidden" name="pic" value="<?php echo htmlentities($row_u['pic'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                  <input type="hidden" name="subject" value="<?php echo htmlentities($row_u['subject'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                  <input type="hidden" name="sy" value="<?php echo htmlentities($row_u['sy'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                  <input type="hidden" name="UserType" value="<?php echo htmlentities($row_u['UserType'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                  <input type="hidden" name="status" value="<?php echo htmlentities($row_u['status'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                  <input type="hidden" name="dateReg" value="<?php echo htmlentities($row_u['dateReg'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">&nbsp;</td>
                  <td><input type="submit" value="Update record" id="button"  /></td>
                </tr>
              </table>
              <input type="hidden" name="id" value="<?php echo $row_u['id']; ?>" />
              <input type="hidden" name="MM_update" value="form1" />
              <input type="hidden" name="id" value="<?php echo $row_u['id']; ?>" />
          </form></td>
        </tr>
      </table>
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
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4");
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5");
var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6");
var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1");
</script>
</body>
</html>
<?php
mysql_free_result($u);

mysql_free_result($my);
?>
