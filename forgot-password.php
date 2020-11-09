<?php
session_start();
require_once 'config.php';
$page_name = basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php');

$reset_email_address = $reset_email_address_err = $reset_err = '';

if (isset($_SESSION['moov_user_logged_in']) && $_SESSION['moov_user_logged_in'] == TRUE) {
	header('location: /moov/');
	
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (empty(trim($_POST['forgotEmailAddress']))) {
		$reset_email_address_err = 'Please enter your email address.';
		
	} else {
		$reset_email_address = trim($_POST['forgotEmailAddress']);
		$verify_account_sql = 'SELECT display_name, is_deleted, is_suspended FROM account WHERE email_address = ?';
		
		if ($verify_account_stmt = mysqli_prepare($conn, $verify_account_sql)) {
			mysqli_stmt_bind_param($verify_account_stmt, 's', $param_email_address);
			
			$param_email_address = $reset_email_address;
			
			if (mysqli_stmt_execute($verify_account_stmt)) {
				mysqli_stmt_store_result($verify_account_stmt);
				
				if (mysqli_stmt_num_rows($verify_account_stmt) == 1) {
					mysqli_stmt_bind_result($verify_account_stmt, $reset_display_name, $reset_deleted_account, $reset_account_status);
					mysqli_stmt_fetch($verify_account_stmt);
					
					if ($reset_account_status == 1) {
						$reset_err = 'Your account is suspended. Therefore, we can\'t proceed your request. If you think this is an error, please contact us immediately.';
						
					}
					
					if ($reset_deleted_account == 1) {
						$reset_err = 'Your account is deleted. Therefore, we can\'t proceed your request. If you think this is an error, please contact us immediately.';
						
					}
					
					if ($reset_account_status == 0 && $reset_deleted_account == 0) {
						$reset_action = TRUE;
						
					}
				} else {
					$reset_action = FALSE;
					$reset_confirmation = TRUE;
					
				}
			} else {
				$reset_error = TRUE;
				$error_message = mysqli_error($conn);
				
			}
		}
		
		mysqli_stmt_close($verify_account_stmt);
		
	}
	
	if (empty($reset_email_address_err) && $reset_action == TRUE) {
		$selector = bin2hex(random_bytes(8));
        $token = random_bytes(32);
		
		$reset_url = 'http://kftech.ddns.net/moov/reset-password?email=' . urlencode($param_email_address) . '&selector=' . $selector . '&validator=' . bin2hex($token);
		
		$date_of_expiry = date('U') + 900;

        $delete_duplicate_reset_sql = 'DELETE FROM reset_password WHERE email_address = ?';
		
		if ($delete_duplicate_reset_stmt = mysqli_prepare($conn, $delete_duplicate_reset_sql)){
            mysqli_stmt_bind_param($delete_duplicate_reset_stmt, 's', $param_email_address);
            mysqli_stmt_execute($delete_duplicate_reset_stmt);

        }
		
		$register_new_reset_sql = 'INSERT INTO reset_password (email_address, selector, token, date_of_expiry) VALUES (?, ?, ?, ?)';

        if ($register_new_reset_stmt = mysqli_prepare($conn, $register_new_reset_sql)){
            mysqli_stmt_bind_param($register_new_reset_stmt, 'ssss', $param_email_address, $param_selector, $param_token, $param_date_of_expiry);
			
			$param_selector = $selector;
			$param_token = password_hash($token, PASSWORD_DEFAULT);
			$param_date_of_expiry = $date_of_expiry;
			
            if (mysqli_stmt_execute($register_new_reset_stmt)) {
				$mail_email = $reset_email_address;
				$mail_name = $reset_display_name;
				$mail_subject = '[Moov] Reset Your Moov Account Password';
				$mail_body = '<h1>Dear ' . $reset_display_name . ',</h1><p class="my-4 text-left">You are receiving this email because we received a password reset request for your account.</p><a class="btn btn-primary btn-block my-4" role="button" href="' . $reset_url . '">Click Here to Reset Password</a><p class="my-4 text-left">This password reset link will expire in 15 minutes.</p><p class="my-4 text-left">If you did not request a password reset, no further action is required.</p><p class="my-4 text-left">Kind Regards,<br/>Moov Admin</p><hr class="mt-5"><small class="text-left">If you\'re having trouble clicking the Reset Password button, copy and paste the URL below into your web browser: <a href="' . $reset_url . '">' . $reset_url . '</a></small>';

				require_once 'mail/mail-customer.php';

				$reset_confirmation = TRUE;
				unset($_POST);
				
			} else {
				$reset_error = TRUE;
				$error_message = mysqli_error($conn);
				
			}
        }
		
		mysqli_stmt_close($delete_duplicate_reset_stmt);
		mysqli_stmt_close($register_new_reset_stmt);
	}
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Forgot Password | Moov</title>

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
		<h1 class="text-center">Reset My Password</h1>
		
		<?php
		if ($reset_confirmation === TRUE) {
            echo '
            <div class="alert alert-warning my-4 alert-dismissible fade show" role="alert">
                If the email address exists, a reset email will be send to you immediately.
                
                <br/><br/>
                
                If you don\'t see the email, it is either the email address is not registered in our system, or check other places it might be, like your junk, spam, social, or other folders.

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ';
			
			unset($_SESSION['moov_user_registration_success']);
        }
		
		if ($reset_error === TRUE) {
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
		
		<p class="mt-4">Enter your email address and we will send you instructions to reset your password.</p>
		
		<div class="container bg-secondary pt-4 pb-2 rounded">
			<form action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php'); ?>" method="post" onSubmit="submitButton()">
				<div class="form-group row align-items-center">
					<label for="forgotEmailAddress" class="col-sm-3 col-form-label">Email Address</label>
					
					<div class="col-sm-9">
						<input type="email" class="form-control <?php echo (!empty($reset_email_address_err) || !empty($reset_err)) ? 'border border-danger' : ''; ?>" id="forgotEmailAddress" name="forgotEmailAddress" value="<?php echo $_POST['forgotEmailAddress']; ?>" onKeyUp="changeEventButton(this)">
						
						<?php
						if (isset($reset_email_address_err) && !empty($reset_email_address_err)) {
							echo '<p class="text-danger mb-0">' . $reset_email_address_err . '</p>';

						}
						?>
					</div>
				</div>
				
				<?php
				if (isset($reset_err) && !empty($reset_err)) {
					echo '<p class="text-danger mb-0">' . $reset_err . '</p>';

				}
				?>

				<button id="resetSubmitButton" type="submit" class="btn btn-secondary btn-block mt-5">
					<span id="submitButton">Reset My Password</span>
					
					<img id="processingIcon" src="/moov/assets/images/processing_icon.svg" class="processing-icon d-none">
					<span id="processingButton" class="d-none">Processing...</span>
				</button>
			</form>
			
			<p class="mb-0 mt-4 text-center">Already have an account? <a href="/moov/login">Login now.</a></p>
			<p class="mb-0 text-center">Don't have an account? <a href="/moov/register">Register now.</a></p>
			
			<script>
				function submitButton() {
					document.getElementById('resetSubmitButton').disabled = true;
					document.getElementById('submitButton').classList.add('d-none');
					document.getElementById('processingIcon').classList.add('d-inline-block');
					document.getElementById('processingIcon').classList.remove('d-none');
					document.getElementById('processingButton').classList.remove('d-none');

				}

				function changeEventButton(event) {
					if (event.keyCode == 13) {
						event.preventDefault;

						document.getElementById('resetSubmitButton').disabled = true;
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