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
  $insertSQL = sprintf("INSERT INTO teachers (id, username, password, fn, ln, mi, bday, contact, address, pic, subject, sy, UserType, status, dateReg) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['id'], "int"),
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
                       GetSQLValueString($_POST['dateReg'], "text"));

  mysql_select_db($database_system, $system);
  $Result1 = mysql_query($insertSQL, $system) or die(mysql_error());

  $insertGoTo = "AddTeacherMsg.php";
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
$query_u = sprintf("SELECT * FROM `admin` WHERE username = %s", GetSQLValueString($colname_u, "text"));
$u = mysql_query($query_u, $system) or die(mysql_error());
$row_u = mysql_fetch_assoc($u);
$totalRows_u = mysql_num_rows($u);

$maxRows_t = 5;
$pageNum_t = 0;
if (isset($_GET['pageNum_t'])) {
  $pageNum_t = $_GET['pageNum_t'];
}
$startRow_t = $pageNum_t * $maxRows_t;

mysql_select_db($database_system, $system);
$query_t = "SELECT * FROM teachers ORDER BY id DESC";
$query_limit_t = sprintf("%s LIMIT %d, %d", $query_t, $startRow_t, $maxRows_t);
$t = mysql_query($query_limit_t, $system) or die(mysql_error());
$row_t = mysql_fetch_assoc($t);

if (isset($_GET['totalRows_t'])) {
  $totalRows_t = $_GET['totalRows_t'];
} else {
  $all_t = mysql_query($query_t);
  $totalRows_t = mysql_num_rows($all_t);
}
$totalPages_t = ceil($totalRows_t/$maxRows_t)-1;

$queryString_t = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_t") == false && 
        stristr($param, "totalRows_t") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_t = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_t = sprintf("&totalRows_t=%d%s", $totalRows_t, $queryString_t);

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
<title>Add New Teacher</title>

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
.yg {
	color: #0F0;
}
</style>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<script src="../SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>
<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
</head>

<body>
<link rel="stylesheet" type="text/css" href="tcal.css" />
<script type="text/javascript" src="tcal.js"></script> 



<table width="1000" align="center" >
  <tr>
    <td width="996" height="83"><img src="../image/logo.jpg" width="1000" height="200" /></td>
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
      <a href="../Admin/Lessons.php">View Lessons</a>
     
    </div>
  </div> 
  <div class="dropdown">
    <button class="dropbtn">User Account
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="../Admin/AddStudents.php">Add Students</a>
      <a href="../Admin/AddTeacher.php">Add Teacher</a>

    </div>
  </div> 
 <div class="dropdown">
    <button class="dropbtn">Quiz 
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
    <td height="307" align="center" bgcolor="#FFFFFF"  background="../image/violet.png" ><table width="950">
      <tr>
        <td width="330" height="372"><form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
          <table width="348" align="center">
            <tr valign="baseline">
              <td colspan="2" align="left" valign="middle" nowrap="nowrap"><strong>NEW TEACHER'S ACCOUNT</strong></td>
              </tr>
            <tr valign="baseline">
              <td width="95" align="right" nowrap="nowrap">Username:</td>
              <td width="213"><span class="textfieldRequiredMsg">*</span><span class="textfieldRequiredMsg">*</span>
                <label for="username"></label>
                <span id="sprytextfield1">
                <input type="text" name="username" id="username" />
                <span class="textfieldRequiredMsg">*</span></span></td>
              </tr>
            <tr valign="baseline">
              <td nowrap="nowrap" align="right">Password:</td>
              <td><span id="sprytextfield2">
                <input name="password" type="text" size="25" placeholder="Type temp password" />
                <span class="textfieldRequiredMsg">*</span></span></td>
              </tr>
            <tr valign="baseline">
              <td nowrap="nowrap" align="right">First Name:</td>
              <td><span id="sprytextfield3">
                <input type="text" name="fn" value="" size="25" />
                <span class="textfieldRequiredMsg">*</span></span></td>
              </tr>
            <tr valign="baseline">
              <td nowrap="nowrap" align="right">Last Name:</td>
              <td><span id="sprytextfield4">
                <input type="text" name="ln" value="" size="25" />
                <span class="textfieldRequiredMsg">*</span></span></td>
              </tr>
            <tr valign="baseline">
              <td nowrap="nowrap" align="right">Middle Name:</td>
              <td><span id="sprytextfield5">
                <input type="text" name="mi" value="" size="25" />
                <span class="textfieldRequiredMsg">*</span></span></td>
              </tr>
            <tr valign="baseline">
              <td nowrap="nowrap" align="right">Birthday:</td>
              <td><span id="sprytextfield6">
              <input name="bday" type="text" class="tcal" value="" size="10" placeholder="mm/dd/yyyy" />
              <span class="textfieldRequiredMsg">*</span><span class="textfieldInvalidFormatMsg">?</span></span></td>
              </tr>
            <tr valign="baseline">
              <td nowrap="nowrap" align="right">Contact No:</td>
              <td><span id="sprytextfield7">
              <input type="text" name="contact" value="" size="25" placeholder="11 Max number" />
              <span class="textfieldRequiredMsg">*</span><span class="textfieldInvalidFormatMsg">?</span><span class="textfieldMaxCharsMsg">?</span></span></td>
              </tr>
            <tr valign="baseline">
              <td nowrap="nowrap" align="right">Home Address:</td>
              <td><span id="sprytextarea1">
                <textarea name="address" cols="25"></textarea>
                <span class="textareaRequiredMsg">*</span></span></td>
              </tr>
            <tr valign="baseline">
              <td nowrap="nowrap" align="right">Subject:</td>
              <td><span id="sprytextfield8">
                <input type="text" name="subject" value="" size="25" />
                <span class="textfieldRequiredMsg">*</span></span></td>
              </tr>
            <tr valign="baseline">
              <td nowrap="nowrap" align="right">S.Y.:</td>
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
                <span class="selectRequiredMsg">?</span></span></td>
              </tr>
            <tr valign="baseline">
              <td nowrap="nowrap" align="right">&nbsp;</td>
              <td><input name="pic" type="hidden" size="32" />
                <input type="hidden" name="UserType" value="0" size="32" />
                <input type="hidden" name="status" value="Active" size="32" />
                <input type="hidden" name="dateReg" value="<?php date_default_timezone_set('Asia/Manila');echo date("m-d-Y h:i:s A"); ?>" size="32" /></td>
              </tr>
            <tr valign="baseline">
              <td nowrap="nowrap" align="right">&nbsp;</td>
              <td><input type="submit" value="Submit" />
                <input type="reset" name="Reset" id="button" value="Reset" /></td>
              </tr>
            </table>
          <input type="hidden" name="id" value="" />
          <input type="hidden" name="MM_insert" value="form1" />
          </form></td>
        <td width="608" align="center"><p>&nbsp;</p>
          <table width="569">
          <tr class="yg">
            <td width="99" align="center" valign="middle" bgcolor="#6633CC">Username</td>
            <td width="134" align="center" valign="middle" bgcolor="#6633CC">Teacher's Name</td>
            <td width="100" align="center" valign="middle" bgcolor="#6633CC">Contact</td>
            <td width="61" align="center" valign="middle" bgcolor="#6633CC">Profile</td>
            <td width="83" align="center" valign="middle" bgcolor="#6633CC">Subject</td>
            <td width="64" align="center" valign="middle" bgcolor="#6633CC">S.Y.</td>
            </tr>
          <?php do { ?>
            <tr>
              <td height="62" align="center" valign="middle" bgcolor="#CCCCCC"><font size="2"><?php echo $row_t['username']; ?></font></td>
              <td align="center" valign="middle" bgcolor="#CCCCCC"><font size="2"><?php echo $row_t['fn']," ", $row_t['mi']," ", $row_t['ln']; ?></td>
              <td align="center" valign="middle" bgcolor="#CCCCCC"><font size="2"><?php echo $row_t['contact']; ?></font></td>
              <td align="center" valign="middle" bgcolor="#CCCCCC"><font size="2"><img src="../Teacher/image/<?php if ($row_t['pic'] == "") echo 'blank.jpg'; else echo $row_t['pic'];?>            " width="64" height="63" /></td>
              <td align="center" valign="middle" bgcolor="#CCCCCC"><font size="2"><?php echo $row_t['subject']; ?></font></td>
              <td align="center" valign="middle" bgcolor="#CCCCCC"><font size="2"><?php echo $row_t['sy']; ?></font></td>
              </tr>
            <?php } while ($row_t = mysql_fetch_assoc($t)); ?>
          </table>
          <p>&nbsp;<font size="2">
            Records  <?php echo min($startRow_t + $maxRows_t, $totalRows_t) ?> of <?php echo $totalRows_t ?></font>&nbsp;          </p>
          <table border="0">
            <tr>
              <td><?php if ($pageNum_t > 0) { // Show if not first page ?>
                <a href="<?php printf("%s?pageNum_t=%d%s", $currentPage, 0, $queryString_t); ?>">First</a>
                <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_t > 0) { // Show if not first page ?>
                <a href="<?php printf("%s?pageNum_t=%d%s", $currentPage, max(0, $pageNum_t - 1), $queryString_t); ?>">Previous</a>
                <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_t < $totalPages_t) { // Show if not last page ?>
                <a href="<?php printf("%s?pageNum_t=%d%s", $currentPage, min($totalPages_t, $pageNum_t + 1), $queryString_t); ?>">Next</a>
                <?php } // Show if not last page ?></td>
              <td><?php if ($pageNum_t < $totalPages_t) { // Show if not last page ?>
                <a href="<?php printf("%s?pageNum_t=%d%s", $currentPage, $totalPages_t, $queryString_t); ?>">Last</a>
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
<script type="text/javascript">
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3");
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4");
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5");
var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6", "date", {format:"mm/dd/yyyy"});
var sprytextfield7 = new Spry.Widget.ValidationTextField("sprytextfield7", "integer", {maxChars:11});
var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1");
var sprytextfield8 = new Spry.Widget.ValidationTextField("sprytextfield8");
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1");

var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
</script>
</body>
</html>
<?php
mysql_free_result($u);

mysql_free_result($t);
?>
