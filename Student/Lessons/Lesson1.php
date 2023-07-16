<?php require_once('../../Connections/system.php'); ?>
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
	
  $logoutGoTo = "../../index.php";
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

$maxRows_les = 5;
$pageNum_les = 0;
if (isset($_GET['pageNum_les'])) {
  $pageNum_les = $_GET['pageNum_les'];
}
$startRow_les = $pageNum_les * $maxRows_les;

$colname_les = "-1";
if (isset($_GET['id'])) {
  $colname_les = $_GET['id'];
}
mysql_select_db($database_system, $system);
$query_les = sprintf("SELECT * FROM lesson WHERE id = %s", GetSQLValueString($colname_les, "int"));
$query_limit_les = sprintf("%s LIMIT %d, %d", $query_les, $startRow_les, $maxRows_les);
$les = mysql_query($query_limit_les, $system) or die(mysql_error());
$row_les = mysql_fetch_assoc($les);

if (isset($_GET['totalRows_les'])) {
  $totalRows_les = $_GET['totalRows_les'];
} else {
  $all_les = mysql_query($query_les);
  $totalRows_les = mysql_num_rows($all_les);
}
$totalPages_les = ceil($totalRows_les/$maxRows_les)-1;

mysql_select_db($database_system, $system);
$query_qz = "SELECT * FROM quiz";
$qz = mysql_query($query_qz, $system) or die(mysql_error());
$row_qz = mysql_fetch_assoc($qz);
$totalRows_qz = mysql_num_rows($qz);

mysql_select_db($database_system, $system);
$query_qstn = "SELECT * FROM quest";
$qstn = mysql_query($query_qstn, $system) or die(mysql_error());
$row_qstn = mysql_fetch_assoc($qstn);
$totalRows_qstn = mysql_num_rows($qstn);

$colname_lslnk = "-1";
if (isset($_GET['id'])) {
  $colname_lslnk = $_GET['id'];
}
mysql_select_db($database_system, $system);
$query_lslnk = sprintf("SELECT * FROM lessonlink WHERE id = %s", GetSQLValueString($colname_lslnk, "int"));
$lslnk = mysql_query($query_lslnk, $system) or die(mysql_error());
$row_lslnk = mysql_fetch_assoc($lslnk);
$totalRows_lslnk = mysql_num_rows($lslnk);

$colname_lstxt = "-1";
if (isset($_GET['id'])) {
  $colname_lstxt = $_GET['id'];
}
mysql_select_db($database_system, $system);
$query_lstxt = sprintf("SELECT * FROM lessontext WHERE id = %s", GetSQLValueString($colname_lstxt, "int"));
$lstxt = mysql_query($query_lstxt, $system) or die(mysql_error());
$row_lstxt = mysql_fetch_assoc($lstxt);
$totalRows_lstxt = mysql_num_rows($lstxt);

$colname_activty = "-1";
if (isset($_GET['ActNo'])) {
  $colname_activty = $_GET['ActNo'];
}
mysql_select_db($database_system, $system);
$query_activty = sprintf("SELECT * FROM activities WHERE ActNo = %s", GetSQLValueString($colname_activty, "text"));
$activty = mysql_query($query_activty, $system) or die(mysql_error());
$row_activty = mysql_fetch_assoc($activty);
$totalRows_activty = mysql_num_rows($activty);

$colname_Recordset1 = "-1";
if (isset($_GET['actid'])) {
  $colname_Recordset1 = $_GET['actid'];
}
mysql_select_db($database_system, $system);
$query_Recordset1 = sprintf("SELECT * FROM actextra WHERE actid = %s", GetSQLValueString($colname_Recordset1, "int"));
$Recordset1 = mysql_query($query_Recordset1, $system) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

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

$php1txt1 = $row_lstxt['text1'];
$php1txt2 = $row_lstxt['text2'];


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<link rel="icon" href="../image/San Isidro logo.png">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Lessons</title>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="/Student/CSS/Table_NavMainHeaderFooter.css" />
<link rel="stylesheet" type="text/css" href="/Student/CSS/TableContent.css" />
<link rel="stylesheet" type="text/css" href="/Student/CSS/lesson1.css" />
<link rel="stylesheet" type="text/css" href="/Student/Lessons/CSS/modal.css" />
<style>


body {
	background-color: #CCC;

}

#tdVid {
	text-align: center;
}
</style>
</head>

<body>

<table width="100%" height="100%" align="center" cellpadding="0" >
<!-- Navbar starts here -->
  <tr>
    <td width="100%" height="200" id="tdLogoLs"></td>
  </tr>
  <tr>
     <td height="50" id="NavBar">
     
  <h1 id="name" > Welcome! <?php echo $row_u['fn']; ?> </h1>
  <a href="../StudentPage.php" id="link" >Home</a>
  <a href="../Profile.php" id="link">Profile</a>
  <a href="../Lessons.php" id="link" style="color:white;cursor:default;">Lessons</a>
 <a href="../Quiz.php" id="link" >Quiz</a>
 <a href="../Activities.php" id="link">Activities</a>
  
 
   <a href="<?php echo $logoutAction ?>" id="logoutbutton">Logout</a>
      
   
</td>
  </tr>
 <!-- Navbar ends here -->
  <tr height="100%">
  

  
    <td height="100%" align="center" id="tdMain">
    
      <!---- tdMain starts here ----->
    
     <div class="LessonText">
      <h1 id="tdQuizR">
        <?php if ($row_les['id'] == 1 ||$row_les['id']== 4 || $row_les['id']==6) echo $row_lstxt['text1']; else echo $row_les['LesNo']; ?>
      </h1>
      <h2 id="tdTitle">
        <?php if ($row_les['id'] == 1 ||$row_les['id']== 4 || $row_les['id']==6) echo $row_lstxt['text2']; else echo $row_les['title']; ?>
      </h2>
      
      <button id='myBtn'>View More</button>
<br />
<br />

     </div>
   
      <table width="100%" height="100%" cellpadding="20" cellspacing="10" >
        <tr height="100%">
        
          <td id="tdVid" align="right" height="50%">
          <video width="50%" height="50%" controls="controls" id="mVid">
                  <source src="../../MathLessonsMp4/Lessons/<?php echo $row_les['file']; ?>" type="video/ogg" />
                </video></td>
        </tr>
        
      </table>
      <div id="myModal" class="modal">

  <!-- Modal content -->
  
  
  <div class="modal-content">
  <span class="close">&times;</span>
  <div id="lesson1">
    <h3>Upon completing this lessons, you will achieve the following learning outcomes:</h3>
    <p>By the end of this lesson, you will be able to:</p>
    <ul class="b">
    <li>Recognize numbers and understand their significance.</li>
    <li>Differentiate between a digit and a number.</li>
    <li>Identify the place value of a digit within a given number.</li>
    <li>Perform addition with whole numbers up to three digits.</li>
    <li>Perform subtraction with whole numbers up to three digits.</li>
    <li>Apply your knowledge of addition and subtraction to solve practical problems encountered in daily life.</li>
    </ul>
	<h3 id="text4">Through these lessons , you will develop a strong foundation in mathematics and problem solving skills, enabling you to confidently apply addition and subtraction skills in various real-life situations.</h3>
<hr />
<br />
	<div class="TextInside">
	<h1>Lesson 1: Place-Value Numeration System</h1>
      <h2>This lesson will teach you about the numerical system known as place value. You will gain an understanding of the significance of each digit within a number. It is important to know this concept before going into addition and subtraction.</h2>
      <p>By the end of this lesson, you will be able to:</p>
      <ul class="b">
      <li>differentiate a digit and a number; and</li>
      <li>identify the value of a digit in a given number</li>
      </ul>
      </div>
      </div>
      
      
  <!-------------------------------------------------------------------------------------lesson2----------------------------------------------->
      <div id="lesson2">
      <h3>After watching these video lessons, you will have a solid understanding of addition and be able to confidently perform addition operations with different types of numbers. This foundational knowledge will serve as a stepping stone for more advanced mathematical concepts and problem-solving techniques. So let's dive in and explore the exciting world of addition!</h3>
    <p>By the end of this lesson, you will be able to:</p>
    <ul class="b">
    <li>add whole numbers up to three digits; and</li>
    <li>apply your knowledge of addition to solve daily problems</li>
    </ul>
	<hr />
<br />
	<div class="TextInside">
      <h2>Steps when using short method</h2>
      <ol class="b">
  <li>Write the addends in column form</li>
  <li>Add the ones</li>
  <li>Add the tens </li>
</ol><br>
<h2>Steps when using expanded form</h2>
<ol class="b">
  <li>Write the addends in column.</li>
  <li>Write the expanded form of the addends.</li>
  <li>Add the ones first followed by the tens.</li>
  <li>Regroup the sum.</li>
  <li>Put together the tens.</li>
  <li>Add all the sum of the hundreds, tens, and ones.</li>
</ol><br>
<h2>add with regrouping using the short method</h2>
<ol class="b">
  <li>Write the addends in column form</li>
  <li>Add the ones ( Regroup 1 in the tens place)</li>
  <li>Add the tens</li>
</ol><br>
<h2>3-digit addends with regrouping using the short method</h2>
<ol class="b">
  <li>Write the addends in column form</li>
  <li>Add the ones. Regroup 1 in the tens place</li>
  <li>Add the tens .Regroup 1 in the hundreds place</li>
  <li>Add the hundreds</li>
</ol><br>
</div>
</div>


<!--------------------------------------------------------------Lesson3------------------------------------------------------------------>
<div id="lesson3">
      <h3>This knowledge will not only be valuable in practical situations but will also serve as a foundation for more advanced mathematical concepts and problem-solving strategies. So let's get started and explore the fascinating world of subtraction!</p>
    <p>By the end of this lesson, you will be able to:</p>
    <ul class="b">
    <li>subtract whole numbers up to three digits; and</li>
    <li>apply your knowledge of subtraction to solve daily problems</li>
      </ul>
	<hr />
<br />
	<div class="TextInside">

      <h2>Steps in Subtracting Numbers Without Regrouping - Expanded Method</h2>
      <ol class="b">
  <li>Write the numbers in column form.</li>
  <li>Write the expanded form.</li>
  <li>Subtract the ones.</li>
  <li>Subtract the tens.</li>
  <li>Add the sum of the difference of the tens and ones.</li>
<br />
</ol><br>
<h2>Steps in Subtracting Numbers Without Regrouping - Short Method</h2>
<ol class="b">
  <li>Write the numbers in the column.</li>
  <li>Subtract the ones.</li>
  <li>Subtract the tens.</li>
  <li>Check your answer by adding.</li>
</ol><br>
<h2>Steps in Subtracting Numbers With Regrouping - Expanded Method</h2>
<ol class="b">
  <li>Write the number in the column and write them in expanded form.</li>
  <li>Subtract the ones.</li>
  <li>Subtract the tens.</li>
  <li>Subtract the hundreds.</li>
  <li>Add the sum of the difference of the tens and ones</li>
</ol><br>
<h2>Steps in Subtracting Numbers With Regrouping - Short Method</h2>
<ol class="b">
  <li>Write the numbers in the column.</li>
  <li>Subtract the ones. Regroup the tens to the ones.</li>
  <li>Subtract the tens.</li>
  <li>Subtract the hundreds</li>
  <li>Check your answer by adding</li>
</ol><br>
<h2>Steps in Subtracting Numbers With Zeros - Expanded Method</h2>
<ol class="b">
  <li>Write the numbers in a column and then write them in expanded form.</li>
  <li>Subtract the ones. If ones is not possible, regroup the tens.</li>
  <li>Subtract the tens. If tens is not possible, regroup the hundreds.</li>
  <li>Subtract the hundreds.</li>
  <li>Add all the differences of ones, tens and hundreds.</li>
</ol><br>
<h2>Steps in Subtracting Numbers With Zeros - Short Method</h2>
<ol class="b">
  <li>Write the numbers in the column.</li>
  <li>Subtract the ones. If not possible, regroup.</li>
  <li>Subtract the tens. If not possible, regroup.</li>
  <li>Subtract the hundreds.</li>
  <li>Check your answer by adding.</li>
</ol><br>

</div>
</div>

            <p>You can also watch this video: <button id="showlinks">View linked videos</button> </p>
      
      
		<div id="listlinks">
        <hr />
        <ul>
	  <li><a href="<?php echo $row_lslnk['link1'];?>" target="iframe_a" id="link1">Link 1</a></li>
	  <li><a href="<?php echo $row_lslnk['link2'];?>" target="iframe_a" id="link2">Link 2</a></li>
	  <li><a href="<?php echo $row_lslnk['link3'];?>" target="iframe_a" id="link3">Link 3</a></li>
       <li> <?php if ($row_les['id'] == 6) echo "<a href='".$row_lslnk['link4']."' target='iframe_a' id='link4'>Link 4</a>"; else echo "";?></li>
       </ul>
	<hr /><br />
        <iframe src="" name="iframe_a" height="600px" width="800px%" title="youtube video"></iframe>

        </div>
</div>        



</div>

      
      </td>
      
      
<!------ tdMain ends here ------->   
      
  </tr>
  <tr align="center">
  <td> <a href="javascript:history.back()">Go Back</a> &nbsp;&nbsp;<?php if ($row_les['id'] == 3 || $row_les['id'] == 5 || $row_les['id'] == 11) echo "<a href='/Student/Quiz.php'>Quizzes</a> &nbsp;&nbsp;";?>  <a href="../Activities.php">Activities</a></td> 
  
  
  
</tr>
<tr id="trFooter">
    <td height="15" id="tdFooter" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>

</body>
</html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script>
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

var les1 = document.getElementById("lesson1");
var les2 = document.getElementById("lesson2");
var les3 = document.getElementById("lesson3");

var vid = document.getElementById("mVid");

// Get php variable
	php = '<?php echo $row_les['id'];?>';
if (php ==="1"){
	btn.style.display ="block";}
	else if (php ==="4"){
	btn.style.display ="block";}
	else if (php ==="6"){
	btn.style.display ="block";}
	else {btn.style.display ="none";}

// When the user clicks the button, open the modal 
btn.onclick = function() {
  modal.style.display = "block";
  vid.pause(); 
  if (php === "1"){
	  les2.style.display ="none";
	  les3.style.display ="none";
  }
  else if(php ==="4"){
	  les1.style.display ="none";
	  les3.style.display ="none";
  }
  else if(php ==="6"){
	  les1.style.display ="none";
	  les2.style.display ="none";
  }
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";

	
  }
}
</script>
<script>
	  var shlinks = document.getElementById("showlinks");
	var dvlinks = document.getElementById("listlinks");
		
		shlinks.onclick = function() {
			if (dvlinks.style.display ==="block"){
			dvlinks.style.display ="none";
		} else if(dvlinks.style.display ==="none"){
			dvlinks.style.display ="block"
			} else { dvlinks.style.display ="block";}
		
		
		
		}
		</script>

<?php
mysql_free_result($u);

mysql_free_result($les);

mysql_free_result($qz);

mysql_free_result($qstn);

mysql_free_result($lslnk);

mysql_free_result($lstxt);

mysql_free_result($activty);

mysql_free_result($Recordset1);
?>
