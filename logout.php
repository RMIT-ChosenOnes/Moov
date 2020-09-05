<?php
session_start();

unset($_SESSION['moov_user_logged_in']);
unset($_SESSION['moov_user_account_id']);
unset($_SESSION['moov_user_display_name']);
unset($_SESSION['moov_user_avatar_status']);
unset($_SESSION['moov_user_avatar_type']);
unset($_SESSION['moov_user_license_expired']);

header('location: /moov/login');
exit;
?>
