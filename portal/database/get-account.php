<?php
session_start();
require_once '../config.php';

if ((!isset($_SESSION['moov_portal_logged_in']) && $_SESSION['moov_portal_logged_in'] != TRUE) || $_SESSION['moov_portal_staff_role'] != 'Admin') {
	header('location: /moov/portal/');
}

$get_account_sql = 'SELECT first_name, last_name, username, email_address, role, is_deactivated FROM portal_account WHERE account_id = ?';

$account_stmt = mysqli_prepare($conn, $get_account_sql);

mysqli_stmt_bind_param($account_stmt, 'i', $_GET['id']);
mysqli_stmt_execute($account_stmt);
mysqli_stmt_store_result($account_stmt);
mysqli_stmt_bind_result($account_stmt, $saved_first_name, $saved_last_name, $saved_username, $saved_email_address, $saved_role, $saved_account_status);
mysqli_close($account_stmt);

while (mysqli_stmt_fetch($account_stmt)) {
    $saved_account_info[] = array('saved_first_name'=>$saved_first_name, 'saved_last_name'=>$saved_last_name, 'saved_username'=>$saved_username, 'saved_email_address'=>$saved_email_address, 'saved_role'=>$saved_role, 'saved_account_status'=>$saved_account_status);
}

echo json_encode($saved_account_info);
?>