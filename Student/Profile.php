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
  $updateSQL = sprintf("UPDATE students SET lrn=%s, username=%s, password=%s, fn=%s, ln=%s, mi=%s, contact=%s, email=%s, adviser=%s, teacher=%s, picture=%s, UserType=%s, sy=%s, dateEncoded=%s WHERE id=%s",
                       GetSQLValueString($_POST['lrn'], "int"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['fn'], "text"),
                       GetSQLValueString($_POST['ln'], "text"),
                       GetSQLValueString($_POST['mi'], "text"),
                       GetSQLValueString($_POST['contact'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['adviser'], "text"),
                       GetSQLValueString($_POST['teacher'], "text"),
                       GetSQLValueString($_POST['picture'], "text"),
                       GetSQLValueString($_POST['UserType'], "int"),
                       GetSQLValueString($_POST['sy'], "text"),
                       GetSQLValueString($_POST['dateEncoded'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_system, $system);
  $Result1 = mysql_query($updateSQL, $system) or die(mysql_error());

  $updateGoTo = "/Student/MsgUpdate2.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
  $updateSQL = sprintf("UPDATE students SET id=%s, lrn=%s, password=%s, fn=%s, ln=%s, mi=%s, contact=%s, email=%s, adviser=%s, teacher=%s, picture=%s, UserType=%s, sy=%s, dateEncoded=%s WHERE username=%s",
                       GetSQLValueString($_POST['id'], "int"),
                       GetSQLValueString($_POST['lrn'], "int"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['fn'], "text"),
                       GetSQLValueString($_POST['ln'], "text"),
                       GetSQLValueString($_POST['mi'], "text"),
                       GetSQLValueString($_POST['contact'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['adviser'], "text"),
                       GetSQLValueString($_POST['teacher'], "text"),
                       GetSQLValueString($_POST['picture'], "text"),
                       GetSQLValueString($_POST['UserType'], "int"),
                       GetSQLValueString($_POST['sy'], "text"),
                       GetSQLValueString($_POST['dateEncoded'], "text"),
                       GetSQLValueString($_POST['username'], "text"));

  mysql_select_db($database_system, $system);
  $Result1 = mysql_query($updateSQL, $system) or die(mysql_error());

  $updateGoTo = "/Student/MsgUpdate.php";
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
$query_u = sprintf("SELECT * FROM students WHERE username = %s", GetSQLValueString($colname_u, "text"));
$u = mysql_query($query_u, $system) or die(mysql_error());
$row_u = mysql_fetch_assoc($u);
$totalRows_u = mysql_num_rows($u);

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
<title>Student | Profile</title>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="/Student/CSS/Table_NavMainHeaderFooter.css" />
<link rel="stylesheet" type="text/css" href="/Student/CSS/profile.css" />
<style>
body,td,th {
	font-family: Georgia, "Times New Roman", Times, serif;
	font-size: 12px;
}
body {
	background-color: #CCC;
}
</style>
</head>

<body>
<table width="100%" height="100%" align="center" >
  <tr>
    <td width="100%" height="150" id="tdLogoPr"></td>
  </tr>
  <tr>
      <td height="50" id="NavBar">
 <h1 id="name" > Welcome, <?php echo $row_u['fn']; ?> !</h1>
  <a href="StudentPage.php" id="link">Home</a>
  <a href="Profile.php"id="link" style="color:red;cursor:default;">Profile</a>
  <a href="Lessons.php"id="link">Lessons</a>
 <a href="Quiz.php"id="link">Quiz</a>
 <a href="Activities.php"id="link">Activities</a>
  
 
   <a href="<?php echo $logoutAction ?>" id="logoutbutton">Logout</a>
      
   </td>
  </tr>
  <tr>
    <td height="100%" align="center" id="tdMain" >
    <table width="500" height="100%" id="tableProfile">
        <tr>
          <td><img src="/Student/image/<?php echo $row_u['picture']; ?>" alt="UserImage" width="200" height="200" id="userImg"/>
          <div class="divName"><p><?php echo $row_u['fn']; ?></p></div></td>
          <td>&nbsp;
            <form action="<?php echo $editFormAction; ?>" method="post" name="form2" id="form2" enctype="multipart/form-data">
              <table width="100%" height="100%"align="center">
                <tr valign="baseline">
                  
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap="nowrap" id="tdCatUp">Username:</td>
                  <td id="tdCatInput"><input type="text" name="username" value="<?php echo $row_u['username']; ?>" size="32" /></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap="nowrap" id="tdCatUp">Password:</td>
                  <td id="tdCatInput"><input type="password" name="password" value="<?php echo htmlentities($row_u['password'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap="nowrap" id="tdCatUp">Firstname:</td>
                  <td id="tdCatInput"><input type="text" name="fn" value="<?php echo htmlentities($row_u['fn'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap="nowrap" id="tdCatUp">Lastname:</td>
                  <td id="tdCatInput"><input type="text" name="ln" value="<?php echo htmlentities($row_u['ln'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap="nowrap" id="tdCatUp">Middle Initial:</td>
                  <td id="tdCatInput"><input type="text" name="mi" value="<?php echo htmlentities($row_u['mi'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap="nowrap" id="tdCatUp">Contact:</td>
                  <td id="tdCatInput"><input type="text" name="contact" value="<?php echo htmlentities($row_u['contact'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap="nowrap">&nbsp;</td>
                  <td ><input type="submit" value="Update record" id="tdCatbutton"/></td>
                </tr>
              </table>
              <input type="hidden" name="id" value="<?php echo htmlentities($row_u['id'], ENT_COMPAT, 'utf-8'); ?>" />
              <input type="hidden" name="email" value="<?php echo htmlentities($row_u['email'], ENT_COMPAT, 'utf-8'); ?>" />
              <input type="hidden" name="adviser" value="<?php echo htmlentities($row_u['adviser'], ENT_COMPAT, 'utf-8'); ?>" />
              <input type="hidden" name="teacher" value="<?php echo htmlentities($row_u['teacher'], ENT_COMPAT, 'utf-8'); ?>" />
              <input type="hidden" name="picture" value="<?php echo htmlentities($row_u['picture'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
              <input type="hidden" name="UserType" value="<?php echo htmlentities($row_u['UserType'], ENT_COMPAT, 'utf-8'); ?>" />
              <input type="hidden" name="sy" value="<?php echo htmlentities($row_u['sy'], ENT_COMPAT, 'utf-8'); ?>" />
              <input type="hidden" name="dateEncoded" value="<?php echo htmlentities($row_u['dateEncoded'], ENT_COMPAT, 'utf-8'); ?>" />
              <input type="hidden" name="MM_update" value="form2" />
              <input type="hidden" name="username" value="<?php echo $row_u['username']; ?>" />
            </form>
          <p>&nbsp;</p></td>
        </tr>
      </table>
      &nbsp;
    <p>&nbsp;</p></td>
  </tr>
  <tr id="trFooter">
    <td height="15" id="tdFooter" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>

<script type="text/javascript">

const togglePassword = document.querySelector('#togglePassword');
const password = document.querySelector('#password');

togglePassword.addEventListener('click', function (e) {
    // toggle the type attribute
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    // toggle the eye / eye slash icon
    this.classList.toggle('bi-eye');
});
</script>
</body>
</html>
<?php
mysql_free_result($u);
?>
