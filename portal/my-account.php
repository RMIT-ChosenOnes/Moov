<?php
session_start();
require_once 'config.php';
$parent_page_name = 'profile';
$page_name = basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php');

$staff_first_name = $staff_last_name = $staff_email_address = $staff_password = '';
$staff_first_name_err = $staff_last_name_err = $staff_email_address_err = $staff_password_err = $staff_confirm_password_err = '';

if (isset($_SESSION['moov_portal_logged_in']) && $_SESSION['moov_portal_logged_in'] == TRUE) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (empty(trim($_POST['staffFirstName']))) {
            $staff_first_name_err = 'Please enter your first name.';

        } else {
            if (preg_match('/^[a-zA-Z\-\s]{3,100}$/', trim($_POST['staffFirstName']))) {
                $staff_first_name = trim($_POST['staffFirstName']);

            } else {
                $staff_first_name_err = 'Please enter a valid first name.';

            }
        }
        
        if (empty(trim($_POST['staffLastName']))) {
            $staff_last_name_err = 'Please enter your last name.';

        } else {
            if (preg_match('/^[a-zA-Z\-\s]{2,100}$/', trim($_POST['staffLastName']))) {
                $staff_last_name = trim($_POST['staffLastName']);

            } else {
                $staff_last_name_err = 'Please enter a valid last name.';

            }
        }
        
        if (empty(trim($_POST['staffEmailAddress']))) {
            $staff_email_address_err = 'Please enter your email address.';

        } else {
            $check_email_duplication_sql = 'SELECT account_id FROM portal_account WHERE email_address = ?';

            if ($check_email_duplication_stmt = mysqli_prepare($conn, $check_email_duplication_sql)) {
                mysqli_stmt_bind_param($check_email_duplication_stmt, 's', $param_temp_staff_email_address);

                $param_temp_staff_email_address = trim($_POST['staffEmailAddress']);

                if (mysqli_stmt_execute($check_email_duplication_stmt)) {
                    mysqli_stmt_store_result($check_email_duplication_stmt);

                    if (mysqli_stmt_num_rows($check_email_duplication_stmt) > 0) {
                        mysqli_stmt_bind_result($check_email_duplication_stmt, $saved_staff_id);
                        mysqli_stmt_fetch($check_email_duplication_stmt);

                        if ($saved_staff_id != $_SESSION['moov_portal_staff_account_id']) {
                            $staff_email_address_err = 'Email address is already in use. Please try another email address.';

                        } else {
                            $staff_email_address = $param_temp_staff_email_address;

                        }
                    } else {
                        $staff_email_address = $param_temp_staff_email_address;

                    }
                } else {
                    $update_error = TRUE;
                    $error_message = mysqli_error($conn);

                }
            }

            mysqli_stmt_close($check_email_duplication_stmt);

        }
        
        if (!empty(trim($_POST['staffUpdatePassword']))) {
            if (!preg_match('/[a-z]+/', trim($_POST['staffUpdatePassword'])) || !preg_match('/[A-Z]+/', trim($_POST['staffUpdatePassword'])) || !preg_match('/[^a-zA-Z0-9]+/', trim($_POST['staffUpdatePassword'])) || strlen(trim($_POST['staffUpdatePassword'])) < 8) {
                $staff_password_err = 'Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number digit, 1 special character, and have at least 8 characters long.';

            }

            if (empty(trim($_POST['staffConfirmPassword']))) {
                $staff_confirm_password_err = 'Please confirm the password again.';

            } else {
                if (trim($_POST['staffUpdatePassword']) == trim($_POST['staffConfirmPassword'])) {
                    $staff_password = trim($_POST['staffUpdatePassword']);

                } else {
                    $staff_password_err = $staff_confirm_password_err = 'Password does not matched. Please try again.';

                }
            }
        }
        
        if (empty($staff_first_name_err) && empty($staff_last_name_err) && empty($staff_email_address_err) && empty($staff_password_err) && empty($staff_confirm_password_err)) {
            $update_staff_account_sql = 'UPDATE portal_account SET first_name = ?, last_name = ?, email_address = ? WHERE account_id = ?';

            if ($update_staff_account_stmt = mysqli_prepare($conn, $update_staff_account_sql)) {
                mysqli_stmt_bind_param($update_staff_account_stmt, 'sssi', $param_staff_first_name, $param_staff_last_name, $param_staff_email_address, $param_staff_account_id);

                $param_staff_first_name = $staff_first_name;
                $param_staff_last_name = $staff_last_name;
                $param_staff_email_address = $staff_email_address;
                $param_staff_account_id = $_SESSION['moov_portal_staff_account_id'];

                if (mysqli_stmt_execute($update_staff_account_stmt)) {
                    if (!empty($staff_password)) {
                        $update_staff_password_sql = 'UPDATE portal_account SET password = ? WHERE account_id = ?';

                        if ($update_staff_password_stmt = mysqli_prepare($conn, $update_staff_password_sql)) {
                            mysqli_stmt_bind_param($update_staff_password_stmt, 'si', $param_staff_password, $param_staff_account_id);

                            $param_staff_password = password_hash($staff_password, PASSWORD_DEFAULT);

                            if (mysqli_stmt_execute($update_staff_password_stmt)) {
                                $record_updated = TRUE;
                                unset($_POST);

                            } else {
                                $update_error = TRUE;
                                $error_message = mysqli_error($conn);

                            }
                        }

                        mysqli_stmt_close($update_staff_password_stmt);

                    }

                    $_SESSION['moov_portal_staff_first_name'] = $staff_first_name;
                    $_SESSION['moov_portal_staff_email'] = $staff_email_address;
                    
                    $record_updated = TRUE;
                    unset($_POST);

                } else {
                    $update_error = TRUE;
                    $error_message = mysqli_error($conn);

                }
            }

            mysqli_stmt_close($update_staff_account_stmt);

        }
    }
} else {
	header('location: /moov/portal/login?url=' . urlencode('/moov/portal/' . $page_name));
	
}
?>

<!DOCTYPE html>
<html>
	
<head>
	<title>My Account | Moov Portal</title>
	
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

<body id="myProfile">
	<?php include 'header.php'; ?>

    <div class="container my-3 footer-align-bottom">
		<h1 class="text-center">My Account</h1>
        
        <?php
        if ($record_updated === TRUE) {
            echo '
            <div class="alert alert-success my-4 alert-dismissible fade show" role="alert">
                Account updated successfully.

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ';
        }
		
		if ($update_error === TRUE) {
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
        ?>
        
        <form action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php'); ?>" method="post" onSubmit="submitButton()">
            <?php
            $get_staff_account_details_sql = 'SELECT first_name, last_name, username, email_address, role FROM portal_account WHERE account_id = ?';

            if ($get_staff_account_details_stmt = mysqli_prepare($conn, $get_staff_account_details_sql)) {
                mysqli_stmt_bind_param($get_staff_account_details_stmt, 'i', $param_staff_account_id);

                $param_staff_account_id = $_SESSION['moov_portal_staff_account_id'];

                if (mysqli_stmt_execute($get_staff_account_details_stmt)) {
                    $get_staff_account_details = mysqli_stmt_get_result($get_staff_account_details_stmt);

                    while ($staff_account_details = mysqli_fetch_assoc($get_staff_account_details)) {
                        $saved_first_name = $staff_account_details['first_name'];
                        $saved_last_name = $staff_account_details['last_name'];
                        $saved_username = $staff_account_details['username'];
                        $saved_email_address = $staff_account_details['email_address'];
                        
                        if ($staff_account_details['role'] == 1) {
                            $saved_staff_role = 'Admin';
                            
                        } elseif ($staff_account_details['role'] == 2) {
                            $saved_staff_role = 'Staff';
                            
                        }

                    }
                }
            }

            mysqli_stmt_close($get_staff_account_details_stmt);
            ?>
            
            <div class="row mt-4">
                <div class="col-sm-4">
                    <div class="text-center">
                        <img class="rounded-circle staff-avatar" src="/moov/portal/assets/images/transparent_background.png" style="background: url('/moov/portal/assets/images/moov_portal_default_avatar_500x500.png');">
                    </div>
                </div>
                
                <div class="col-sm-8 mt-5 mt-sm-0">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-6">
                                <label for="staffUsername">Username</label>

                                <input type="text" class="form-control-plaintext" id="staffUsername" name="staffUsername" value="<?php echo $saved_username; ?>" onKeyUp="changeEventButton(this)" readonly>
                            </div>
                            
                            <div class="col-6">
                                <label for="staffRole">Role</label>

                                <input type="text" class="form-control-plaintext" id="staffRole" name="staffRole" value="<?php echo $saved_staff_role; ?>" onKeyUp="changeEventButton(this)" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mt-4">
                        <label for="staffFirstName">First Name</label>

                        <input type="text" class="form-control <?php echo !empty($staff_first_name_err) ? 'border border-danger' : ''; ?>" id="staffFirstName" name="staffFirstName" value="<?php echo isset($_POST['myDetails']) ? $_POST['staffFirstName'] : $saved_first_name; ?>" onKeyDown="showSubmitButton()" onKeyUp="changeEventButton(this)">

                        <?php
                        if (isset($staff_first_name_err) && !empty($staff_first_name_err)) {
                            echo '<p class="text-danger mb-0">' . $staff_first_name_err . '</p>';

                        }
                        ?>
                    </div>
                    
                    <div class="form-group mt-4">
                        <label for="staffLastName">Last Name</label>

                        <input type="text" class="form-control <?php echo !empty($staff_last_name_err) ? 'border border-danger' : ''; ?>" id="staffLastName" name="staffLastName" value="<?php echo isset($_POST['myDetails']) ? $_POST['staffLastName'] : $saved_last_name; ?>" onKeyDown="showSubmitButton()" onKeyUp="changeEventButton(this)">

                        <?php
                        if (isset($staff_last_name_err) && !empty($staff_last_name_err)) {
                            echo '<p class="text-danger mb-0">' . $staff_last_name_err . '</p>';

                        }
                        ?>
                    </div>
                    
                    <div class="form-group mt-4">
                        <label for="staffEmailAddress">Email Address</label>

                        <input type="email" class="form-control <?php echo !empty($staff_email_address_err) ? 'border border-danger' : ''; ?>" id="staffEmailAddress" name="staffEmailAddress" value="<?php echo isset($_POST['myDetails']) ? $_POST['staffEmailAddress'] : $saved_email_address; ?>" onKeyDown="showSubmitButton()" onKeyUp="changeEventButton(this)">

                        <?php
                        if (isset($staff_email_address_err) && !empty($staff_email_address_err)) {
                            echo '<p class="text-danger mb-0">' . $staff_email_address_err . '</p>';

                        }
                        ?>
                    </div>
                    
                    <hr class="my-5">
								
                    <div class="form-group">
                        <label for="staffUpdatePassword">Update Password</label>

                        <input type="password" class="form-control <?php echo !empty($staff_password_err) ? 'border border-danger' : ''; ?>" id="staffUpdatePassword" name="staffUpdatePassword" aria-describedby="passwordInfo" value="<?php echo $_POST['staffUpdatePassword']; ?>" onKeyDown="showSubmitButton()" onKeyUp="changeEventButton(this)">

                        <?php
                        if (isset($staff_password_err) && !empty($staff_password_err)) {
                            echo '<p class="text-danger mb-0">' . $staff_password_err . '</p>';

                        } else {
                            echo '<small id="passwordInfo" class="form-text text-muted">Minimum 8 characters, must contain at least 1 uppercase letter, 1 lowercase letter, 1 number digit, and 1 special character.</small>';

                        }
                        ?>
                    </div>

                    <div class="form-group mt-4">
                        <label for="staffConfirmPassword">Confirm Password</label>

                        <input type="password" class="form-control <?php echo !empty($staff_confirm_password_err) ? 'border border-danger' : ''; ?>" id="staffConfirmPassword" name="staffConfirmPassword" value="<?php echo $_POST['staffConfirmPassword']; ?>" onKeyDown="showSubmitButton()" onKeyUp="changeEventButton(this)">

                        <?php
                        if (isset($staff_confirm_password_err) && !empty($staff_confirm_password_err)) {
                            echo '<p class="text-danger mb-0">' . $staff_confirm_password_err . '</p>';

                        }
                        ?>
                    </div>
                    
                    <button id="accountSubmitButton" name="myDetails" type="submit" class="btn btn-primary btn-block mt-5">
                        <span id="submitButton">Update</span>
					
						<img id="processingIcon" src="/moov/assets/images/processing_icon.svg" class="processing-icon d-none">
						<span id="processingButton" class="d-none">Processing...</span>
                    </button>
                </div>
            </div>
        </form>
	</div>
    
    <script>
        document.getElementById('myProfile').onload = function() {
            document.getElementById('accountSubmitButton').disabled = true;
            
        }
        
        function showSubmitButton() {
            document.getElementById('accountSubmitButton').disabled = false;
            
        }
        
        function submitButton() {
            document.getElementById('submitButton').classList.add('d-none');
            document.getElementById('processingIcon').classList.add('d-inline-block');
            document.getElementById('processingIcon').classList.remove('d-none');
            document.getElementById('processingButton').classList.remove('d-none');

        }

        function changeEventButton(event) {
            if (event.keyCode == 13) {
                event.preventDefault;

                document.getElementById('submitButton').classList.add('d-none');
                document.getElementById('processingIcon').classList.add('d-inline-block');
                document.getElementById('processingIcon').classList.remove('d-none');
                document.getElementById('processingButton').classList.remove('d-none');

            }
        }
    </script>

    <?php include 'footer.php'; ?>
</body>
	
</html>

<?php mysqli_close($conn); ?>