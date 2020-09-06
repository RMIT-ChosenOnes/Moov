<?php
session_start();
require_once '../config.php';
$parent_page_name = 'customer';
$page_name = 'modify-customer';

$suspend_reason = $suspend_remarks = $suspend_forever = $suspend_reinstate_date = '';
$suspend_reason_err = $suspend_remarks_err = $suspend_forever_err = $suspend_reinstate_date_err = '';

$today_date = date('Y-m-d');
$accepted_reactivate_date = date('Y-m-d', strtotime($today_date . '+7 days'));
$search_date_symbol = array('/', '.');
$replace_date_symbol = array('-', '-');

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $customer_id = $_GET['id'];
    
    $check_account_suspend_status_sql = 'SELECT is_suspended FROM moov.account WHERE account_id = ?';
    $check_account_suspend_status_stmt = mysqli_prepare($conn, $check_account_suspend_status_sql);
    
    mysqli_stmt_bind_param($check_account_suspend_status_stmt, 'i', $param_customer_account_id);
    $param_customer_account_id = $customer_id;
    
    if (mysqli_stmt_execute($check_account_suspend_status_stmt)) {
        mysqli_stmt_store_result($check_account_suspend_status_stmt);
        
        if (mysqli_stmt_num_rows($check_account_suspend_status_stmt) == 1) {
            mysqli_stmt_bind_result($check_account_suspend_status_stmt, $saved_account_suspend_status);
            mysqli_stmt_fetch($check_account_suspend_status_stmt);
            
            if ($saved_account_suspend_status == 1) {
                $_SESSION['moov_portal_account_suspend_error'] = TRUE;
                
                header('location: /moov/portal/database/modify-customer');
                
            }
        } else {
            header('location: /moov/portal/database/modify-customer');
            
        }
    } else {
        $suspended_error = TRUE;
        $error_message = mysqli_error($conn);
        
    }
    
    mysqli_stmt_close($check_account_suspend_status_stmt);
    
} else {
    header('location: /moov/portal/database/modify-customer');
    
}

if (isset($_SESSION['moov_portal_logged_in']) && $_SESSION['moov_portal_logged_in'] == TRUE) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (empty(trim($_POST['suspendReason'])) || trim($_POST['suspendReason']) == '') {
            $suspend_reason_err = 'Please select a reason to suspend customer\'s account.';
            
        } else {
            $suspend_reason = trim($_POST['suspendReason']);
            
        }
        
        if (empty(trim($_POST['suspendRemarks']))) {
            $suspend_remarks_err = 'Please enter remarks about the customer account to be suspended.';
            
        } else {
            if (strlen(trim($_POST['suspendRemarks'])) > 10) {
                $suspend_remarks = htmlentities(trim($_POST['suspendRemarks']));

            } else {
                $suspend_remarks_err = 'Please enter a valid remarks.';

            }
        }
        
        if (!isset($_POST['suspendForever']) && empty($_POST['suspendForever'])) {
            $suspend_forever_err = 'Please select to suspend the account forever or for a short-term.';
            
        } else {
            if ($_POST['suspendForever'] == 'yes') {
                $suspend_forever = 1;
                $suspend_reinstate_date = NULL;
                
            } elseif ($_POST['suspendForever'] == 'no') {
                $suspend_forever = 0;
                
                if (empty(trim($_POST['suspendReinstateDate']))) {
                    $suspend_reinstate_date_err = 'Please enter the date of reinstate.';

                } else {
                    if ((preg_match('/[^0-9\.\-\/]/', trim($_POST['suspendReinstateDate']))) || strlen(trim($_POST['suspendReinstateDate'])) < 8) {
                        $suspend_reinstate_date_err = 'Please enter a valid date of reinstate.';

                    } else {
                        $temp_date_of_expiry = trim($_POST['suspendReinstateDate']);

                        $replace_temp_dor = date('Y-m-d', strtotime(str_replace($search_date_symbol, $replace_date_symbol, $temp_date_of_expiry)));

                        if ($replace_temp_dor >= $accepted_reactivate_date) {
                            $suspend_reinstate_date = $replace_temp_dor;

                        } else {
                            $suspend_reinstate_date_err = 'The date of reinstate must be a date that is not less than seven (7) days prior to todays date.';

                        }
                    }
                }
            }
        }
        
        if (empty($suspend_reason_err) && empty($suspend_remarks_err) && empty($suspend_forever_err) && empty($suspend_reinstate_date_err)) {
            $insert_suspend_account_sql = 'INSERT INTO moov.suspend_account (account_id, staff_id, suspend_reason, suspend_forever, reinstate_date, remarks) VALUES (?, ?, ?, ?, ?, ?)';
            $insert_suspend_account_stmt = mysqli_prepare($conn, $insert_suspend_account_sql);
            
            mysqli_stmt_bind_param($insert_suspend_account_stmt, 'iiisss', $param_customer_account_id, $param_staff_id, $param_suspend_reason, $param_suspend_forever, $param_suspend_reinstate_date, $param_suspend_remarks);
            $param_staff_id = $_SESSION['moov_portal_staff_account_id'];
            $param_suspend_reason = $suspend_reason;
            $param_suspend_forever = $suspend_forever;
            $param_suspend_reinstate_date = $suspend_reinstate_date;
            $param_suspend_remarks = $suspend_remarks;
            
            if (mysqli_stmt_execute($insert_suspend_account_stmt)) {
                $get_suspend_reason_sql = 'SELECT reason FROM moov.suspend_reason WHERE reason_id = ?';
                $get_suspend_reason_stmt = mysqli_prepare($conn, $get_suspend_reason_sql);
                mysqli_stmt_bind_param($get_suspend_reason_stmt, 'i', $param_suspend_reason);
                
                if (mysqli_stmt_execute($get_suspend_reason_stmt)) {
                    $get_suspend_reason = mysqli_stmt_get_result($get_suspend_reason_stmt);
                    
                    while ($suspend_reason = mysqli_fetch_assoc($get_suspend_reason)) {
                        $reason = $suspend_reason['reason'];
                        
                    }
                    
                    mysqli_free_result($suspend_reason);
                    
                }
                
                mysqli_stmt_close($get_suspend_reason_stmt);
                
                $set_account_suspended_sql = 'UPDATE moov.account SET is_suspended = ? WHERE account_id = ?';
                $set_account_suspended_stmt = mysqli_prepare($conn, $set_account_suspended_sql);

                mysqli_stmt_bind_param($set_account_suspended_stmt, 'ii', $param_suspended_status, $param_customer_account_id);
                $param_suspended_status = 1;
                
                if (mysqli_stmt_execute($set_account_suspended_stmt)) {
                    $reactivate_message = '<p class="my-4 text-left">Your account will be reinstated on ' . $param_suspend_reinstate_date . '. If you can\'t access your account by then, please <a href="http://kftech.ddns.net/moov/contact">contact us</a> immediately.</p>';
                    
                    $mail_email = $_POST['suspendEmailAddress'];
                    $mail_name = $_POST['suspendDisplayName'];
                    $mail_subject = '[Moov] Your Account is Suspended!';
                    $mail_body = '<h1>Hi ' . $_POST['suspendDisplayName'] . ',</h1><p class="my-4 text-left">The following changes to your Moov account, ' . $mail_email . ', were made on ' . date('Y-m-d, H:i:s') . ':</p><p class="my-4"><b>Account Suspended</b><br/>Reason: ' . $reason . '</p>' . ($param_suspend_forever == 0 ? $reactivate_message : '') . '<p class="my-4 text-left">If you think this is an error, please <a href="http://kftech.ddns.net/moov/contact">contact us</a> immediately.</p><p class="my-4 text-left">Kind Regards,<br/>Moov Admin</p>';
                    
                    require_once '../../mail/mail-customer.php';
                    
                    $_SESSION['moov_portal_account_suspended'] = TRUE;
                    $_SESSION['moov_portal_account_suspended_display_name'] = $_POST['suspendDisplayName'];
                    
                    unset($_POST);
                    
                    header('location: /moov/portal/database/modify-customer');
                    
                } else {
                    $suspended_error = TRUE;
                    $error_message = mysqli_error($conn);
                    
                }
                
                mysqli_stmt_close($set_account_suspended_stmt);
                
            } else {
                $suspended_error = TRUE;
                $error_message = mysqli_error($conn);

            }
            
            mysqli_stmt_close($insert_suspend_account_stmt);
            
        } else {
            $suspend_error = TRUE;
            
        }
    }
} else {
    header('location: /moov/portal/login?url=' . urlencode('/moov/portal/database/modify-customer'));
    
}
?>

<!DOCTYPE html>
<html>
	
<head>
	<title>Suspend Customer Account | Moov Portal</title>
	
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
    <link rel="stylesheet" type="text/css" href="/moov/portal/assets/style/bootstrap.css">

    <!-- Self Defined CSS -->
    <link rel="stylesheet" type="text/css" href="/moov/portal/assets/style/style.css?<?php echo date('l jS \of F Y h:i:s A'); ?>">

    <!-- Favicon -->
	<link rel="icon" type="image/png" sizes="96x96" href="/moov/portal/assets/favicon/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/moov/portal/assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/moov/portal/assets/favicon/favicon-16x16.png">
</head>

<body id="accountSuspend">
	<?php include '../header.php'; ?>

    <div class="container my-3 footer-align-bottom">
		<h1 class="text-center">Suspend Customer Account</h1>
        
        <?php
        if ($suspended_error === TRUE) {
            echo '
            <div class="alert alert-warning my-4 alert-dismissible fade show" role="alert">
                Oops! There is an error occurred. Please try again later. If you continue to see this error, please contact the administrator.

			' . (!empty($error_message) ? '<br/><br/><b>Error:</b> ' . $error_message : '') . '

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ';
        }
            
        $get_customer_details_sql = 'SELECT first_name, last_name, display_name, email_address, contact_number, date_of_birth, has_avatar, avatar_type, created_at FROM moov.account WHERE account_id = ?';
        $get_customer_details_stmt = mysqli_prepare($conn, $get_customer_details_sql);
        
        mysqli_stmt_bind_param($get_customer_details_stmt, 'i', $param_customer_account_id);
        
        if (mysqli_stmt_execute($get_customer_details_stmt)) {
            $get_customer_details = mysqli_stmt_get_result($get_customer_details_stmt);
            
            while ($customer_details = mysqli_fetch_assoc($get_customer_details)) {
                $customer_first_name = $customer_details['first_name'];
                $customer_last_name = $customer_details['last_name'];
                $customer_display_name = $customer_details['display_name'];
                $customer_email_address = $customer_details['email_address'];
                $customer_contact_number = $customer_details['contact_number'];
                $customer_date_of_birth = date('d/m/Y', strtotime($customer_details['date_of_birth']));
                $customer_avatar_status = $customer_details['has_avatar'];
                $customer_avatar_type = $customer_details['avatar_type'];
                $customer_member_since = date('d/m/Y', strtotime($customer_details['created_at']));
                
            }
            
            mysqli_free_result($get_customer_details);
            
        }
        
        mysqli_stmt_close($get_customer_details_stmt);
        ?>
        
        <!-- Customer Details -->
        <h3 class="mt-4">Customer Details</h3>
        
        <div class="row align-items-center">
            <div class="col-sm-6 order-2 order-sm-2">
                <div class="row mt-5 mt-sm-4 align-items-center">
                    <div class="col-5">
                        <p class="font-weight-bold text-break">First Name</p>
                    </div>
                    
                    <div class="col-7">
                        <p class="text-break"><?php echo $customer_first_name; ?></p>
                    </div>
                </div>
                
                <div class="row mt-2 align-items-center">
                    <div class="col-5">
                        <p class="font-weight-bold text-break">Last Name</p>
                    </div>
                    
                    <div class="col-7">
                        <p class="text-break"><?php echo $customer_last_name; ?></p>
                    </div>
                </div>
                
                <div class="row mt-2 align-items-center">
                    <div class="col-5">
                        <p class="font-weight-bold text-break">Display Name</p>
                    </div>
                    
                    <div class="col-7">
                        <p class="text-break"><?php echo $customer_display_name; ?></p>
                    </div>
                </div>
                
                <div class="row mt-2 align-items-center">
                    <div class="col-5">
                        <p class="font-weight-bold text-break">Email Address</p>
                    </div>
                    
                    <div class="col-7">
                        <p class="text-break"><?php echo $customer_email_address; ?></p>
                    </div>
                </div>
                
                <div class="row mt-2 align-items-center">
                    <div class="col-5">
                        <p class="font-weight-bold text-break">Date of Birth</p>
                    </div>
                    
                    <div class="col-7">
                        <p class="text-break"><?php echo $customer_date_of_birth; ?></p>
                    </div>
                </div>
                
                <div class="row mt-2 align-items-center">
                    <div class="col-5">
                        <p class="font-weight-bold text-break">Contact Number</p>
                    </div>
                    
                    <div class="col-7">
                        <p class="text-break">+61<?php echo $customer_contact_number; ?></p>
                    </div>
                </div>
                
                <div class="row mt-2 align-items-center">
                    <div class="col-5">
                        <p class="font-weight-bold text-break">Member Since</p>
                    </div>
                    
                    <div class="col-7">
                        <p class="text-break"><?php echo $customer_member_since; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 text-center order-1 order-sm-2">
                <?php
                if ($customer_avatar_status == 0) {
                    $avatar_file_path = 'moov_default_avatar_500x500.png';

                } elseif ($customer_avatar_status == 1) {
                    $avatar_file_path = 'avatar_' . $customer_id . '.' . $customer_avatar_type;

                }
                ?>

                <img class="rounded-circle customer-avatar mt-4 mt-sm-0" src="/moov/assets/images/transparent_background.png" style="background: url('/moov/avatar/<?php echo $avatar_file_path; ?>');">
            </div>
        </div>
        
        <?php
        $get_license_details_sql = 'SELECT license_number, date_of_expiry, country_of_issue, state_of_issue, is_expired, created_at FROM moov.driving_license WHERE account_id = ?';
        $get_license_details_stmt = mysqli_prepare($conn, $get_license_details_sql);
        
        mysqli_stmt_bind_param($get_license_details_stmt, 'i', $param_customer_account_id);
        
        if (mysqli_stmt_execute($get_license_details_stmt)) {
            $get_license_details = mysqli_stmt_get_result($get_license_details_stmt);
            
            while ($license_details = mysqli_fetch_assoc($get_license_details)) {
                $get_country_of_issue_sql = 'SELECT country FROM moov.country WHERE country_id = ?';
                $get_country_of_issue_stmt = mysqli_prepare($conn, $get_country_of_issue_sql);
                
                mysqli_stmt_bind_param($get_country_of_issue_stmt, 'i', $param_country_of_issue);
                $param_country_of_issue = $license_details['country_of_issue'];
                
                if (mysqli_stmt_execute($get_country_of_issue_stmt)) {
                    $get_country_of_issue = mysqli_stmt_get_result($get_country_of_issue_stmt);
                    
                    while ($country_of_issue = mysqli_fetch_assoc($get_country_of_issue)) {
                        $license_country_of_issue = $country_of_issue['country'];
                        
                    }
                }
                
                $get_state_of_issue_sql = 'SELECT state FROM moov.australia_state WHERE state_id = ?';
                $get_state_of_issue_stmt = mysqli_prepare($conn, $get_state_of_issue_sql);
                
                mysqli_stmt_bind_param($get_state_of_issue_stmt, 's', $param_state_of_issue);
                $param_state_of_issue = $license_details['state_of_issue'];
                
                if (mysqli_stmt_execute($get_state_of_issue_stmt)) {
                    $get_state_of_issue = mysqli_stmt_get_result($get_state_of_issue_stmt);
                    
                    while ($state_of_issue = mysqli_fetch_assoc($get_state_of_issue)) {
                        $license_state_of_issue = $state_of_issue['state'];
                        
                    }
                }
                
                $license_number = $license_details['license_number'];
                $license_expired = $license_details['is_expired'];
                $license_date_of_expiry = date('d/m/Y', strtotime($license_details['date_of_expiry']));
                $license_created_at = date('d/m/Y', strtotime($license_details['created_at']));
                
            }
            
            mysqli_free_result($license_details);
            mysqli_free_result($country_of_issue);
            mysqli_free_result($state_of_issue);
            
        }
        
        mysqli_stmt_close($get_license_details_stmt)
        ?>
        
        <!-- License Details -->
        <h3 class="mt-5">License Details</h3>
        
        <div class="row align-items-start">
            <div class="col-sm-6">
                <div class="row mt-4 align-items-center">
                    <div class="col-5">
                        <p class="font-weight-bold text-break">Full Name on License</p>
                    </div>
                    
                    <div class="col-7">
                        <p class="text-break"><?php echo $customer_first_name . ' ' . $customer_last_name; ?></p>
                    </div>
                </div>
                
                <div class="row mt-2 align-items-center">
                    <div class="col-5">
                        <p class="font-weight-bold text-break">License Number</p>
                    </div>
                    
                    <div class="col-7">
                        <p class="text-break"><?php echo $license_number; ?></p>
                    </div>
                </div>
                
                <div class="row mt-2 align-items-center">
                    <div class="col-5">
                        <p class="font-weight-bold text-break">License Expiry</p>
                    </div>
                    
                    <div class="col-7">
                        <p class="text-break"><?php echo $license_date_of_expiry; ?></p>
                    </div>
                </div>
                
                <div class="row mt-2 align-items-center">
                    <div class="col-5">
                        <p class="font-weight-bold text-break">Last Modified</p>
                    </div>
                    
                    <div class="col-7">
                        <p class="text-break"><?php echo $license_created_at; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6">
                <div class="row mt-2 mt-sm-4">
                    <div class="col-5">
                        <p class="font-weight-bold text-break">Country of Issue</p>
                    </div>
                    
                    <div class="col-7">
                        <p class="text-break"><?php echo $license_country_of_issue; ?></p>
                    </div>
                </div>
                
                <?php
                if (!empty($license_state_of_issue)) {
                    echo '
                    <div class="row mt-2 align-items-center">
                        <div class="col-5">
                            <p class="font-weight-bold text-break">State of Issue</p>
                        </div>

                        <div class="col-7">
                            <p class="text-break">' . $license_state_of_issue . '</p>
                        </div>
                    </div>
                    ';
                    
                }
                
                if ($license_expired == 0) {
                    $license_status = 'Active';
                    
                } elseif ($license_expired == 1) {
                    $license_status = 'Expired';
                    
                }
                ?>
                
                <div class="row mt-2 align-items-center">
                    <div class="col-5">
                        <p class="font-weight-bold text-break">License Status</p>
                    </div>
                    
                    <div class="col-7">
                        <p class="text-break"><?php echo $license_status; ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-5 align-items-center">
            <div class="col-sm-6">
                <a class="btn btn-primary btn-block" href="/moov/portal/database/modify-customer" role="button">Cancel</a>
            </div>
            
            <div class="col-sm-6 mt-4 mt-sm-0">
                <a class="btn btn-warning btn-block" role="button" data-toggle="modal" data-target="#customerProfileSuspend">Confirm to Suspend</a>
                
                <form class="mb-0" action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php'); ?>?id=<?php echo $customer_id; ?>" method="post" onSubmit="submitButton()">
                    <div class="modal fade" id="customerProfileSuspend" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="customerProfileSuspendLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                            <div class="modal-content">
                            
                                <div class="modal-header">
                                    <h2 class="modal-title text-warning" id="customerProfileSuspendLabel">Warning!</h2>
                                </div>

                                <div class="modal-body">
                                    <p class="text-center">You are about to suspend <?php echo $customer_display_name; ?>'s account. Are you sure?</p>

                                    <p class="font-weight-bold text-center">If yes, please enter the following details to continue.</p>
                                    
                                    <input type="hidden" name="suspendDisplayName" id="suspendDisplayName" value="<?php echo $customer_display_name; ?>">
                                    <input type="hidden" name="suspendEmailAddress" id="suspendEmailAddress" value="<?php echo $customer_email_address; ?>">

                                    <div class="form-group mt-4 align-items-center">
                                        <label class="col-form-label" for="suspendReason">Select a Reason to Suspend</label>

                                        <select id="suspendReason" class="form-control <?php echo !empty($suspend_reason_err) ? 'border border-danger' : ''; ?>" name="suspendReason" onKeyUp="changeEventButton(this)">
                                            <option value="" selected>Select a Reason</option>

                                            <?php
                                            $get_reason_list_sql = 'SELECT reason_id, reason FROM moov.suspend_reason ORDER BY reason ASC';
                                            $get_reason_list = mysqli_query($conn, $get_reason_list_sql);

                                            if (mysqli_num_rows($get_reason_list) > 0) {
                                                while ($reason_list = mysqli_fetch_assoc($get_reason_list)) {
                                                    $selected_reason = (isset($_POST['suspendReason']) && $_POST['suspendReason'] == $reason_list['reason_id']) ? ' selected="selected"' : '';

                                                    echo '<option value="' . $reason_list['reason_id'] . '"' . $selected_reason . '>' . $reason_list['reason'] . '</option>';
                                                }

                                                mysqli_free_result($get_reason_list);
                                                
                                            }
                                            ?>
                                        </select>

                                        <?php
                                        if (isset($suspend_reason_err) && !empty($suspend_reason_err)) {
                                            echo '<p class="text-danger mb-0">' . $suspend_reason_err . '</p>';

                                        }
                                        ?>
                                    </div>
                                    
                                    <div class="form-group mt-4 align-items-center">
                                        <label class="col-form-label" for="suspendRemarks">Remarks</label>

                                        <textarea class="form-control <?php echo !empty($suspend_remarks_err) ? 'border border-danger' : ''; ?>" id="suspendRemarks" name="suspendRemarks" rows="5" onKeyUp="changeEventButton(this)"><?php echo $_POST['suspendRemarks']; ?></textarea>

                                        <?php
                                        if (isset($suspend_remarks_err) && !empty($suspend_remarks_err)) {
                                            echo '<p class="text-danger mb-0">' . $suspend_remarks_err . '</p>';

                                        }
                                        ?>
                                    </div>
                                    
                                    <div class="form-group mt-4 align-items-center">
                                        <label class="col-form-label" for="suspendForever">Is Customer Going to be Suspended Forever?</label>

                                        <div class="form-check form-check-inline ml-sm-4">
                                            <input class="form-check-input" type="radio" name="suspendForever" id="suspendForeverYes" value="yes" <?php echo ($_POST['suspendForever'] == 'yes') ? 'checked' : ''; ?> onChange="showReactivateDate(this.value)" onKeyUp="changeEventButton(this)">
                                            
                                            <label class="form-check-label <?php echo !empty($suspend_forever_err) ? 'text-danger' : ''; ?>" for="suspendForeverYes">Yes</label>
                                        </div>
                                        
                                        <div class="form-check form-check-inline ml-4">
                                            <input class="form-check-input" type="radio" name="suspendForever" id="suspendForeverNo" value="no" <?php echo ($_POST['suspendForever'] == 'no') ? 'checked' : ''; ?> onChange="showReactivateDate(this.value)" onKeyUp="changeEventButton(this)">
                                            
                                            <label class="form-check-label <?php echo !empty($suspend_forever_err) ? 'text-danger' : ''; ?>" for="suspendForeverNo">No</label>
                                        </div>

                                        <?php
                                        if (isset($suspend_forever_err) && !empty($suspend_forever_err)) {
                                            echo '<p class="text-danger mb-0">' . $suspend_forever_err . '</p>';

                                        }
                                        ?>
                                    </div>
                                    
                                    <div id="reactivateDate" class="form-group mt-4 align-items-center <?php echo $_POST['suspendForever'] == 'no' ? 'd-block' : 'd-none'; ?>">
                                        <label class="col-form-label" for="suspendReinstateDate">Reinstate Date</label>
                                        
                                        <input type="date" class="form-control <?php echo !empty($suspend_reinstate_date_err) ? 'border border-danger' : ''; ?>" id="suspendReinstateDate" name="suspendReinstateDate" min="<?php echo $accepted_reactivate_date; ?>" placeholder="dd / mm / yyyy" value="<?php echo $_POST['suspendReinstateDate']; ?>" onKeyUp="changeEventButton(this)">

                                        <?php
                                        if (isset($suspend_reinstate_date_err) && !empty($suspend_reinstate_date_err)) {
                                            echo '<p class="text-danger mb-0">' . $suspend_reinstate_date_err . '</p>';

                                        }
                                        ?>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="reset" class="btn btn-primary" data-dismiss="modal">Cancel</button>

                                    <button id="suspendSubmitButton" type="submit" class="btn btn-warning">
                                        <span id="submitButton">Suspend Customer Account</span>
					
                                        <img id="processingIcon" src="/moov/assets/images/processing_icon.svg" class="processing-icon d-none">
                                        <span id="processingButton" class="d-none">Processing...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
	</div>
    
    <script>
        document.getElementById('accountSuspend').onload = function() {
            document.getElementById('reactivateDate').classList.add('d-none');
            
            var suspendCustomerAccountErrorStatus = <?php echo !empty($suspend_error) ? $suspend_error : 0; ?>;
            
            if (suspendCustomerAccountErrorStatus == 1) {
                $('#customerProfileSuspend').modal('show');
                
            }
        }
        
        function showReactivateDate(selection) {
            if (selection == 'no') {
                document.getElementById('reactivateDate').classList.remove('d-none');
                
            } else if (selection == 'yes') {
                document.getElementById('reactivateDate').classList.add('d-none');
                
            }
        }
        
        function submitButton() {
            document.getElementById('suspendSubmitButton').disabled = true;
            document.getElementById('submitButton').classList.add('d-none');
            document.getElementById('processingIcon').classList.add('d-inline-block');
            document.getElementById('processingIcon').classList.remove('d-none');
            document.getElementById('processingButton').classList.remove('d-none');

        }

        function changeEventButton(event) {
            if (event.keyCode == 13) {
                event.preventDefault;

                document.getElementById('suspendSubmitButton').disabled = true;
                document.getElementById('submitButton').classList.add('d-none');
                document.getElementById('processingIcon').classList.add('d-inline-block');
                document.getElementById('processingIcon').classList.remove('d-none');
                document.getElementById('processingButton').classList.remove('d-none');

            }
        }
    </script>

    <?php include '../footer.php'; ?>
</body>
	
</html>

<?php mysqli_close($conn); ?>