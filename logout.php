<?php
session_start();

unset($_SESSION['moov_user_logged_in']);
unset($_SESSION['moov_user_account_id']);
unset($_SESSION['moov_user_first_name']);

header('location: /moov/login');
exit;
?>
