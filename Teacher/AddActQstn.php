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
  $insertSQL = sprintf("INSERT INTO actqst (id, ActID, ActNo, username, DateIn, DateOut, Teacher, sy, itemNo, question, answer, a, b, c, d) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['id'], "int"),
                       GetSQLValueString($_POST['ActID'], "text"),
                       GetSQLValueString($_POST['ActNo'], "text"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['DateIn'], "text"),
                       GetSQLValueString($_POST['DateOut'], "text"),
                       GetSQLValueString($_POST['Teacher'], "text"),
                       GetSQLValueString($_POST['sy'], "text"),
                       GetSQLValueString($_POST['itemNo'], "text"),
                       GetSQLValueString($_POST['question'], "text"),
                       GetSQLValueString($_POST['answer'], "text"),
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

$maxRows_Act = 5;
$pageNum_Act = 0;
if (isset($_GET['pageNum_Act'])) {
  $pageNum_Act = $_GET['pageNum_Act'];
}
$startRow_Act = $pageNum_Act * $maxRows_Act;

$colname_Act = "-1";
if (isset($_GET['ActID'])) {
  $colname_Act = $_GET['ActID'];
}
$colname2_Act = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname2_Act = $_SESSION['MM_Username'];
}
mysql_select_db($database_system, $system);
$query_Act = sprintf("SELECT * FROM activities WHERE ActID = %s  and username = %s", GetSQLValueString($colname_Act, "int"),GetSQLValueString($colname2_Act, "text"));
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

$maxRows_Qstn = 2;
$pageNum_Qstn = 0;
if (isset($_GET['pageNum_Qstn'])) {
  $pageNum_Qstn = $_GET['pageNum_Qstn'];
}
$startRow_Qstn = $pageNum_Qstn * $maxRows_Qstn;

$colname_Qstn = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_Qstn = $_SESSION['MM_Username'];
}
$colname2_Qstn = "-1";
if (isset($_GET['ActID'])) {
  $colname2_Qstn = $_GET['ActID'];
}
mysql_select_db($database_system, $system);
$query_Qstn = sprintf("SELECT * FROM actqst WHERE username = %s and ActID = %s ORDER BY id DESC", GetSQLValueString($colname_Qstn, "text"),GetSQLValueString($colname2_Qstn, "int"));
$query_limit_Qstn = sprintf("%s LIMIT %d, %d", $query_Qstn, $startRow_Qstn, $maxRows_Qstn);
$Qstn = mysql_query($query_limit_Qstn, $system) or die(mysql_error());
$row_Qstn = mysql_fetch_assoc($Qstn);

if (isset($_GET['totalRows_Qstn'])) {
  $totalRows_Qstn = $_GET['totalRows_Qstn'];
} else {
  $all_Qstn = mysql_query($query_Qstn);
  $totalRows_Qstn = mysql_num_rows($all_Qstn);
}
$totalPages_Qstn = ceil($totalRows_Qstn/$maxRows_Qstn)-1;

$colname_Qstn2 = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_Qstn2 = $_SESSION['MM_Username'];
}
$colname2_Qstn2 = "-1";
if (isset($_GET['ActID'])) {
  $colname2_Qstn2 = $_GET['ActID'];
}
mysql_select_db($database_system, $system);
$query_Qstn2 = sprintf("SELECT *, count(ActID) as Total FROM actqst WHERE username = %s and ActID = %s", GetSQLValueString($colname_Qstn2, "text"),GetSQLValueString($colname2_Qstn2, "int"));
$Qstn2 = mysql_query($query_Qstn2, $system) or die(mysql_error());
$row_Qstn2 = mysql_fetch_assoc($Qstn2);
$totalRows_Qstn2 = mysql_num_rows($Qstn2);

$queryString_Qstn = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_Qstn") == false && 
        stristr($param, "totalRows_Qstn") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_Qstn = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_Qstn = sprintf("&totalRows_Qstn=%d%s", $totalRows_Qstn, $queryString_Qstn);

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
<title>Add Act. Questions</title>

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
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
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
</div></td>
  </tr>
  
  <tr>
    <td height="800" align="center" id="tdMain">
    <table width="962">
      <tr>
        <td width="323" height="21">&nbsp;</td>
        <td width="627">&nbsp;</td>
      </tr>
      <tr>
        <td height="21">&nbsp;
          <p>&nbsp;</p>
          <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
            <table align="left">
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><strong>Add Activity Questioner</strong></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Activity No.</td>
                <td><strong><font size="4" color="#FF0000"><?php echo $row_Act['ActNo']; ?></font></strong></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Add Question:</td>
                <td><span id="sprytextfield1">
                  <input type="text" name="question" value="" size="32" />
                  <span class="textfieldRequiredMsg">A value is required.</span></span></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Right Answer:</td>
                <td><label for="answer"></label>
                  <select name="answer" id="answer">
                    <option value="" <?php if (!(strcmp("", "."))) {echo "selected=\"selected\"";} ?>></option>
                    <option value="A" <?php if (!(strcmp("A", "."))) {echo "selected=\"selected\"";} ?>>A</option>
                    <option value="B" <?php if (!(strcmp("B", "."))) {echo "selected=\"selected\"";} ?>>B</option>
                    <option value="C" <?php if (!(strcmp("C", "."))) {echo "selected=\"selected\"";} ?>>C</option>
                    <option value="D" <?php if (!(strcmp("D", "."))) {echo "selected=\"selected\"";} ?>>D</option>
                  </select></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Choices A:</td>
                <td><input type="text" name="a" value="" size="32" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Choices B:</td>
                <td><input type="text" name="b" value="" size="32" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Choices C:</td>
                <td><input type="text" name="c" value="" size="32" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Choices D:</td>
                <td><input type="text" name="d" value="" size="32" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><input type="hidden" name="ActID" value="<?php echo $row_Act['ActID']; ?>" size="32" />
                  <input type="hidden" name="ActNo" value="<?php echo $row_Act['ActNo']; ?>" size="32" />
                  <input type="hidden" name="username" value="<?php echo $row_u['username']; ?>" size="32" />
                  <input type="hidden" name="DateIn" value="<?php echo $row_Act['DateIn']; ?>" size="32" />
                  <input type="hidden" name="DateOut" value="<?php echo $row_Act['DateOut']; ?>" size="32" />
                  <input type="hidden" name="Teacher" value="<?php echo $row_Act['Teacher']; ?>" size="32" />
                  <input type="hidden" name="sy" value="<?php echo $row_Act['sy']; ?>" size="32" />
                  <input type="hidden" name="itemNo" value="<?php echo $row_Act['NoItems']; ?>" size="32" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td>
                  <?php if($totalRows_Qstn == $row_Act['NoItems']) echo '<input type="submit" value="Insert record" disabled="disabled"/>'; else echo '<input type="submit" value="Insert record" />'; ?></td>
              </tr>
            </table>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>
              <input type="hidden" name="id" value="" />
              <input type="hidden" name="MM_insert" value="form1" />
            </p>
          </form>
          <p>&nbsp;</p></td>
        <td align="center" ><p>&nbsp;</p>
          <table width="425">
            <tr>
              <td colspan="2"><strong>TOTAL QUESTIONER: [<?php echo $row_Qstn2['Total']; ?> ]</strong></td>
              </tr>
            <?php do { ?>
              <tr>
                <td width="89" bgcolor="#CCCCCC"><font size="2"><strong>Activity No.</strong></font></td>
                <td width="383" bgcolor="#FFFFCC"><font size="2"><?php echo $row_Qstn['ActNo']; ?></font></td>
                </tr>
              <tr>
                <td bgcolor="#CCCCCC"><font size="2"><strong>Question:</strong></font></td>
                <td bgcolor="#FFFFCC"><font size="2"><?php echo $row_Qstn['question']; ?></font></td>
                </tr>
              <tr>
                <td bgcolor="#CCCCCC"><font size="2">Right Answer:</font></td>
                <td bgcolor="#FFFFCC"><font size="2" class="red"><?php echo $row_Qstn['answer']; ?></font></td>
                </tr>
              <tr>
                <td align="right" bgcolor="#CCCCCC"><font size="2">Choices A.</font></td>
                <td bgcolor="#FFFFCC"><font size="2"><?php echo $row_Qstn['a']; ?></font></td>
                </tr>
              <tr>
                <td align="right" bgcolor="#CCCCCC"><font size="2">Choices B.</font></td>
                <td bgcolor="#FFFFCC"><font size="2"><?php echo $row_Qstn['b']; ?></font></td>
                </tr>
              <tr>
                <td align="right" bgcolor="#CCCCCC"><font size="2">Choices C.</font></td>
                <td bgcolor="#FFFFCC"><font size="2"><?php echo $row_Qstn['c']; ?></font></td>
                </tr>
              <tr>
                <td align="right" bgcolor="#CCCCCC"><font size="2">Choices D.</font></td>
                <td bgcolor="#FFFFCC"><font size="2"><?php echo $row_Qstn['d']; ?></font></td>
                </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                </tr>
              <?php } while ($row_Qstn = mysql_fetch_assoc($Qstn)); ?>
          </table>
          <p>&nbsp;
Records  <?php echo min($startRow_Qstn + $maxRows_Qstn, $totalRows_Qstn) ?> of <?php echo $totalRows_Qstn ?>&nbsp;          </p>
          <table border="0">
            <tr>
              <td><?php if ($pageNum_Qstn > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_Qstn=%d%s", $currentPage, 0, $queryString_Qstn); ?>">First</a>
                <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_Qstn > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_Qstn=%d%s", $currentPage, max(0, $pageNum_Qstn - 1), $queryString_Qstn); ?>">Previous</a>
                <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_Qstn < $totalPages_Qstn) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_Qstn=%d%s", $currentPage, min($totalPages_Qstn, $pageNum_Qstn + 1), $queryString_Qstn); ?>">Next</a>
                <?php } // Show if not last page ?></td>
              <td><?php if ($pageNum_Qstn < $totalPages_Qstn) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_Qstn=%d%s", $currentPage, $totalPages_Qstn, $queryString_Qstn); ?>">Last</a>
                <?php } // Show if not last page ?></td>
            </tr>
          </table>
          </p>
          <p>&nbsp;</p></td>
      </tr>
      <tr>
        <td height="21"></td>
        <td>&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr id="trFooter">
    <td height="21" id="tdFooter" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
</script>
</body>
</html>
<?php
mysql_free_result($u);

mysql_free_result($my);

mysql_free_result($Act);

mysql_free_result($Qstn);

mysql_free_result($Qstn2);
?>
