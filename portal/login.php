<?php
session_start();
require_once 'config.php';

$referrer_url = $login_username = $login_password = '';
$login_username_err = $login_password_err = $login_err = '';

if (isset($_SESSION['moov_portal_logged_in']) && $_SESSION['moov_portal_logged_in'] == TRUE) {
	header('location: /moov/portal/');
	
} else {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$referrer_url = trim($_POST['referrerUrl']);
		
		if (empty(trim($_POST['portalUsername']))) {
			$login_username_err = 'Please enter a valid username.';
			
		} else {
			$login_username = trim($_POST['portalUsername']);
			
		}
		
		if (empty(trim($_POST['portalPassword']))) {
			$login_password_err = 'Please enter a valid password.';
			
		} else {
			$login_password = trim($_POST['portalPassword']);
			
		}
		
		if (empty($login_username_err) && empty($login_password_err)) {
			$login_sql = 'SELECT first_name, email_address, password, role, is_deactivated FROM portal_account WHERE username = ?';
			
			if ($login_stmt = mysqli_prepare($conn, $login_sql)) {
				mysqli_stmt_bind_param($login_stmt, 's', $param_username);
				
				$param_username = $login_username;
				
				if (mysqli_stmt_execute($login_stmt)) {
					mysqli_stmt_store_result($login_stmt);
					
					if (mysqli_stmt_num_rows($login_stmt) == 1) {
						mysqli_stmt_bind_result($login_stmt, $staff_first_name, $staff_email, $staff_saved_password, $staff_role, $staff_account_status);
						
						if (mysqli_stmt_fetch($login_stmt)) {
							if (password_verify($login_password, $staff_saved_password)) {
								if ($staff_account_status == 1) {
									$login_err = 'Your account is deactivated. If you think this is an error, please contact the administrator.';

								} elseif ($staff_account_status == 0) {
									if (isset($_POST['portalRemember']) && $_POST['portalRemember'] == 'on') {
										setcookie('moov_portal_username', $login_username, time() + (86400 * 30), '/moov/portal/');
										
									}
									
									session_start();
									
									$_SESSION['moov_portal_logged_in'] = TRUE;
									$_SESSION['moov_portal_staff_first_name'] = $staff_first_name;
									$_SESSION['moov_portal_staff_email'] = $staff_email;
									
									if ($staff_role == 1) {
										$_SESSION['moov_portal_staff_role'] = 'Admin';
										
									} elseif ($staff_role == 2) {
										$_SESSION['moov_portal_staff_role'] = 'Staff';
										
									}
									
									if (!empty($referrer_url)) {
										header('location: ' . $referrer_url);
										
									} else {
										header('location: /moov/portal/');
										
									}
									
									unset($_POST);
									
								}
							} else {
								$login_password_err = 'Password does not match with the associated account.';

							}
						}
					} else {
						$login_err = 'There\'s no account associated with this username. If you think this is an error, please contact the administrator.';
						
					}
				} else {
					$login_error = TRUE;
					$error_message = mysqli_error($conn);
					
				}
			}
			
			mysqli_stmt_close($login_stmt);
			
		}
	}
}
?>

<!DOCTYPE html>
<html>
	
<head>
	<title>Login | Moov Portal</title>
	
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
		<div id="loginCard" class="card bg-secondary mx-auto">
			<img src="assets/logo/moov_portal_logo_1200x600.png" class="card-img-top w-50 mx-auto mt-2" alt="">
			
			<div class="card-body">
				<h1 class="card-title">Welcome to Moov Portal</h1>
				
				<?php
				if ($login_error === TRUE) {
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
				
				<form class="mt-5 mx-lg-5" action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php'); ?>" method="post" onSubmit="submitButton()">
					<input type="hidden" id="referrerUrl" name="referrerUrl" value="<?php echo $_GET['url'] . $_POST['referrerUrl'] ?>">
					
					<div class="col-auto p-0">
						<label class="sr-only" for="portalUsername">Username</label>

						<div class="input-group">
							<div class="input-group-prepend">
								<div class="input-group-text">
									<img src="assets/images/login_username_icon.svg" class="mx-auto">
								</div>
							</div>
							
							<?php
							if (isset($_COOKIE['moov_portal_username'])) {
								$saved_login_username = $_COOKIE['moov_portal_username'];
								
							}
							?>
							
							<input type="text" class="form-control form-control-lg <?php echo (!empty($login_username_err) || !empty($login_err)) ? 'border border-danger' : ''; ?>" id="portalUsername" name="portalUsername" placeholder="Username" value="<?php echo !empty($_POST['portalUsername']) ? $_POST['portalUsername'] : $saved_login_username; ?>" onKeyUp="changeEventButton(this)">
						</div>
						
						<?php
						if (isset($login_username_err) && !empty($login_username_err)) {
							echo '<p class="text-danger mb-0 text-left">' . $login_username_err . '</p>';

						}
						?>
					</div>
					
					<div class="col-auto mt-3 p-0">
						<label class="sr-only" for="portalPassword">Password</label>

						<div class="input-group">
							<div class="input-group-prepend">
								<div class="input-group-text">
									<img src="assets/images/login_password_icon.svg" class="mx-auto">
								</div>
							</div>
							
							<input type="password" class="form-control form-control-lg <?php echo (!empty($login_password_err) || !empty($login_err)) ? 'border border-danger' : ''; ?>" id="portalPassword" name="portalPassword" placeholder="Password" value="<?php echo $_POST['portalPassword']; ?>" onKeyUp="changeEventButton(this)">
						</div>
						
						<?php
						if (isset($login_password_err) && !empty($login_password_err)) {
							echo '<p class="text-danger mb-0 text-left">' . $login_password_err . '</p>';

						}
						?>
					</div>
					
					<div class="form-group form-check mt-3">
						<input type="checkbox" class="form-check-input" id="portalRemember" name="portalRemember" <?php echo (isset($_POST['portalRemember']) && $_POST['portalRemember'] == 'on') ? 'checked' : ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['portalRemember']) ? '' : 'checked'); ?>>

						<label class="form-check-label" for="portalRemember">Remember my username</label>
					</div>
					
					<?php
					if (isset($login_err) && !empty($login_err)) {
						echo '<p class="text-danger mb-0">' . $login_err . '</p>';

					}
					?>
					
					<button id="loginSubmitButton" type="submit" class="btn btn-primary btn-block btn-lg mt-4">
						<span id="submitButton">Login</span>
					
						<img id="processingIcon" src="/moov/assets/images/processing_icon.svg" class="processing-icon d-none">
						<span id="processingButton" class="d-none">Processing...</span>
					</button>
				</form>
				
				<script>
					function submitButton() {
						document.getElementById('loginSubmitButton').disabled = true;
						document.getElementById('submitButton').classList.add('d-none');
						document.getElementById('processingIcon').classList.add('d-inline-block');
						document.getElementById('processingIcon').classList.remove('d-none');
						document.getElementById('processingButton').classList.remove('d-none');

					}

					function changeEventButton(event) {
						if (event.keyCode == 13) {
							event.preventDefault;

							document.getElementById('loginSubmitButton').disabled = true;
							document.getElementById('submitButton').classList.add('d-none');
							document.getElementById('processingIcon').classList.add('d-inline-block');
							document.getElementById('processingIcon').classList.remove('d-none');
							document.getElementById('processingButton').classList.remove('d-none');

						}
					}
				</script>
				
				<p class="mb-0 mt-4 text-center"><a href="/moov/portal/forgot-password">Forgot password?</a></p>
			</div>
			
			<div class="py-2 text-logo">
				<h6 class="mb-0">Copyright &copy; 2020 &#64; portal.moov.com</h6>
			</div>
		</div>
	</div>
</body>
	
</html>

<?php mysqli_close($conn); ?>