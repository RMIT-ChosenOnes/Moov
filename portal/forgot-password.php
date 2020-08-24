<?php
session_start();
require_once 'config.php';

$forgot_username = $forgot_username_err = $forgot_err = '';

if (isset($_SESSION['moov_portal_logged_in']) && $_SESSION['moov_portal_logged_in'] == TRUE) {
	header('location: /moov/portal/');
	
} else {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (empty(trim($_POST['portalForgotUsername']))) {
			$forgot_username_err = 'Please enter a valid username.';
			
		} else {
			$forgot_username = trim($_POST['portalForgotUsername']);
			$verify_portal_account_sql = 'SELECT first_name, email_address, role, is_deactivated FROM portal_account WHERE username = ?';
			
			if ($verify_portal_account_stmt = mysqli_prepare($conn, $verify_portal_account_sql)) {
				mysqli_stmt_bind_param($verify_portal_account_stmt, 's', $param_username);
				
				$param_username = $forgot_username;
				
				if (mysqli_stmt_execute($verify_portal_account_stmt)) {
					mysqli_stmt_store_result($verify_portal_account_stmt);
					
					if(mysqli_stmt_num_rows($verify_portal_account_stmt) == 1) {
						mysqli_stmt_bind_result($verify_portal_account_stmt, $saved_portal_first_name, $saved_portal_email_address, $saved_portal_role, $saved_portal_account_status);
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
			$login_sql = 'SELECT first_name, email_address, password, role, is_deactivated FROM portal_account WHERE username = ?';
			
			
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
		<div id="loginCard" class="card bg-secondary mx-auto">
			<img src="assets/logo/moov_portal_logo_1200x600.png" class="card-img-top w-50 mx-auto mt-2" alt="">
			
			<div class="card-body">
				<h1 class="card-title">Forgot Password</h1>
				
				<p class="card-text my-4 mx-lg-5">Enter your username below and we will notify the adminstrator to change your password.</p>
				
				<form class="mx-lg-5" action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php'); ?>" method="post">
					<div class="col-auto p-0">
						<label class="sr-only" for="portalForgotUsername">Username</label>

						<div class="input-group">
							<div class="input-group-prepend">
								<div class="input-group-text">
									<img src="assets/images/login_username_icon.svg" class="mx-auto">
								</div>
							</div>
							
							<input type="text" class="form-control form-control-lg <?php echo (!empty($forgot_username_err) || !empty($forgot_err)) ? 'border border-danger' : ''; ?>" id="portalForgotUsername" name="portalForgotUsername" placeholder="Username" value="<?php echo $_POST['portalForgotUsername']; ?>">
						</div>
						
						<?php
						if (isset($forgot_username_err) && !empty($forgot_username_err)) {
							echo '<p class="text-danger mb-0 text-left">' . $forgot_username_err . '</p>';

						}
						?>
					</div>
					
					<?php
					if (isset($forgot_err) && !empty($forgot_err)) {
						echo '<p class="text-danger mb-0">' . $forgot_err . '</p>';

					}
					?>
					
					<button type="submit" class="btn btn-primary btn-block btn-lg mt-5">Submit</button>
				</form>
				
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