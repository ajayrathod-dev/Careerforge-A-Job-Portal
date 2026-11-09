<?php
error_reporting(0);
ini_set('display_errors', 0);


date_default_timezone_set("Asia/Kolkata");
$DB_SERVER = "localhost";
$DB_NAME = "job_portal";
$username = "job_portal";
$password = "job_portal@878889";
$link = mysqli_connect($DB_SERVER, $username, $password, $DB_NAME);
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}else{
    //echo "connected";
}


?>