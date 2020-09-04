<?php
session_start();
require_once 'config.php';
$page_name = 'register';

$referrer_temp_account_id = $dl_date_of_birth = $dl_contact_number = $dl_first_name = $dl_last_name = $dl_license_number = $dl_date_of_expiry = $dl_country_of_issue = $dl_state_of_issue = '';
$dl_date_of_birth_err = $dl_contact_number_err = $dl_first_name_err = $dl_last_name_err = $dl_license_number_err = $dl_date_of_expiry_err = $dl_country_of_issue_err = $dl_state_of_issue_err = '';

$today_date = date('Y-m-d');
$accepted_date_of_expiry = date('Y-m-d', strtotime($today_date . '+7 days'));
$search_date_symbol = array('/', '.');
$replace_date_symbol = array('-', '-');
$search_contact_number_symbol = array('-', ' ');
$replace_contact_number_symbol = array('', '');

if (isset($_SESSION['moov_user_logged_in']) && $_SESSION['moov_user_logged_in'] == TRUE) {
	header('location: /moov/');
	
}

if (isset($_SESSION['moov_user_temp_account_id']) && !empty($_SESSION['moov_user_temp_account_id'])) {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$referrer_temp_account_id = $_SESSION['moov_user_temp_account_id'];
		
		if (empty(trim($_POST['dlDateOfBirth']))) {
			$dl_date_of_birth_err = 'Please enter your date of birth.';

		} else {
			if ((preg_match('/[^0-9\.\-\/]/', trim($_POST['dlDateOfBirth']))) || strlen(trim($_POST['dlDateOfBirth'])) < 8) {
				$dl_date_of_birth_err = 'Please enter a valid date of birth.';

			} else {
				$temp_date_of_birth = trim($_POST['dlDateOfBirth']);

				$replace_temp_dob = date('Y-m-d', strtotime(str_replace($search_date_symbol, $replace_date_symbol, $temp_date_of_birth)));

				$current_age = floor((strtotime($today_date) - strtotime($replace_temp_dob)) / 31556926);

				if ($current_age >= 17) {
					$dl_date_of_birth = $replace_temp_dob;

				} else {
					$dl_date_of_birth_err = 'Oops. You aren\'t old enough to drive in Australia. Please come back and try again when you are 17 years old.';

				}
			}
		}
		
		if (empty(trim($_POST['dlContactNumber']))) {
			$dl_contact_number_err = 'Please enter your contact number.';
			
		} else {
			$temp_contact_number = trim($_POST['dlContactNumber']);
				
			$replace_temp_cn = str_replace($search_contact_number_symbol, $replace_contact_number_symbol, $temp_contact_number);
            
            if (substr($replace_temp_cn, 0, 1) == 0) {
                $replace_temp_cn = substr($replace_temp_cn, 1);
            }
            
			if (preg_match('/^(0)?(4){1}[0-9]{8}$/', $replace_temp_cn)) {
				$dl_contact_number = $replace_temp_cn;

			} else {
				$dl_contact_number_err = 'Please enter a valid Australian contact number.';

			}
		}

		if (empty(trim($_POST['dlFirstName']))) {
			$dl_first_name_err = 'Please enter your first name. First Name must match on driving license.';

		} else {
			if (preg_match('/^[a-zA-zw\-\s]+$/', trim($_POST['dlFirstName']))) {
				$dl_first_name = trim($_POST['dlFirstName']);

			} else {
				$dl_first_name_err = 'Please enter a valid first name.';

			}
		}

		if (empty(trim($_POST['dlLastName']))) {
			$dl_last_name_err = 'Please enter your last name. Last Name must match on driving license.';

		} else {
			if (preg_match('/^[a-zA-zw\-\s]+$/', trim($_POST['dlLastName']))) {
				$dl_last_name = trim($_POST['dlLastName']);

			} else {
				$dl_last_name_err = 'Please enter a valid last name.';

			}
		}

		if (empty(trim($_POST['dlDateOfExpiry']))) {
			$dl_date_of_expiry_err = 'Please enter the date of expiry on your driving license.';

		} else {
			if ((preg_match('/[^0-9\.\-\/]/', trim($_POST['dlDateOfExpiry']))) || strlen(trim($_POST['dlDateOfExpiry'])) < 8) {
				$dl_date_of_expiry_err = 'Please enter a valid date of expiry.';

			} else {
				$temp_date_of_expiry = trim($_POST['dlDateOfExpiry']);

				$replace_temp_doe = date('Y-m-d', strtotime(str_replace($search_date_symbol, $replace_date_symbol, $temp_date_of_expiry)));

				if ($replace_temp_doe >= $accepted_date_of_expiry) {
					$dl_date_of_expiry = $replace_temp_doe;

				} else {
					$dl_date_of_expiry_err = 'Your current driving license is expiring soon or has already expired. Unfortunately, we are not able to continue to register with your current driving license. Please try again with a new driving license.';

				}
			}
		}

		if (empty(trim($_POST['dlCountryOfIssue'])) || trim($_POST['dlCountryOfIssue']) == '') {
			$dl_country_of_issue_err = 'Please select the country of issue.';

		} else {
			$dl_country_of_issue = trim($_POST['dlCountryOfIssue']);
			
			if ($dl_country_of_issue == 9) {
				if (empty(trim($_POST['dlStateOfIssue'])) || trim($_POST['dlStateOfIssue']) == '') {
					$dl_state_of_issue_err = 'Please select the state of issue.';
					
				} else {
					$dl_state_of_issue = trim($_POST['dlStateOfIssue']);
					
				}
			} else {
				$dl_state_of_issue = NULL;
                
			}
		}
		
		if (empty(trim($_POST['dlLicenseNumber']))) {
			$dl_license_number_err = 'Please enter your driving license number.';

		} else {
			if ($dl_country_of_issue == 9) {
				if ($dl_state_of_issue == 'act') {
					if (preg_match('/^[0-9]{10}$/', trim($_POST['dlLicenseNumber']))) {
						$dl_license_number = trim($_POST['dlLicenseNumber']);

					} else {
						$dl_license_number_err = 'Please enter a valid Australian Capital Territory driving license number.';

					}
				} elseif ($dl_state_of_issue == 'nsw') {
					if (preg_match('/^[0-9]{10}$/', trim($_POST['dlLicenseNumber']))) {
						$dl_license_number = trim($_POST['dlLicenseNumber']);

					} else {
						$dl_license_number_err = 'Please enter a valid New South Wales driving license number.';

					}
				} elseif ($dl_state_of_issue == 'nt') {
					if (preg_match('/^[0-9]{7}$/', trim($_POST['dlLicenseNumber']))) {
						$dl_license_number = trim($_POST['dlLicenseNumber']);

					} else {
						$dl_license_number_err = 'Please enter a valid Northern Territory driving license number.';

					}
				} elseif ($dl_state_of_issue == 'qld') {
					if (preg_match('/^[0-9]{7,9}$/', trim($_POST['dlLicenseNumber']))) {
						$dl_license_number = trim($_POST['dlLicenseNumber']);

					} else {
						$dl_license_number_err = 'Please enter a valid Queensland driving license number.';

					}
				} elseif ($dl_state_of_issue == 'sa') {
					if (preg_match('/^[A-Z]{1}[0-9]{5}$/', strtoupper(trim($_POST['dlLicenseNumber'])))) {
						$dl_license_number = strtoupper(trim($_POST['dlLicenseNumber']));

					} else {
						$dl_license_number_err = 'Please enter a valid South Australia driving license number.';

					}
				} elseif ($dl_state_of_issue == 'tas') {
					if (preg_match('/^[0-9]{6,7}$/', trim($_POST['dlLicenseNumber']))) {
						$dl_license_number = trim($_POST['dlLicenseNumber']);

					} else {
						$dl_license_number_err = 'Please enter a valid Tasmania driving license number.';

					}
				} elseif ($dl_state_of_issue == 'vic') {
					if (preg_match('/^[0-9]{8,9}$/', trim($_POST['dlLicenseNumber']))) {
						$dl_license_number = trim($_POST['dlLicenseNumber']);

					} else {
						$dl_license_number_err = 'Please enter a valid Victoria driving license number.';

					}
				} elseif ($dl_state_of_issue == 'wa') {
					if (preg_match('/^[0-9]{9}$/', trim($_POST['dlLicenseNumber']))) {
						$dl_license_number = trim($_POST['dlLicenseNumber']);

					} else {
						$dl_license_number_err = 'Please enter a valid Western Australia driving license number.';

					}
				} else {
					$dl_license_number_err = 'Please enter a valid driving license number.';
					
				}
			} else {
				if (preg_match('/^[0-9A-Z\-\s\/]{5,20}$/', strtoupper(trim($_POST['dlLicenseNumber'])))) {
					$dl_license_number = strtoupper(trim($_POST['dlLicenseNumber']));

				} else {
					$dl_license_number_err = 'Please enter a valid driving license number.';

				}
			}
		}

		if (empty($dl_date_of_birth_err) && empty($dl_contact_number_err) && empty($dl_first_name_err) && empty($dl_last_name_err) && empty($dl_license_number_err) && empty($dl_date_of_expiry_err) && empty($dl_country_of_issue_err) && empty($dl_state_of_issue_err)) {
			$get_temp_account_sql = 'SELECT display_name, email_address, password FROM account_temp WHERE account_temp_id = ?';

			if ($get_temp_account_stmt = mysqli_prepare($conn, $get_temp_account_sql)) {
				mysqli_stmt_bind_param($get_temp_account_stmt, 'i', $param_temp_account_id);

				$param_temp_account_id = $referrer_temp_account_id;

				if (mysqli_stmt_execute($get_temp_account_stmt)) {
					mysqli_stmt_store_result($get_temp_account_stmt);

					if (mysqli_stmt_num_rows($get_temp_account_stmt) == 1) {
						mysqli_stmt_bind_result($get_temp_account_stmt, $temp_display_name, $temp_email_address, $temp_password);

						if (mysqli_stmt_fetch($get_temp_account_stmt)) {
							$register_account_sql = 'INSERT INTO account (display_name, email_address, password, contact_number, date_of_birth) VALUES (?, ?, ?, ?, ?)';

							if ($register_account_stmt = mysqli_prepare($conn, $register_account_sql)) {
								mysqli_stmt_bind_param($register_account_stmt, 'sssss', $param_display_name, $param_email_address, $param_password, $param_contact_number, $param_date_of_birth);
								
								$param_display_name = $temp_display_name;
								$param_email_address = $temp_email_address;
								$param_password = $temp_password;
								$param_contact_number = $dl_contact_number;
								$param_date_of_birth = $dl_date_of_birth;
								
								if (mysqli_stmt_execute($register_account_stmt)) {
									$account_id = mysqli_insert_id($conn);

									$register_driving_license_sql = 'INSERT INTO driving_license (account_id, first_name, last_name, license_number, date_of_expiry, country_of_issue, state_of_issue) VALUES (?, ?, ?, ?, ?, ?, ?)';

									if ($register_driving_license_stmt = mysqli_prepare($conn, $register_driving_license_sql)) {
										mysqli_stmt_bind_param($register_driving_license_stmt, 'issssis', $param_account_id, $param_first_name, $param_last_name, $param_license_number, $param_date_of_expiry, $param_country_of_issue, $param_state_of_issue);

										$param_account_id = $account_id;
										$param_first_name = $dl_first_name;
										$param_last_name = $dl_last_name;
										$param_license_number = $dl_license_number;
										$param_date_of_expiry = $dl_date_of_expiry;
										$param_country_of_issue = $dl_country_of_issue;
										$param_state_of_issue = $dl_state_of_issue;

										if (mysqli_stmt_execute($register_driving_license_stmt)) {
											$delete_temp_record_sql = 'DELETE FROM account_temp WHERE account_temp_id = ' . $referrer_temp_account_id;

											if (mysqli_query($conn, $delete_temp_record_sql)) {
												$mail_email = $temp_email_address;
												$mail_name = $temp_display_name;
												$mail_subject = 'Welcome to Moov!';
												$mail_body = '<h1>Welcome to Moov!</h1><h3>Hi ' . $temp_display_name . ',</h3><p class="my-4">Congratulations, you have successfully created an account with Moov!</p><p class="my-4"><a href="http://kftech.ddns.net/moov/login">Log in</a> today to see what vehicles are available near you!</p><a class="btn btn-primary btn-block my-4" href="http://kftech.ddns.net/moov/login" role="button">Login Now!</a><p class="text-left my-4">Kind Regards,<br/>Moov Admin</p>';

												require_once 'mail/mail-customer.php';
												
												unset($_SESSION['moov_user_temp_account_id']);
												unset($_SESSION['moov_user_temp_account_display_name']);
												unset($_POST);

												$_SESSION['moov_user_registration_success'] = TRUE;
												header('location: /moov/login');

											}  else {
												$register_error = TRUE;
												$error_message = mysqli_error($conn);

											}
										} else {
											$register_error = TRUE;
											$error_message = mysqli_error($conn);

										}
									}
									
									mysqli_stmt_close($register_driving_license_stmt);
									
								} else {
									$register_error = TRUE;
									$error_message = mysqli_error($conn);

								}
							}

							mysqli_stmt_close($register_account_stmt);
							
						}
					} else {
						$register_error = TRUE;
						$error_message = mysqli_error($conn);

					}
				}
			}

			mysqli_stmt_close($get_temp_account_stmt);

		}
	}
} else {
	header('location: /moov/register');
	
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Register | Moov</title>

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
	<link rel="icon" type="image/png" sizes="16x16" href="/moov/assets/favicon/favicon-16x16.png">
</head>

<body>
    <?php include 'header.php'; ?>

   	<div class="container my-3">
		<h1 class="text-center">Driver Profile</h1>
		
		<?php
		if ($register_error === TRUE) {
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
        ?>
		
		<p class="mt-4">
			Dear <?php echo $_SESSION['moov_user_temp_account_display_name']; ?>, thank you for registering with Moov. Before we can proceed with your account registration, we were hoping you could prove that you are legal to drive in Australia. You are required to fill in below fields with your current driving license.
		</p>
		
		<div class="container bg-secondary pt-4 pb-2 rounded">
			<form action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php'); ?>" method="post" onSubmit="submitButton()">
				<h4>Driver Details</h4>
				
				<div class="form-group row align-items-center">
					<label for="dlDateOfBirth" class="col-sm-3 col-form-label">Date of Birth</label>
					
					<div class="col-sm-9">
						<input type="date" class="form-control <?php echo !empty($dl_date_of_birth_err) ? 'border border-danger' : ''; ?>" id="dlDateOfBirth" name="dlDateOfBirth" placeholder="dd / mm / yyyy" value="<?php echo $_POST['dlDateOfBirth']; ?>" onKeyUp="changeEventButton(this)">
						
						<?php
						if (isset($dl_date_of_birth_err) && !empty($dl_date_of_birth_err)) {
							echo '<p class="text-danger mb-0">' . $dl_date_of_birth_err . '</p>';

						}
						?>
					</div>
				</div>
				
				<div class="form-group row mt-4 align-items-center">
					<label for="dlContactNumber" class="col-sm-3 col-form-label">Contact Number</label>
					
					<div class="col-sm-9 input-group">
						<div class="input-group-prepend">
							<span class="input-group-text" id="country-code">+61</span>
						</div>
						
						<input type="tel" class="form-control <?php echo !empty($dl_contact_number_err) ? 'border border-danger' : ''; ?>" id="dlContactNumber" name="dlContactNumber" aria-describedby="country-code" value="<?php echo $_POST['dlContactNumber']; ?>" onKeyUp="changeEventButton(this)">
						
						<?php
						if (isset($dl_contact_number_err) && !empty($dl_contact_number_err)) {
							echo '<div class="col-12 p-0"><p class="text-danger mb-0">' . $dl_contact_number_err . '</p></div>';

						}
						?>
					</div>
				</div>
				
				<hr class="my-4">
				
				<h4>License Details</h4>
				
				<div class="form-group row align-items-center">
					<label for="dlFirstName" class="col-sm-3 col-form-label">First Name</label>
					
					<div class="col-sm-9">
						<input type="text" class="form-control <?php echo !empty($dl_first_name_err) ? 'border border-danger' : ''; ?>" id="dlFirstName" name="dlFirstName" aria-describedby="firstNameInfo" value="<?php echo !empty($_POST['dlFirstName']) ? $_POST['dlFirstName'] : $_SESSION['moov_user_temp_account_first_name']; ?>" onKeyUp="changeEventButton(this)">
						
						<?php
						if (isset($dl_first_name_err) && !empty($dl_first_name_err)) {
							echo '<p class="text-danger mb-0">' . $dl_first_name_err . '</p>';

						} else {
							echo '<small id="firstNameInfo" class="form-text text-muted">First Name must match on driving license.</small>';

						}
						?>
					</div>
				</div>
				
				<div class="form-group row mt-4 align-items-center">
					<label for="dlLastName" class="col-sm-3 col-form-label">Last Name</label>
					
					<div class="col-sm-9">
						<input type="text" class="form-control <?php echo !empty($dl_last_name_err) ? 'border border-danger' : ''; ?>" id="dlLastName" name="dlLastName" aria-describedby="lastNameInfo" value="<?php echo !empty($_POST['dlLastName']) ? $_POST['dlLastName'] : $_SESSION['moov_user_temp_account_last_name']; ?>" onKeyUp="changeEventButton(this)">
						
						<?php
						if (isset($dl_last_name_err) && !empty($dl_last_name_err)) {
							echo '<p class="text-danger mb-0">' . $dl_last_name_err . '</p>';

						} else {
							echo '<small id="lastNameInfo" class="form-text text-muted">Last Name must match on driving license.</small>';

						}
						?>
					</div>
				</div>
				
				<div class="form-group row mt-4 align-items-center">
					<label for="dlLicenseNumber" class="col-sm-3 col-form-label">License Number</label>
					
					<div class="col-sm-9">
						<input type="text" class="form-control <?php echo !empty($dl_license_number_err) ? 'border border-danger' : ''; ?>" id="dlLicenseNumber" name="dlLicenseNumber" value="<?php echo $_POST['dlLicenseNumber']; ?>" onKeyUp="changeEventButton(this)">
						
						<?php
						if (isset($dl_license_number_err) && !empty($dl_license_number_err)) {
							echo '<p class="text-danger mb-0">' . $dl_license_number_err . '</p>';

						}
						?>
					</div>
				</div>
				
				<div class="form-group row mt-4 align-items-center">
					<label for="dlDateOfExpiry" class="col-sm-3 col-form-label">Date of Expiry</label>
					
					<div class="col-sm-9">
						<input type="date" class="form-control <?php echo !empty($dl_date_of_expiry_err) ? 'border border-danger' : ''; ?>" id="dlDateOfExpiry" placeholder="dd / mm / yyyy" name="dlDateOfExpiry" min="<?php echo date('Y-m-d', strtotime(date('Y-m-d') . '+7 days')); ?>" value="<?php echo $_POST['dlDateOfExpiry']; ?>" onKeyUp="changeEventButton(this)">
						
						<?php
						if (isset($dl_date_of_expiry_err) && !empty($dl_date_of_expiry_err)) {
							echo '<p class="text-danger mb-0">' . $dl_date_of_expiry_err . '</p>';

						}
						?>
					</div>
				</div>
				
				<div class="form-group row mt-4 align-items-center">
					<label for="dlCountryOfIssue" class="col-sm-3 col-form-label">Country of Issue</label>
					
					<div class="col-sm-9">
						<select id="dlCountryOfIssue" class="form-control <?php echo !empty($dl_country_of_issue_err) ? 'border border-danger' : ''; ?>" name="dlCountryOfIssue" onChange="selectedCountry(this.value)" onKeyUp="changeEventButton(this)">
							<option value="" selected>Select Country of Issue</option>
						
							<?php
							$get_country_list_sql = 'SELECT * FROM country ORDER BY country ASC';
							$get_country_list = mysqli_query($conn, $get_country_list_sql);

							if (mysqli_num_rows($get_country_list) > 0) {
								while ($country_list = mysqli_fetch_assoc($get_country_list)) {
									$selected_country = (isset($_POST['dlCountryOfIssue']) && $_POST['dlCountryOfIssue'] == $country_list['country_id']) ? ' selected="selected"' : '';

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
				</div>
				
				<div id="stateOfIssue" class="form-group row mt-4 align-items-center <?php echo $_POST['dlCountryOfIssue'] == 9 ? 'd-flex' : 'd-none'; echo !empty($dl_state_of_issue_err) ? 'border border-danger' : ''; ?>">
					<label for="dlStateOfIssue" class="col-sm-3 col-form-label">State of Issue</label>
					
					<div class="col-sm-9">
						<select id="dlStateOfIssue" class="form-control <?php echo !empty($dl_state_of_issue_err) ? 'border border-danger' : ''; ?>" name="dlStateOfIssue" onKeyUp="changeEventButton(this)">
							<option value="" selected>Select State of Issue</option>
							
							<?php
							$get_australia_state_list_sql = 'SELECT * FROM australia_state ORDER BY state ASC';
							$get_australia_state_list = mysqli_query($conn, $get_australia_state_list_sql);

							if (mysqli_num_rows($get_australia_state_list) > 0) {
								while ($australia_state_list = mysqli_fetch_assoc($get_australia_state_list)) {
									$selected_australia_state= (isset($_POST['dlStateOfIssue']) && $_POST['dlStateOfIssue'] == $australia_state_list['state_id']) ? ' selected="selected"' : '';

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
				
				<button id="registerSubmitButton" type="submit" class="btn btn-secondary btn-block mt-5">
					<span id="submitButton">Register</span>
					
					<img id="processingIcon" src="/moov/assets/images/processing_icon.svg" class="processing-icon d-none">
					<span id="processingButton" class="d-none">Processing...</span>
				</button>
			</form>
			
			<script>
				function selectedCountry(country) {
					if (country == 9) {
						document.getElementById('stateOfIssue').classList.remove('d-none');

					} else {
						document.getElementById('stateOfIssue').classList.add('d-none');
						document.getElementById('stateOfIssue').classList.remove('d-flex');

					}
				}
				
				function submitButton() {
					document.getElementById('registerSubmitButton').disabled = true;
					document.getElementById('submitButton').classList.add('d-none');
					document.getElementById('processingIcon').classList.add('d-inline-block');
					document.getElementById('processingIcon').classList.remove('d-none');
					document.getElementById('processingButton').classList.remove('d-none');

				}

				function changeEventButton(event) {
					if (event.keyCode == 13) {
						event.preventDefault;

						document.getElementById('registerSubmitButton').disabled = true;
						document.getElementById('submitButton').classList.add('d-none');
						document.getElementById('processingIcon').classList.add('d-inline-block');
						document.getElementById('processingIcon').classList.remove('d-none');
						document.getElementById('processingButton').classList.remove('d-none');

					}
				}
			</script>
		</div>
	</div>

    <?php include 'footer.php'; ?>
</body>

</html>

<?php mysqli_close($conn); ?>