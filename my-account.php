<?php
session_start();
require_once 'config.php';
$parent_page_name = 'account';
$page_name = basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php');

$user_display_name = $user_email_address = $user_contact_number = $user_date_of_birth = $user_password = $user_confirm_password = $user_avatar = $user_avatar_status = $user_avatar_type = $remove_account_password = $dl_id = $dl_first_name = $dl_last_name = $dl_license_number = $dl_date_of_expiry = $dl_country_of_issue = $dl_state_of_issue = '';
$user_display_name_err = $user_email_address_err = $user_contact_number_err = $user_password_err = $user_confirm_password_err = $user_avatar_err = $dl_license_number_err = $dl_date_of_expiry_err = $dl_country_of_issue_err = $dl_state_of_issue_err = '';

$today_date = date('Y-m-d');
$accepted_date_of_expiry = date('Y-m-d', strtotime($today_date . '+7 days'));
$search_date_symbol = array('/', '.');
$replace_date_symbol = array('-', '-');
$search_contact_number_symbol = array('-', ' ');
$replace_contact_number_symbol = array('', '');
$user_avatar_save_directory = 'avatar/';

if (isset($_SESSION['moov_user_logged_in']) && $_SESSION['moov_user_logged_in'] == TRUE) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Submit for My Details
        if (isset($_POST['myDetails'])) {
            $user_date_of_birth = $_POST['userDateOfBirth'];

            if (empty(trim($_POST['userDisplayName']))) {
                $user_display_name_err = 'Please enter your display name.';

            } else {
                if (preg_match('/^[a-zA-Z\-\s]{3,100}$/', trim($_POST['userDisplayName']))) {
                    $user_display_name = trim($_POST['userDisplayName']);

                } else {
                    $user_display_name_err = 'Please enter a valid display name.';

                }
            }

            if (empty(trim($_POST['userEmailAddress']))) {
                $user_email_address_err = 'Please enter your email address.';

            } else {
                $check_email_address_duplication_sql = 'SELECT account_id FROM account WHERE email_address = ? AND is_deleted = 0';
                $check_email_address_duplication_stmt = mysqli_prepare($conn, $check_email_address_duplication_sql);

                mysqli_stmt_bind_param($check_email_address_duplication_stmt, 's', $param_user_temp_email_address);
                $param_user_temp_email_address = trim($_POST['userEmailAddress']);

                if (mysqli_stmt_execute($check_email_address_duplication_stmt)) {
                    mysqli_stmt_store_result($check_email_address_duplication_stmt);

                    if (mysqli_stmt_num_rows($check_email_address_duplication_stmt) > 0) {
                        mysqli_stmt_bind_result($check_email_address_duplication_stmt, $saved_user_account_id);
                        mysqli_stmt_fetch($check_email_address_duplication_stmt);

                        if ($saved_user_account_id != $_SESSION['moov_user_account_id']) {
                            $user_email_address_err = 'Email address is already in use. Please try another email address.';

                        } elseif ($saved_user_account_id == $_SESSION['moov_user_account_id']) {
                            $user_email_address = $param_user_temp_email_address;

                        }
                    } else {
                        $user_email_address = $param_user_temp_email_address;

                    }
                } else {
                    $update_error = TRUE;
                    $error_message = mysqli_error($conn);

                }

                mysqli_stmt_close($check_email_address_duplication_stmt);

            }

            if (empty(trim($_POST['userContactNumber']))) {
                $user_contact_number_err = 'Please enter your contact number.';

            } else {
                $temp_contact_number = trim($_POST['userContactNumber']);

                $replace_temp_cn = str_replace($search_contact_number_symbol, $replace_contact_number_symbol, $temp_contact_number);

                if (preg_match('/^(0)?(4){1}[0-9]{8}$/', $replace_temp_cn)) {
                    $user_contact_number = $replace_temp_cn;

                } else {
                    $user_contact_number_err = 'Please enter a valid Australian contact number.';

                }
            }

            if (!empty(trim($_POST['userUpdatePassword']))) {
                if (!preg_match('/[a-z]+/', trim($_POST['userUpdatePassword'])) || !preg_match('/[A-Z]+/', trim($_POST['userUpdatePassword'])) || !preg_match('/[^a-zA-Z0-9]+/', trim($_POST['userUpdatePassword'])) || strlen(trim($_POST['userUpdatePassword'])) < 8) {
                    $user_password_err = 'Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number digit, 1 special character, and have at least 8 characters long.';

                }

                if (empty(trim($_POST['userConfirmPassword']))) {
                    $user_confirm_password_err = 'Please confirm the password again.';

                } else {
                    if (trim($_POST['userUpdatePassword']) == trim($_POST['userConfirmPassword'])) {
                        $user_password = trim($_POST['userUpdatePassword']);

                    } else {
                        $user_password_err = $user_confirm_password_err = 'Password does not matched. Please try again.';

                    }
                }
            }

            if (isset($_FILES['userAvatar']) && $_FILES['userAvatar']['name'] != '') {
                $img_file_name = basename($_FILES["userAvatar"]["name"]);
                $img_file_type = strtolower(pathinfo($img_file_name, PATHINFO_EXTENSION));

                if ($_FILES['userAvatar']['size'] > 5000000) {
                    $user_avatar_err = 'Sorry. Your file is too big. Maximum file size is 5MB. Please try again.';

                } elseif ($img_file_type != 'jpg' && $img_file_type != 'jpeg' && $img_file_type != 'png') {
                    $user_avatar_err = 'Sorry. You have uploaded an unsupported file type. Please try again.';

                } else {
                    $user_avatar = $user_avatar_save_directory . 'avatar_' . $_SESSION['moov_user_account_id'] . '.' . $img_file_type;

                    if(move_uploaded_file($_FILES['userAvatar']['tmp_name'], $user_avatar)) {
                        $user_avatar_status = 1;
                        $user_avatar_type = $img_file_type;
                        $uploaded_avatar = TRUE;

                    } else {
                        $update_error = TRUE;
                        $user_avatar_status = 0;
                        $user_avatar_type = NULL;

                    }
                }
            }

            if (empty($user_display_name_err) && empty($user_email_address_err) && empty($user_contact_number_err) && empty($user_password_err) && empty($user_confirm_password_err) && empty($user_avatar_err)) {
                $update_user_account_sql = 'UPDATE account SET display_name = ?, email_address = ?, contact_number = ? WHERE account_id = ?';

                if ($update_user_account_stmt = mysqli_prepare($conn, $update_user_account_sql)) {
                    mysqli_stmt_bind_param($update_user_account_stmt, 'sssi', $param_user_display_name, $param_user_email_address, $param_user_contact_number, $param_user_account_id);

                    $param_user_display_name = $user_display_name;
                    $param_user_email_address = $user_email_address;
                    $param_user_contact_number = $user_contact_number;
                    $param_user_account_id = $_SESSION['moov_user_account_id'];

                    if (mysqli_stmt_execute($update_user_account_stmt)) {
                        $updated = TRUE;
                        $_SESSION['moov_user_display_name'] = $param_user_display_name;

                        // Upload Avatar
                        if ($user_avatar_status == 1) {
                            $upload_user_avatar_sql = 'UPDATE account SET has_avatar = ?, avatar_type = ? WHERE account_id = ?';

                            if ($upload_user_avatar_stmt = mysqli_prepare($conn, $upload_user_avatar_sql)) {
                                mysqli_stmt_bind_param($upload_user_avatar_stmt, 'isi', $param_user_avatar_status, $param_user_avatar_type, $param_user_account_id);

                                $param_user_avatar_status = $user_avatar_status;
                                $param_user_avatar_type = $user_avatar_type;

                                if (mysqli_stmt_execute($upload_user_avatar_stmt)) {
                                    $uploaded_avatar = TRUE;
                                    $_SESSION['moov_user_avatar_status'] = $param_user_avatar_status;
                                    $_SESSION['moov_user_avatar_type'] = $param_user_avatar_type;

                                } else {
                                    $update_error = TRUE;
                                    $error_message = mysqli_error($conn);
                                }
                            }

                            unset($_POST);
                            mysqli_stmt_close($upload_user_avatar_stmt);

                        }

                        // Update Password
                        if (!empty($user_password)) {
                            $update_user_password_sql = 'UPDATE account SET password = ? WHERE account_id = ?';

                            if ($update_user_password_stmt = mysqli_prepare($conn, $update_user_password_sql)) {
                                mysqli_stmt_bind_param($update_user_password_stmt, 'si', $param_user_password, $param_user_account_id);

                                $param_user_password = password_hash($user_password, PASSWORD_DEFAULT);

                                if (mysqli_stmt_execute($update_user_password_stmt)) {
                                    $updated = TRUE;

                                } else {
                                    $update_error = TRUE;
                                    $error_message = mysqli_error($conn);

                                }
                            }

                            unset($_POST);
                            mysqli_stmt_close($update_user_password_stmt);

                        }

                        unset($_POST);

                    } else {
                        $update_error = TRUE;
                        $error_message = mysqli_error($conn);

                    }
                }

                mysqli_stmt_close($update_user_account_stmt);

            }
        }

        // Delete User Account
        if (isset($_POST['removeAccount'])) {
            if (empty(trim($_POST['accountRemovePassword']))) {
                $delete_error = TRUE;
                $delete_error_message = 'You need to confirm your password before you can delete your account.';

            } else {
                $remove_account_password = trim($_POST['accountRemovePassword']);

                $verify_password_sql = 'SELECT password FROM account WHERE account_id = ?';
                $verify_password_stmt = mysqli_prepare($conn, $verify_password_sql);

                mysqli_stmt_bind_param($verify_password_stmt, 'i', $param_user_account_id);
                $param_user_account_id = $_SESSION['moov_user_account_id'];

                if (mysqli_stmt_execute($verify_password_stmt)) {
                    mysqli_stmt_store_result($verify_password_stmt);

                    if (mysqli_stmt_num_rows($verify_password_stmt) == 1) {
                        mysqli_stmt_bind_result($verify_password_stmt, $saved_account_password);
                        mysqli_stmt_fetch($verify_password_stmt);

                        if (password_verify($remove_account_password, $saved_account_password)) {
                            $delete_account_sql = 'UPDATE account SET is_deleted = ? WHERE account_id = ?';
                            $delete_account_stmt = mysqli_prepare($conn, $delete_account_sql);

                            mysqli_stmt_bind_param($delete_account_stmt, 'ii', $param_account_delete_status, $param_user_account_id);
                            $param_account_delete_status = 1;

                            if (mysqli_stmt_execute($delete_account_stmt)) {
                                sleep(5);

                                unset($_SESSION['moov_user_logged_in']);
                                unset($_SESSION['moov_user_account_id']);
                                unset($_SESSION['moov_user_first_name']);

                                $_SESSION['moov_user_account_deleted'] = TRUE;

                                header('location: /moov/login');
                                exit;

                            } else {
                                $delete_error = TRUE;
                                $delete_error_message = mysqli_error($conn);

                            }

                            mysqli_stmt_close($delete_account_stmt);

                        } else {
                            $delete_error = TRUE;
                            $delete_error_message = 'The entered password does not match with your current password.';

                        }
                    } else {
                        $delete_error = TRUE;

                    }
                } else {
                    $delete_error = TRUE;
                    $delete_error_message = mysqli_error($conn);

                }

                mysqli_stmt_close($verify_password_stmt);

            }
        }

        // Remove Avatar
        if (isset($_POST['removeAvatar'])) {
            $remove_avatar_sql = 'UPDATE account SET has_avatar = ?, avatar_type = ? WHERE account_id = ?';
            $remove_avatar_stmt = mysqli_prepare($conn, $remove_avatar_sql);

            mysqli_stmt_bind_param($remove_avatar_stmt, 'isi', $param_remove_avatar_status, $param_remove_avatar_type, $param_user_account_id);
            $param_remove_avatar_status = 0;
            $param_remove_avatar_type = NULL;
            $param_user_account_id = $_SESSION['moov_user_account_id'];

            if (mysqli_stmt_execute($remove_avatar_stmt)) {
                $removed_avatar = TRUE;
                $_SESSION['moov_user_avatar_status'] = 0;
                $_SESSION['moov_user_avatar_type'] = '';

            } else {
                $update_error = TRUE;
                $error_message = mysqli_error($conn);

            }

            mysqli_stmt_close($remove_avatar_stmt);

        }
        
        // Update Driving License
        if (isset($_POST['updateLicense'])) {
            $dl_id = $_POST['drivingLicenseId'];
            
            if (empty(trim($_POST['drivingLicenseDateOfExpiry']))) {
                $dl_date_of_expiry_err = 'Please enter the date of expiry on your driving license.';

            } else {
                if ((preg_match('/[^0-9\.\-\/]/', trim($_POST['drivingLicenseDateOfExpiry']))) || strlen(trim($_POST['drivingLicenseDateOfExpiry'])) < 8) {
                    $dl_date_of_expiry_err = 'Please enter a valid date of expiry.';

                } else {
                    $temp_date_of_expiry = trim($_POST['drivingLicenseDateOfExpiry']);

                    $replace_temp_doe = date('Y-m-d', strtotime(str_replace($search_date_symbol, $replace_date_symbol, $temp_date_of_expiry)));

                    if ($replace_temp_doe >= $accepted_date_of_expiry) {
                        $dl_date_of_expiry = $replace_temp_doe;

                    } else {
                        $dl_date_of_expiry_err = 'Your current driving license is expiring soon or has already expired. Unfortunately, we are not able to continue to update with your current driving license. Please try again with a new driving license.';

                    }
                }
            }
            
            if (empty(trim($_POST['drivingLicenseCountryOfIssue'])) || trim($_POST['drivingLicenseCountryOfIssue']) == '') {
                $dl_country_of_issue_err = 'Please select the country of issue.';

            } else {
                $dl_country_of_issue = trim($_POST['drivingLicenseCountryOfIssue']);

                if ($dl_country_of_issue == 9) {
                    if (empty(trim($_POST['drivingLicenseStateOfIssue'])) || trim($_POST['drivingLicenseStateOfIssue']) == '') {
                        $dl_state_of_issue_err = 'Please select the state of issue.';

                    } else {
                        $dl_state_of_issue = trim($_POST['drivingLicenseStateOfIssue']);

                    }
                } else {
                    $dl_state_of_issue = NULL;
                    
                }
            }
            
            if (empty(trim($_POST['drivingLicenseNumber']))) {
                $dl_license_number_err = 'Please enter your driving license number.';
                
            } else {
                if ($dl_country_of_issue == 9) {
                    if ($dl_state_of_issue == 'act') {
                        if (preg_match('/^[0-9]{10}$/', trim($_POST['drivingLicenseNumber']))) {
                            $dl_license_number = trim($_POST['drivingLicenseNumber']);

                        } else {
                            $dl_license_number_err = 'Please enter a valid Australian Capital Territory driving license number.';

                        }
                    } elseif ($dl_state_of_issue == 'nsw') {
                        if (preg_match('/^[0-9]{10}$/', trim($_POST['drivingLicenseNumber']))) {
                            $dl_license_number = trim($_POST['drivingLicenseNumber']);

                        } else {
                            $dl_license_number_err = 'Please enter a valid New South Wales driving license number.';

                        }
                    } elseif ($dl_state_of_issue == 'nt') {
                        if (preg_match('/^[0-9]{7}$/', trim($_POST['drivingLicenseNumber']))) {
                            $dl_license_number = trim($_POST['drivingLicenseNumber']);

                        } else {
                            $dl_license_number_err = 'Please enter a valid Northern Territory driving license number.';

                        }
                    } elseif ($dl_state_of_issue == 'qld') {
                        if (preg_match('/^[0-9]{7,9}$/', trim($_POST['drivingLicenseNumber']))) {
                            $dl_license_number = trim($_POST['drivingLicenseNumber']);

                        } else {
                            $dl_license_number_err = 'Please enter a valid Queensland driving license number.';

                        }
                    } elseif ($dl_state_of_issue == 'sa') {
                        if (preg_match('/^[A-Z]{1}[0-9]{5}$/', strtoupper(trim($_POST['drivingLicenseNumber'])))) {
                            $dl_license_number = strtoupper(trim($_POST['drivingLicenseNumber']));

                        } else {
                            $dl_license_number_err = 'Please enter a valid South Australia driving license number.';

                        }
                    } elseif ($dl_state_of_issue == 'tas') {
                        if (preg_match('/^[0-9]{6,7}$/', trim($_POST['drivingLicenseNumber']))) {
                            $dl_license_number = trim($_POST['drivingLicenseNumber']);

                        } else {
                            $dl_license_number_err = 'Please enter a valid Tasmania driving license number.';

                        }
                    } elseif ($dl_state_of_issue == 'vic') {
                        if (preg_match('/^[0-9]{8,9}$/', trim($_POST['drivingLicenseNumber']))) {
                            $dl_license_number = trim($_POST['drivingLicenseNumber']);

                        } else {
                            $dl_license_number_err = 'Please enter a valid Victoria driving license number.';

                        }
                    } elseif ($dl_state_of_issue == 'wa') {
                        if (preg_match('/^[0-9]{9}$/', trim($_POST['drivingLicenseNumber']))) {
                            $dl_license_number = trim($_POST['drivingLicenseNumber']);

                        } else {
                            $dl_license_number_err = 'Please enter a valid Western Australia driving license number.';

                        }
                    } else {
                        $dl_license_number_err = 'Please enter a valid driving license number.';

                    }
                } else {
                    if (preg_match('/^[0-9A-Z\-\s\/]{5,20}$/', strtoupper(trim($_POST['drivingLicenseNumber'])))) {
                        $dl_license_number = strtoupper(trim($_POST['drivingLicenseNumber']));

                    } else {
                        $dl_license_number_err = 'Please enter a valid driving license number.';

                    }
                }
            }
            
            if (empty($dl_license_number_err) && empty($dl_date_of_expiry_err) && empty($dl_country_of_issue_err) && empty($dl_state_of_issue_err)) {
                $update_new_license_sql = 'INSERT INTO driving_license (account_id, license_number, date_of_expiry, country_of_issue, state_of_issue) VALUES (?, ?, ?, ?, ?)';
                
                if ($update_new_license_stmt = mysqli_prepare($conn, $update_new_license_sql)) {
                    mysqli_stmt_bind_param($update_new_license_stmt, 'issis', $param_user_account_id, $param_dl_license_number, $param_dl_date_of_expiry, $param_dl_country_of_issue, $param_dl_state_of_issue);
                    
                    $param_user_account_id = $_SESSION['moov_user_account_id'];
                    $param_dl_license_number = $dl_license_number;
                    $param_dl_date_of_expiry = $dl_date_of_expiry;
                    $param_dl_country_of_issue = $dl_country_of_issue;
                    $param_dl_state_of_issue = $dl_state_of_issue;
                    
                    if (mysqli_stmt_execute($update_new_license_stmt)) {
                        $set_current_license_expired_sql = 'UPDATE driving_license SET is_expired = ? WHERE driving_license_id = ?';
                        $set_current_license_expired_stmt = mysqli_prepare($conn, $set_current_license_expired_sql);
                        
                        mysqli_stmt_bind_param($set_current_license_expired_stmt, 'ii', $param_dl_status, $param_dl_id);
                        $param_dl_status = 1;
                        $param_dl_id = $dl_id;
                        
                        if (mysqli_stmt_execute($set_current_license_expired_stmt)) {
                            $dl_updated = TRUE;
                            unset($_POST);
                            unset($_SESSION['moov_user_license_expired']);
                            
                        } else {
                            $update_error = TRUE;
                            $error_message = mysqli_error($conn);
                            
                        }
                        
                        mysqli_stmt_close($set_current_license_expired_stmt);
                        
                    }else {
                        $update_error = TRUE;
                        $error_message = mysqli_error($conn);

                    }
                }
                
                mysqli_stmt_close($update_new_license_stmt);
                
            } else {
                $driving_license_error = TRUE;
                
            }
        }
    }
} else {
    header('location: /moov/login?url=' . urlencode('/moov/' . $page_name));
    
}
?>

<!DOCTYPE html>
<html>
	
<head>
	<title>My Account | Moov</title>
	
	<!-- meta tag -->
	<meta charset="UTF-8">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="Chosen Ones">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-171692999-2"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'UA-171692999-2');
	</script>
	
	<!-- JavaScript from Bootstrap -->
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
	
	<!-- CSS from Bootstrap v4.5.2 -->
    <link rel="stylesheet" type="text/css" href="/moov/assets/style/bootstrap.css">

    <!-- Self Defined CSS -->
    <link rel="stylesheet" type="text/css" href="/moov/assets/style/style.css?<?php echo date('l jS \of F Y h:i:s A'); ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="96x96" href="/moov/assets/favicon/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/moov/assets/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/moov/assets/favicon/favicon-16x16.png">
</head>

<body id="myProfile">
	<?php include 'header.php'; ?>
    
	<div class="container my-3 footer-align-bottom">
		<h1 class="text-center">My Account</h1>
        
        <?php
        if ($_SESSION['moov_user_license_expired'] === TRUE) {
            echo '
            <div class="alert alert-warning my-4 alert-dismissible fade show" role="alert">
                Your driver license is expired. Please update your new driver license before you can continue to book a car.

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ';
        }
        
		if ($update_error === TRUE) {
            echo '
            <div class="alert alert-warning my-4 alert-dismissible fade show" role="alert">
                Oops! There is an error occurred. Please try again later. If you continue to see this error, please contact us immediately.

			' . (!empty($error_message) ? '<br/><br/><b>Error:</b> ' . $error_message : '') . '

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ';
        }
        
        if ($delete_error === TRUE) {
            echo '
            <div class="alert alert-warning my-4 alert-dismissible fade show" role="alert">
                Oops! There is an error occurred. We can\'t proceed with your request now.

			' . (!empty($delete_error_message) ? '<br/><br/><b>Error:</b> ' . $delete_error_message : '') . '

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ';
        }
        
        if ($uploaded_avatar === TRUE) {
            echo '
            <div class="alert alert-success my-4 alert-dismissible fade show" role="alert">
                Your avatar uploaded successfully.

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ';
        }
        
        if ($removed_avatar === TRUE) {
            echo '
            <div class="alert alert-success my-4 alert-dismissible fade show" role="alert">
                Your avatar removed successfully.

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ';
        }
        
        if ($updated === TRUE) {
            echo '
            <div class="alert alert-success my-4 alert-dismissible fade show" role="alert">
                Account details updated successfully.

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ';
        }
        
        if ($dl_updated === TRUE) {
            echo '
            <div class="alert alert-success my-4 alert-dismissible fade show" role="alert">
                Your new driver license is updated successfully.

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ';
        }
        ?>
		
		<ul class="nav nav-tabs" id="myAccount" role="tablist">
			<li class="nav-item" role="presentation">
				<a class="nav-link active" id="myProfile" data-toggle="tab" href="#my-profile" role="tab" aria-controls="my-profile" aria-selected="true"><h4>My Profile</h4></a>
			</li>
			
			<li class="nav-item" role="presentation">
				<a class="nav-link" id="myLicense" data-toggle="tab" href="#my-license" role="tab" aria-controls="my-license" aria-selected="false"><h4>My License</h4></a>
			</li>
			
			<li class="nav-item" role="presentation">
				<a class="nav-link" id="myPayment" data-toggle="tab" href="#my-payment" role="tab" aria-controls="my-payment" aria-selected="false"><h4>My Payment Method</h4></a>
			</li>
		</ul>
		
		<form action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php'); ?>" method="post" enctype="multipart/form-data" onSubmit="submittton()">
            <div class="tab-content" id="myAccountContent">
                <!-- My Profile -->
                <div class="tab-pane fade show active" id="my-profile" role="tabpanel" aria-labelledby="myProfile">
                    <?php
                    $get_user_account_details_sql = 'SELECT first_name, last_name, display_name, email_address, contact_number, date_of_birth, has_avatar, avatar_type FROM account WHERE account_id = ?';

                    if ($get_user_account_details_stmt = mysqli_prepare($conn, $get_user_account_details_sql)) {
                        mysqli_stmt_bind_param($get_user_account_details_stmt, 'i', $param_user_account_id);

                        $param_user_account_id = $_SESSION['moov_user_account_id'];

                        if (mysqli_stmt_execute($get_user_account_details_stmt)) {
                            $get_user_account_details = mysqli_stmt_get_result($get_user_account_details_stmt);

                            while ($user_account_details = mysqli_fetch_assoc($get_user_account_details)) {
                                $saved_first_name = $user_account_details['first_name'];
                                $saved_last_name = $user_account_details['last_name'];
                                $saved_display_name = $user_account_details['display_name'];
                                $saved_email_address = $user_account_details['email_address'];
                                $saved_contact_number = $user_account_details['contact_number'];
                                $saved_date_of_birth = date('d/m/Y', strtotime($user_account_details['date_of_birth']));
                                $saved_avatar_status = $user_account_details['has_avatar'];
                                $saved_avatar_type = $user_account_details['avatar_type'];

                            }
                        }
                    }

                    mysqli_stmt_close($get_user_account_details_stmt);
                    ?>
                    
					<div class="container-fluid mt-4">
						<div class="row">
							<div class="col-sm-4 mb-5 mb-sm-0">
                                <h4 class="d-block d-sm-none text-center mb-3">My Avatar</h4>
                                
								<div class="text-center">
                                    <?php
                                    if ($saved_avatar_status == 0) {
                                        $avatar_file_path = 'moov_default_avatar_500x500.png';
                                        
                                    } elseif ($saved_avatar_status == 1) {
                                        $avatar_file_path = 'avatar_' . $_SESSION['moov_user_account_id'] . '.' . $saved_avatar_type;
                                        
                                    }
                                    ?>
                                    
									<img class="rounded-circle user-avatar" src="/moov/assets/images/transparent_background.png" style="background: url('/moov/avatar/<?php echo $avatar_file_path; ?>');">
								</div>
                                
                                <div class="custom-file mt-5 mb-4">
									<input type="file" class="custom-file-input" id="userAvatar" name="userAvatar" aria-describedby="userAvatarFileName" onChange="showUploadAvatarName(this.value)" onClick="showSubmitButton()">
									
									<label id="userAvatarLabel" class="custom-file-label" for="userAvatar">Browse Your Avatar</label>
                                    
                                    <small id="userAvatarFileName" class="form-text text-muted"><?php echo empty($user_avatar_err) ? 'Max. file size is 5MB. Supported file type: JPG, JPEG, PNG.' : ''; ?></small>
                                    
                                    <?php
									if (isset($user_avatar_err) && !empty($user_avatar_err)) {
										echo '<p id="userAvatarError" class="text-danger mb-0">' . $user_avatar_err . '</p>';

									}
									?>
                                    
                                    
								</div>
                                
                                <button id="removeAvatarButton" type="submit" class="btn btn-secondary btn-block mt-5" name="removeAvatar">Delete My Avatar</button>
							</div>
							
							<div class="col-sm-8 mt-3 mt-sm-0">
                                <h4 class="d-block d-sm-none text-center mb-3">My Details</h4>
                                
                                <div class="form-group">
									<label for="userFirstName">First Name</label>
									
									<input type="text" class="form-control" id="userFirstName" name="userFirstName" aria-describedby="firstNameInfo" value="<?php echo $saved_first_name; ?>" readonly>
                                    
                                    <small id="firstNameInfo" class="form-text text-muted">If you wish to change your first name, please contact us.</small>
								</div>
                                
                                <div class="form-group mt-4">
									<label for="userLastName">Last Name</label>
									
									<input type="text" class="form-control" id="userLastName" name="userLastName" aria-describedby="lastNameInfo" value="<?php echo $saved_last_name; ?>" readonly>
									
									<small id="lastNameInfo" class="form-text text-muted">If you wish to change your last name, please contact us.</small>
								</div>
                                
								<div class="form-group mt-4">
									<label for="userDisplayName">Display Name</label>
									
									<input type="text" class="form-control <?php echo !empty($user_display_name_err) ? 'border border-danger' : ''; ?>" id="userDisplayName" name="userDisplayName" value="<?php echo isset($_POST['myDetails']) ? $_POST['userDisplayName'] : $saved_display_name; ?>" onKeyDown="showSubmitButton()">
									
									<?php
									if (isset($user_display_name_err) && !empty($user_display_name_err)) {
										echo '<p class="text-danger mb-0">' . $user_display_name_err . '</p>';

									}
									?>
								</div>
								
								<div class="form-group mt-4">
									<label for="userEmailAddress">Email Address</label>
									
									<input type="email" class="form-control <?php echo !empty($user_email_address_err) ? 'border border-danger' : ''; ?>" id="userEmailAddress" name="userEmailAddress" value="<?php echo isset($_POST['myDetails']) ? $_POST['userEmailAddress'] : $saved_email_address; ?>" onKeyDown="showSubmitButton()">
									
									<?php
									if (isset($user_email_address_err) && !empty($user_email_address_err)) {
										echo '<p class="text-danger mb-0">' . $user_email_address_err . '</p>';

									}
									?>
								</div>
								
								<div class="form-group mt-4">
									<label for="userContactNumber">Contact Number</label>
									
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="country-code">+61</span>
                                        </div>
                                        
                                        <input type="tel" class="form-control <?php echo !empty($user_contact_number_err) ? 'border border-danger' : ''; ?>" id="userContactNumber" name="userContactNumber" value="<?php echo isset($_POST['myDetails']) ? $_POST['userContactNumber'] : $saved_contact_number; ?>" onKeyDown="showSubmitButton()">
									
                                        <?php
                                        if (isset($user_contact_number_err) && !empty($user_contact_number_err)) {
                                            echo '<div class="col-12 p-0"><p class="text-danger mb-0">' . $user_contact_number_err . '</p></div>';

                                        }
                                        ?>
                                    </div>
								</div>
								
								<div class="form-group mt-4">
									<label for="userDateOfBirth">Date of Birth</label>
									
									<input type="text" class="form-control" id="userDateOfBirth" name="userDateOfBirth" value="<?php echo $saved_date_of_birth; ?>" readonly>
								</div>
								
								<hr class="my-5">
								
								<div class="form-group">
									<label for="userUpdatePassword">Update Password</label>
									
									<input type="password" class="form-control <?php echo !empty($user_password_err) ? 'border border-danger' : ''; ?>" id="userUpdatePassword" name="userUpdatePassword" aria-describedby="passwordInfo" value="<?php echo $_POST['userUpdatePassword']; ?>" onKeyDown="showSubmitButton()">
									
                                    <?php
                                    if (isset($user_password_err) && !empty($user_password_err)) {
                                        echo '<p class="text-danger mb-0">' . $user_password_err . '</p>';

                                    } else {
                                        echo '<small id="passwordInfo" class="form-text text-muted">Minimum 8 characters, must contain at least 1 uppercase letter, 1 lowercase letter, 1 number digit, and 1 special character.</small>';

                                    }
                                    ?>
								</div>
								
								<div class="form-group mt-4">
									<label for="userConfirmPassword">Confirm Password</label>
									
									<input type="password" class="form-control <?php echo !empty($user_confirm_password_err) ? 'border border-danger' : ''; ?>" id="userConfirmPassword" name="userConfirmPassword" value="<?php echo $_POST['userConfirmPassword']; ?>" onKeyDown="showSubmitButton()">
									
									<?php
									if (isset($user_confirm_password_err) && !empty($user_confirm_password_err)) {
										echo '<p class="text-danger mb-0">' . $user_confirm_password_err . '</p>';

									}
									?>
								</div>
								
								<button id="accountSubmitButton" name="myDetails" type="submit" class="btn btn-secondary btn-block mt-5">Update</button>
                                
                                <a class="btn btn-danger btn-block mt-4" role="button" data-toggle="modal" data-target="#userProfileDeleteConfirmation">Delete My Account</a>
                                
                                <div class="modal fade" id="userProfileDeleteConfirmation" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="userProfileDeleteLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h2 class="modal-title text-danger" id="userProfileDeleteLabel">Are You Sure?</h2>
                                            </div>
                                            
                                            <div class="modal-body text-center">
                                                <p>Are you sure you want to delete your account permanently? This action cannot be undone.</p>
                                                
                                                <b class="mt-5">If yes, please enter your account password to continue.</b>
                                                
                                                <input type="password" id="accountRemovePassword" name="accountRemovePassword" class="form-control my-3 text-center" placeholder="Your Password">
                                            </div>
                                            
                                            <div class="modal-footer">
                                                <button type="reset" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                                                
                                                <button type="submit" class="btn btn-danger" name="removeAccount">Delete My Account</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
							</div>
						</div>
					</div>
                </div>

                <!-- My Driving License -->
                <div class="tab-pane fade" id="my-license" role="tabpanel" aria-labelledby="myLicense">
                    <?php
                    $get_driving_license_details_sql = 'SELECT driving_license_id, license_number, date_of_expiry, country_of_issue, state_of_issue, is_expired, created_at FROM driving_license WHERE account_id = ? ORDER BY driving_license_id DESC LIMIT 1';

                    if ($get_driving_license_details_stmt = mysqli_prepare($conn, $get_driving_license_details_sql)) {
                        mysqli_stmt_bind_param($get_driving_license_details_stmt, 'i', $param_user_account_id);
                        
                        if (mysqli_stmt_execute($get_driving_license_details_stmt)) {
                            $get_driving_license_details = mysqli_stmt_get_result($get_driving_license_details_stmt);

                            while ($driving_license_details = mysqli_fetch_assoc($get_driving_license_details)) {
                                // Get driving license country
                                $get_country_of_issue_sql = 'SELECT country FROM country WHERE country_id = ?';
                                $get_country_of_issue_stmt = mysqli_prepare($conn, $get_country_of_issue_sql);

                                mysqli_stmt_bind_param($get_country_of_issue_stmt, 'i', $param_country_of_issue_id);
                                $param_country_of_issue_id  = $driving_license_details['country_of_issue'];

                                if (mysqli_stmt_execute($get_country_of_issue_stmt)) {
                                    $get_country_of_issue = mysqli_stmt_get_result($get_country_of_issue_stmt);

                                    while ($country_of_issue = mysqli_fetch_assoc($get_country_of_issue)) {
                                        $saved_country_of_issue = $country_of_issue['country'];

                                    }
                                }

                                // Get driving license state
                                $get_state_of_issue_sql = 'SELECT state FROM australia_state WHERE state_id = ?';
                                $get_state_of_issue_stmt = mysqli_prepare($conn, $get_state_of_issue_sql);

                                mysqli_stmt_bind_param($get_state_of_issue_stmt, 's', $param_state_of_issue_id);
                                $param_state_of_issue_id = $driving_license_details['state_of_issue'];

                                if (mysqli_stmt_execute($get_state_of_issue_stmt)) {
                                    $get_state_of_issue = mysqli_stmt_get_result($get_state_of_issue_stmt);

                                    while ($state_of_issue = mysqli_fetch_assoc($get_state_of_issue)) {
                                        $saved_state_of_issue = $state_of_issue['state'];

                                    }
                                }

                                mysqli_stmt_close($get_state_of_issue_stmt);
                                mysqli_stmt_close($get_country_of_issue_stmt);

                                $saved_driving_license_id = $driving_license_details['driving_license_id'];
                                $saved_license_number = $driving_license_details['license_number'];
                                $saved_date_of_expiry = date('d/m/Y', strtotime($driving_license_details['date_of_expiry']));
                                $saved_license_status = $driving_license_details['is_expired'];
                                $saved_created_date = date('d/m/Y', strtotime($driving_license_details['created_at']));

                            }
                        }
                    }

                    $driving_license_issue_place = (!empty($saved_state_of_issue) ? $saved_state_of_issue . ', ' : '') . $saved_country_of_issue;

                    mysqli_stmt_close($get_user_account_details_stmt);
                    ?>

                    <div class="container-fluid mt-4">
                        <div class="row">
                            <div class="col-sm-7 mb-5 mb-sm-0 px-0 px-sm-3">
                                <div id="driving-license" class="card mx-auto border-dark">
                                    <?php
                                    if ($saved_license_status == 1) {
                                        echo '
                                        <div class="dl-expired position-absolute ml-2 text-danger border-danger p-2 rounded">
                                            <p class="h1 display-1 mb-0">Expired</p>
                                        </div>
                                        ';
                                    }
                                    ?>
                                    
                                    <div class="card-body">
                                        <p class="card-title lead text-uppercase text-center font-weight-bolder">Driver License</p>
                                        <p class="card-subtitle text-uppercase text-center font-weight-bold"><?php echo $driving_license_issue_place; ?></p>

                                        <div class="row align-items-center mt-3">
                                            <div class="col-7">
                                                <h4 class="text-uppercase card-text mb-0"><?php echo $saved_first_name; ?></h4>
                                                <p class="text-uppercase card-text"><?php echo $saved_last_name; ?></p>

                                                <small class="card-text mb-0">License No.</small>
                                                <p class="card-text"><?php echo $saved_license_number; ?></p>

                                                <small class="card-text mb-0">License Expiry</small>
                                                <p class="card-text"><?php echo $saved_date_of_expiry; ?></p>
                                            </div>

                                            <div class="col-5 text-right">
                                                <img class="rounded user-avatar" src="/moov/assets/images/transparent_background.png" style="background: url('/moov/avatar/<?php echo $avatar_file_path; ?>');">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mx-auto driving-license-content">
                                    <small class="font-italic text-muted mx-auto">Your current license was updated on <?php echo $saved_created_date; ?>.</small>
                                    
                                    <a class="btn btn-secondary btn-block mt-5" role="button" data-toggle="modal" data-target="#updateDrivingLicense">Update My License</a>
                                
                                    <div class="modal fade" id="updateDrivingLicense" tabindex="-1" aria-labelledby="updateDrivingLicenseLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h3 class="modal-title" id="updateDrivingLicenseLabel">New License Details</h3>
                                                    
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <input type="hidden" name="drivingLicenseId" id="drivingLicenseId" value="<?php echo $saved_driving_license_id; ?>">
                                                    
                                                    <div class="form-group row">
                                                        <div class="col-sm-6">
                                                            <label for="drivingLicenseFirstName">First Name</label>

                                                            <input type="text" class="form-control" id="drivingLicenseFirstName" name="drivingLicenseFirstName" aria-describedby="dlFirstNameInfo" value="<?php echo $saved_first_name; ?>" readonly>
                                                            
                                                            <small id="dlFirstNameInfo" class="form-text text-muted">If you wish to change your first name, please contact us.</small>
                                                        </div>
                                                        
                                                        <div class="col-sm-6 mt-4 mt-sm-0">
                                                            <label for="drivingLicenseLastName">Last Name</label>

                                                            <input type="text" class="form-control" id="drivingLicenseLastName" name="drivingLicenseLastName" aria-describedby="dlLastNameInfo" value="<?php echo $saved_last_name; ?>" readonly>
                                                            
                                                            <small id="dlLastNameInfo" class="form-text text-muted">If you wish to change your last name, please contact us.</small>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-group row mt-4">
                                                        <div class="col-sm-6">
                                                            <label for="drivingLicenseNumber">License Number</label>

                                                            <input type="text" class="form-control <?php echo !empty($dl_license_number_err) ? 'border border-danger' : ''; ?>" id="drivingLicenseNumber" name="drivingLicenseNumber" value="<?php echo $_POST['drivingLicenseNumber']; ?>">

                                                            <?php
                                                            if (isset($dl_license_number_err) && !empty($dl_license_number_err)) {
                                                                echo '<p class="text-danger mb-0">' . $dl_license_number_err . '</p>';

                                                            }
                                                            ?>
                                                        </div>
                                                        
                                                        <div class="col-sm-6 mt-4 mt-sm-0">
                                                            <label for="drivingLicenseDateOfExpiry">Date of Expiry</label>

                                                            <input type="date" class="form-control <?php echo !empty($dl_date_of_expiry_err) ? 'border border-danger' : ''; ?>" id="drivingLicenseDateOfExpiry" placeholder="dd / mm / yyyy" min="<?php echo $accepted_date_of_expiry; ?>" name="drivingLicenseDateOfExpiry" value="<?php echo $_POST['drivingLicenseDateOfExpiry']; ?>">

                                                            <?php
                                                            if (isset($dl_date_of_expiry_err) && !empty($dl_date_of_expiry_err)) {
                                                                echo '<p class="text-danger mb-0">' . $dl_date_of_expiry_err . '</p>';

                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-group row mt-4">
                                                        <div class="col-sm-6">
                                                            <label for="drivingLicenseCountryOfIssue">Country of Issue</label>
                                                            
                                                            <select id="drivingLicenseCountryOfIssue" class="form-control <?php echo !empty($dl_country_of_issue_err) ? 'border border-danger' : ''; ?>" name="drivingLicenseCountryOfIssue" onChange="selectedCountry(this.value)">
                                                                <option value="" selected>Select Country of Issue</option>

                                                                <?php
                                                                $get_country_list_sql = 'SELECT * FROM country ORDER BY country ASC';
                                                                $get_country_list = mysqli_query($conn, $get_country_list_sql);

                                                                if (mysqli_num_rows($get_country_list) > 0) {
                                                                    while ($country_list = mysqli_fetch_assoc($get_country_list)) {
                                                                        $selected_country = (isset($_POST['drivingLicenseCountryOfIssue']) && $_POST['drivingLicenseCountryOfIssue'] == $country_list['country_id']) ? ' selected="selected"' : '';

                                                                        echo '<option value="' . $country_list['country_id'] . '"' . $selected_country . '>' . $country_list['country'] . '</option>';
                                                                    }

                                                                    mysqli_free_result($get_country_list);
                                                                }
                                                                ?>
                                                            </select>

                                                            <?php
                                                            if (isset($dl_country_of_issue_err) && !empty($dl_country_of_issue_err)) {
                                                                echo '<p class="text-danger mb-0">' . $dl_country_of_issue_err . '</p>';

                                                            }
                                                            ?>
                                                        </div>
                                                        
                                                        <div id="stateOfIssue" class="col-sm-6 mt-4 mt-sm-0 <?php echo $_POST['drivingLicenseCountryOfIssue'] == 9 ? 'd-block' : 'd-none'; ?>">
                                                            <label for="drivingLicenseStateOfIssue">State of Issue</label>
                                                            
                                                            <select id="drivingLicenseStateOfIssue" class="form-control <?php echo !empty($dl_state_of_issue_err) ? 'border border-danger' : ''; ?>" name="drivingLicenseStateOfIssue">
                                                                <option value="" selected>Select State of Issue</option>

                                                                <?php
                                                                $get_australia_state_list_sql = 'SELECT * FROM australia_state ORDER BY state ASC';
                                                                $get_australia_state_list = mysqli_query($conn, $get_australia_state_list_sql);

                                                                if (mysqli_num_rows($get_australia_state_list) > 0) {
                                                                    while ($australia_state_list = mysqli_fetch_assoc($get_australia_state_list)) {
                                                                        $selected_australia_state= (isset($_POST['drivingLicenseStateOfIssue']) && $_POST['drivingLicenseStateOfIssue'] == $australia_state_list['state_id']) ? ' selected="selected"' : '';

                                                                        echo '<option value="' . $australia_state_list['state_id'] . '"' . $selected_australia_state . '>' . $australia_state_list['state'] . '</option>';
                                                                    }

                                                                    mysqli_free_result($australia_state_list);
                                                                }
                                                                ?>
                                                            </select>

                                                            <?php
                                                            if (isset($dl_state_of_issue_err) && !empty($dl_state_of_issue_err)) {
                                                                echo '<p class="text-danger mb-0">' . $dl_state_of_issue_err . '</p>';

                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="reset" class="btn btn-primary" data-dismiss="modal">Cancel</button>

                                                    <button type="submit" class="btn btn-secondary" name="updateLicense">Update My License</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-5 px-0 px-sm-2 mt-3 mt-sm-0">
                                <h4 class="text-center mb-3">My License History</h4>

                                <p>Click on "View My License History" to view 10 of your most recent license history records.</p>
                                
                                <a class="btn btn-secondary btn-block mt-5" role="button" data-toggle="modal" data-target="#viewLicenseHistory">View My License History</a>
                                
                                <div class="modal fade" id="viewLicenseHistory" tabindex="-1" aria-labelledby="viewLicenseHistoryLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h3 class="modal-title" id="viewLicenseHistoryLabel">My License History</h3>

                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                <?php
                                                $current_record = 1;
                                                $max_records = 10;

                                                $count_expired_license_sql = 'SELECT COUNT(*) FROM driving_license WHERE account_id = ' . $_SESSION['moov_user_account_id'] . ' AND is_expired = 1';
                                                $count_expired_license = mysqli_query($conn, $count_expired_license_sql);
                                                $total_records = mysqli_fetch_array($count_expired_license)[0];

                                                if ($total_records > 0) {
                                                    $get_expired_license_list_sql = 'SELECT license_number, date_of_expiry, country_of_issue, state_of_issue FROM driving_license WHERE account_id = ? AND is_expired = ? ORDER BY driving_license_id DESC LIMIT ?';

                                                    if ($get_expired_license_list_stmt = mysqli_prepare($conn, $get_expired_license_list_sql)) {
                                                        mysqli_stmt_bind_param($get_expired_license_list_stmt, 'iii', $param_user_account_id, $param_dl_status, $param_dl_limit);

                                                        $param_dl_status = 1;
                                                        $param_dl_limit = $max_records;

                                                        if (mysqli_stmt_execute($get_expired_license_list_stmt)) {
                                                            $get_expired_license_list = mysqli_stmt_get_result($get_expired_license_list_stmt);

                                                            echo '<div class="row mb-4">';

                                                            while ($expired_license_list = mysqli_fetch_assoc($get_expired_license_list)) {
                                                                // Get driving license country
                                                                $get_country_of_issue_sql = 'SELECT country FROM country WHERE country_id = ?';
                                                                $get_country_of_issue_stmt = mysqli_prepare($conn, $get_country_of_issue_sql);

                                                                mysqli_stmt_bind_param($get_country_of_issue_stmt, 'i', $param_country_of_issue_id);
                                                                $param_country_of_issue_id  = $expired_license_list['country_of_issue'];

                                                                if (mysqli_stmt_execute($get_country_of_issue_stmt)) {
                                                                    $get_country_of_issue = mysqli_stmt_get_result($get_country_of_issue_stmt);

                                                                    while ($country_of_issue = mysqli_fetch_assoc($get_country_of_issue)) {
                                                                        $dl_expired_country_of_issue = $country_of_issue['country'];

                                                                    }
                                                                }

                                                                // Get driving license state
                                                                $get_state_of_issue_sql = 'SELECT state FROM australia_state WHERE state_id = ?';
                                                                $get_state_of_issue_stmt = mysqli_prepare($conn, $get_state_of_issue_sql);

                                                                mysqli_stmt_bind_param($get_state_of_issue_stmt, 's', $param_state_of_issue_id);
                                                                $param_state_of_issue_id = $expired_license_list['state_of_issue'];

                                                                if (mysqli_stmt_execute($get_state_of_issue_stmt)) {
                                                                    $get_state_of_issue = mysqli_stmt_get_result($get_state_of_issue_stmt);

                                                                    while ($state_of_issue = mysqli_fetch_assoc($get_state_of_issue)) {
                                                                        $dl_expired_state_of_issue = $state_of_issue['state'];

                                                                    }
                                                                }

                                                                mysqli_stmt_close($get_state_of_issue_stmt);
                                                                mysqli_stmt_close($get_country_of_issue_stmt);

                                                                echo '
                                                                <div class="col-sm-6 mt-4">
                                                                    <div class="card">
                                                                        <p class="card-header lead mb-0 text-center">#' . $current_record . '</p>
                                                                        <div class="card-body">
                                                                            <small class="card-text mb-0">License No.</small>
                                                                            <p class="card-text">' . $expired_license_list['license_number'] . '</p>

                                                                            <small class="card-text mb-0">License Expiry</small>
                                                                            <p class="card-text">' . $expired_license_list['date_of_expiry'] . '</p>

                                                                            <small class="card-text mb-0">Place of Issue</small>
                                                                            <p class="card-text">' . (!empty($dl_expired_state_of_issue) ? $dl_expired_state_of_issue . ', ' : '') . $dl_expired_country_of_issue . '</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                ';

                                                                $current_record++;
                                                            }

                                                            echo '</div>';

                                                        }
                                                    }
                                                } else {
                                                    echo '<p class="card-text">You do not have any expired driver license.</p>';

                                                }

                                                mysqli_free_result($total_records);
                                                ?>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="reset" class="btn btn-primary" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- My Payment Method -->
                <div class="tab-pane fade" id="my-payment" role="tabpanel" aria-labelledby="myPayment">
                    <?php require 'coming-soon.php'; ?>
                </div>
            </div>
        </form>
    </div>
    
    <script>
        document.getElementById('myProfile').onload = function() {
            document.getElementById('accountSubmitButton').disabled = true;
            
            // Show tab if $_GET['tab'] is defined in URL
            var showTab = <?php echo !empty($_GET['tab']) ? $_GET['tab'] : '\'\''; ?>;
            
            if (showTab != '') {
                $('#<?php echo $_GET['tab']; ?>').tab('show');
                
            }
            
            // Show avatar delete button
            var avatarStatus = <?php echo $_SESSION['moov_user_avatar_status']; ?>;
            
            if (avatarStatus == 0) {
                document.getElementById('removeAvatarButton').classList.add('d-none');
                
            }
            
            // Show My License tab and error message if submitted form contains error
            var drivingLicenseErrorStatus = <?php echo !empty($driving_license_error) ? $driving_license_error : 0; ?>;
            
            if (drivingLicenseErrorStatus == 1) {
                $('#myLicense').tab('show');
                $('#updateDrivingLicense').modal('show');
                
            }
            
            // Show My License tab if updated license
            var drivingLicenseUpdateStatus = <?php echo !empty($dl_updated) ? $dl_updated : 0; ?>;
            
            if (drivingLicenseUpdateStatus == 1) {
                $('#myLicense').tab('show');
                
            }
        }
        
        function showSubmitButton() {
            document.getElementById('accountSubmitButton').disabled = false;
            
        }
        
        function showUploadAvatarName(filename) {
            document.getElementById('userAvatarFileName').innerHTML = 'File: ' + filename.split("\\").pop();
            document.getElementById('userAvatarLabel').innerHTML = 'File uploaded successfully.';
            document.getElementById('userAvatarError').innerHTML = '';
            
        }
        
        function selectedCountry(country) {
            if (country == 9) {
                document.getElementById('stateOfIssue').classList.remove('d-none');

            } else {
                document.getElementById('stateOfIssue').classList.add('d-none');
                document.getElementById('stateOfIssue').classList.remove('d-flex');

            }
        }
    </script>

    <?php include 'footer.php'; ?>
</body>
	
</html>

<?php mysqli_close($conn); ?>