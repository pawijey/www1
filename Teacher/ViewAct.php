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
	
  $logoutGoTo = "/index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
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

$colname_u = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_u = $_SESSION['MM_Username'];
}
mysql_select_db($database_system, $system);
$query_u = sprintf("SELECT * FROM teachers WHERE username = %s", GetSQLValueString($colname_u, "text"));
$u = mysql_query($query_u, $system) or die(mysql_error());
$row_u = mysql_fetch_assoc($u);
$totalRows_u = mysql_num_rows($u);

$colname_my = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_my = $_SESSION['MM_Username'];
}
mysql_select_db($database_system, $system);
$query_my = sprintf("SELECT * FROM mystudents WHERE username = %s ORDER BY ln ASC", GetSQLValueString($colname_my, "text"));
$my = mysql_query($query_my, $system) or die(mysql_error());
$row_my = mysql_fetch_assoc($my);
$totalRows_my = mysql_num_rows($my);

mysql_select_db($database_system, $system);
$query_vact = "SELECT * FROM activities";
$vact = mysql_query($query_vact, $system) or die(mysql_error());
$row_vact = mysql_fetch_assoc($vact);
$totalRows_vact = mysql_num_rows($vact);

$colname_act = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_act = $_SESSION['MM_Username'];
}
mysql_select_db($database_system, $system);
$query_act = sprintf("SELECT *,  sum(IF (a = answer, '1', '0')) as A FROM actanswer WHERE username = %s GROUP BY ActNo", GetSQLValueString($colname_act, "text"));
$act = mysql_query($query_act, $system) or die(mysql_error());
$row_act = mysql_fetch_assoc($act);
$totalRows_act = mysql_num_rows($act);

$colname_ansact = "-1";
if (isset($_GET['ActNo'])) {
  $colname_ansact = $_GET['ActNo'];
}
mysql_select_db($database_system, $system);
$query_ansact = sprintf("SELECT * FROM actanswer WHERE ActNo = %s", GetSQLValueString($colname_ansact, "text"));
$ansact = mysql_query($query_ansact, $system) or die(mysql_error());
$row_ansact = mysql_fetch_assoc($ansact);
$totalRows_ansact = mysql_num_rows($ansact);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>View Activities</title>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="/Teacher/CSS/mainCSS.css">
<link rel="stylesheet" type="text/css" href="/Teacher/CSS/tableCSS.css">
<link rel="stylesheet" type="text/css" href="/Teacher/CSS/TableContent.css">
<link rel="stylesheet" type="text/css" href="/Teacher/CSS/lesson.css" />
<style>


body {
	background-color: #CCC;
	text-align:center;

}

#tdActR{
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	font-size:24px;
	text-shadow: 1px 1px black;
	color: green;
}
</style>
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
      <a href="AddQuiz.php">Add Quiz</a>
      <a href="AddActivities.php">Add Activities</a>
      <a href="ViewQuiz.php">View Quiz</a>
      <a href="ViewAct.php"style="color:gray; cursor: default;"><img src="/image/icons/PixelMixer/folder.ico" width="16" height="16" />View Activities</a>
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
</div>
</td>
  </tr>
  
  
  <tr>
    <td height="515" align="center" id="tdMain"  >
    <table width="100%" border="0" align="center" cellspacing="15" cellpadding="10">
      <tr>
        <td width="86" id="tdCatLs">Activity</td>
        <td width="98" id="tdCatLs">LESSON</td>
        <td width="150" id="tdCatLs">TOTAL ITEMS</td>
        <td width="200" id="tdCatLs">TEACHER</td>
        <td width="150" id="tdCatLs">DATE START</td>
        <td width="122" id="tdCatLs">DUE DATE</td>
        <td width="200" id="tdCatLs">SCHOOL YEAR</td>
        </tr>
        <?php do { ?>
      <tr class="tdPhp">

        <td id="tdActR"><?php echo $row_vact['ActNo']; ?></td>
        <td id="tdActR"><?php echo $row_vact['Lesson']; ?></td>
        <td id="tdActR"><?php echo $row_vact['NoItems']; ?></td>
        <td id="tdActR"><?php echo $row_vact['Teacher']; ?></td>
        <td id="tdActR"><?php echo $row_vact['DateIn']; ?></td>
        <td id="tdActR"><?php echo $row_vact['DateOut']; ?></td>
        <td id="tdActR"><?php echo $row_vact['sy']; ?></td>
        <td width="110" >
        <a id="linkView"href="DeleteActivities.php?ActID=<?php echo $row_vact['ActID'];?>"><img src="/image/icons/VistaIco/Symbol-Delete.ico" alt="DeleteQuiz" width="25" height="25" /></a>
        </td>
        </tr>
        <?php } while ($row_vact = mysql_fetch_assoc($vact)); ?>
    </table>
      <p style="text-align: center">&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
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
<?php
mysql_free_result($u);

mysql_free_result($my);

mysql_free_result($vact);

mysql_free_result($act);

mysql_free_result($ansact);
?>
