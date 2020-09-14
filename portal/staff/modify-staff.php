<?php
session_start();
require_once '../config.php';
$parent_page_name = 'staff';
$page_name = 'staff';

$selected_staff_id = $modify_first_name = $modify_last_name = $modify_email_address = $modify_role = $modify_password = '';
$modify_first_name_err = $modify_last_name_err = $modify_email_address_err = $modify_role_err = $modify_password_err = $modify_confirm_password_err = '';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $staff_id = $_GET['id'];
    
} else {
    header('location: /moov/portal/staff/');
    
}

if (isset($_SESSION['moov_portal_logged_in']) && $_SESSION['moov_portal_logged_in'] == TRUE) {
	if (isset($_SESSION['moov_portal_staff_role']) && $_SESSION['moov_portal_staff_role'] == 'Admin') {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$selected_staff_id = $_POST['selectedStaffId'];

			if (empty(trim($_POST['staffFirstName']))) {
				$modify_first_name_err = 'Please enter staff\'s first name.';

			} else {
				if (preg_match('/^[a-zA-Z\-\s]{3,100}$/', trim($_POST['staffFirstName']))) {
					$modify_first_name = trim($_POST['staffFirstName']);

				} else {
					$modify_first_name_err = 'Please enter a valid staff\'s first name.';

				}
			}

			if (empty(trim($_POST['staffLastName']))) {
				$modify_last_name_err = 'Please enter staff\'s last name.';

			} else {
				if (preg_match('/^[a-zA-Z\-\s]{2,100}$/', trim($_POST['staffLastName']))) {
					$modify_last_name = trim($_POST['staffLastName']);

				} else {
					$modify_last_name_err = 'Please enter a valid staff\'s last name.';

				}
			}

			if (empty(trim($_POST['staffEmailAddress']))) {
				$modify_email_address_err = 'Please enter staff\'s email address.';

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
							
							if ($saved_staff_id != $staff_id) {
								$modify_email_address_err = 'Email address is already in use. Please try another email address.';
								
							} else {
								$modify_email_address = $param_temp_staff_email_address;
								
							}
						} else {
							$modify_email_address = $param_temp_staff_email_address;

						}
					} else {
						$update_error = TRUE;
						$error_message = mysqli_error($conn);

					}
				}
				
				mysqli_stmt_close($check_email_duplication_stmt);
				
			}

			if (empty(trim($_POST['staffRole'])) || trim($_POST['staffRole']) == 0) {
				$modify_role_err = 'Please assign an appropriate role.';

			} else {
				$modify_role = trim($_POST['staffRole']);

			}

			if (!empty(trim($_POST['staffUpdatePassword']))) {
				if (!preg_match('/[a-z]+/', trim($_POST['staffUpdatePassword'])) || !preg_match('/[A-Z]+/', trim($_POST['staffUpdatePassword'])) || !preg_match('/[^a-zA-Z0-9]+/', trim($_POST['staffUpdatePassword'])) || strlen(trim($_POST['staffUpdatePassword'])) < 8) {
					$modify_password_err = 'Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number digit, 1 special character, and have at least 8 characters long.';
					
				}
				
				if (empty(trim($_POST['staffConfirmPassword']))) {
					$modify_confirm_password_err = 'Please confirm the password again.';

				} else {
					if (trim($_POST['staffUpdatePassword']) == trim($_POST['staffConfirmPassword'])) {
						$modify_password = trim($_POST['staffUpdatePassword']);

					} else {
						$modify_password_err = $modify_confirm_password_err = 'Password does not matched. Please try again.';

					}
				}
			}

			if (empty($modify_first_name_err) && empty($modify_last_name_err) && empty($modify_email_address_err) && empty($modify_role_err) && empty($modify_password_err) && empty($modify_confirm_password_err)) {
				$update_staff_account_sql = 'UPDATE portal_account SET first_name = ?, last_name = ?, email_address = ?, role = ? WHERE account_id = ?';

				if ($update_staff_account_stmt = mysqli_prepare($conn, $update_staff_account_sql)) {
					mysqli_stmt_bind_param($update_staff_account_stmt, 'sssii', $param_staff_first_name, $param_staff_last_name, $param_staff_email_address, $param_staff_role, $param_staff_account_id);
					
					$param_staff_first_name = $modify_first_name;
					$param_staff_last_name = $modify_last_name;
					$param_staff_email_address = $modify_email_address;
					$param_staff_role = $modify_role;
					$param_staff_account_id = $staff_id;
					
					if (mysqli_stmt_execute($update_staff_account_stmt)) {
						if (!empty($modify_password)) {
							$update_staff_password_sql = 'UPDATE portal_account SET password = ? WHERE account_id = ?';

							if ($update_staff_password_stmt = mysqli_prepare($conn, $update_staff_password_sql)) {
								mysqli_stmt_bind_param($update_staff_password_stmt, 'si', $param_staff_password, $param_staff_account_id);
								
								$param_staff_password = password_hash($modify_password, PASSWORD_DEFAULT);
								
								if (mysqli_stmt_execute($update_staff_password_stmt)) {
                                    $_SESSION['moov_portal_staff_account_password_updated'] = TRUE;
									
								} else {
									$update_error = TRUE;
									$error_message = mysqli_error($conn);

								}
							}
							
							mysqli_stmt_close($update_staff_password_stmt);
							
						}

						$_SESSION['moov_portal_staff_account_updated'] = TRUE;
                        $_SESSION['moov_portal_staff_account_name'] = $param_staff_first_name;
                        
						unset($_POST);
                        
                        header('location: /moov/portal/staff/');
						
					} else {
						$update_error = TRUE;
						$error_message = mysqli_error($conn);

					}
				}
				
				mysqli_stmt_close($update_staff_account_stmt);
				
			}
		}
	} else {
		header('location: /moov/portal/');
		
	}
} else {
	header('location: /moov/portal/login?url=' . urlencode('/moov/portal/' . $page_name));
	
}
?>

<!DOCTYPE html>
<html>
	
<head>
	<title>Modify Staff | Moov Portal</title>
	
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

<body id="modifyAccount">
	<?php include '../header.php'; ?>

    <div class="container my-3 footer-align-bottom">
		<h1 class="text-center">Modify Staff Account</h1>
		
		<?php
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
        
        $get_staff_details_sql = 'SELECT username, first_name, last_name, email_address, role FROM portal_account WHERE account_id = ?';
        $get_staff_details_stmt = mysqli_prepare($conn, $get_staff_details_sql);
        
        mysqli_stmt_bind_param($get_staff_details_stmt, 'i', $param_staff_account_id);
        
        $param_staff_account_id = $staff_id;
        
        if (mysqli_stmt_execute($get_staff_details_stmt)) {
            $get_staff_details = mysqli_stmt_get_result($get_staff_details_stmt);
            
            while ($staff_details = mysqli_fetch_assoc($get_staff_details)) {
                $staff_first_name = $staff_details['first_name'];
                $staff_last_name = $staff_details['last_name'];
                $staff_username = $staff_details['username'];
                $staff_email_address = $staff_details['email_address'];
                $staff_role = $staff_details['role'];
                
            }
            
            mysqli_free_result($get_staff_details);
            
        }
        
        mysqli_stmt_close($get_staff_details_stmt);
        ?>
        
        
		
		<form class="mt-5" action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php') . '?id=' . $staff_id; ?>" method="post" onSubmit="submitButton()">
            <div class="form-group row align-items-center">
                <label for="staffUsername" class="col-sm-3 col-form-label">Username</label>

                <div class="col-sm-9">
                    <input type="text" class="form-control-plaintext" id="staffUsername" name="staffUsername" value="<?php echo $staff_username; ?>" onKeyUp="changeEventButton(this)" readonly>
                </div>
            </div>

            <div class="form-group row mt-4 align-items-center">
                <label for="staffFirstName" class="col-sm-3 col-form-label">First Name</label>

                <div class="col-sm-9">
                    <input type="text" class="form-control <?php echo !empty($modify_first_name_err) ? 'border border-danger' : ''; ?>" id="staffFirstName" name="staffFirstName" value="<?php echo isset($_POST['modifyStaff']) ? $_POST['staffFirstName'] : $staff_first_name; ?>" onKeyUp="changeEventButton(this)">

                    <?php
                    if (isset($modify_first_name_err) && !empty($modify_first_name_err)) {
                        echo '<p class="text-danger mb-0 text-left">' . $modify_first_name_err . '</p>';

                    }
                    ?>
                </div>
            </div>

            <div class="form-group row mt-4 align-items-center">
                <label for="staffLastName" class="col-sm-3 col-form-label">Last Name</label>

                <div class="col-sm-9">
                    <input type="text" class="form-control <?php echo !empty($modify_last_name_err) ? 'border border-danger' : ''; ?>" id="staffLastName" name="staffLastName" value="<?php echo isset($_POST['modifyStaff']) ? $_POST['staffLastName'] : $staff_last_name; ?>" onKeyUp="changeEventButton(this)">

                    <?php
                    if (isset($modify_last_name_err) && !empty($modify_last_name_err)) {
                        echo '<p class="text-danger mb-0 text-left">' . $modify_last_name_err . '</p>';

                    }
                    ?>
                </div>
            </div>

            <div class="form-group row mt-4 align-items-center">
                <label for="staffEmailAddress" class="col-sm-3 col-form-label">Email Address</label>

                <div class="col-sm-9">
                    <input type="email" class="form-control <?php echo !empty($modify_email_address_err) ? 'border border-danger' : ''; ?>" id="staffEmailAddress" name="staffEmailAddress" value="<?php echo isset($_POST['modifyStaff']) ? $_POST['staffEmailAddress'] : $staff_email_address; ?>" onKeyUp="changeEventButton(this)">

                    <?php
                    if (isset($modify_email_address_err) && !empty($modify_email_address_err)) {
                        echo '<p class="text-danger mb-0 text-left">' . $modify_email_address_err . '</p>';

                    }
                    ?>
                </div>
            </div>

            <div class="form-group row mt-4 align-items-center">
                <label for="staffRole" class="col-sm-3 col-form-label">Role</label>

                <div class="col-sm-9">
                    <select id="staffRole" class="form-control <?php echo !empty($modify_role_err) ? 'border border-danger' : ''; ?>" name="staffRole" onKeyUp="changeEventButton(this)">
                        <option value="" selected>Select Staff's Role</option>

                        <?php
                        $get_role_sql = 'SELECT role_id, role FROM portal_account_role ORDER BY role ASC';
                        $get_role = mysqli_query($conn, $get_role_sql);

                        if (mysqli_num_rows($get_role) > 0) {
                            while ($role = mysqli_fetch_assoc($get_role)) {
                                $selected_role = ((isset($_POST['staffRole']) && $_POST['staffRole'] == $role['role_id']) || $staff_role == $role['role_id']) ? ' selected="selected"' : '';

                                echo '<option value="' . $role['role_id'] . '"' . $selected_role . '>' . $role['role'] . '</option>';
                            }

                            mysqli_free_result($get_role);
                            
                        }
                        ?>
                    </select>

                    <?php
                    if (isset($modify_role_err) && !empty($modify_role_err)) {
                        echo '<p class="text-danger mb-0 text-left">' . $modify_role_err . '</p>';

                    }
                    ?>
                </div>
            </div>

            <hr class="my-5">

            <div class="form-group row align-items-center">
                <label for="staffUpdatePassword" class="col-sm-3 col-form-label">Update Password</label>

                <div class="col-sm-9">
                    <input type="password" class="form-control <?php echo !empty($modify_password_err) ? 'border border-danger' : ''; ?>" id="staffUpdatePassword" name="staffUpdatePassword" aria-describedby="passwordInfo" value="<?php echo $_POST['staffUpdatePassword']; ?>" onKeyUp="changeEventButton(this)">

                    <?php
                    if (isset($modify_password_err) && !empty($modify_password_err)) {
                        echo '<p class="text-danger mb-0">' . $modify_password_err . '</p>';

                    } else {
                        echo '<small id="passwordInfo" class="form-text text-muted">Minimum 8 characters, must contain at least 1 uppercase letter, 1 lowercase letter, 1 number digit, and 1 special character.</small>';

                    }
                    ?>
                </div>
            </div>

            <div class="form-group row mt-4 align-items-center">
                <label for="staffConfirmPassword" class="col-sm-3 col-form-label">Confirm Password</label>

                <div class="col-sm-9">
                    <input type="password" class="form-control <?php echo !empty($modify_confirm_password_err) ? 'border border-danger' : ''; ?>" id="staffConfirmPassword" name="staffConfirmPassword" value="<?php echo $_POST['staffConfirmPassword']; ?>" onKeyUp="changeEventButton(this)">

                    <?php
                    if (isset($modify_confirm_password_err) && !empty($modify_confirm_password_err)) {
                        echo '<p class="text-danger mb-0 text-left">' . $modify_confirm_password_err . '</p>';

                    }
                    ?>
                </div>
            </div>
            
            <div class="row mt-5">
                <div class="col-sm-6">
                    <a role="button" class="btn btn-secondary btn-block" href="/moov/portal/staff/">Cancel</a>
                </div>
                
                <div class="col-sm-6 mt-4 mt-sm-0">
                    <button id="updateSubmitButton" name="modifyStaff" type="submit" class="btn btn-primary btn-block">
                        <span id="submitButton">Update Account</span>

                        <img id="processingIcon" src="/moov/portal/assets/images/processing_icon.svg" class="processing-icon d-none">
                        <span id="processingButton" class="d-none">Processing...</span>
                    </button>
                </div>
            </div>
		</form>
		
		<script>
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
	</div>

    <?php include '../footer.php'; ?>
</body>
	
</html>

<?php mysqli_close($conn); ?>