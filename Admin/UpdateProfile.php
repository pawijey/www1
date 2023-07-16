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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE `admin` SET username=%s, password=%s, fn=%s, ln=%s, mi=%s, bday=%s, address=%s, contact=%s, email=%s, pic=%s, UserType=%s WHERE id=%s",
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['fn'], "text"),
                       GetSQLValueString($_POST['ln'], "text"),
                       GetSQLValueString($_POST['mi'], "text"),
                       GetSQLValueString($_POST['bday'], "text"),
                       GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString($_POST['contact'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['pic'], "text"),
                       GetSQLValueString($_POST['UserType'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_system, $system);
  $Result1 = mysql_query($updateSQL, $system) or die(mysql_error());

  $updateGoTo = "UpdateMsg.php";
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
$query_u = sprintf("SELECT * FROM `admin` WHERE username = %s", GetSQLValueString($colname_u, "text"));
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
//$queryString_my = sprintf("&totalRows_my=%d%s", $totalRows_my, $queryString_my);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Profile Picture</title>

<link rel="icon" href="../image/San Isidro logo.png">


<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>


.button {
  background-color: #4CAF50;
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  cursor: pointer;
}



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
</style>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.white {	color: #FFF;
}
</style>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
</head>

<body>

<?php
/*
* 	   Simple file Upload system with PHP.
* 	   Created By Tech Stream
* 	   Original Source at http://techstream.org/Web-Development/PHP/Single-File-Upload-With-PHP
*      This program is free software; you can redistribute it and/or modify
*      it under the terms of the GNU General Public License as published by
*      the Free Software Foundation; either version 2 of the License, or
*      (at your option) any later version.
*      
*      This program is distributed in the hope that it will be useful,
*      but WITHOUT ANY WARRANTY; without even the implied warranty of
*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*      GNU General Public License for more details.
*     
*/
	if(isset($_FILES['image'])){
		$errors= array();
		$file_name = $_FILES['image']['name'];
		$file_size =$_FILES['image']['size'];
		$file_tmp =$_FILES['image']['tmp_name'];
		$file_type=$_FILES['image']['type'];   
		$file_ext=strtolower(end(explode('.',$_FILES['image']['name'])));
		
		$expensions= array("jpeg","jpg","png","pdf"); 		
		if(in_array($file_ext,$expensions)=== false){
			$errors[]="extension not allowed, please choose a JPEG or PNG file.";
		}
		if($file_size > 2097152){
		$errors[]='File size must be excately 2 MB';
		}				
		if(empty($errors)==true){
			move_uploaded_file($file_tmp,"upload/".$file_name);
			echo "Success";
		}else{
			print_r($errors);
		}
	}
?>



<table width="1000" align="center" >
  <tr>
    <td width="996" height="83"><img src="../image/logo.jpg" width="1000" height="200" /></td>
  </tr>
  <tr>
  
    <td height="21" bgcolor="#FFFFFF">
<div class="navbar">
<a href="<?php echo $logoutAction ?>">Logout</a>
  <a href="../Admin/admin.php">Home</a>
  <a href="../Admin/Profile.php"><font color="yellow">Profile</font></a>
 
  
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
    <button class="dropbtn">Quiz 
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
    <td height="515" align="center" bgcolor="#FFFFFF"  background="../image/violet.png" ><table width="512">
      <tr>
        <td width="504" height="20" align="center">Update Profile Picture</td>
        </tr>
      <tr>
        <td height="12" align="center">
        <form method="post" enctype="multipart/form-data" name="form" id="form">
          <p>
            <input name="filename" type="hidden" id="a" value="<?php echo ($_FILES['fileup']['name']); ?>" />
          <center>  Upload File:
           <input type="file" name="fileup"/>
           <input type="submit" name='submit' value="Upload" /></center>
          </p>
        </form>
        </td>
      </tr>
      <tr>
        <td height="21" align="center">&nbsp;<span class="white">
          <?php


$uploadpath = 'image/';      // directory to store the uploaded files
$max_size = 2000;          // maximum file size, in KiloBytes
$alwidth = 900;            // maximum allowed width, in pixels
$alheight = 800;           // maximum allowed height, in pixels
$allowtype = array('bmp', 'gif', 'jpg', 'jpe', 'png');        // allowed extensions

if(isset($_FILES['fileup']) && strlen($_FILES['fileup']['name']) > 1) {
  $uploadpath = $uploadpath . basename( $_FILES['fileup']['name']);       // gets the file name
  $sepext = explode('.', strtolower($_FILES['fileup']['name']));
  $type = end($sepext);       // gets extension
  list($width, $height) = getimagesize($_FILES['fileup']['tmp_name']);     // gets image width and height
  $err = '';         // to store the errors

  // Checks if the file has allowed type, size, width and height (for images)
  if(!in_array($type, $allowtype)) $err .= 'The file: <b>'. $_FILES['fileup']['name']. '</b> not has the allowed extension type.';
  if($_FILES['fileup']['size'] > $max_size*1000) $err .= '<br/>Maximum file size must be: '. $max_size. ' KB.';
  if(isset($width) && isset($height) && ($width >= $alwidth || $height >= $alheight)) $err .= '<br/>The maximum Width x Height must be: '. $alwidth. ' x '. $alheight;




  // If no errors, upload the image, else, output the errors
  if($err == '') {
    if(move_uploaded_file($_FILES['fileup']['tmp_name'], $uploadpath)) { 
    //  if(move_uploaded_file($_FILES['fileup'], $uploadpath)) { 
  
	echo 'File: <b>'. basename( $_FILES['fileup']['name']). '</b> Successfully uploaded: Then save!';
      echo '<br/>File type: <b>'. $_FILES['fileup']['type'] .'</b>';
    //  echo '<br />Size: <b>'. number_format($_FILES['fileup']['size']/1024, 3, '.', '') .'</b> KB';
    //  if(isset($width) && isset($height)) echo '<br/>Image Width x Height: '. $width. ' x '. $height;
   //   echo '<br/><br/>Image address: <b>http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['REQUEST_URI']), '\\/').'/'.$uploadpath.'</b>';
    }
    else echo '<b>Unable to upload the file.</b>';
  }
  else echo $err;
}



?>
        </span>
          <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
            <table align="center">
              <tr valign="baseline">
                <td align="center"><img src="image//<?php echo $row_u['pic']; ?>" width="150" height="150" /></td>
              </tr>
              <tr valign="baseline">
                <td><input name="username" type="hidden" value="<?php echo htmlentities($row_u['username'], ENT_COMPAT, 'utf-8'); ?>" size="32" readonly="readonly" />                  <span id="sprytextfield1">
                  <input type="hidden" name="password" value="<?php echo htmlentities($row_u['password'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                  <span class="textfieldRequiredMsg">A value is required.</span></span><span id="sprytextfield2">
                    <input type="hidden" name="fn" value="<?php echo htmlentities($row_u['fn'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                    <span class="textfieldRequiredMsg">A value is required.</span></span><span id="sprytextfield3">
                    <input type="hidden" name="ln" value="<?php echo htmlentities($row_u['ln'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                    <span class="textfieldRequiredMsg">A value is required.</span></span><span id="sprytextfield4">
                    <input type="hidden" name="mi" value="<?php echo htmlentities($row_u['mi'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                    <span class="textfieldRequiredMsg">A value is required.</span></span><span id="sprytextfield5">
                    <input type="hidden" name="bday" value="<?php echo htmlentities($row_u['bday'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                    <span class="textfieldRequiredMsg">A value is required.</span></span>                 
                     <input type="hidden" name="address" value="<?php echo htmlentities($row_u['address'], ENT_COMPAT, 'utf-8'); ?>" size="32" />                  
                    <input type="hidden" name="contact" value="<?php echo htmlentities($row_u['contact'], ENT_COMPAT, 'utf-8'); ?>" size="32" />                 
                     <input type="hidden" name="email"value="<?php echo htmlentities($row_u['email'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
                  <input type="hidden" name="pic" value="<?php echo ($_FILES['fileup']['name']); ?>" size="32" />
                  <input type="hidden" name="UserType" value="<?php echo htmlentities($row_u['UserType'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
              </tr>
              <tr valign="baseline">
                <td align="center"><input name="Submit" type="submit" class="button" value="Save"></td>
              </tr>
            </table>
            <input type="hidden" name="id" value="<?php echo $row_u['id']; ?>" />
            <input type="hidden" name="MM_update" value="form1" />
            <input type="hidden" name="id" value="<?php echo $row_u['id']; ?>" />
        </form>
          <p>&nbsp;</p></td>
        </tr>
      <tr>
        <td height="21"></td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td height="21" bgcolor="#FFFFFF" background="" >Allrights resserved @ SIES 2023</td>
  </tr>
</table>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3");
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4");
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5");
</script>
</body>
</html>
<?php
mysql_free_result($u);
?>
