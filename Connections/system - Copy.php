<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_system = "localhost";
$database_system = "u894004248_system";
$username_system = "u894004248_root";
$password_system = "Pawijey!1224";
$system = mysql_pconnect($hostname_system, $username_system, $password_system) or trigger_error(mysql_error(),E_USER_ERROR); 
?>