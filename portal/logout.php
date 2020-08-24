<?php
session_start();

unset($_SESSION['moov_portal_logged_in']);
unset($_SESSION['moov_portal_staff_first_name']);
unset($_SESSION['moov_portal_staff_email']);
unset($_SESSION['moov_portal_staff_role']);

header('location: /moov/portal/login');
exit;
?>
