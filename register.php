<?php
session_start();
require_once 'config.php';

// Receive input from client side
$fName = $_POST['fName'];
$lName = $_POST['lName'];
$email = strtolower($_POST['email']);
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Validate password strength
$uppercase = preg_match('@[A-Z]@', $password);
$lowercase = preg_match('@[a-z]@', $password);
$number    = preg_match('@[0-9]@', $password);
$specialChars = preg_match('@[^\w]@', $password);

// Prepare error message triggers
$form_input_error = false;
$password_match_error = false;
$email_exists_error = false;
$password_strength_error = false;

if ($_POST['submit']) {
	// Validate form inputs are not empty
	if ($fName == NULL || $lName == NULL || $email == NULL || $password == NULL || $confirmPassword == NULL) {
		$form_input_error = true;
	}
	// Validate if password meets the requirements
	elseif (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
		$password_strength_error = true;
	}
	// validate if password and confirm password match
	elseif ($password != $confirmPassword) {
		$password_match_error = true;
	} 
	else {
		// Search database for if email exists
		$query = "select * from account where email_address = '$email'";
		$record = mysqli_query($conn, $query) or die(mysqli_error($conn));
		$row_count = mysqli_num_rows($record);
		if ($row_count > 0){
			// Trigger error alert for email existing in database
			$email_exists_error = true;
		} 
		else {
			// enter data to the database
			$query = "insert into temp_account(first_name, last_name, email_address, password) values('$fName','$lName', '$email', '$hashedPassword')";
			mysqli_query($conn, $query) or die(mysqli_error($conn));
			echo '<script>window.location.href="drivingLicense.php";</script>';
		}
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

	<!-- JavaScript from Bootstrap -->
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

	<!-- CSS from Bootstrap v4.5.2 -->
	<link rel="stylesheet" type="text/css" href="assets/style/bootstrap.css">

	<!-- Self Defined CSS -->
	<link rel="stylesheet" type="text/css" href="assets/style/style.css?<?php echo date('l jS \of F Y h:i:s A'); ?>">

	<!-- Favicon -->
	<link rel="icon" type="image/png" sizes="96x96" href="assets/favicon/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
</head>

<body>

	<?php include 'header.php'; ?>

	<container id="userRegister" class="d-flex m-0 p-0 vh-100">

		<div class="container m-auto text-center">
			<h1>REGISTRATION</h1>
			<div id="registerCard" class="card bg-secondary mx-auto">

				<div class="card-body">
					<form class="mt-5 mx-lg-5" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
						<?php
						// Display error message if form is missing inputs
						if ($form_input_error == true)
							echo '<div class="alert alert-warning" role="alert">You have not completely filled the signup form.</div>';
						// Display error message if passwords do not match
						elseif ($password_match_error == true)
							echo '<div class="alert alert-danger" role="alert">Passwords do not match. Please try again.</div>';
						// Display error message if email was found in database
						elseif ($email_exists_error == true)
							echo '<div class="alert alert-warning" role="alert">The email you have entered currently belongs to an account. Please try another email.</div>';
						// Display error message for password strength
						elseif ($password_strength_error == true)
							echo '<div class="alert alert-danger" role="alert">Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.</div>';
						?>
						<div class="form-group row">
							<label for="fName" class="col-sm-2 col-form-label" style=text-align:left;>First Name</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="fName" name= "fName">
							</div>
						</div>
						<div class="form-group row">
							<label for="lName" class="col-sm-2 col-form-label" style=text-align:left;>Last Name</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="lName" name="lName">
							</div>
						</div>
						<div class="form-group row">
							<label for="email" class="col-sm-2 col-form-label" style=text-align:left;>Email Address</label>
							<div class="col-sm-10">
								<input type="email" class="form-control" id="email" name="email">
							</div>
						</div>
						<div class="form-group row">
							<label for="password" class="col-sm-2 col-form-label" style=text-align:left;>Password</label>
							<div class="col-sm-10">
								<input type="password" class="form-control" id="password" name="password">
							</div>
						</div>
						<div class="form-group row">
							<label for="confirmPassword" class="col-sm-2 col-form-label" style=text-align:left;>Confirm Password</label>
							<div class="col-sm-10">
								<input type="password" class="form-control" id="confirmPassword" name="confirmPassword">
							</div>
						</div>

						<div class="form-group">
							<input type="submit" class="btn btn-lg btn-primary btn-block" name="submit" value="Continue to Register">
						</div>
					</form>
				</div>
				<div class="py-2 text-logo">
					<p>Already have an account? <a href="#">Sign in</a>.</p>
				</div>
			</div>
		</div>
	</container>
	<?php include 'footer.php'; ?>
</body>

</html>

<?php mysqli_close($conn); ?>