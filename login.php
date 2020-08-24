<?php
session_start();
require_once 'config.php';
$page_name = 'login';

$login_email_address = $login_password = '';
$login_email_address_err = $login_password_err = $login_err = '';

if (isset($_SESSION['moov_user_logged_in']) && $_SESSION['moov_user_logged_in'] == TRUE) {
	header('location: /moov/');
	
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (empty(trim($_POST['loginEmailAddress']))) {
		$login_email_address_err = 'Please enter your email address.';
		
	} else {
		$login_email_address = trim($_POST['loginEmailAddress']);
		
	}
	
	if (empty(trim($_POST['loginPassword']))) {
		$login_password_err = 'Please enter your login password.';
		
	} else {
		$login_password = trim($_POST['loginPassword']);
		
	}
	
	if (empty($login_email_address_err) && empty($login_password_err)) {
		$user_login_sql = 'SELECT account_id, first_name, password, is_deactivated FROM account WHERE email_address = ?';
		
		if ($user_login_stmt = mysqli_prepare($conn, $user_login_sql)) {
			mysqli_stmt_bind_param($user_login_stmt, 's', $param_email_address);
			
			$param_email_address = $login_email_address;
			
			if (mysqli_stmt_execute($user_login_stmt)) {
				mysqli_stmt_store_result($user_login_stmt);
				
				if (mysqli_stmt_num_rows($user_login_stmt) == 1) {
					mysqli_stmt_bind_result($user_login_stmt, $user_account_id, $user_first_name, $user_password, $user_account_status);
					
					if (mysqli_stmt_fetch($user_login_stmt)) {
						if (password_verify($login_password, $user_password)) {
							if ($user_account_status == 1) {
								$login_err = 'Your account is suspended. If you think this is an error, please contact us immediately.';

							} elseif ($user_account_status == 0) {
								if (isset($_POST['usernameRemember']) && $_POST['usernameRemember'] == 'on') {
									setcookie('moov_user_email_address', $login_email_address, time() + (86400 * 30), '/moov/');
									
								}

								session_start();
								
								$_SESSION['moov_user_logged_in'] = TRUE;
								$_SESSION['moov_user_account_id'] = $user_account_id;
								$_SESSION['moov_user_first_name'] = $user_first_name;
								
								header('location: /moov/');
								unset($_POST);
							}
						} else {
							$login_password_err = 'Password does not match with the associated account. Please try again.';
							
						}
					}
				} else {
					$login_err = 'There\'s no account associated with this email address. If you think this is an error, please contact us immediately.';
					
				}
			} else {
				$login_error = TRUE;
				
			}
		}
		
		mysqli_stmt_close($user_login_stmt);
		
	}
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login | Moov</title>

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
		<h1 class="text-center">Login</h1>
		
		<?php
		if ($_SESSION['moov_user_registration_success'] === TRUE) {
            echo '
            <div class="alert alert-success my-4 alert-dismissible fade show" role="alert">
                Account registered successfully. You can now login with your login credentials.

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ';
			
			unset($_SESSION['moov_user_registration_success']);
        }
		
		if ($_SESSION['moov_user_reset_password_success'] === TRUE) {
            echo '
            <div class="alert alert-success my-4 alert-dismissible fade show" role="alert">
                Password reset successfully. You can now login with your new login credentials.

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ';
			
			unset($_SESSION['moov_user_reset_password_success']);
        }
		
		if ($login_error === TRUE) {
            echo '
            <div class="alert alert-warning my-4 alert-dismissible fade show" role="alert">
                Oops! There is an error occurred. Please try again later. If you continue to see this error, please contact us immediately.

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
					<label for="loginEmailAddress" class="col-sm-3 col-form-label">Email Address</label>
					
					<div class="col-sm-9">
						<?php
						if (isset($_COOKIE['moov_user_email_address'])) {
							$saved_login_email_address = $_COOKIE['moov_user_email_address'];

						}
						?>
						
						<input type="email" class="form-control <?php echo (!empty($login_email_address_err) || !empty($login_err)) ? 'border border-danger' : ''; ?>" id="loginEmailAddress" name="loginEmailAddress" value="<?php echo !empty($_POST['loginEmailAddress']) ? $_POST['loginEmailAddress'] : $saved_login_email_address; ?>">
						
						<?php
						if (isset($login_email_address_err) && !empty($login_email_address_err)) {
							echo '<p class="text-danger mb-0">' . $login_email_address_err . '</p>';

						}
						?>
					</div>
				</div>
				
				<div class="form-group row mt-4 align-items-center">
					<label for="loginPassword" class="col-sm-3 col-form-label">Password</label>
					
					<div class="col-sm-9">
						<input type="password" class="form-control <?php echo (!empty($login_password_err) || !empty($login_err)) ? 'border border-danger' : ''; ?>" id="loginPassword" name="loginPassword" value="<?php echo $_POST['loginPassword']; ?>">
						
						<?php
						if (isset($login_password_err) && !empty($login_password_err)) {
							echo '<p class="text-danger mb-0">' . $login_password_err . '</p>';

						}
						?>
					</div>
				</div>
				
				<div class="form-group form-check mt-4">
					<input type="checkbox" class="form-check-input" id="usernameRemember" name="usernameRemember" <?php echo (isset($_POST['usernameRemember']) && $_POST['usernameRemember'] == 'on') ? 'checked' : ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['usernameRemember']) ? '' : 'checked'); ?>>

					<label class="form-check-label" for="usernameRemember">Remember my email address</label>
				</div>
				
				<?php
				if (isset($login_err) && !empty($login_err)) {
					echo '<p class="text-danger mb-0">' . $login_err . '</p>';

				}
				?>

				<button type="submit" class="btn btn-secondary btn-block mt-5">Login</button>
			</form>
			
			<p class="mb-0 mt-4 text-center"><a href="/moov/forgot-password">Forgot password?</a></p>
			<p class="mb-0 text-center">Don't have an account? <a href="/moov/register">Register now.</a></p>
		</div>
    </div>
	
    <?php include 'footer.php'; ?>
</body>

</html>

<?php mysqli_close($conn); ?>