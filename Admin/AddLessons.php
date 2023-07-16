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
  $insertSQL = sprintf("INSERT INTO lesson (id, username, teacher, title, subject, `file`, `date`, LesNo) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['id'], "int"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['teacher'], "text"),
                       GetSQLValueString($_POST['title'], "text"),
                       GetSQLValueString($_POST['subject'], "text"),
                       GetSQLValueString($_POST['file'], "text"),
                       GetSQLValueString($_POST['date'], "date"),
                       GetSQLValueString($_POST['LesNo'], "text"));

  mysql_select_db($database_system, $system);
  $Result1 = mysql_query($insertSQL, $system) or die(mysql_error());

  $insertGoTo = "/Admin/Lessons.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO lesson (id, username, teacher, title, subject, `file`, `date`, LesNo) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['id'], "int"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['teacher'], "text"),
                       GetSQLValueString($_POST['title'], "text"),
                       GetSQLValueString($_POST['subject'], "text"),
                       GetSQLValueString($_POST['file'], "text"),
                       GetSQLValueString($_POST['date'], "date"),
                       GetSQLValueString($_POST['LesNo'], "text"));

  mysql_select_db($database_system, $system);
  $Result1 = mysql_query($insertSQL, $system) or die(mysql_error());

  $insertGoTo = "/Admin/Lessons.php";
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

mysql_select_db($database_system, $system);
$query_ut = "SELECT * FROM teachers";
$ut = mysql_query($query_ut, $system) or die(mysql_error());
$row_ut = mysql_fetch_assoc($ut);
$totalRows_ut = mysql_num_rows($ut);

$maxRows_les = 5;
$pageNum_les = 0;
if (isset($_GET['pageNum_les'])) {
  $pageNum_les = $_GET['pageNum_les'];
}
$startRow_les = $pageNum_les * $maxRows_les;

mysql_select_db($database_system, $system);
$query_les = "SELECT * FROM lesson ORDER BY `date` DESC";
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
//$queryString_my = sprintf("&totalRows_my=%d%s", $totalRows_my, $queryString_my);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Add Lessons</title>

<link rel="icon" href="../image/San Isidro logo.png">

<link rel="stylesheet" type="text/css" href="tcal.css" />
	<script type="text/javascript" src="tcal.js"></script>
	<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
	<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>

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
	color: #FFFF80;
}
</style>
<link href="/SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="/SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p>&nbsp;</p>
<table width="1000" align="center" >
  <tr>
    <td width="996" height="83"><img src="../image/logo1.jpg" width="1000" height="200" /></td>
  </tr>
  <tr>
  
    <td height="21" bgcolor="#FFFFFF">
<div class="navbar">
<a href="<?php echo $logoutAction ?>">Logout</a>
  <a href="../Admin/admin.php">Home</a>
  <a href="../Admin/Profile.php">Profile</a>
 
  
  <div class="dropdown">
    <button class="dropbtn"><font color="yellow">Lessons</font> 
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
      <a href="../Admin/StudList.php">Add Teacher</a>

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
    <td height="407" align="center" bgcolor="#FFFFFF"  background="../image/violet.png" ><table width="950" height="53">
      <tr>
        <td width="275" align="center"><form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
          <p><strong>ADD NEW LESSON</strong></p>
          <table align="center">
            <tr valign="baseline">
              <td nowrap="nowrap" align="right">Teacher Name:</td>
              <td><span id="sprytextfield2">
                <input name="teacher" type="text" size="25" />
                <span class="textfieldRequiredMsg">*</span></span></td>
            </tr>
            <tr valign="baseline">
              <td nowrap="nowrap" align="right">Lesson Title:</td>
              <td><span id="sprytextfield1">
                <input type="text" name="title" value="" size="25" />
                <span class="textfieldRequiredMsg">*</span></span></td>
            </tr>
            <tr valign="baseline">
              <td nowrap="nowrap" align="right">Subject:</td>
              <td><span id="sprytextfield3">
                <input type="text" name="subject" value="" size="25" />
                <span class="textfieldRequiredMsg">*</span></span></td>
            </tr>
            <tr valign="baseline">
              <td nowrap="nowrap" align="right">File:</td>
              <td><span id="spryselect1" >
                <select name="file" id="File" required="required">
                  <option disabled="disabled" selected="selected" value="" <?php if (!(strcmp("", "."))) {echo "selected=\"selected\"";} ?>>Select File</option>
                  <option value="LESSON 1 PART 1.mp4" <?php if (!(strcmp("LESSON 1 PART 1.mp4", "."))) {echo "selected=\"selected\"";} ?>>LESSON 1 PART 1.mp4</option>
                  <option value="LESSON 1 PART 2.mp4" <?php if (!(strcmp("LESSON 1 PART 2.mp4", "."))) {echo "selected=\"selected\"";} ?>>LESSON 1 PART 2.mp4</option>
                  <option value="LESSON 1 PART 3.mp4" <?php if (!(strcmp("LESSON 1 PART 3.mp4", "."))) {echo "selected=\"selected\"";} ?>>LESSON 1 PART 3.mp4</option>
                  <option value="LESSON 2 PART 1.mp4" <?php if (!(strcmp("LESSON 2 PART 1.mp4", "."))) {echo "selected=\"selected\"";} ?>>LESSON 2 PART 1.mp4</option>
                  <option value="LESSON 2 PART 2.mp4" <?php if (!(strcmp("LESSON 2 PART 2.mp4", "."))) {echo "selected=\"selected\"";} ?>>LESSON 2 PART 2.mp4</option>
                  <option value="Lesson 3 - Part 1.mp4" <?php if (!(strcmp("Lesson 3 - Part 1.mp4", "."))) {echo "selected=\"selected\"";} ?>>Lesson 3 - Part 1.mp4</option>
                  <option value="Lesson 3 - Part 2.mp4" <?php if (!(strcmp("Lesson 3 - Part 2.mp4", "."))) {echo "selected=\"selected\"";} ?>>Lesson 3 - Part 2.mp4</option>
                  <option value="Lesson 3 - Part 3.mp4" <?php if (!(strcmp("Lesson 3 - Part 3.mp4", "."))) {echo "selected=\"selected\"";} ?>>Lesson 3 - Part 3.mp4</option>
                  <option value="Lesson 3 - Part 4.mp4" <?php if (!(strcmp("Lesson 3 - Part 4.mp4", "."))) {echo "selected=\"selected\"";} ?>>Lesson 3 - Part 4.mp4</option>
                  <option value="Lesson 3 - Part 5.mp4" <?php if (!(strcmp("Lesson 3 - Part 5.mp4", "."))) {echo "selected=\"selected\"";} ?>>Lesson 3 - Part 5.mp4</option>
                  <option value="Lesson 3 - Part 6.mp4" <?php if (!(strcmp("Lesson 3 - Part 6.mp4", "."))) {echo "selected=\"selected\"";} ?>>Lesson 3 - Part 6.mp4</option>
                </select>
                <span class="selectRequiredMsg">*</span></span></td>
            </tr>
            <tr valign="baseline">
              <td nowrap="nowrap" align="right">Date:</td>
              <td><span id="sprytextfield4">
                <input type="text" name="date"  class="tcal" value="" size="10" />
                <span class="textfieldRequiredMsg">*</span></span></td>
            </tr>
            <tr valign="baseline">
              <td nowrap="nowrap" align="right">Lesson #::</td>
              <td><span id="sprytextfield5">
                <input type="text" name="LesNo" value="" size="15" />
                <span class="textfieldRequiredMsg">*</span></span></td>
            </tr>
            <tr valign="baseline">
              <td nowrap="nowrap" align="right">&nbsp;</td>
              <td><input type="submit" value="Insert record" /></td>
            </tr>
          </table>
          <input type="hidden" name="id" value="" />
          <input name="username" type="hidden" value="<?php echo $row_ut['username']; ?>" />
          <input type="hidden" name="MM_insert" value="form1" />
        </form></td>
        <td width="663" align="center"><table width="500">
            <tr class="yellow">
              <td width="46" align="center" bgcolor="#333333">ID</td>
              <td width="125" align="center" bgcolor="#333333">Title</td>
              <td width="114" align="center" bgcolor="#333333">Subject</td>
              <td width="114" align="center" bgcolor="#333333">File Name</td>
              <td width="77" align="center" bgcolor="#333333">Lesson No.</td>
            </tr>
            <?php do { ?>
              <tr>
                <td align="center" bgcolor="#CCCCCC"><?php echo $row_les['id']; ?></td>
                <td align="center" bgcolor="#CCCCCC"><font size="2"><?php echo $row_les['title']; ?></font></td>
                <td align="center" bgcolor="#CCCCCC"><font size="2"><?php echo $row_les['subject']; ?></font></td>
                <td align="center" bgcolor="#CCCCCC"><font size="2"><?php echo $row_les['file']; ?></font></td>
                <td align="center" bgcolor="#CCCCCC"><font size="2"><?php echo $row_les['LesNo']; ?></font></td>
              </tr>
              <?php } while ($row_les = mysql_fetch_assoc($les)); ?>
          </table>
          <p>&nbsp;
Records <?php echo ($startRow_les + 1) ?> to <?php echo min($startRow_les + $maxRows_les, $totalRows_les) ?> of <?php echo $totalRows_les ?>&nbsp;          </p>
          <table border="0">
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
        </table>
          </p></td>
      </tr>
  </table>
    <p>&nbsp;</p></td>
  </tr>
  <tr>
    <td height="21" bgcolor="#FFFFFF" background="" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3");
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1");
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4");
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5");
</script>
</body>
</html>
<?php
mysql_free_result($u);

mysql_free_result($ut);

mysql_free_result($les);
?>
