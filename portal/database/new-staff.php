<?php
session_start();
require_once '../config.php';
$parent_page_name = 'database';
$page_name = 'new staff';

$staff_role = $staff_first_name = $staff_last_name = $staff_username = $staff_email_address = $staff_password = '';
$staff_role_err = $staff_first_name_err = $staff_last_name_err = $staff_username_err = $staff_email_address_err = $staff_password_err = $staff_confirm_password_err = '';

if (!isset($_SESSION['moov_portal_logged_in'])) {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (empty(trim($_POST['staffRole'])) || trim($_POST['staffRole']) == 0) {
			$staff_role_err = 'Please assign an appropriate role.';
			
		} else {
			$staff_role = trim($_POST['staffRole']);
			
		}
		
		if (empty(trim($_POST['staffFirstName']))) {
			$staff_first_name_err = 'Please enter staff\'s first name.';
			
		} else {
			$staff_first_name = trim($_POST['staffFirstName']);
			
		}
		
		if (empty(trim($_POST['staffLastName']))) {
			$staff_last_name_err = 'Please enter staff\'s last name.';
			
		} else {
			$staff_last_name = trim($_POST['staffLastName']);
			
		}
		
		if (empty(trim($_POST['staffUsername']))) {
			$staff_username_err = 'Please enter an username.';
			
		} else {
			$check_username_duplication_sql = 'SELECT account_id FROM portal_account WHERE username = "' . trim($_POST['staffUsername']) . '"';
			$check_username_duplication = mysqli_query($conn, $check_username_duplication_sql);
			
			if (mysqli_num_rows($check_username_duplication) > 0) {
				$staff_username_err = 'Username has already been taken. Please try another username.';
						
			} else {
				$staff_username = trim($_POST['staffUsername']);
				
			}
		}
		
		if (empty(trim($_POST['staffEmailAddress']))) {
			$staff_email_address_err = 'Please enter staff\'s email address.';
			
		} else {
			$check_email_duplication_sql = 'SELECT account_id FROM portal_account WHERE email_address = "' . trim($_POST[staffEmailAddress]) . '"';
			$check_email_duplication = mysqli_query($conn, $check_email_duplication_sql);
			
			if (mysqli_num_rows($check_email_duplication) > 0) {
				$staff_email_address_err = 'Email address has already been taken. Please try another email address.';
				
			} else {
				$staff_email_address = trim($_POST['staffEmailAddress']);
				
			}
		}
		
		if (empty(trim($_POST['staffPassword']))) {
			$staff_password_err = 'Please enter a valid password.';
			
		}
		
		if (empty(trim($_POST['staffConfirmPassword']))) {
			$staff_confirm_password_err = 'Please confirm the password again.';
			
		} else {
			if (trim($_POST['staffPassword']) == trim($_POST['staffConfirmPassword'])) {
				$staff_password = trim($_POST['staffPassword']);
				
			} else {
				$staff_password_err = $staff_confirm_password_err = 'Password does not matched. Please try again.';
				
			}
		}
		
		if (empty($staff_role_err) && empty($staff_first_name_err) && empty($staff_last_name_err) && empty($staff_username_err) && empty($staff_email_address_err) && empty($staff_password_err) && empty($staff_confirm_password_err)) {
			$register_staff_sql = 'INSERT INTO portal_account (first_name, last_name, username, email_address, password, role) VALUES ("' . $staff_first_name . '", "' . $staff_last_name . '", "' . $staff_username . '", "' . $staff_email_address . '", "' . password_hash($staff_password, PASSWORD_DEFAULT) . '", ' . $staff_role .')';
			
			if ($register_stmt = mysqli_prepare($conn, $register_staff_sql)) {
				mysqli_stmt_bind_param($register_stmt, 'sssssi', $param_first_name, $param_last_name, $param_username, $param_email_address, $param_password, $param_role);
				
				$param_first_name = $staff_first_name;
				$param_last_name = $staff_last_name;
				$param_username = $staff_username;
				$param_email_address = $staff_email_address;
				$param_password = $staff_password;
				$param_role = $staff_role;
				
				if (mysqli_stmt_execute($register_stmt)) {
					if (isset($_POST['staffNotify']) && $_POST['staffNotify'] == 'on') {
						$mail_email = $staff_email_address;
						$mail_name = $staff_first_name;
						$mail_subject = 'Your Account Access to Moov Portal';
						$mail_body = 'Dear ' . $staff_first_name . ',<br/><br/>Please find below your account access to Moov Portal.<br/><br/>Portal: <a href="http://kftech.ddns.net/moov/portal/" target="_blank">http://kftech.ddns.net/moov/portal/</a><br/>Username: ' . $staff_username . '<br/>Password: ' . $staff_password . '<br/><br/>Please do not share your password with anyone else.<br/><br/>Thank you.<br/><br/>Kind Regards,<br/>Moov';

						require_once '../mail.php';
					}
					
					$registered = TRUE;
					$checked_notify = TRUE;
					unset($_POST);
					
				} else {
					echo 'Error: ' . $register_staff_sql . '<br/>' . mysqli_error($conn);
				}
			}
		}
	}
}
?>

<!DOCTYPE html>
<html>
	
<head>
	<title>Register New Staff | Moov Portal</title>
	
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

<body>
	<?php include '../header.php'; ?>

    <div class="container my-3">		
		<h1 class="text-center">Register New Staff</h1>
		
		<?php
        if ($registered === TRUE) {
            echo '
            <div class="alert alert-success my-4 alert-dismissible fade show" role="alert">
                Account registered successfully.

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ';
        }
        ?>
		
		<form class="mt-5" action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php'); ?>" method="post">
			<div class="form-group row">
				<label for="staffRole" class="col-sm-2 col-form-label">Role</label>
				
				<div class="col-sm-10">
					<select id="staffRole" class="form-control <?php echo !empty($staff_role_err) ? 'border border-danger' : ''; ?>" name="staffRole">
						<option value="0" selected>Select Staff's Role</option>
						
						<?php
						$get_role_sql = 'SELECT * FROM portal_account_role ORDER BY role ASC';
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
					if (isset($staff_role_err) && !empty($staff_role_err)) {
						echo '<p class="text-danger mb-0">' . $staff_role_err . '</p>';
						
					}
					?>
				</div>
			</div>
			
			<div class="row mt-4">
				<div class="form-group col-md-6">
					<label for="staffFirstName">First Name</label>
					
					<input type="text" class="form-control <?php echo !empty($staff_first_name_err) ? 'border border-danger' : ''; ?>" id="staffFirstName" name="staffFirstName" value="<?php echo $_POST['staffFirstName']; ?>">
					
					<?php
					if (isset($staff_first_name_err) && !empty($staff_first_name_err)) {
						echo '<p class="text-danger mb-0">' . $staff_first_name_err . '</p>';
						
					}
					?>
				</div>
				
				<div class="form-group col-md-6">
					<label for="staffLastName">Last Name</label>
					
					<input type="text" class="form-control <?php echo !empty($staff_last_name_err) ? 'border border-danger' : ''; ?>" id="staffLastName" name="staffLastName" value="<?php echo $_POST['staffLastName']; ?>">
					
					<?php
					if (isset($staff_last_name_err) && !empty($staff_last_name_err)) {
						echo '<p class="text-danger mb-0">' . $staff_last_name_err . '</p>';
						
					}
					?>
				</div>
			</div>
			
			<div class="row mt-2">
				<div class="form-group col-md-6">
					<label for="staffUsername">Username</label>
					
					<input type="text" class="form-control <?php echo !empty($staff_username_err) ? 'border border-danger' : ''; ?>" id="staffUsername" name="staffUsername" aria-describedby="usernameInfo" value="<?php echo $_POST['staffUsername']; ?>">
					
					<?php
					if (isset($staff_username_err) && !empty($staff_username_err)) {
						echo '<p class="text-danger mb-0">' . $staff_username_err . '</p>';
						
					} else {
						echo '<small id="usernameInfo" class="form-text text-muted font-italic">Format: firstname.lastname. Username can\'t be changed after registration.</small>';
						
					}
					?>
				</div>
				
				<div class="form-group col-md-6">
					<label for="staffEmailAddress">Email Address</label>
					
					<input type="email" class="form-control <?php echo !empty($staff_email_address_err) ? 'border border-danger' : ''; ?>" id="staffEmailAddress" name="staffEmailAddress" value="<?php echo $_POST['staffEmailAddress']; ?>">
					
					<?php
					if (isset($staff_email_address_err) && !empty($staff_email_address_err)) {
						echo '<p class="text-danger mb-0">' . $staff_email_address_err . '</p>';
						
					}
					?>
				</div>
			</div>
			
			<div class="row mt-2">
				<div class="form-group col-md-6">
					<label for="staffPassword">Password</label>
					
					<input type="password" class="form-control <?php echo !empty($staff_password_err) ? 'border border-danger' : ''; ?>" id="staffPassword" name="staffPassword" value="<?php echo $_POST['staffPassword']; ?>">
					
					<?php
					if (isset($staff_password_err) && !empty($staff_password_err)) {
						echo '<p class="text-danger mb-0">' . $staff_password_err . '</p>';
						
					}
					?>
				</div>
				
				<div class="form-group col-md-6">
					<label for="staffConfirmPassword">Confirm Password</label>
					
					<input type="password" class="form-control <?php echo !empty($staff_confirm_password_err) ? 'border border-danger' : ''; ?>" id="staffConfirmPassword" name="staffConfirmPassword" value="<?php echo $_POST['staffConfirmPassword']; ?>">
					
					<?php
					if (isset($staff_confirm_password_err) && !empty($staff_confirm_password_err)) {
						echo '<p class="text-danger mb-0">' . $staff_confirm_password_err . '</p>';
						
					}
					?>
				</div>
			</div>
			
			<div class="form-group form-check mt-2">
				<input type="checkbox" class="form-check-input" id="staffNotify" name="staffNotify" <?php echo (isset($_POST['staffNotify']) && $_POST['staffNotify'] == 'on') ? 'checked' : $checked_notify == TRUE ? 'checked' : ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['staffNotify']) ? '' : 'checked'); ?>>
				
				<label class="form-check-label" for="staffNotify">Notify staff via email</label>
			</div>
			
			<button type="submit" class="btn btn-primary btn-block mt-5">Register</button>
		</form>
	</div>

    <?php include '../footer.php'; ?>
</body>
	
</html>

<?php mysqli_close($conn); ?>