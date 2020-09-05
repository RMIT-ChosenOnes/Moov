<?php
session_start();
require_once '../config.php';
$parent_page_name = 'customer';
$page_name = 'modify-customer';

$deactivate_reason = $deactivate_remarks = $deactivate_forever = $deactivate_reactivate_date = '';
$deactivate_reason_err = $deactivate_remarks_err = $deactivate_forever_err = $deactivate_reactivate_date_err = '';

$today_date = date('Y-m-d');
$accepted_reactivate_date = date('Y-m-d', strtotime($today_date . '+7 days'));

echo(htmlentities($_POST['deactivateRemarks']));

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $customer_id = $_GET['id'];
    
} else {
    header('location: /moov/portal/modify-customer');
    
}

if (isset($_SESSION['moov_portal_logged_in']) && $_SESSION['moov_portal_logged_in'] == TRUE) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (empty(trim($_POST['deactivateReason'])) || trim($_POST['deactivateReason']) == '') {
            $deactivate_reason_err = 'Please select a reason to deactivate customer\'s account.';
            
        } else {
            $deactivate_reason = trim($_POST['deactivateReason']);
            
        }
        
        if (empty(trim($_POST['deactivateRemarks']))) {
            $deactivate_remarks_err = 'Please enter remarks about the customer account to be deactivated.';
            
        } else {
            if (strlen(trim($_POST['deactivateRemarks'])) > 10) {
                $deactivate_remarks = htmlentities(trim($_POST['deactivateRemarks']));

            } else {
                $deactivate_remarks_err = 'Please enter a valid remarks.';

            }
        }
        
        if (empty($deactivate_reason_err) && empty($deactivate_remarks_err) && empty($deactivate_forever_err) && empty($deactivate_reactivate_date_err)) {
            
        } else {
            $deactivate_error = TRUE;
            
        }
    }
} else {
    header('location: /moov/portal/login?url=' . urlencode('/moov/portal/database/modify-customer'));
    
}
?>

<!DOCTYPE html>
<html>
	
<head>
	<title>Deactivate Customer | Moov Portal</title>
	
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

<body id="accountDeactivate">
	<?php include '../header.php'; ?>

    <div class="container my-3">
		<h1 class="text-center">Customer Account Deactivation</h1>
        
        <?php
        $get_customer_details_sql = 'SELECT first_name, last_name, display_name, email_address, contact_number, date_of_birth, has_avatar, avatar_type, created_at FROM moov.account WHERE account_id = ?';
        $get_customer_details_stmt = mysqli_prepare($conn, $get_customer_details_sql);
        
        mysqli_stmt_bind_param($get_customer_details_stmt, 'i', $param_customer_id);
        $param_customer_id = $customer_id;
        
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
        
        mysqli_stmt_bind_param($get_license_details_stmt, 'i', $param_customer_id);
        
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
                <a class="btn btn-warning btn-block" role="button" data-toggle="modal" data-target="#customerProfileDeactivate">Confirm to Deactivate</a>
                
                <form class="mb-0" action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php'); ?>?id=<?php echo $customer_id; ?>" method="post">
                    <div class="modal fade" id="customerProfileDeactivate" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="customerProfileDeactivateLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                            <div class="modal-content">
                            
                                <div class="modal-header">
                                    <h2 class="modal-title text-warning" id="customerProfileDeactivateLabel">Warning!</h2>
                                </div>

                                <div class="modal-body">
                                    <p class="text-center">You are about to deactivate <?php echo $customer_display_name; ?>'s account. Are you sure?</p>

                                    <p class="font-weight-bold text-center">If yes, please enter the following details to continue.</p>

                                    <div class="form-group mt-4 align-items-center">
                                        <label class="col-form-label" for="deactivateReason">Select a Reason to Deactivate</label>

                                        <select id="deactivateReason" class="form-control <?php echo !empty($deactivate_reason_err) ? 'border border-danger' : ''; ?>" name="deactivateReason">
                                            <option value="" selected>Select a Reason</option>

                                            <?php
                                            $get_reason_list_sql = 'SELECT reason_id, reason FROM moov.deactivate_reason ORDER BY reason ASC';
                                            $get_reason_list = mysqli_query($conn, $get_reason_list_sql);

                                            if (mysqli_num_rows($get_reason_list) > 0) {
                                                while ($reason_list = mysqli_fetch_assoc($get_reason_list)) {
                                                    $selected_reason = (isset($_POST['deactivateReason']) && $_POST['deactivateReason'] == $reason_list['reason_id']) ? ' selected="selected"' : '';

                                                    echo '<option value="' . $reason_list['reason_id'] . '"' . $selected_reason . '>' . $reason_list['reason'] . '</option>';
                                                }

                                                mysqli_free_result($get_reason_list);
                                                
                                            }
                                            ?>
                                        </select>

                                        <?php
                                        if (isset($deactivate_reason_err) && !empty($deactivate_reason_err)) {
                                            echo '<p class="text-danger mb-0">' . $deactivate_reason_err . '</p>';

                                        }
                                        ?>
                                    </div>
                                    
                                    <div class="form-group mt-4 align-items-center">
                                        <label class="col-form-label" for="deactivateRemarks">Remarks</label>

                                        <textarea class="form-control <?php echo !empty($deactivate_remarks_err) ? 'border border-danger' : ''; ?>" id="deactivateRemarks" name="deactivateRemarks" rows="5"><?php echo $_POST['deactivateRemarks']; ?></textarea>

                                        <?php
                                        if (isset($deactivate_remarks_err) && !empty($deactivate_remarks_err)) {
                                            echo '<p class="text-danger mb-0">' . $deactivate_remarks_err . '</p>';

                                        }
                                        ?>
                                    </div>
                                    
                                    <div class="form-group mt-4 align-items-center">
                                        <label class="col-form-label" for="deactivateForever">Is Customer Going to be Deactivated Forever?</label>

                                        <div class="form-check form-check-inline ml-sm-4">
                                            <input class="form-check-input" type="radio" name="deactivateForever" id="deactivateForeverYes" value="yes">
                                            
                                            <label class="form-check-label" for="deactivateForeverYes">Yes</label>
                                        </div>
                                        
                                        <div class="form-check form-check-inline ml-4">
                                            <input class="form-check-input" type="radio" name="deactivateForever" id="deactivateForeverNo" value="no">
                                            
                                            <label class="form-check-label" for="deactivateForeverNo">No</label>
                                        </div>

                                        <?php
                                        if (isset($deactivate_forever_err) && !empty($deactivate_forever_err)) {
                                            echo '<p class="text-danger mb-0">' . $deactivate_forever_err . '</p>';

                                        }
                                        ?>
                                    </div>
                                    
                                    <div class="form-group mt-4 align-items-center">
                                        <label class="col-form-label" for="deactivateReactivateDate">Reactivate Date</label>
                                        
                                        <input type="date" class="form-control <?php echo !empty($deactivate_reactivate_date_err) ? 'border border-danger' : ''; ?>" id="deactivateReactivateDate" name="deactivateReactivateDate" min="<?php echo $accepted_reactivate_date; ?>" placeholder="dd / mm / yyyy" value="<?php echo $_POST['deactivateReactivateDate']; ?>">

                                        <?php
                                        if (isset($deactivate_reactivate_date_err) && !empty($deactivate_reactivate_date_err)) {
                                            echo '<p class="text-danger mb-0">' . $deactivate_reactivate_date_err . '</p>';

                                        }
                                        ?>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="reset" class="btn btn-primary" data-dismiss="modal">Cancel</button>

                                    <button type="submit" class="btn btn-warning" name="deactivteAccount">Deactivate Customer Account</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
	</div>
    
    <script>
        document.getElementById('accountDeactivate').onload = function() {
            var deactivateCustomerAccountErrorStatus = <?php echo !empty($deactivate_error) ? $deactivate_error : 0; ?>;
            
            if (deactivateCustomerAccountErrorStatus == 1) {
                $('#customerProfileDeactivate').modal('show');
                
            }
        }
    </script>

    <?php include '../footer.php'; ?>
</body>
	
</html>

<?php mysqli_close($conn); ?>