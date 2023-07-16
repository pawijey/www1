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
  $insertSQL = sprintf("INSERT INTO quest (id, QzID, QzNo, DateIn, DateOut, username, Teacher, sy, question, answer, itemNo, a, b, c, d) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['id'], "int"),
                       GetSQLValueString($_POST['QzID'], "text"),
                       GetSQLValueString($_POST['QzNo'], "text"),
                       GetSQLValueString($_POST['DateIn'], "text"),
                       GetSQLValueString($_POST['DateOut'], "text"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['Teacher'], "text"),
                       GetSQLValueString($_POST['sy'], "text"),
                       GetSQLValueString($_POST['question'], "text"),
                       GetSQLValueString($_POST['answer'], "text"),
                       GetSQLValueString($_POST['itemNo'], "int"),
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

$maxRows_qz = 10;
$pageNum_qz = 0;
if (isset($_GET['pageNum_qz'])) {
  $pageNum_qz = $_GET['pageNum_qz'];
}
$startRow_qz = $pageNum_qz * $maxRows_qz;

$colname_qz = "-1";
if (isset($_GET['QzNo'])) {
  $colname_qz = $_GET['QzNo'];
}
mysql_select_db($database_system, $system);
$query_qz = sprintf("SELECT * FROM quiz WHERE QzNo = %s", GetSQLValueString($colname_qz, "text"));
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

$maxRows_quest = 5;
$pageNum_quest = 0;
if (isset($_GET['pageNum_quest'])) {
  $pageNum_quest = $_GET['pageNum_quest'];
}
$startRow_quest = $pageNum_quest * $maxRows_quest;

$colname_quest = "-1";
if (isset($_GET['QzNo'])) {
  $colname_quest = $_GET['QzNo'];
}
mysql_select_db($database_system, $system);
$query_quest = sprintf("SELECT * FROM quest WHERE QzNo = %s", GetSQLValueString($colname_quest, "text"));
$query_limit_quest = sprintf("%s LIMIT %d, %d", $query_quest, $startRow_quest, $maxRows_quest);
$quest = mysql_query($query_limit_quest, $system) or die(mysql_error());
$row_quest = mysql_fetch_assoc($quest);

if (isset($_GET['totalRows_quest'])) {
  $totalRows_quest = $_GET['totalRows_quest'];
} else {
  $all_quest = mysql_query($query_quest);
  $totalRows_quest = mysql_num_rows($all_quest);
}
$totalPages_quest = ceil($totalRows_quest/$maxRows_quest)-1;

$colname_quest1 = "-1";
if (isset($_GET['QzNo'])) {
  $colname_quest1 = $_GET['QzNo'];
}
mysql_select_db($database_system, $system);
$query_quest1 = sprintf("SELECT *, count(QzNo) as Total FROM quest WHERE QzNo = %s", GetSQLValueString($colname_quest1, "text"));
$quest1 = mysql_query($query_quest1, $system) or die(mysql_error());
$row_quest1 = mysql_fetch_assoc($quest1);
$totalRows_quest1 = mysql_num_rows($quest1);

$queryString_quest = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_quest") == false && 
        stristr($param, "totalRows_quest") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_quest = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_quest = sprintf("&totalRows_quest=%d%s", $totalRows_quest, $queryString_quest);

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
<title>Add Questions</title>

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
	color: #FFFF80;
}
.ygreen {
	color: #80FF00;
}
.yellow {
	color: #FF0;
}
</style>
<link href="../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>
<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
</head>

<body>
<table width="100%" align="center" >
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
        <td width="373" height="268" align="center"><form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
          <table align="center">
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><input type="hidden" name="id" value="" size="32" />
                  <input type="hidden" name="QzID" value="<?php echo $row_qz1['QzID']; ?>" size="32" />
                  <input type="hidden" name="QzNo" value="<?php echo $row_qz1['QzNo']; ?>" size="32" />
                  <input type="hidden" name="DateIn" value="<?php echo $row_qz1['DateIn']; ?>" size="32" />
                  <input type="hidden" name="DateOut" value="<?php echo $row_qz1['DateOut']; ?>" size="32" />
                  <input type="hidden" name="username" value="<?php echo $row_u['username']; ?>" size="32" />
                  <input type="hidden" name="Teacher" value="<?php echo $row_qz1['Teacher']; ?>" size="32" />
                  <input type="hidden" name="sy" value="<?php echo $row_qz1['sy']; ?>" size="32" />
                  <input name="itemNo" type="HIDDEN" value="10" size="10" readonly="readonly" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><strong>Add Quiz Questioner</strong></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Question:</td>
                <td><span id="sprytextarea1">
                  <textarea name="question" cols="32"></textarea>
                  <span class="textareaRequiredMsg">?</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Answer:</td>
                <td><label for="answer"></label>
                  <span id="spryselect1">
                  <select name="answer" id="answer">
                    <option value="" <?php if (!(strcmp("", "."))) {echo "selected=\"selected\"";} ?>></option>
                    <option value="A" <?php if (!(strcmp("A", "."))) {echo "selected=\"selected\"";} ?>>A</option>
                    <option value="B" <?php if (!(strcmp("B", "."))) {echo "selected=\"selected\"";} ?>>B</option>
                    <option value="C" <?php if (!(strcmp("C", "."))) {echo "selected=\"selected\"";} ?>>C</option>
                    <option value="D" <?php if (!(strcmp("D", "."))) {echo "selected=\"selected\"";} ?>>D</option>
                  </select>
                  <span class="selectRequiredMsg">?</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Choice A:</td>
                <td><span id="sprytextfield1">
                  <input type="text" name="a" value="" size="32" />
                  <span class="textfieldRequiredMsg">?</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Choice B:</td>
                <td><span id="sprytextfield2">
                  <input type="text" name="b" value="" size="32" />
                  <span class="textfieldRequiredMsg">?</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Choice C:</td>
                <td><span id="sprytextfield3">
                  <input type="text" name="c" value="" size="32" />
                  <span class="textfieldRequiredMsg">?</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Choice D:</td>
                <td><span id="sprytextfield4">
                  <input type="text" name="d" value="" size="32" />
                  <span class="textfieldRequiredMsg">?</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td>
                  
                  
                  <?php if ($row_qz['NoItems'] == $row_quest1['Total']) echo '<input type="submit" value="Insert record" disabled="disabled"/>'; else echo '<input type="submit" value="Insert record" />'; ?></td>
              </tr>
            </table>
            <input type="hidden" name="MM_insert" value="form1" />
        </form></td>
        <td width="1231" align="center"><p><font size="5"><?php echo $row_qz['Lesson']; ?></font></p>
          <p>LIST OF QUESTIONERS </p>
          <table width="1021">
            <tr class="yellow">
            <td width="38" align="center" bgcolor="#333333">QzNo</td>
            <td width="402" align="center" bgcolor="#333333">Questioner</td>
            <td width="47" align="center" bgcolor="#333333">Answer</td>
            <td width="139" align="center" bgcolor="#333333">Choice A</td>
            <td width="120" align="center" bgcolor="#333333">Choice B</td>
            <td width="128" align="center" bgcolor="#333333">Choice C</td>
            <td width="115" align="center" bgcolor="#333333">Choice D</td>
          </tr>
          <?php do { ?>
            <tr>
              <td align="center" bgcolor="#CCCCCC"><?php $QzNo = $row_quest['QzNo']; echo $QzNo;?></td>
              <td align="left" bgcolor="#CCCCCC"><?php echo $row_quest['question']; ?> 


<a href="http://localhost" 
target="popup" 
onclick="window.open('http://localhost/Admin/AddQuestionsEdit.php?id=<?php echo $row_quest['id'];?>','popup','width=600,height=600'); return false;">
<img src="../image/icons/IcoJoy/reply.png" width="16" height="16" /><font size="1">edit</font></a>
</td>
              <td align="center" bgcolor="#CCCCCC"><?php $ans = $row_quest['answer']; echo $ans; ?></td>
              <td align="center" bgcolor="#CCCCCC"><?php echo $row_quest['a']; ?></td>
              <td align="center" bgcolor="#CCCCCC"><?php echo $row_quest['b']; ?></td>
              <td align="center" bgcolor="#CCCCCC"><?php echo $row_quest['c']; ?></td>
              <td align="center" bgcolor="#CCCCCC"><?php echo $row_quest['d']; ?></td>
            </tr>
            <?php } while ($row_quest = mysql_fetch_assoc($quest)); ?>
      </table>
          <p>&nbsp;
Records <?php echo ($startRow_quest + 1) ?> to <?php echo min($startRow_quest + $maxRows_quest, $totalRows_quest) ?> of <?php echo $totalRows_quest ?>&nbsp;          </p>
          <table border="0">
            <tr>
              <td><?php if ($pageNum_quest > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_quest=%d%s", $currentPage, 0, $queryString_quest); ?>">First</a>
                  <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_quest > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_quest=%d%s", $currentPage, max(0, $pageNum_quest - 1), $queryString_quest); ?>">Previous</a>
                  <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_quest < $totalPages_quest) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_quest=%d%s", $currentPage, min($totalPages_quest, $pageNum_quest + 1), $queryString_quest); ?>">Next</a>
                  <?php } // Show if not last page ?></td>
              <td><?php if ($pageNum_quest < $totalPages_quest) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_quest=%d%s", $currentPage, $totalPages_quest, $queryString_quest); ?>">Last</a>
                  <?php } // Show if not last page ?></td>
            </tr>
          </table>
          </p>
</p></td>
      </tr>
      <tr>
        <td height="21" align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="21" bgcolor="#FFFFFF" background="" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>
<script type="text/javascript">
var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1");
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1");
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3");
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4");
</script>
</body>
</html>
<?php
mysql_free_result($u);

mysql_free_result($qz);

mysql_free_result($quest);

mysql_free_result($quest1);
?>
