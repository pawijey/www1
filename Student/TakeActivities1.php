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
if (isset($_GET['actno'])) {
  $colname_act = $_GET['actno'];
}
mysql_select_db($database_system, $system);
$query_act = sprintf("SELECT * FROM actextra WHERE actno = %s", GetSQLValueString($colname_act, "text"));
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
if (isset($_GET['actno'])) {
  $colname_act1 = $_GET['actno'];
}
mysql_select_db($database_system, $system);
$query_act1 = sprintf("SELECT * FROM actsub WHERE actno = %s", GetSQLValueString($colname_act1, "text"));
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
if (isset($_GET['actno'])) {
  $colname_act2 = $_GET['actno'];
}
mysql_select_db($database_system, $system);
$query_act2 = sprintf("SELECT * FROM actsub WHERE actno = %s", GetSQLValueString($colname_act2, "text"));
$act2 = mysql_query($query_act2, $system) or die(mysql_error());
$row_act2 = mysql_fetch_assoc($act2);
$totalRows_act2 = mysql_num_rows($act2);

$colname_ans = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_ans = $_SESSION['MM_Username'];
}
$colname2_ans = "-1";
if (isset($_GET['actno'])) {
  $colname2_ans = $_GET['actno'];
}
mysql_select_db($database_system, $system);
$query_ans = sprintf("SELECT *, count(actno) as total FROM actup WHERE username = %s and actno = %s ", GetSQLValueString($colname_ans, "text"),GetSQLValueString($colname2_ans, "text"));
$ans = mysql_query($query_ans, $system) or die(mysql_error());
$row_ans = mysql_fetch_assoc($ans);
$totalRows_ans = mysql_num_rows($ans);

mysql_select_db($database_system, $system);
$query_Recordset1 = "SELECT * FROM actup";
$Recordset1 = mysql_query($query_Recordset1, $system) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

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

date_default_timezone_set('Asia/Manila');

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
.container1 {
    width: 50%;
    height: 80%;
    display: block;
    justify-content: center;
    align-items: center;
	margin-top:1%;
	padding-top:50px;
	padding-bottom:50px;
	background-color: rgb(204,204,204,0.5);
	cursor:default;
	font-family:"Times New Roman", Times, serif;
	box-shadow: 4px 5px green;
	border-top: 1px solid green;
	border-left: 1px solid green;
	border-radius: 10px;
	}
.container1 li{
	text-align: left;
	font-size: 24px;
	margin-left: 5%;
}
.container1 img {
    margin-bottom:1%;
}
.container1 h2 {
	text-align:justify;
	text-justify:inter-word;
	width: 90%;
	color: black;
	-webkit-text-stroke: 0.5px rgb(0,128,0);
	font-size: 24px;
}
link {
	visibility:visible;
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
        <td height="10" colspan="14" id="tdQT"><strong><font size="6">Activity No. <?php echo $row_act1['actno']; ?></font></strong></td>
      </tr>
      <tr>
        <td width="1295" height="3" id="tdQT">Date Started: <strong><?php echo $row_act1['DateIn']; ?></strong></td>

        </tr>
      <tr>
        <td height="4" colspan="14" id="tdQT">Date End: <strong><?php echo $row_act1['DateOut']; ?></strong></td>
      </tr>
      <tr>
        	<td align="center"><strong><font size="6">Activity <?php echo $row_act['actno'];?>:</font> <font size="7" color=" green"><?php echo $row_act['actname'];?></strong></font><br />
            	<a id="link" href="/Student/actupload.php?actno=<?php echo $row_act1['actno']; ?>">Take Activity</a>
                <h1 id="score">SCORE: <?php echo $row_ans['score']; ?></h1></td>
        </tr>
      <tr>
        <td height="800" align="center" >
        <div class="container1" id="container1">
        <h1> Instructions:</h1>
        	<ol>
            	<li>Read and analyze the situation.</li>
                <li>Answer the problem in your notebook.</li>
                <li>Take a picture of your answer.</li>
                <li>Submit it using the submit button.</li>
              </ol>
                
                <h2><strong>Tom has 42 pencils in his pencil case. His friend Jake gives him 13 more pencils. How many pencils does Tom have now?</strong></h2>
                <img src="/Teacher/image/act1.png" width="600" height="350" /><br />

                <a href="/Teacher/ExtraActivities/<?php echo $row_act1['subfile'];?>">Download Activity</a>
<br />

        </div>
        <div class="container1" id="container2">
        <h1> Instructions:</h1>
        	<ol>
            	<li>Read and analyze the situation.</li>
                <li>Answer the problem in your notebook.</li>
                <li>Take a picture of your answer.</li>
                <li>Submit it using the submit button.</li>
              </ol>
                
                <h2><strong>Mary has 25 stickers, and her friend John gives her 12 more stickers. How many stickers does Mary have in total?</strong></h2>
                <img src="/Teacher/image/ac2.png" width="600" height="350" /><br />

                <a href="/Teacher/ExtraActivities/<?php echo $row_act1['subfile'];?>">Download Activity</a>
<br />

        </div>
        <div class="container1" id="container3">
        <h1> Instructions:</h1>
        	<ol>
            	<li>Read and analyze the situation.</li>
                <li>Answer the problem in your notebook.</li>
                <li>Take a picture of your answer.</li>
                <li>Submit it using the submit button.</li>
              </ol>
                
                <h2><strong>At the school fair, there were 256 people with tickets sold for the rides. However, due to bad weather, 89 people with tickets decided not to attend the fair. How many people actually attended the fair?</strong></h2>
                <img src="/Teacher/image/act3.png" width="600" height="350" /><br />

                <a href="/Teacher/ExtraActivities/<?php echo $row_act1['subfile'];?>">Download Activity</a>
<br />

        </div>
        <br />
<br />
<br />

        </td>
      </tr>
      <tr>
        <td align="center"><a href="Activities1.php">Go Back</a></td>
        </tr>
    </table>
    </td>
  </tr>
  <tr id="trFooter">
    <td height="15" id="tdFooter" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>

</body>
</html>
<script>
// Get php variable $act['actno']
	php = '<?php echo $row_act['actno'];?>';

	

	php1 = '<?php echo $row_ans['total'];?>';
	php2 = '<?php echo $row_ans['username'];?>';
	php3 = '<?php echo $row_u['username'];?>';

	
	var link = document.getElementById("link");
	if (php1 == "1" && php2){
		link.style.visibility ="hidden";
		document.getElementById("score").style.visibility="visible";
	} else {
		document.getElementById("score").style.visibility="hidden";
	}
	
	
	
// Get div container1
	var con1 = document.getElementById("container1");
// Get div container2
	var con2 = document.getElementById("container2");
// Get div container3
	var con3 = document.getElementById("container3");
	
	if (php == "1"){
		con2.style.display = "none";
		con3.style.display = "none";
	}
	else if(php =="2"){
		con1.style.display = "none";
		con3.style.display = "none";
	}
	else if(php =="3"){
		con1.style.display = "none";
		con2.style.display = "none";
	}
		
</script>
<?php
mysql_free_result($u);

mysql_free_result($act);

mysql_free_result($act1);

mysql_free_result($act2);

mysql_free_result($ans);

mysql_free_result($Recordset1);
?>
