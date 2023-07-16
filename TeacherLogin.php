<?php require_once('Connections/system.php'); ?>
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
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['username'])) {
  $loginUsername=$_POST['username'];
  $password=$_POST['password'];
  $MM_fldUserAuthorization = "";
  $MM_redirectLoginSuccess = "Teacher/TeacherPage.php";
  $MM_redirectLoginFailed = "error.php";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_system, $system);
  
  $LoginRS__query=sprintf("SELECT username, password FROM teachers WHERE username=%s AND password=%s",
    GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $system) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
     $loginStrGroup = "";
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Teacher| Home</title>

<link rel="icon" href="../image/San Isidro logo.png">
<link rel="stylesheet" type="text/css" href="/CSS/linkindex.css" />
<link rel="stylesheet" type="text/css" href="/CSS/tableindex.css" />
<link rel="stylesheet" type="text/css" href="/CSS/userlogin.css" />

<script src="SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
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

<style type="text/css">
body,td,th {
	font-family: Georgia, "Times New Roman", Times, serif;
	font-size: 12px;
}
body {
	background-color: #CCC;
}
</style>
<link href="SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%" align="center" >

  <tr>
    <td width="100%" height="400" id="tdLogo"><</td>
  </tr>
  <tr>
    <td height="10"  width="100%">
    <table width="100%"  cellpadding="0" class="NavTable">
        <tr class="trNav" height="50">
          <td width="850" height="40" bgcolor="" id="tdTime"><time id="row2"></time> 
            <time id="row1"></time>
          <time id="row2"></time>            <script type="text/javascript">updateClock();</script>          </td>
          <td width="64" align="center"><a href="TeacherLogin.php" id="aTeach" style="color:red;">TEACHER</a></td>
          <td width="70" align="center"><a href="StudentLogin.php" id="aStud">STUDENT</a></td>
        </tr>
    </table>
    </td>
  </tr>
  
  <tr>
    <td height="500" align="center"  background="" id="tdMain">
    <table width="1000" height="100%">
      <tr>
        <td width="684" height="592" align="center" id="tdWelcome"><h1 id="welcome">WELCOME!</h1>
        <hr>
        <p id="pQuote">"DEVELOP A PASSION FOR LEARNING. IF YOU DO, YOU WILL NEVER CEASE TO GROW."<br />-ANTHONY J. D'ANGELO</p>
        </td>
        <td width="304" align="center"><p>&nbsp;</p>
          <p>&nbsp;</p>
          <p>&nbsp;</p>
          <p>&nbsp;</p>
          <p>&nbsp;</p>
        <table width="246" height="187">
          <tr>
              <td width="148" id="loginform">
              <img src="/image/TeachLogin.png" width="100" height="100" id="userIcon" align="center"/>
              <form id="login" name="login" method="POST" action="<?php echo $loginFormAction; ?>">
                <table width="148" align="center">
                  <tr>
                    <td width="140" align="center">
                    <label for="username"></label>
                    <span id="sprytextfield1">
                    <input type="text" name="username" id="username" placeholder="Username" class="input"/>
                    <span class="textfieldRequiredMsg">A value is required.</span></span></td>
                  </tr>
                  <tr>
                    <td>
                    <label for="password"></label>
                    <span id="sprytextfield2">
                    <input type="password" name="password" id="password" placeholder="Password"/>
                    <span class="textfieldRequiredMsg">A value is required.</span></span></td>
                  </tr>
                  <tr>
                    <td align="center">
                    <input type="submit" name="button" id="button" value="Sign-In" />
                    </td>
                  </tr>
                </table>
              </form>
              </td>
            </tr>
    </table>
          <p>&nbsp;</p>
          <p>&nbsp;</p>
          <p>&nbsp;</p>
          <p>&nbsp;</p>
          <p>&nbsp;</p>
          <p>&nbsp;</p>
          <p>&nbsp;</p>
          </td>
      </tr>
    </table ></td>
  </tr>
  <tr bgcolor="#FF3FAA" id="trFooter">
    <td height="15" id="tdFooter" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");

</script>
</body>
</html>