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

mysql_select_db($database_system, $system);
$query_act = "SELECT * FROM activities ORDER BY ActID ASC";
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
.yellow {
	color: #FFFF80;
}
</style>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
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
    <td height="347" align="center" bgcolor="#FFFFFF"  background="../image/violet.png" ><table width="950">
      <tr>
        <td width="268" align="center">&nbsp;
          <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
            <table align="center">
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><input type="hidden" name="ActID" value="" size="32" />
                  <input type="hidden" name="username" value="<?php echo $row_u['username']; ?>" size="32" />
                  ADD NEW SCHED ACTIVITIES</td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Activity No:</td>
                <td><span id="sprytextfield1">
                <input type="text" name="ActNo" value="" size="15" />
                <span class="textfieldRequiredMsg">*</span><span class="textfieldInvalidFormatMsg">?</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">DateStart:</td>
                <td><span id="sprytextfield2">
                <input type="text" name="DateIn" value="" size="30" placeholder="Ex. June 01, 1999 08:00:00 AM/PM" />
                <span class="textfieldRequiredMsg">*</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">DateEnd:</td>
                <td><span id="sprytextfield3">
                  <input type="text" name="DateOut" value="" size="30" placeholder="Ex. June 01, 1999 08:00:00 AM/PM"  />
                  <span class="textfieldRequiredMsg">*</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">No. of Items:</td>
                <td><input name="NoItems" type="text" value="10" size="10" readonly="readonly" />
                  <img src="../image/icons/IcoJoy/login.png" width="16" height="16" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Name of Teacher:</td>
                <td><span id="sprytextfield4">
                  <input type="text" name="Teacher" value="" size="25" />
                  <span class="textfieldRequiredMsg">*</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">S.Y.:</td>
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
                <td nowrap="nowrap" align="right">Lesson No.:</td>
                <td><span id="sprytextfield5">
                  <input type="text" name="Lesson" value="" size="15" />
                  <span class="textfieldRequiredMsg">*</span></span></td>
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
        <td width="670" align="center"><table width="520">
            <tr class="yellow">
              <td width="42" align="center" bgcolor="#333333">ActNo</td>
              <td width="158" align="center" bgcolor="#333333">DateStart</td>
              <td width="147" align="center" bgcolor="#333333">DateEnd</td>
              <td width="42" align="center" bgcolor="#333333">Items</td>
              <td width="76" align="center" bgcolor="#333333">Lesson No.</td>
              <td width="27" align="center" bgcolor="#333333">Q</td>
            </tr>
            <?php do { ?>
              <tr>
                <td align="center" bgcolor="#CCCCCC"><?php $acts = $row_act['ActNo']; echo $acts; ?></td>
                <td align="center" bgcolor="#CCCCCC"><?php $d1 = $row_act['DateIn']; echo $d1;?></td>
                <td align="center" bgcolor="#CCCCCC"><?php $d2 = $row_act['DateOut']; echo $d2 ;?></td>
                <td align="center" bgcolor="#CCCCCC"><?php $no = $row_act['NoItems']; echo $no; ?></td>
                <td align="center" bgcolor="#CCCCCC"><?php $les = $row_act['Lesson']; echo $les; ?></td>
                <td align="center" bgcolor="#CCCCCC"><a href=" AddQActivities.php?ActID=<?php echo $row_act['ActID'];?>"><img src="../image/icons/IcoJoy/action_add.png" width="16" height="16" /></a></td>
              </tr>
              <?php } while ($row_act = mysql_fetch_assoc($act)); ?>
          </table>
          <p>&nbsp;
Records <?php echo ($startRow_act + 1) ?> to <?php echo min($startRow_act + $maxRows_act, $totalRows_act) ?> of <?php echo $totalRows_act ?>&nbsp;          </p>
          <table border="0">
            <tr>
              <td><?php if ($pageNum_act > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_act=%d%s", $currentPage, 0, $queryString_act); ?>">First</a>
                  <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_act > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_act=%d%s", $currentPage, max(0, $pageNum_act - 1), $queryString_act); ?>">Previous</a>
                  <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_act < $totalPages_act) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_act=%d%s", $currentPage, min($totalPages_act, $pageNum_act + 1), $queryString_act); ?>">Next</a>
                  <?php } // Show if not last page ?></td>
              <td><?php if ($pageNum_act < $totalPages_act) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_act=%d%s", $currentPage, $totalPages_act, $queryString_act); ?>">Last</a>
                  <?php } // Show if not last page ?></td>
            </tr>
        </table>
          </p></td>
      </tr>
    </table>      <p>&nbsp;</p></td>
  </tr>
  <tr>
    <td height="21" bgcolor="#FFFFFF" background="" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "integer");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none");
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3");
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4");
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1");
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5");
</script>
</body>
</html>
<?php
mysql_free_result($u);

mysql_free_result($mp4);

mysql_free_result($act);
?>
