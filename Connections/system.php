<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_system = "localhost";
$database_system = "system";
$username_system = "root";
$password_system = "";
$system = mysql_pconnect($hostname_system, $username_system, $password_system) or trigger_error(mysql_error(),E_USER_ERROR); 
?>