<?php
session_start();
require_once 'config.php';
use PHPMailer\PHPMailer\PHPMailer;
require '/var/lib/vendor/autoload.php';

$forgot_username = $forgot_username_err = $forgot_err = '';

if (isset($_SESSION['moov_portal_logged_in']) && $_SESSION['moov_portal_logged_in'] == TRUE) {
	header('location: /moov/portal/');
	
} else {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (empty(trim($_POST['portalForgotUsername']))) {
			$forgot_username_err = 'Please enter a valid username.';
			
		} else {
			$forgot_username = trim($_POST['portalForgotUsername']);
			$verify_portal_account_sql = 'SELECT first_name, last_name, email_address, role, is_deactivated FROM portal_account WHERE username = ?';
			
			if ($verify_portal_account_stmt = mysqli_prepare($conn, $verify_portal_account_sql)) {
				mysqli_stmt_bind_param($verify_portal_account_stmt, 's', $param_username);
				
				$param_username = $forgot_username;
				
				if (mysqli_stmt_execute($verify_portal_account_stmt)) {
					mysqli_stmt_store_result($verify_portal_account_stmt);
					
					if(mysqli_stmt_num_rows($verify_portal_account_stmt) == 1) {
						mysqli_stmt_bind_result($verify_portal_account_stmt, $saved_portal_first_name, $saved_portal_last_name, $saved_portal_email_address, $saved_portal_role, $saved_portal_account_status);
						mysqli_stmt_fetch($verify_portal_account_stmt);
						
						if ($saved_portal_account_status == 1) {
							$forgot_err = 'Your account is suspended. Therefore, we can\'t proceed your request. If you think this is an error, please contact the administrator.';
							
						} elseif ($saved_portal_account_status == 0) {
							$reset_action = TRUE;
							
						}
					} else {
						$reset_action = FALSE;
						$reset_confirmation = TRUE;
						
					}
				} else {
					$reset_error = TRUE;
					
				}
			}
			
			mysqli_stmt_close($verify_portal_account_stmt);
			
		}
		
		if (empty($forgot_username_err) && $reset_action == TRUE) {
			$get_admin_list_sql = 'SELECT first_name, email_address FROM portal_account WHERE role = ? AND is_deactivated = ?';
			
			if ($get_admin_list_stmt = mysqli_prepare($conn, $get_admin_list_sql)) {
				mysqli_stmt_bind_param($get_admin_list_stmt, 'ii', $param_role, $param_status);
				
				$param_role = 1;
				$param_status = 0;
				
				if (mysqli_stmt_execute($get_admin_list_stmt)) {
					$get_admin_list = mysqli_stmt_get_result($get_admin_list_stmt);
					
					while ($admin_list = mysqli_fetch_assoc($get_admin_list)) {
						$receiver_admin[$admin_list['email_address']] = $admin_list['first_name'];
						
					}
					
					$mail = new PHPMailer(true);

					try {
						// Server
						$mail->isSMTP();
						$mail->Host         = 'smtp.gmail.com';
						$mail->SMTPAuth     = true;
						$mail->SMTPSecure   = PHPMailer::ENCRYPTION_STARTTLS;
						$mail->Port         = 587;
						$mail->Username     = 'moov.chosenones@gmail.com';
						$mail->Password     = 'qUbcyx-zetqe1-zubdyj';

						// Recipients
						$mail->setFrom('moov.chosenones@gmail.com', 'Moov Portal Admin');
						$mail->addReplyTo('moov.chosenones@gmail.com', 'Moov Portal Admin');

						foreach ($receiver_admin as $admin_email_address => $admin_first_name) {
							$mail->addBCC($admin_email_address, $admin_first_name);
							
						}
						
						// Mail
						$mail->isHTML(true);
						$mail->Subject  = '[Moov Portal] New Password Change Request';
						$mail->Body		= '<link rel="stylesheet" type="text/css" href="http://121.200.18.218:8080/moov/portal/assets/style/bootstrap.css"><link rel="stylesheet" type="text/css" href="http://121.200.18.218:8080/moov/portal/assets/style/style.css"><body class="d-flex m-4 p-0"><div class="container mx-auto text-center"><img src="http://121.200.18.218:8080/moov/mail/assets/logo/moov_mail_logo_400x200.png" class="mx-auto"><h4>Dear Admin,</h4><p class="my-4">You have received a new request from the below staff to change their login password.</p><p class="text-left"><b>Username:</b> ' . $forgot_username . '<br/><b>First Name:</b> ' . $saved_portal_first_name . '<br/><b>Last Name:</b> ' . $saved_portal_last_name . '<br/><b>Email Address:</b> ' . $saved_portal_email_address . '<br/><b>Assigned Role:</b> ' . ($saved_portal_role == 1 ? 'Admin' : 'Staff') . '</p><a class="btn btn-primary btn-block my-4" href="http://kftech.ddns.net/moov/portal/database/modify-staff" role="button">Login</a><p class="my-4 text-left">Thank you.</p><p class="my-4 text-left">Kind Regards,<br/>Moov Portal Admin</p></div></body>';

						$mail->send();

					} catch (Exception $e) {
						$$error_message = $mail->ErrorInfo;

					}
					
					$reset_confirmation = TRUE;
					unset($_POST);
					
				} else {
					$reset_error = TRUE;
					
				}
			}
			
			mysqli_stmt_close($get_admin_list_stmt);
			
		}
	}
}
?>

<!DOCTYPE html>
<html>
	
<head>
	<title>Forgot Password | Moov Portal</title>
	
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

<body id="portalLogin" class="d-flex m-0 p-0 vh-100">
	<div class="container m-auto text-center">
		<div id="forgotCard" class="card bg-secondary mx-auto">
			<img src="assets/logo/moov_portal_logo_1200x600.png" class="card-img-top w-50 mx-auto mt-2" alt="">
			
			<div class="card-body">
				<h1 class="card-title">Forgot Password</h1>
				
				<?php
				if ($reset_confirmation == TRUE) {
					echo '<p class="card-text my-4 mx-lg-5">We have notified the administrator about your request. If your account exists, the administrator will be in contact soon.</p>';
					
				} else {
					if ($reset_error === TRUE) {
						echo '
						<div class="alert alert-warning my-4 alert-dismissible fade show" role="alert">
							Oops! There is an error occurred. Please try again later. If you continue to see this error, please contact the administrator.
							
						' . $error_message . '
						
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						';
					}
					
					echo '
					<p class="card-text my-4 mx-lg-5">Enter your username below and we will notify the adminstrator to change your password.</p>
				
					<form class="mx-lg-5" action="' . basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php') . '" method="post" onSubmit="submitButton()">
						<div id="test" class="col-auto p-0">
							<label class="sr-only" for="portalForgotUsername">Username</label>

							<div class="input-group">
								<div class="input-group-prepend">
									<div class="input-group-text">
										<img src="assets/images/login_username_icon.svg" class="mx-auto">
									</div>
								</div>

								<input type="text" class="form-control form-control-lg ' . ((!empty($forgot_username_err) || !empty($forgot_err)) ? 'border border-danger' : '') . ' ?>" id="portalForgotUsername" name="portalForgotUsername" placeholder="Username" value="' . $_POST['portalForgotUsername'] . '" onKeyUp="changeEventButton(this)">
							</div>
					';

							if (isset($forgot_username_err) && !empty($forgot_username_err)) {
								echo '<p class="text-danger mb-0 text-left">' . $forgot_username_err . '</p>';

							}
					
					echo '
						</div>
					';

						if (isset($forgot_err) && !empty($forgot_err)) {
							echo '<p class="text-danger mb-0">' . $forgot_err . '</p>';

						}

					echo '
						<button id="forgotSubmitButton" type="submit" class="btn btn-primary btn-block btn-lg mt-5">
							<span id="submitButton">Submit</span>
					
							<img id="processingIcon" src="/moov/assets/images/processing_icon.svg" class="processing-icon d-none">
							<span id="processingButton" class="d-none">Processing...</span>
						</button>
					</form>
					';
					
				}
				?>
				
				<script>
					function submitButton() {
						document.getElementById('forgotSubmitButton').disabled = true;
						document.getElementById('submitButton').classList.add('d-none');
						document.getElementById('processingIcon').classList.add('d-inline-block');
						document.getElementById('processingIcon').classList.remove('d-none');
						document.getElementById('processingButton').classList.remove('d-none');

					}

					function changeEventButton(event) {
						if (event.keyCode == 13) {
							event.preventDefault;

							document.getElementById('forgotSubmitButton').disabled = true;
							document.getElementById('submitButton').classList.add('d-none');
							document.getElementById('processingIcon').classList.add('d-inline-block');
							document.getElementById('processingIcon').classList.remove('d-none');
							document.getElementById('processingButton').classList.remove('d-none');

						}
					}
				</script>
				
				<p class="mb-0 mt-4 text-center"><a href="/moov/portal/login">Back to Login page.</a></p>
			</div>
			
			<div class="py-2 text-logo">
				<h6 class="mb-0">Copyright &copy; 2020 &#64; portal.moov.com</h6>
			</div>
		</div>
	</div>
</body>
	
</html>

<?php mysqli_close($conn); ?>