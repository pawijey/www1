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

$colname_qz = "-1";
if (isset($_GET['QzID'])) {
  $colname_qz = $_GET['QzID'];
}
mysql_select_db($database_system, $system);
$query_qz = sprintf("SELECT * FROM quiz WHERE QzID = %s", GetSQLValueString($colname_qz, "int"));
$qz = mysql_query($query_qz, $system) or die(mysql_error());
$row_qz = mysql_fetch_assoc($qz);
$totalRows_qz = mysql_num_rows($qz);

$maxRows_qstn = 10;
$pageNum_qstn = 0;
if (isset($_GET['pageNum_qstn'])) {
  $pageNum_qstn = $_GET['pageNum_qstn'];
}
$startRow_qstn = $pageNum_qstn * $maxRows_qstn;

$colname_qstn = "-1";
if (isset($_GET['QzID'])) {
  $colname_qstn = $_GET['QzID'];
}
mysql_select_db($database_system, $system);
$query_qstn = sprintf("SELECT * FROM quest WHERE QzID = %s ORDER BY id ASC", GetSQLValueString($colname_qstn, "text"));
$query_limit_qstn = sprintf("%s LIMIT %d, %d", $query_qstn, $startRow_qstn, $maxRows_qstn);
$qstn = mysql_query($query_limit_qstn, $system) or die(mysql_error());
$row_qstn = mysql_fetch_assoc($qstn);

if (isset($_GET['totalRows_qstn'])) {
  $totalRows_qstn = $_GET['totalRows_qstn'];
} else {
  $all_qstn = mysql_query($query_qstn);
  $totalRows_qstn = mysql_num_rows($all_qstn);
}
$totalPages_qstn = ceil($totalRows_qstn/$maxRows_qstn)-1;

$colname_qstn1 = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_qstn1 = $_SESSION['MM_Username'];
}
$colname2_qstn1 = "-1";
if (isset($_GET['QzID'])) {
  $colname2_qstn1 = $_GET['QzID'];
}
mysql_select_db($database_system, $system);
$query_qstn1 = sprintf("SELECT COUNT(*) as total FROM quest WHERE username = %s and QzID = %s", GetSQLValueString($colname_qstn1, "text"),GetSQLValueString($colname2_qstn1, "text"));
$qstn1 = mysql_query($query_qstn1, $system) or die(mysql_error());
$row_qstn1 = mysql_fetch_assoc($qstn1);
$totalRows_qstn1 = mysql_num_rows($qstn1);

$colname_qz = "-1";
if (isset($_GET['QzID'])) {
  $colname_qz = $_GET['QzID'];
}
mysql_select_db($database_system, $system);
$query_qz = sprintf("SELECT * FROM quiz WHERE QzID = %s", GetSQLValueString($colname_qz, "int"));
$qz = mysql_query($query_qz, $system) or die(mysql_error());
$row_qz = mysql_fetch_assoc($qz);
$totalRows_qz = mysql_num_rows($qz);

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
<title>Add Questions</title>

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
<link href="../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>
<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
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
    <td height="515" align="center" id="tdMain" >
    <table width="950">
      <tr>
        <td width="285">&nbsp;</td>
        <td width="653">&nbsp;</td>
      </tr>
      <tr>
        <td height="131" align="center">&nbsp;
          <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
            <table align="center">
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><input type="hidden" name="QzID" value="<?php echo $row_qz['QzID']; ?>" size="5" />
                  <input type="hidden" name="QzNo" value="<?php echo $row_qz['QzNo']; ?>" size="32" />
                  <input type="hidden" name="DateIn" value="<?php echo $row_qz['DateIn']; ?>" size="32" />
                  <input type="hidden" name="DateOut" value="<?php echo $row_qz['DateOut']; ?>" size="32" />
                  <input type="hidden" name="username" value="<?php echo $row_u['username']; ?>" size="5" />
                  <input type="hidden" name="Teacher" value="<?php echo $row_qz['Teacher']; ?>" size="32" />
                  <input type="hidden" name="sy" value="<?php echo $row_qz['sy']; ?>" size="32" />
                  <input type="hidden" name="itemNo" value="<?php echo $row_qz['NoItems']; ?>" size="10" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><strong>Add Questioner</strong></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Question:</td>
                <td><span id="sprytextarea1">
                  <textarea name="question" cols="25"></textarea>
                  <span class="textareaRequiredMsg">*</span></span></td>
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
                  <span class="selectRequiredMsg">*</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">A:</td>
                <td><span id="sprytextfield1">
                  <input type="text" name="a" value="" size="15" />
                  <span class="textfieldRequiredMsg">*</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">B:</td>
                <td><span id="sprytextfield2">
                  <input type="text" name="b" value="" size="15" />
                  <span class="textfieldRequiredMsg">*</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">C:</td>
                <td><span id="sprytextfield3">
                  <input type="text" name="c" value="" size="15" />
                  <span class="textfieldRequiredMsg">*</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">D:</td>
                <td><span id="sprytextfield4">
                  <input type="text" name="d" value="" size="15" />
                  <span class="textfieldRequiredMsg">*</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><?php if ($row_qz['NoItems'] ==  $totalRows_qstn ) echo ('<input type="submit" value="Add Question" disabled="disabled" />');
				 else echo ('<input type="submit" value="Add Question" />'); ?>
                 
                 
                  <input type="reset" name="Reset" id="button" value="Clear" /></td>
              </tr>
            </table>
            <input type="hidden" name="id" value="" />
            <input type="hidden" name="MM_insert" value="form1" />
          </form>
          <p>&nbsp;</p></td>
        <td align="center" background="../image/square.png"><table width="600">
          <tr>
            <td colspan="2"><strong><font size="6">Quiz:</font><font size="6"><?php echo $row_qz['QzNo']; ?></font></strong></td>
          </tr>
          <tr>
            <td width="302">Date Start: <strong><?php echo $row_qstn['DateIn']; ?></strong></td>
            <td width="294">Date End: <strong><?php echo $row_qstn['DateOut']; ?></strong></td>
          </tr>
          <tr>
            <td colspan="2" bgcolor="#000000"><table width="600">
              <tr class="yellow">
                <td width="254" align="center" bgcolor="#FFFFFF">Question</td>
                <td width="38" align="center" bgcolor="#FFFFFF"><font size="2">Answer</font></td>
                <td width="79" align="center" bgcolor="#FFFFFF">A</td>
                <td width="66" align="center" bgcolor="#FFFFFF">B</td>
                <td width="74" align="center" bgcolor="#FFFFFF">C</td>
                <td width="61" align="center" bgcolor="#FFFFFF">D</td>
              </tr>
              <?php do { ?>
              <tr>
                <td align="left" bgcolor="#CCCCCC"><?php echo $row_qstn['question']; ?></td>
                <td align="center" bgcolor="#CCCCCC"><?php if($ans = $row_qstn['answer']) echo $ans; ?></td>
                <td align="center" bgcolor="#CCCCCC"><font size="2"><?php if ($a= $row_qstn['a']) echo $a; ?></font></td>
                <td align="center" bgcolor="#CCCCCC"><font size="2"><?php if ($b = $row_qstn['b']) echo $b; ?></font></td>
                <td align="center" bgcolor="#CCCCCC"><font size="2"><?php if ($c=$row_qstn['c']) echo $c; ?></font></td>
                <td align="center" bgcolor="#CCCCCC"><font size="2"><?php if ($d = $row_qstn['d']) echo $d; ?></font></td>
              </tr>
              <?php } while ($row_qstn = mysql_fetch_assoc($qstn)); ?>
            </table></td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;
Records   <?php echo min($startRow_qstn + $maxRows_qstn, $totalRows_qstn) ?> of <?php echo $totalRows_qstn ?></td>
          </tr>
        </table>
          &nbsp;</td>
      </tr>
  </table>
    <p>&nbsp;</p></td>
  </tr>
    <tr id="trFooter">
    <td height="21" id="tdFooter" >Allrights resserved @ SIES 2023</td>
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

mysql_free_result($my);

mysql_free_result($qz);

mysql_free_result($qstn);

mysql_free_result($qstn1);
?>
