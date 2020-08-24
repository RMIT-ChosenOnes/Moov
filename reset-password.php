<?php
session_start();
require_once 'config.php';
$page_name = basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php');

$reset_selector = $reset_validator = $reset_password = $reset_confirm_password = '';
$reset_password_err = $reset_confirm_password_err = $reset_err = '';

if (isset($_SESSION['moov_user_logged_in']) && $_SESSION['moov_user_logged_in'] == TRUE) {
	header('location: /moov/');
	
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$today_date = date('U');
	
	$reset_selector = $_POST['resetSelector'];
	$reset_validator = $_POST['resetValidator'];
	
	if (empty(trim($_POST['resetPassword']))) {
		$reset_password_err = 'Please enter new password.';
		
	} elseif (!preg_match('/[a-z]+/', trim($_POST['resetPassword'])) || !preg_match('/[A-Z]+/', trim($_POST['resetPassword'])) || !preg_match('/[^a-zA-Z0-9]+/', trim($_POST['resetPassword'])) || strlen(trim($_POST['resetPassword'])) < 8) {
		$reset_password_err = 'Your password must contain at least one uppercase letter, one lowercase letter, one number digit, one special character, and have at least 8 characters long.';

	}
	
	if (empty(trim($_POST['resetConfirmPassword']))) {
		$reset_confirm_password_err = 'Please confirm your new password again.';

	} else {
		if (trim($_POST['resetPassword']) == trim($_POST['resetConfirmPassword'])) {
			$reset_password = trim($_POST['resetPassword']);

		} else {
			$reset_password_err = $reset_confirm_password_err = 'Password does not matched. Please try again.';

		}
	}
	
	if (empty($reset_password_err) && empty($reset_confirm_password_err)) {
		$get_reset_status_sql = 'SELECT email_address, token FROM reset_password WHERE selector = ? AND date_of_expiry >= ?';
		
		if ($get_reset_status_stmt = mysqli_prepare($conn, $get_reset_status_sql)) {
			mysqli_stmt_bind_param($get_reset_status_stmt, 'ss', $param_selector, $param_today_date);
			
			$param_selector = $reset_selector;
			$param_today_date = $today_date;
			
			if (mysqli_stmt_execute($get_reset_status_stmt)) {
				mysqli_stmt_store_result($get_reset_status_stmt);
				
				if (mysqli_stmt_num_rows($get_reset_status_stmt) == 1) {
					mysqli_stmt_bind_result($get_reset_status_stmt, $saved_email_address, $saved_token);
					
					if (mysqli_stmt_fetch($get_reset_status_stmt)) {
						$token_bin = hex2bin($reset_validator);
						
						if (password_verify($token_bin, $saved_token)) {
							$update_password_sql = 'UPDATE account SET password = ? WHERE email_address = ?';
							
							if($update_password_stmt = mysqli_prepare($conn, $update_password_sql)) {
								mysqli_stmt_bind_param($update_password_stmt, 'ss', $param_reset_password, $param_email_address);
								
								$param_reset_password = password_hash($reset_password, PASSWORD_DEFAULT);
								$param_email_address = $saved_email_address;
								
								if (mysqli_stmt_execute($update_password_stmt)) {
									$delete_reset_token_sql = 'DELETE FROM reset_password WHERE selector = ?';
									
									if ($delete_reset_token_stmt = mysqli_prepare($conn, $delete_reset_token_sql)) {
										mysqli_stmt_bind_param($delete_reset_token_stmt, 's', $param_selector);
										mysqli_stmt_execute($delete_reset_token_stmt);
										
										$_SESSION['moov_user_reset_password_success'] = TRUE;
										unset($_POST);
										
										header('location: /moov/login');
										
									}
								} else {
									$reset_error = TRUE;
								
								}
							}
							
							mysqli_stmt_close($update_password_stmt);
							mysqli_stmt_close($delete_reset_token_stmt);
							
						} else {
							$reset_err = 'Token does not match with our system. Please either copy and paste the link provided in your email or submit a new reset request again.';
							
						}
					}
				} else {
					$reset_err = 'The link is no longer valid. Please submit a new reset request again.';
					
				}
			} else {
				$reset_error = TRUE;

			}
		}
		
		mysqli_stmt_close($get_reset_status_stmt);
		
	}
} elseif (empty($_GET['selector']) || empty($_GET['validator'])) {
	header('location: /moov/login');
	
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Reset Password | Moov</title>

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
		<h1 class="text-center">Reset Your New Password</h1>
		
		<?php
		if ($reset_error === TRUE) {
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
				<input type="hidden" id="resetSelector" name="resetSelector" value="<?php echo !empty($_POST['resetSelector']) ? $_POST['resetSelector'] : $_GET['selector']; ?>">
				<input type="hidden" id="resetValidator" name="resetValidator" value="<?php echo !empty($_POST['resetValidator']) ? $_POST['resetValidator'] : $_GET['validator']; ?>">
				
				<div class="form-group row align-items-center">
					<label for="resetEmailAddress" class="col-sm-3 col-form-label">Email Address</label>

					<div class="col-sm-9">
						<input type="text" class="form-control-plaintext" id="resetEmailAddress" name="resetEmailAddress" value="<?php echo !empty($_POST['resetEmailAddress']) ? $_POST['resetEmailAddress'] : $_GET['email']; ?>" readonly>
					</div>
				</div>
				
				<div class="form-group row align-items-center">
					<label for="resetPassword" class="col-sm-3 col-form-label">New Password</label>
					
					<div class="col-sm-9">
						<input type="password" class="form-control <?php echo (!empty($reset_password_err) || !empty($reset_err)) ? 'border border-danger' : ''; ?>" id="resetPassword" name="resetPassword" aria-describedby="passwordInfo" value="<?php echo $_POST['resetPassword']; ?>">
						
						<?php
						if (isset($reset_password_err) && !empty($reset_password_err)) {
							echo '<p class="text-danger mb-0">' . $reset_password_err . '</p>';

						} else {
							echo '<small id="passwordInfo" class="form-text text-muted">Password must contain at least one uppercase letter, one lowercase letter, one number digit, one special character, and have at least 8 characters long.</small>';

						}
						?>
					</div>
				</div>
				
				<div class="form-group row mt-4 align-items-center">
					<label for="resetConfirmPassword" class="col-sm-3 col-form-label">Confirm New Password</label>
					
					<div class="col-sm-9">
						<input type="password" class="form-control <?php echo (!empty($reset_confirm_password_err) || !empty($reset_err)) ? 'border border-danger' : ''; ?>" id="resetConfirmPassword" name="resetConfirmPassword" value="<?php echo $_POST['resetConfirmPassword']; ?>">
						
						<?php
						if (isset($reset_confirm_password_err) && !empty($reset_confirm_password_err)) {
							echo '<p class="text-danger mb-0">' . $reset_confirm_password_err . '</p>';

						}
						?>
					</div>
				</div>
				
				<?php
				if (isset($reset_err) && !empty($reset_err)) {
					echo '<p class="text-danger mb-0">' . $reset_err . '</p>';

				}
				?>

				<button type="submit" class="btn btn-secondary btn-block mt-5">Reset My Password</button>
			</form>
		</div>
    </div>
	
    <?php include 'footer.php'; ?>
</body>

</html>

<?php mysqli_close($conn); ?>