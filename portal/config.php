<?php
$servername = 'localhost';
$username = 'pp';
$password = 'IEFo7NyYSR8tkPse';
$database = 'moov_portal';

$conn = new mysqli($servername, $username, $password, $database);

if (!conn) {
    die('Connection failed: ' . mysqli_connect_error());
}
?>