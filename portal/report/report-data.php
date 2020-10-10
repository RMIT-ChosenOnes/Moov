<?php
session_start();
require_once '../../config.php';

if(empty($_REQUEST['id'])) {
    echo "<script>history.back()</script>";
    return;
}

$sql = "UPDATE `booking_report` SET `status` = 1, `resolved_portal_account_id` = ".$_SESSION['moov_portal_staff_account_id']." WHERE `booking_report`.`report_id` =  ".$_REQUEST['id'];
$res = $conn->query($sql);

echo "<script>alert('successfully !');location.href='/moov/portal/report/';</script>";
?>