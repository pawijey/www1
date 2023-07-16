<?php require_once('../Connections/system.php'); ?>
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
  $insertSQL = sprintf("INSERT INTO actanswer (id, username, ActID, ActNo, DateIn, DateOut, Teacher, sy, itemNo, answer, a) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['id'], "int"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['ActID'], "text"),
                       GetSQLValueString($_POST['ActNo'], "text"),
                       GetSQLValueString($_POST['DateIn'], "text"),
                       GetSQLValueString($_POST['DateOut'], "text"),
                       GetSQLValueString($_POST['Teacher'], "text"),
                       GetSQLValueString($_POST['sy'], "text"),
                       GetSQLValueString($_POST['itemNo'], "text"),
                       GetSQLValueString($_POST['answer'], "text"),
                       GetSQLValueString($_POST['a'], "text"));

  mysql_select_db($database_system, $system);
  $Result1 = mysql_query($insertSQL, $system) or die(mysql_error());
}

$currentPage = $_SERVER["PHP_SELF"];

$colname_u = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_u = $_SESSION['MM_Username'];
}
mysql_select_db($database_system, $system);
$query_u = sprintf("SELECT * FROM students WHERE username = %s", GetSQLValueString($colname_u, "text"));
$u = mysql_query($query_u, $system) or die(mysql_error());
$row_u = mysql_fetch_assoc($u);
$totalRows_u = mysql_num_rows($u);

$colname_act = "-1";
if (isset($_GET['ActNo'])) {
  $colname_act = $_GET['ActNo'];
}
mysql_select_db($database_system, $system);
$query_act = sprintf("SELECT * FROM activities WHERE ActNo = %s ", GetSQLValueString($colname_act, "text"));
$act = mysql_query($query_act, $system) or die(mysql_error());
$row_act = mysql_fetch_assoc($act);
$totalRows_act = mysql_num_rows($act);

$maxRows_act1 = 1;
$pageNum_act1 = 0;
if (isset($_GET['pageNum_act1'])) {
  $pageNum_act1 = $_GET['pageNum_act1'];
}
$startRow_act1 = $pageNum_act1 * $maxRows_act1;

$colname_act1 = "-1";
if (isset($_GET['ActNo'])) {
  $colname_act1 = $_GET['ActNo'];
}
mysql_select_db($database_system, $system);
$query_act1 = sprintf("SELECT * FROM actqst WHERE ActNo = %s", GetSQLValueString($colname_act1, "text"));
$query_limit_act1 = sprintf("%s LIMIT %d, %d", $query_act1, $startRow_act1, $maxRows_act1);
$act1 = mysql_query($query_limit_act1, $system) or die(mysql_error());
$row_act1 = mysql_fetch_assoc($act1);

if (isset($_GET['totalRows_act1'])) {
  $totalRows_act1 = $_GET['totalRows_act1'];
} else {
  $all_act1 = mysql_query($query_act1);
  $totalRows_act1 = mysql_num_rows($all_act1);
}
$totalPages_act1 = ceil($totalRows_act1/$maxRows_act1)-1;

$colname_act2 = "-1";
if (isset($_GET['ActNo'])) {
  $colname_act2 = $_GET['ActNo'];
}
mysql_select_db($database_system, $system);
$query_act2 = sprintf("SELECT * FROM actqst WHERE ActNo = %s", GetSQLValueString($colname_act2, "text"));
$act2 = mysql_query($query_act2, $system) or die(mysql_error());
$row_act2 = mysql_fetch_assoc($act2);
$totalRows_act2 = mysql_num_rows($act2);

$colname_ans = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_ans = $_SESSION['MM_Username'];
}
$colname2_ans = "-1";
if (isset($_GET['ActNo'])) {
  $colname2_ans = $_GET['ActNo'];
}
mysql_select_db($database_system, $system);
$query_ans = sprintf("SELECT *, count(ActNo) as total FROM actanswer WHERE username = %s and ActNo = %s ", GetSQLValueString($colname_ans, "text"),GetSQLValueString($colname2_ans, "text"));
$ans = mysql_query($query_ans, $system) or die(mysql_error());
$row_ans = mysql_fetch_assoc($ans);
$totalRows_ans = mysql_num_rows($ans);

$queryString_act1 = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_act1") == false && 
        stristr($param, "totalRows_act1") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_act1 = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_act1 = sprintf("&totalRows_act1=%d%s", $totalRows_act1, $queryString_act1);

$queryString_question = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_question") == false && 
        stristr($param, "totalRows_question") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_question = "&" . htmlentities(implode("&", $newParams));
  }
}
//$queryString_question = sprintf("&totalRows_question=%d%s", $totalRows_question, $queryString_question);

$queryString_qz1 = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_qz1") == false && 
        stristr($param, "totalRows_qz1") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_qz1 = "&" . htmlentities(implode("&", $newParams));
  }
}
//$queryString_qz1 = sprintf("&totalRows_qz1=%d%s", $totalRows_qz1, $queryString_qz1);

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

?>
<?php 
 $i = 1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<link rel="icon" href="../image/San Isidro logo.png">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Take Quiz</title>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="/Student/CSS/Table_NavMainHeaderFooter.css" />
<link rel="stylesheet" type="text/css" href="/Student/CSS/TableContent.css" />
<link rel="stylesheet" type="text/css" href="/Student/CSS/takequizcss.css" />
<script type="text/javascript">;
function updateClock() {
//© OBH 2015 - www.oliverboorman.biz 

var days = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
var months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

var now = new Date();
var day = now.getDay();
var date = now.getDate();
var month = now.getMonth();
var year = now.getFullYear();
var hour = now.getHours();
var minute = now.getMinutes();
var second = now.getSeconds();
var AMorPM = " AM";

if(hour>=12) AMorPM = " PM";
if(hour>12) hour -= 12;

if(hour<10) hour = "0" + hour;
if(minute<10) minute = "0" + minute;
if(second<10) second = "0" + second;

var firstRow = hour + ":" + minute + ":" + second + AMorPM + '';
document.getElementById("row1").innerHTML = firstRow;
var secondRow = days[day] + "&nbsp;" + date + "/" + months[month] + "/" + year + '';
document.getElementById("row2").innerHTML = secondRow;

setTimeout("updateClock()",1000);
} 
</script>
<style>
body {
	background-color: #CCC;
	font-family:Arial, Helvetica, sans-serif;
}

</style>
<link href="../SpryAssets/SpryValidationRadio.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationRadio.js" type="text/javascript"></script>
</head>

<body>


<script>
  function myFunction() {
    window.location.href="<?php printf("%s?pageNum_act1=%d%s", $currentPage, min($totalPages_act1, $pageNum_act1 + 1), $queryString_act1); ?>";
  }
 </script>

<table width="100%" align="center" disable="disable">
  <tr>
    <td width="100%" height="50" id="tdLogoAc"></td>
  </tr>
  <tr>
    <td height="21" id="NavBar" align="center">
    
   <time id="row2"></time><time id="row1"></time>
<script type="text/javascript">updateClock();</script>
</td>
  </tr>
  <tr>
    <td height="100%" align="center" id="tdMain"  >
      <table width="100%" height="100%">
      <tr>
        <td height="9" colspan="14" id="tdQzName">
        <table align="left" width="87%">
        <tr>
        	<td width="120" id="name1">Name: </td>
            <td width="1293" align="lef" id="StudQzName"> <?php echo $row_u['fn']; ?> <?php echo $row_u['ln']; ?></td>

        </tr>
        </table>
		</td>
        </tr>
      <tr >
        <td height="10" colspan="14" id="tdQT"><strong><font size="6">Activity No. <?php echo $row_act1['ActNo']; ?></font></strong></td>
      </tr>
      <tr>
        <td width="1295" height="3" id="tdQT">Date Started: <strong><?php echo $row_act1['DateIn']; ?></strong></td>

        </tr>
      <tr>
        <td height="4" colspan="14" id="tdQT">Date End: <strong><?php echo $row_act1['DateOut']; ?></strong></td>
      </tr>
      <tr>
        <td height="283" colspan="14" align="center">
        <form action="<?php printf("%s?pageNum_act1=%d%s", $currentPage, min($totalPages_act1, $pageNum_act1 + 1), $queryString_act1); ?>" method="post" name="form1" id="form1">
          <table>
              <?php do { ?>
                <tr>
                  <td>
                  <table width="100%" align="center" >
                    <tr valign="baseline">
                      <td align="left" valign="middle" id="tdQst" width="25%"><?php if ($row_act1['id'] >76) echo "A bakery had 950 cookies, and they sold 687 cookies. How many cookies are left in the bakery?";?><br /> <?php echo $row_act1['question']; ?></td>
                    </tr>
                    <tr valign="baseline">
                      <td align="left" valign="middle" ><span id="spryradio1">
                      
                        <label class="container">
                    <input type="radio" name="a" value="A" id="a_0" />
                  <font size="5">  A</font>
                  <strong><font size="5">&nbsp;&nbsp;<?php if($row_act1['id'] == 64 || $row_act1['id'] == 65 || $row_act1['id'] == 66 || $row_act1['id'] == 69 || $row_act1['id'] == 70) 
				  echo "<img src='/Student/image/act10/".$row_act1['a']." width='100'height='100' id='img1'/>"; else echo $row_act1['a'];?></font></strong><br />
				  </font></strong>
                  <span class="checkmark"></span>
                   </label>
                 
                  <label class="container">
                    <input type="radio" name="a" value="B" id="a_1" />
                  <font size="5">   B</font>
                  <strong><font size="5">&nbsp;&nbsp;<?php if($row_act1['id'] == 64 || $row_act1['id'] == 65 || $row_act1['id'] == 66 || $row_act1['id'] == 69 || $row_act1['id'] == 70) 
				  echo "<img src='/Student/image/act10/".$row_act1['b']." width='100'height='100' id='img1'/>"; else echo $row_act1['b'];?></font></strong><br />
                  
                  <span class="checkmark"></span>
                  </label>
                  
                  <label class="container">
                    <input type="radio" name="a" value="C" id="a_2" />
                    <font size="5"> C</font>
                    <strong><font size="5">&nbsp;&nbsp;<?php echo $row_act1['c']; ?></font></strong><br />
                    <span class="checkmark"></span>
                    </label>
                  
                  <label class="container">
                    <input type="radio" name="a" value="D" id="a_3" />
                  <font size="5">   D</font>
                  <strong><font size="5">&nbsp;&nbsp;<?php echo $row_act1['d']; ?></font></strong><br />
                  <span class="checkmark"></span>
                  </label>
                        
                        
                        <span class="radioRequiredMsg">Please make a selection.</span></span><br />
                        </td>
                      </tr>
                    <tr valign="baseline">
                      <td align="center" valign="middle" nowrap="nowrap"><?php if($row_ans['total'] == $row_act1['itemNo'] & $row_ans['itemNo'] == $row_act1['itemNo'] ) echo  " "; else if($row_ans['total'] < $row_act1['itemNo']) echo  '<input type="submit" value="Submit"  style="font-size : 20px; width: 50%; height: 50px;"  />';  ?></td>
                      </tr>
                    <tr valign="baseline">
                      <td align="center" valign="middle" nowrap="nowrap"><input type="hidden" name="id" value="" size="32" />
                        <input type="hidden" name="username" value="<?php echo $row_u['username']; ?>" size="32" />
                        <input type="hidden" name="ActID" value="<?php echo $row_act1['id']; ?>" size="32" />
                        <input type="hidden" name="ActNo" value="<?php echo $row_act1['ActNo']; ?>" size="32" />
                        <input type="hidden" name="DateIn" value="<?php echo $row_act1['DateIn']; ?>" size="32" />
                        <input type="hidden" name="DateOut" value="<?php echo $row_act1['DateOut']; ?>" size="32" />
                        <input type="hidden" name="Teacher" value="<?php echo $row_act1['Teacher']; ?>" size="32" />
                        <input type="hidden" name="sy" value="<?php echo $row_act1['sy']; ?>" size="32" />
                        <input type="hidden" name="itemNo" value="<?php echo $row_act1['itemNo']; ?>" size="32" />
                        <input type="hidden" name="answer" value="<?php echo $row_act1['answer']; ?>" size="32" />
                        <font color="#FF0000">
                        <?php if($row_ans['total'] == $row_act1['itemNo'] & $row_ans['itemNo'] == $row_act1['itemNo'] ) echo  "You finished the Activity!"; else "";  ?>
                        </font></td>
                      </tr>
                    <tr valign="baseline">
                      <td align="center" valign="middle" nowrap="nowrap"><a href="Results2.php?ActNo=<?php echo $row_ans['ActNo'];?>"><?php if($row_ans['total'] == $row_act1['itemNo'] & $row_ans['itemNo'] == $row_act1['itemNo'] ) echo  "View Score"; else "";  ?>
                      </a></td>
                      </tr>
                    </table></td>
                </tr>
                <?php } while ($row_act1 = mysql_fetch_assoc($act1)); ?>
            </table>
            <input type="hidden" name="MM_insert" value="form1" />
        </form>
        </td>
      </tr>
      <tr>
        <td></td>
        </tr>
    </table>
    </td>
  </tr>
  <tr id="trFooter">
    <td height="15" id="tdFooter" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>
<script type="text/javascript">
var spryradio1 = new Spry.Widget.ValidationRadio("spryradio1");
</script>


</body>
</html>
<?php
mysql_free_result($u);

mysql_free_result($act);

mysql_free_result($act1);

mysql_free_result($act2);

mysql_free_result($ans);


?>
