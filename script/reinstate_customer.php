#!/usr/bin/env php
<?php
require_once '/var/www/html/moov/config.php';

$reinstated_account = [];
$mail_reinstated_table = '';

$reinstate_account_list_sql = 'SELECT suspend_id, suspend_account.account_id, display_name FROM suspend_account LEFT JOIN account ON suspend_account.account_id = account.account_id WHERE (suspend_account.reinstate_date <= CURRENT_DATE) AND suspend_account.is_invalid = 0';
$reinstate_account_list_stmt = mysqli_prepare($conn, $reinstate_account_list_sql);

if (mysqli_stmt_execute($reinstate_account_list_stmt)) {
    $reinstate_account_list = mysqli_stmt_get_result($reinstate_account_list_stmt);
    
    while ($account_list = mysqli_fetch_assoc($reinstate_account_list)) {
        $reinstate_account_status_sql = 'UPDATE account SET is_suspend = 0 WHERE account_id = ?';
        $reinstate_account_status_stmt = mysqli_prepare($conn, $reinstate_account_status_sql);
        
        mysqli_stmt_bind_param($reinstate_account_status_stmt, 'i', $param_account_id);
        $param_account_id = $account_list['account_id'];
        
        mysqli_stmt_execute($reinstate_account_status_stmt);
        
        $invalid_suspend_record_sql = 'UPDATE suspend_account SET is_invalid = 1 WHERE suspend_id = ?';
        $invalid_suspend_record_stmt = mysqli_prepare($conn, $invalid_suspend_record_sql);
        
        mysqli_stmt_bind_param($invalid_suspend_record_stmt, 'i', $param_suspend_id);
        $param_suspend_id = $account_list['suspend_id'];
        
        mysqli_stmt_execute($invalid_suspend_record_stmt);
        
        $reinstated_account += [$account_list['account_id'] => $account_list['display_name']];

    }
}

foreach ($reinstated_account as $reinstated_account_id => $reinstated_account_name) {
    $mail_reinstated_table .= '<tr><td scope="row">' . $reinstated_account_id . '</td><td>' . $reinstated_account_name . '</td></tr>';
    
}

if (mysqli_num_rows($reinstate_account_list) == 0) {
    $mail_body_content = '<p class="my-4 text-left">You do not have any account that need to be reinstated.</p>';
    
} else {
    $mail_body_content = '<p class="my-4 text-left">Below is the list of account that has reinstated today, ' . date('Y-m-d') . '.</p><table class="table w-100 text-center border"><thead><tr><th scope="col">Account ID</th><th scope="col">Name</th></tr></thead><tbody>' . $mail_reinstated_table . '</tbody></table>';
    
}

$mail_email = 'fisherlim20@outlook.com';
$mail_name = 'Moov Admin';
$mail_subject = '[Moov Portal] Successfully Executed Reinstate Customer Account Script';
$mail_body = '<h1>Dear Moov Portal Admin,</h1>' . $mail_body_content . '<p class="text-left my-4">Thank you.</p><p class="my-4 text-left">Kind Regards,<br/>Moov Portal Admin</p>';

require_once '/var/www/html/moov/mail/mail-portal.php';

mysqli_stmt_close($reinstate_account_list_stmt);
mysqli_stmt_close($reinstate_account_status_stmt);
mysqli_stmt_close($invalid_suspend_record_stmt);

?>