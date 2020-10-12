<?php
session_start();
require_once '../config.php';

$get_account_sql = 'SELECT first_name, last_name, email_address, date_of_birth, contact_number, IF(has_avatar=0, \'moov_default_avatar_500x500.png\', CONCAT(\'avatar_\', acc.account_id, \'.\', avatar_type)), IF(is_expired=0, \'Active\', \'Expired\'), IF(is_deleted = 1, \'Deleted\', IF(is_suspended = 1, \'Suspended\', \'Active\')) FROM moov.account AS acc LEFT JOIN moov.driving_license AS dl ON acc.account_id = dl.account_id WHERE acc.account_id = ? ORDER BY driving_license_id DESC LIMIT 1';
$account_stmt = mysqli_prepare($conn, $get_account_sql);

mysqli_stmt_bind_param($account_stmt, 'i', $_GET['id']);
mysqli_stmt_execute($account_stmt);
mysqli_stmt_store_result($account_stmt);
mysqli_stmt_bind_result($account_stmt, $first_name, $last_name, $email_address, $date_of_birth, $contact_number, $avatar_name, $dl_status, $account_status);
mysqli_close($account_stmt);

while (mysqli_stmt_fetch($account_stmt)) {
    $account[] = array('firstName'=>$first_name, 'lastName'=>$last_name, 'emailAddress'=>$email_address, 'dateOfBirth'=>$date_of_birth, 'contactNumber'=>$contact_number, 'avatarName'=>$avatar_name, 'dlStatus'=>$dl_status, 'accountStatus'=>$account_status);
	
}

echo json_encode($account);
?>
