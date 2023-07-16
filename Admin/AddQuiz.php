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

$maxRows_qz = 5;
$pageNum_qz = 0;
if (isset($_GET['pageNum_qz'])) {
  $pageNum_qz = $_GET['pageNum_qz'];
}
$startRow_qz = $pageNum_qz * $maxRows_qz;

mysql_select_db($database_system, $system);
$query_qz = "SELECT * FROM quiz";
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
$query_quest = "SELECT * FROM quest";
$quest = mysql_query($query_quest, $system) or die(mysql_error());
$row_quest = mysql_fetch_assoc($quest);
$totalRows_quest = mysql_num_rows($quest);

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
//$queryString_my = sprintf("&totalRows_my=%d%s", $totalRows_my, $queryString_my);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Add Quiz</title>

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
.yw {
	color: #D6D6D6;
}
</style>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
</head>

<body>
<table width="100%" align="center" >
  <tr>
    <td width="996" height="83"><img src="../image/logo.png" width="1614" height="244" /></td>
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
    <td height="422" align="center" bgcolor="#FFFFFF"  background="../image/violet.png" ><table width="100%">
      <tr>
        <td width="323" height="338">&nbsp;
          <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
            <table align="center">
              <tr valign="baseline">
                <td width="102" align="right" nowrap="nowrap">&nbsp;</td>
                <td width="270"><strong>ADD NEW QUIZZES SCHEDULE</strong></td>
                </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">No. of Quiz::</td>
                <td><span id="sprytextfield1">
                <input type="text" name="QzNo" value="" size="15" placeholder="Type Number" />
                <span class="textfieldRequiredMsg">*</span><span class="textfieldInvalidFormatMsg">?</span></span></td>
                </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Date Start:</td>
                <td><span id="sprytextfield2">
                <input type="text" name="DateIn" value="" size="27"  placeholder="Ex. June 01, 1999 08:00:00 AM/PM" />
                <span class="textfieldRequiredMsg">*</span><span class="textfieldInvalidFormatMsg">?</span></span></td>
                </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Date End:</td>
                <td><span id="sprytextfield3">
                <input type="text" name="DateOut" value="" size="27" placeholder="Ex. June 01, 1999 08:00:00 AM/PM" />
                <span class="textfieldRequiredMsg">*</span><span class="textfieldInvalidFormatMsg">?</span></span></td>
                </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">No. Items:</td>
                <td><span id="sprytextfield4">
                  <input name="NoItems" type="text" value="10" size="15" readonly="readonly" />
                  <span class="textfieldRequiredMsg">*</span></span><img src="../image/icons/Pinvoke/lock.png" width="16" height="16" /></td>
                </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Teacher's Name:</td>
                <td><span id="sprytextfield5">
                  <input type="text" name="Teacher" value="" size="20" />
                  <span class="textfieldRequiredMsg">*</span></span></td>
                </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Lesson No.::</td>
                <td><span id="sprytextfield6">
                  <input type="text" name="Lesson" value="" size="20" placeholder="ex. Lesson 1"/>
                  <span class="textfieldRequiredMsg">*</span></span></td>
                </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">S.Y.</td>
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
                <td><input type="hidden" name="username" value="<?php echo $row_u['username']; ?>" size="32" />
                  <input type="hidden" name="QzID" value="" size="32" /></td>
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
        <td width="615" align="center"><strong>QUIZZES SCHEDULES&nbsp;</strong>
          <table width="815">
            <tr class="yw">
              <td width="55" align="center" bgcolor="#3333CC">QzID</td>
              <td width="57" align="center" bgcolor="#3333CC">QzNo</td>
              <td width="237" align="center" bgcolor="#3333CC">DateStart</td>
              <td width="240" align="center" bgcolor="#3333CC">DateEnd</td>
              <td width="69" align="center" bgcolor="#3333CC">No.Items</td>
              <td width="85" align="center" bgcolor="#3333CC">S.Y.</td>
              <td width="40" align="center" bgcolor="#3333CC">AddQ</td>
            </tr>
            <?php do { ?>
              <tr>
                <td align="center" bgcolor="#CCCCCC"><font size="2"><?php echo $row_qz['QzID']; ?></font></td>
                <td align="center" bgcolor="#CCCCCC"><font size="2"><?php echo $row_qz['QzNo']; ?></font></td>
                <td align="center" bgcolor="#CCCCCC"><font size="2"><?php echo $row_qz['DateIn']; ?></font></td>
                <td align="center" bgcolor="#CCCCCC"><font size="2"><?php echo $row_qz['DateOut']; ?></font></td>
                <td align="center" bgcolor="#CCCCCC"><font size="2"><?php echo $row_qz['NoItems']; ?></font></td>
                <td align="center" bgcolor="#CCCCCC"><font size="2"><?php echo $row_qz['sy']; ?></font></td>
                <td align="center" bgcolor="#CCCCCC"><a href="AddQuestions.php?QzNo=<?php echo $row_qz['QzNo'];?>"><img src="../image/icons/Pinvoke/sign_plus.png" width="16" height="16" /></a></td>
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
    <p>&nbsp;</p></td>
  </tr>
  <tr>
    <td height="21" bgcolor="#FFFFFF" background="" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "integer");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "date", {format:"mm/dd/yyyy"});
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "date");
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4");
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5");
var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6");
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1");
</script>
</body>
</html>
<?php
mysql_free_result($u);

mysql_free_result($qz);

mysql_free_result($quest);
?>
