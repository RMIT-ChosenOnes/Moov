<?php
session_start();

unset($_SESSION['moov_user_logged_in']);
unset($_SESSION['moov_user_account_id']);
unset($_SESSION['moov_user_first_name']);
unset($_SESSION['moov_user_avatar_status']);
unset($_SESSION['moov_user_avatar_type']);

header('location: /moov/login');
exit;
?>
