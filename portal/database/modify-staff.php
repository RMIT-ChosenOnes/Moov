<?php
session_start();
require_once '../config.php';
$parent_page_name = 'database';
$page_name = basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php');

$selected_staff_id = $modify_first_name = $modify_last_name = $modify_email_address = $modify_role = $modify_password = $modify_account_status = '';
$modify_first_name_err = $modify_last_name_err = $modify_email_address_err = $modify_role_err = $modify_password_err = $modify_confirm_password_err = '';

if (isset($_SESSION['moov_portal_logged_in']) && $_SESSION['moov_portal_logged_in'] == TRUE) {
	if (isset($_SESSION['moov_portal_staff_role']) && $_SESSION['moov_portal_staff_role'] == 'Admin') {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$selected_staff_id = $_POST['selectedStaffId'];

			if (empty(trim($_POST['staffFirstName']))) {
				$modify_first_name_err = 'Please enter staff\'s first name.';

			} else {
				$modify_first_name = trim($_POST['staffFirstName']);

			}

			if (empty(trim($_POST['staffLastName']))) {
				$modify_last_name_err = 'Please enter staff\'s last name.';

			} else {
				$modify_last_name = trim($_POST['staffLastName']);

			}

			if (empty(trim($_POST['staffEmailAddress']))) {
				$modify_email_address_err = 'Please enter staff\'s email address.';

			} else {
				$modify_email_address = trim($_POST['staffEmailAddress']);

			}

			if (empty(trim($_POST['staffRole'])) || trim($_POST['staffRole']) == 0) {
				$modify_role_err = 'Please assign an appropriate role.';

			} else {
				$modify_role = trim($_POST['staffRole']);

			}

			if (!empty(trim($_POST['staffUpdatePassword']))) {
				if (!preg_match('/[a-z]+/', trim($_POST['staffUpdatePassword'])) || !preg_match('/[A-Z]+/', trim($_POST['staffUpdatePassword'])) || !preg_match('/[^a-zA-Z0-9]+/', trim($_POST['staffUpdatePassword'])) || strlen(trim($_POST['staffUpdatePassword'])) < 8) {
					$modify_password_err = 'Password must contain at least one uppercase letter, one lowercase letter, one number digit, one special character, and have at least 8 characters long.';
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

			if (isset($_POST['staffDeactivate']) && $_POST['staffDeactivate'] == 'on') {
				$modify_account_status = 1;

			} else {
				$modify_account_status = 0;

			}

			if (empty($modify_first_name_err) && empty($modify_last_name_err) && empty($modify_email_address_err) && empty($modify_role_err) && empty($modify_password_err) && empty($modify_confirm_password_err)) {
				$update_staff_account_sql = 'UPDATE portal_account SET first_name="' . $modify_first_name . '", last_name="' . $modify_last_name . '", email_address="' . $modify_email_address . '", role=' . $modify_role . ', is_deactivated=' . $modify_account_status . ' WHERE account_id=' . $selected_staff_id;

				if (mysqli_query($conn, $update_staff_account_sql)) {
					if (!empty($modify_password)) {
						$update_staff_password_sql = 'UPDATE portal_account SET password="' . password_hash($modify_password, PASSWORD_DEFAULT) . '" WHERE account_id=' . $selected_staff_id;

						if (mysqli_query($conn, $update_staff_password_sql)) {
							$record_updated = TRUE;

						} else {
							echo 'Error: ' . $update_staff_password_sql . '<br/>' . mysqli_error($conn);

						}
					}

					$record_updated = TRUE;
					unset($_POST);

				} else {
					echo 'Error: ' . $update_staff_account_sql . '<br/>' . mysqli_error($conn);

				}
			} else {
				$record_updated = FALSE;

			}
		}
	} else {
		header('location: /moov/portal/');
		
	}
} else {
	header('location: /moov/portal/login?url=/moov/portal/' . $parent_page_name . '/' . $page_name);
	
}
?>

<!DOCTYPE html>
<html>
	
<head>
	<title>Staff Account Management | Moov Portal</title>
	
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

    <div class="container my-3">
		<h1 class="text-center">Staff Account Management</h1>
		
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
        ?>
		
		<div class="mt-5">
			<select id="staffAccount" class="form-control <?php echo !empty($staff_role_err) ? 'border border-danger' : ''; ?>" name="staffAccount" onChange="getAccountInfo(this.value)">
				<option value="" selected>Select Account to Modify</option>

				<?php
				$get_account_list_sql = 'SELECT account_id, first_name, last_name FROM portal_account ORDER BY first_name ASC';
				$get_account_list = mysqli_query($conn, $get_account_list_sql);

				if (mysqli_num_rows($get_account_list) > 0) {
					while ($account_list = mysqli_fetch_assoc($get_account_list)) {
						$selected_account = (isset($_POST['selectedStaffId']) && $_POST['selectedStaffId'] == $account_list['account_id']) ? ' selected="selected"' : '';

						echo '<option value="' . $account_list['account_id'] . '"' . $selected_account . '>' . $account_list['first_name'] . ', ' . strtoupper($account_list['last_name']) . '</option>';
					}

					mysqli_free_result($get_account_list);
				}
				?>
			</select>
		</div>
		
		<form class="mt-5" action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php'); ?>" method="post">
			<input type="hidden" id="selectedStaffId" name="selectedStaffId" value="<?php echo $_POST['selectedStaffId']; ?>">
			
			<div id="accountInformation" class="<?php echo $record_updated === FALSE ? 'd-block' : ''; ?>">
				<div class="form-group row">
					<label for="staffUsername" class="col-sm-3 col-form-label">Username</label>

					<div class="col-sm-9">
						<input type="text" class="form-control-plaintext" id="staffUsername" name="staffUsername" value="<?php echo $_POST['staffUsername']; ?>" readonly>
					</div>
				</div>

				<div class="form-group row mt-4">
					<label for="staffFirstName" class="col-sm-3 col-form-label">First Name</label>

					<div class="col-sm-9">
						<input type="text" class="form-control <?php echo !empty($modify_first_name_err) ? 'border border-danger' : ''; ?>" id="staffFirstName" name="staffFirstName" value="<?php echo $_POST['staffFirstName']; ?>">

						<?php
						if (isset($modify_first_name_err) && !empty($modify_first_name_err)) {
							echo '<p class="text-danger mb-0 text-left">' . $modify_first_name_err . '</p>';

						}
						?>
					</div>
				</div>

				<div class="form-group row mt-4">
					<label for="staffLastName" class="col-sm-3 col-form-label">Last Name</label>

					<div class="col-sm-9">
						<input type="text" class="form-control <?php echo !empty($modify_last_name_err) ? 'border border-danger' : ''; ?>" id="staffLastName" name="staffLastName" value="<?php echo $_POST['staffLastName']; ?>">

						<?php
						if (isset($modify_last_name_err) && !empty($modify_last_name_err)) {
							echo '<p class="text-danger mb-0 text-left">' . $modify_last_name_err . '</p>';

						}
						?>
					</div>
				</div>

				<div class="form-group row mt-4">
					<label for="staffEmailAddress" class="col-sm-3 col-form-label">Email Address</label>

					<div class="col-sm-9">
						<input type="email" class="form-control <?php echo !empty($modify_email_address_err) ? 'border border-danger' : ''; ?>" id="staffEmailAddress" name="staffEmailAddress" value="<?php echo $_POST['staffEmailAddress']; ?>">

						<?php
						if (isset($modify_email_address_err) && !empty($modify_email_address_err)) {
							echo '<p class="text-danger mb-0 text-left">' . $modify_email_address_err . '</p>';

						}
						?>
					</div>
				</div>

				<div class="form-group row mt-4">
					<label for="staffRole" class="col-sm-3 col-form-label">Role</label>

					<div class="col-sm-9">
						<select id="staffRole" class="form-control <?php echo !empty($modify_role_err) ? 'border border-danger' : ''; ?>" name="staffRole">
							<option value="" selected>Select Staff's Role</option>

							<?php
							$get_role_sql = 'SELECT role_id, role FROM portal_account_role ORDER BY role ASC';
							$get_role = mysqli_query($conn, $get_role_sql);

							if (mysqli_num_rows($get_role) > 0) {
								while ($role = mysqli_fetch_assoc($get_role)) {
									$selected_role = (isset($_POST['staffRole']) && $_POST['staffRole'] == $role['role_id']) ? ' selected="selected"' : '';

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

				<div class="form-group row">
					<label for="staffUpdatePassword" class="col-sm-3 col-form-label">Update Password</label>

					<div class="col-sm-9">
						<input type="password" class="form-control <?php echo !empty($modify_password_err) ? 'border border-danger' : ''; ?>" id="staffUpdatePassword" name="staffUpdatePassword" aria-describedby="passwordInfo" value="<?php echo $_POST['staffUpdatePassword']; ?>">
						
						<?php
						if (isset($modify_password_err) && !empty($modify_password_err)) {
							echo '<p class="text-danger mb-0">' . $modify_password_err . '</p>';

						} else {
							echo '<small id="passwordInfo" class="form-text text-muted">Password must contain at least one uppercase letter, one lowercase letter, one number digit, one special character, and have at least 8 characters long.</small>';

						}
						?>
					</div>
				</div>

				<div class="form-group row mt-4">
					<label for="staffConfirmPassword" class="col-sm-3 col-form-label">Confirm Password</label>

					<div class="col-sm-9">
						<input type="password" class="form-control <?php echo !empty($modify_confirm_password_err) ? 'border border-danger' : ''; ?>" id="staffConfirmPassword" name="staffConfirmPassword" value="<?php echo $_POST['staffConfirmPassword']; ?>">

						<?php
						if (isset($modify_confirm_password_err) && !empty($modify_confirm_password_err)) {
							echo '<p class="text-danger mb-0 text-left">' . $modify_confirm_password_err . '</p>';

						}
						?>
					</div>
				</div>

				<hr class="my-5">

				<div class="form-group form-check">
					<input type="checkbox" class="form-check-input" id="staffDeactivate" name="staffDeactivate" data-toggle="modal" data-target="#staffDeactivateConfirmation" onchange="valueCheck(this)" <?php echo (isset($_POST['staffDeactivate']) && $_POST['staffDeactivate'] == 'on') ? 'checked' : ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['staffDeactivate']) ? '' : 'checked'); ?>>

					<label class="form-check-label" for="staffDeactivate">Deactivate Staff Account</label>
					
					<div class="modal fade" id="staffDeactivateConfirmation" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staffDeactivateLabel" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered">
							<div class="modal-content">
								<div class="modal-header">
									<h2 class="modal-title text-warning text-uppercase" id="staffDeactivateLabel">Warning!</h2>
								</div>
								
								<div class="modal-body">
									You are about to <span id="staffAction"></span> <span id="staffFullName"><?php echo $_POST['staffFirstName'] . ' ' . $_POST['staffLastName']; ?></span>'s access to Moov Portal. Are you confirm?
								</div>
								
								<div class="modal-footer">
									<button type="button" class="btn btn-primary" data-dismiss="modal">Confirmed and Proceed</button>
								</div>
							</div>
						</div> 
					</div>
				</div>

				<button type="submit" class="btn btn-primary btn-block mt-5">Update Account</button>
			</div>
		</form>
		
		<script>
			document.getElementById('modifyAccount').onload = function hideAccountInfo() {
				document.getElementById('accountInformation').style.display = 'none';
			}
			
			function getAccountInfo(id) {
				var xhttpAccount, resultAccount, parsedAccount, accountInfo;

				xhttpAccount = new XMLHttpRequest();

				xhttpAccount.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						resultAccount = this.responseText;
						parsedAccount = JSON.parse(resultAccount);
						accountInfo = parsedAccount[0];

						document.getElementById('accountInformation').style.display = 'block';

						document.getElementById('staffUsername').value = accountInfo.saved_username;
						document.getElementById('staffFirstName').value = accountInfo.saved_first_name;
						document.getElementById('staffLastName').value = accountInfo.saved_last_name;
						document.getElementById('staffEmailAddress').value = accountInfo.saved_email_address;
						document.getElementById('staffRole').value = accountInfo.saved_role;
						document.getElementById('staffFullName').innerHTML = accountInfo.saved_first_name + ' ' + accountInfo.saved_last_name;
						
						if (accountInfo.saved_account_status == 0) {
							document.getElementById('staffDeactivate').checked = false;
							
						} else if (accountInfo.saved_account_status == 1) {
							document.getElementById('staffDeactivate').checked = true;
							
						}
					}
				};

				xhttpAccount.open('GET', '/moov/portal/database/get-account?id=' + id, true);
				xhttpAccount.send();

				document.getElementById('selectedStaffId').value = id;
			}
			
			function valueCheck(check) {
				if (check.checked == true) {
					document.getElementById('staffAction').innerHTML = 'deactivate';
					
				} else if (check.checked == false) {
					document.getElementById('staffAction').innerHTML = 'activate';
					
				}
			}
		</script>
	</div>

    <?php include '../footer.php'; ?>
</body>
	
</html>

<?php mysqli_close($conn); ?>