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

$maxRows_act = 5;
$pageNum_act = 0;
if (isset($_GET['pageNum_act'])) {
  $pageNum_act = $_GET['pageNum_act'];
}
$startRow_act = $pageNum_act * $maxRows_act;

mysql_select_db($database_system, $system);
$query_act = "SELECT * FROM actextra";
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

mysql_select_db($database_system, $system);
$query_act1 = "SELECT * FROM actsub";
$act1 = mysql_query($query_act1, $system) or die(mysql_error());
$row_act1 = mysql_fetch_assoc($act1);
$totalRows_act1 = mysql_num_rows($act1);

mysql_select_db($database_system, $system);
$query_Qstn = "SELECT *, count(actno) as Tot FROM actsub GROUP BY actno ORDER BY id ASC ";
$Qstn = mysql_query($query_Qstn, $system) or die(mysql_error());
$row_Qstn = mysql_fetch_assoc($Qstn);
$totalRows_Qstn = mysql_num_rows($Qstn);

$colname_actup = "-1";
if (isset($_GET['actid'])) {
  $colname_actup = $_GET['actid'];
}
mysql_select_db($database_system, $system);
$query_actup = sprintf("SELECT * FROM actup WHERE username = %s", GetSQLValueString($colname_actup, "text"));
$actup = mysql_query($query_actup, $system) or die(mysql_error());
$row_actup = mysql_fetch_assoc($actup);
$totalRows_actup = mysql_num_rows($actup);

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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<link rel="icon" href="../image/San Isidro logo.png">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Activties | Extra</title>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="/Student/CSS/Table_NavMainHeaderFooter.css" />
<link rel="stylesheet" type="text/css" href="/Student/CSS/TableContent.css" />
<style>

body {
	background-color: #CCC;
}

</style>
</head>

<body>
<table width="100%" height="100%"align="center" >
<!-- Navbar starts here -->
  <tr>
    <td width="100%" height="150" id="tdLogoAc"></td>
  </tr>
  <tr>
    <td height="50" bgcolor="#FFFFFF" id="NavBar">

  <h1 id="name" >Welcome, <?php echo $row_u['fn']; ?> !</h1>
  <a href="StudentPage.php" id="link" >Home</a>
  <a href="Profile.php" id="link">Profile</a>
  <a href="Lessons.php" id="link">Lessons</a>
 <a href="Quiz.php" id="link" >Quiz</a>
 <a href="Activities.php" id="link" style="color:red;cursor:default;">Activities</a>
  
 
   <a href="<?php echo $logoutAction ?>" id="logoutbutton">Logout</a>
      
   
</td>
  </tr>
  <tr>
    <td height="100%" align="center" id="tdMain" ><p>&nbsp;</p>
      <table width="100%">
      <tr>
        <td width="612" height="21" align="center" id="tdQuizR">LIST OF ACTIVITIES:</td>
      </tr>
      <tr>
        <td height="21">
        <table width="100%" align="center" cellpadding="5" cellspacing="15">
            <tr id="qzCat">
              <td width="50" align="center"  id="tdQzC">Activity</td>
              <td width="300" align="center" id="tdQzC">Name</td>
              <td width="80" align="center" id="tdQzC">Date Start</td>
              <td width="80" align="center"id="tdQzC">Date End</td>
              <td width="41" align="center"id="tdQzC">Take</td>
            </tr>
            <?php do { ?>
              <tr id="qzCatRd">
                <td align="center" id="tdLsR" ><?php echo $row_act['actno']; ?></td>
                <td align="center" id="tdLsR"> <?php echo $row_act['actname'];?></td>
                <td align="center" id="tdLsR" ><?php echo $row_act['DateIn']; ?></td>
                <td align="center" id="tdLsR" ><?php echo $row_act['DateOut']; ?></td>
                <td align="center" id="tdLsR" ><a href="TakeActivities1.php?actno=<?php echo $row_act['actno'];?>">
				<?php if($row_act['ActNo'] = $row_Qstn['Tot']) echo '<img src="../image/icons/Pinvoke/pencil1.png" width="16" height="16" /></a>' ; elseif ($row_act['ActNo'] != $row_Qstn['Tot']) echo "" ;   ?></a></td>
              </tr>
              <?php } while ($row_act = mysql_fetch_assoc($act)); ?>
          </table>
          </td>
      </tr>
      <tr>
        <td height="21"></td>
      </tr>
      <tr>
        <td height="40" align="center">
        <table border="0">
          <tr>
              <td></td>
            </tr>
            <tr>
            	<td>
</td>
            </tr>
        </table>
        </td>
      </tr>
    </table>
      <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p></td>
  </tr>
   <tr id="trFooter">
    <td height="21" id="tdFooter" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>
</body>
</html>
<script>
	var php = '<?php echo $row_actup['actno'];?>';
	var php1 = '<?php echo $row_act1['actno'];?>';
	var head = document.getElementById("text");
	if (php !== php1){

	}
		
	
</script>
<?php
mysql_free_result($u);

mysql_free_result($act);

mysql_free_result($act1);

mysql_free_result($Qstn);

mysql_free_result($actup);
?>
