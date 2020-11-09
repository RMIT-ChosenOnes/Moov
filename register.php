<?php
session_start();
require_once 'config.php';
$page_name = basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php');

$user_display_name = $user_email_address = $user_password = '';
$user_display_name_err = $user_email_address_err = $user_password_err = $user_confirm_password_err = '';

if (isset($_SESSION['moov_user_temp_register']) && $_SESSION['moov_user_temp_register'] == TRUE) {
	header('location: /moov/register-driver-profile');
	
} elseif (isset($_SESSION['moov_user_logged_in']) && $_SESSION['moov_user_logged_in'] == TRUE) {
	header('location: /moov/');
	
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
		$check_email_address_duplication_sql = 'SELECT account_id FROM account WHERE email_address = "' . trim($_POST['userEmailAddress']) . '" AND is_deleted = 0';
		$check_email_address_duplication = mysqli_query($conn, $check_email_address_duplication_sql);

		if (mysqli_num_rows($check_email_address_duplication) > 0) {
			$user_email_address_err = 'Email address is already in use. Please try another email address.';

		} else {
			$user_email_address = trim($_POST['userEmailAddress']);

		}
	}

	if (empty(trim($_POST['userPassword']))) {
		$user_password_err = 'Please enter a valid password.';

	} elseif (!preg_match('/[a-z]+/', trim($_POST['userPassword'])) || !preg_match('/[A-Z]+/', trim($_POST['userPassword'])) || !preg_match('/[^a-zA-Z0-9]+/', trim($_POST['userPassword'])) || strlen(trim($_POST['userPassword'])) < 8) {
		$user_password_err = 'Your password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number digit, 1 special character, and have at least 8 characters long.';

	}

	if (empty(trim($_POST['userConfirmPassword']))) {
		$user_confirm_password_err = 'Please confirm your password again.';

	} else {
		if (trim($_POST['userPassword']) == trim($_POST['userConfirmPassword'])) {
			$user_password = trim($_POST['userPassword']);

		} else {
			$user_password_err = $user_confirm_password_err = 'Password does not matched. Please try again.';

		}

	}

	if (empty($user_display_name_err) && empty($user_email_address_err) && empty($user_password_err) && empty($user_confirm_passowrd_err)) {
        $_SESSION['moov_user_temp_register'] = TRUE;
        $_SESSION['moov_user_temp_account_display_name'] = $user_display_name;
        $_SESSION['moov_user_temp_account_email_address'] = $user_email_address;
        $_SESSION['moov_user_temp_account_password'] = password_hash($user_password, PASSWORD_DEFAULT);
        
        unset($_POST);
        header('location: /moov/register-driver-profile');
        
	}
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

	<div class="container my-3 footer-align-bottom">
		<h1 class="text-center">Registration</h1>
		
		<?php
		if ($register_error === TRUE) {
            echo '
            <div class="alert alert-warning my-4 alert-dismissible fade show" role="alert">
                Oops! There is an error occured. Please try again later. If you continue to see this error, please contact us immediately.
				
			' . (!empty($error_message) ? '<br/><br/><b>Error:</b> ' . $error_message : '') . '

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ';
        }
        ?>
		
		<div class="container bg-secondary pt-4 pb-2 rounded">
			<form action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php'); ?>" method="post">
				<div class="form-group row align-items-center">
					<label for="userDisplayName" class="col-sm-3 col-form-label">Display Name</label>
					
					<div class="col-sm-9">
						<input type="text" class="form-control <?php echo !empty($user_display_name_err) ? 'border border-danger' : ''; ?>" id="userDisplayName" name="userDisplayName" placeholder="i.e. your first name" value="<?php echo $_POST['userDisplayName']; ?>">
						
						<?php
						if (isset($user_display_name_err) && !empty($user_display_name_err)) {
							echo '<p class="text-danger mb-0 text-left">' . $user_display_name_err . '</p>';

						}
						?>
					</div>
				</div>
				
				<div class="form-group row mt-4 align-items-center">
					<label for="userEmailAddress" class="col-sm-3 col-form-label">Email Address</label>
					
					<div class="col-sm-9">
						<input type="email" class="form-control <?php echo !empty($user_email_address_err) ? 'border border-danger' : ''; ?>" id="userEmailAddress" name="userEmailAddress" value="<?php echo $_POST['userEmailAddress']; ?>">
						
						<?php
						if (isset($user_email_address_err) && !empty($user_email_address_err)) {
							echo '<p class="text-danger mb-0 text-left">' . $user_email_address_err . '</p>';

						}
						?>
					</div>
				</div>
				
				<div class="form-group row mt-4 align-items-center">
					<label for="userPassword" class="col-sm-3 col-form-label">Password</label>
					
					<div class="col-sm-9">
						<input type="password" class="form-control <?php echo !empty($user_password_err) ? 'border border-danger' : ''; ?>" id="userPassword" name="userPassword" aria-describedby="passwordInfo" value="<?php echo $_POST['userPassword']; ?>">
						
						<?php
						if (isset($user_password_err) && !empty($user_password_err)) {
							echo '<p class="text-danger mb-0">' . $user_password_err . '</p>';

						} else {
							echo '<small id="passwordInfo" class="form-text text-muted">Minimum 8 characters, must contain at least 1 uppercase letter, 1 lowercase letter, 1 number digit, and 1 special character.</small>';

						}
						?>
					</div>
				</div>
				
				<div class="form-group row mt-4 align-items-center">
					<label for="userConfirmPassword" class="col-sm-3 col-form-label">Confirm Password</label>
					
					<div class="col-sm-9">
						<input type="password" class="form-control <?php echo !empty($user_confirm_password_err) ? 'border border-danger' : ''; ?>" id="userConfirmPassword" name="userConfirmPassword" value="<?php echo $_POST['userConfirmPassword']; ?>">
						
						<?php
						if (isset($user_confirm_password_err) && !empty($user_confirm_password_err)) {
							echo '<p class="text-danger mb-0">' . $user_confirm_password_err . '</p>';

						}
						?>
					</div>
				</div>

				<button type="submit" id="userLoginSubmit" class="btn btn-secondary btn-block mt-5">Continue Registration</button>
			</form>
			
			<p class="mb-0 mt-4 text-center">Already have an account? <a href="/moov/login">Login now.</a></p>
		</div>
	</div>
	
	<?php include 'footer.php'; ?>
</body>

</html>

<?php mysqli_close($conn); ?>