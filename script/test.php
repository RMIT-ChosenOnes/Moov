#!/usr/bin/env php
<?php
//session_start()
require_once '/var/www/html/moov/config.php';

$set_account_1_expired_sql = 'UPDATE account SET is_deleted = 1 WHERE account_id = 1';
$set_account_1_expired_stmt = mysqli_prepare($conn, $set_account_1_expired_sql);

mysqli_stmt_execute($set_account_1_expired_stmt);

?>
