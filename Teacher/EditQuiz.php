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
  $updateSQL = sprintf("UPDATE quiz SET QzNo=%s, DateIn=%s, DateOut=%s, NoItems=%s, username=%s, Teacher=%s, sy=%s, Lesson=%s WHERE QzID=%s",
                       GetSQLValueString($_POST['QzNo'], "text"),
                       GetSQLValueString($_POST['DateIn'], "text"),
                       GetSQLValueString($_POST['DateOut'], "text"),
                       GetSQLValueString($_POST['NoItems'], "text"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['Teacher'], "text"),
                       GetSQLValueString($_POST['sy'], "text"),
                       GetSQLValueString($_POST['Lesson'], "text"),
                       GetSQLValueString($_POST['QzID'], "int"));

  mysql_select_db($database_system, $system);
  $Result1 = mysql_query($updateSQL, $system) or die(mysql_error());

  $updateGoTo = "/Teacher/ViewQuiz.php";
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

$colname_Recordset1 = "-1";
if (isset($_GET['QzID'])) {
  $colname_Recordset1 = $_GET['QzID'];
}
mysql_select_db($database_system, $system);
$query_Recordset1 = sprintf("SELECT * FROM quiz WHERE QzID = %s", GetSQLValueString($colname_Recordset1, "int"));
$Recordset1 = mysql_query($query_Recordset1, $system) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>View Quiz</title>

<link rel="stylesheet" type="text/css" href="tcal.css" />
	<script type="text/javascript" src="tcal.js"></script>
	<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
	<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
.navbar {
  overflow: hidden;
  background-color: #333;
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
</style>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="1000" align="center" >
  <tr>
    <td width="996" height="83"><img src="../image/logo.jpg" width="1000" height="200" /></td>
  </tr>
  <tr>
    <td height="21" bgcolor="#FFFFFF">
<div class="navbar">
<a href="<?php echo $logoutAction ?>">Logout</a>
  <a href="TeacherPage.php">Home</a>
  <a href="Profile.php">Profile</a>
  <
  
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
      <a href="AddStudents.php">Add New</a>
      <a href="StudList.php">Student List</a>
     
    </div>
  </div> 

 <div class="dropdown">
    <button class="dropbtn"><font color="yellow">Quiz</font> 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="AddQuiz.php">Add Quiz</a>
      <a href="AddActivities.php">Add Activities</a>
      <a href="ViewQuiz.php">View Quiz</a>
   </div>
  </div> 
   <a href="">Welcome  <?php echo $row_u['fn']; ?></a>
    </div>
  </div> 
</div></td>
  </tr>
  <tr>
    <td height="515" align="center" bgcolor="#FFFFFF"  ><p>&nbsp;</p>
      <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
        <table align="center">
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">QzNo:</td>
            <td><span id="sprytextfield1">
              <input type="text" name="QzNo" placeholder="<?php echo $row_Recordset1['QzNo']; ?>" size="32" required="required"/>
            <span class="textfieldRequiredMsg">A value is required.</span></span></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">DateIn:</td>
            <td><span id="sprytextfield2">
              <input name="DateIn" type="text" class="tcal" placeholder="<?php echo $row_Recordset1['DateIn']; ?>" size="32" readonly="readonly" required="required"/>
            <span class="textfieldRequiredMsg">A value is required.</span></span></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">DateOut:</td>
            <td><span id="sprytextfield3">
              <input name="DateOut" type="text" class="tcal" placeholder="<?php echo $row_Recordset1['DateOut']; ?>" size="32" readonly="readonly" required="required"/>
            <span class="textfieldRequiredMsg">A value is required.</span></span></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">NoItems:</td>
            <td><span id="sprytextfield4">
              <input name="NoItems" type="text" value="<?php echo $row_Recordset1['NoItems']; ?>" size="32" required="required"/>
            <span class="textfieldRequiredMsg">A value is required.</span></span></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Teacher:</td>
            <td><span id="sprytextfield5">
              <input type="text" name="Teacher" placeholder="<?php echo $row_Recordset1['Teacher']; ?>" size="32" required="required"/>
            <span class="textfieldRequiredMsg">A value is required.</span></span></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Sy:</td>
            <td><span id="sprytextfield6">
            <select name="sy" id="sy"  placeholder="<?php echo $row_Recordset1['sy'];?>>
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
 
            <span class="textfieldRequiredMsg">A value is required.</span></span></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Lesson:</td>
            <td><span id="sprytextfield7">
            
              <input type="text" name="Lesson" placeholder="<?php echo $row_Recordset1['Lesson']; ?>" size="32" />
            <span class="textfieldRequiredMsg">A value is required.</span></span></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">&nbsp;</td>
            <td><input type="submit" value="Update record" /></td>
          </tr>
        </table>
        <input type="hidden" name="QzID" value="<?php echo $row_Recordset1['QzID']; ?>" />
        <input type="hidden" name="username" value="<?php echo $row_Recordset1['username']; ?>" />
        <input type="hidden" name="MM_update" value="form1" />
        <input type="hidden" name="QzID" value="<?php echo $row_Recordset1['QzID']; ?>" />
      </form>
      <p>&nbsp;</p>
<p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p></td>
  </tr>
  <tr>
    <td height="21" bgcolor="#FFFFFF" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3");
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4");
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5");
var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6");
var sprytextfield7 = new Spry.Widget.ValidationTextField("sprytextfield7");
</script>
</body>
</html>
<?php
mysql_free_result($u);

mysql_free_result($my);

mysql_free_result($Recordset1);
?>
