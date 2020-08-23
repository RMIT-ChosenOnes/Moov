<?php
session_start();
require_once 'config.php';

echo $_SESSION['moov_user_temp_account_id'];

// SIGNUP FORM
// Receive input from client side
$fName = $_POST['fName'];
$lName = $_POST['lName'];
$licenseNumber = $_POST['licenseNumber'];
$dateOfBirth = $_POST['dateOfBirth'];
$dateOfExpiry = $_POST['dateOfExpiry'];
$countryOfIssue = $_POST['countryOfIssue'];

// Prepare error message triggers
$form_input_error = false;

// Verify email uniqueness
if ($_POST['submit']) {
    // Validate form inputs are not empty
    if ($fName == NULL || $lName == NULL || $licenseNumber == NULL || $dateOfBirth == NULL || $dateOfExpiry == NULL || $countryOfIssue == NULL) {
        $form_input_error = true;
    } else {
        $query = "insert into driving_license(account_id, first_name, last_name, license_number, date_of_birth, date_of_expiry, country_of_issue) values(1,'$fName','$lName', '$licenseNumber', '$dateOfBirth', '$dateOfExpiry', '$countryOfIssue')";
        mysqli_query($conn, $query) or die(mysqli_error($conn));
        echo '<script>window.location.href="index.php";</script>';
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

   	<div class="container my-3">
		<h1 class="text-center">Driving License</h1>
		
		<p class="mt-4">
			Dear <?php echo $_SESSION['moov_user_temp_account_first_name']; ?>, thank you for registering with Moov. Before we can proceed with your account registration, we were hoping you could prove that you are legal to drive in Australia. You are required to fill in below fields with your driving license.
		</p>
		
		<div class="container bg-secondary pt-4 pb-2 rounded">
			<form action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php'); ?>" method="post">
				<div class="form-group row align-items-center">
					<label for="dlFirstName" class="col-sm-3 col-form-label" style=text-align:left;>First Name</label>
					
					<div class="col-sm-9">
						<input type="text" class="form-control" id="dlFirstName" name="dlFirstName" value="<?php echo !empty($_POST['dlFirstName']) ? $_POST['dlFirstName'] : $_SESSION['moov_user_temp_account_first_name']; ?>">
					</div>
				</div>
				
				<div class="form-group row mt-4 align-items-center">
					<label for="dlLastName" class="col-sm-3 col-form-label" style=text-align:left;>Last Name</label>
					
					<div class="col-sm-9">
						<input type="text" class="form-control" id="dlLastName" name="dlLastName" value="<?php echo !empty($_POST['dlLastName']) ? $_POST['dlLastName'] : $_SESSION['moov_user_temp_account_last_name']; ?>">
					</div>
				</div>
				
				<div class="form-group row mt-4 align-items-center">
					<label for="dlLicenseNumber" class="col-sm-3 col-form-label" style=text-align:left;>License Number</label>
					
					<div class="col-sm-9">
						<input type="text" class="form-control" id="dlLicenseNumber" name="dlLicenseNumber" value="<?php echo $_POST['dlLicenseNumber']; ?>">
					</div>
				</div>
				
				<div class="form-group row mt-4 align-items-center">
					<label for="dlDateOfBirth" class="col-sm-3 col-form-label" style=text-align:left;>Date of Birth</label>
					
					<div class="col-sm-9">
						<input type="date" class="form-control" id="dlDateOfBirth" name="dlDateOfBirth" value="<?php echo $_POST['dlDateOfBirth']; ?>">
					</div>
				</div>
				
				<div class="form-group row mt-4 align-items-center">
					<label for="dlDateOfExpiry" class="col-sm-3 col-form-label" style=text-align:left;>Date of Expiry</label>
					
					<div class="col-sm-9">
						<input type="date" class="form-control" id="dlDateOfExpiry" name="dlDateOfExpiry" value="<?php echo $_POST['dlDateOfExpiry']; ?>">
					</div>
				</div>
				
				<div class="form-group row mt-4 align-items-center">
					<label for="dlCountryOfIssuer" class="col-sm-3 col-form-label" style=text-align:left;>Country of Issuer</label>
					
					<div class="col-sm-9"  class="form-control">
						<select name="dlCountryOfIssuer"  class="form-control">
							<?php
							$sql = mysqli_query($conn, "SELECT country FROM country");
							while ($row = $sql->fetch_assoc()) {
								echo "<option value=\"country\">" . $row['country'] . "</option>";
							}
							?>
						</select>
					</div>
				</div>

				<button type="submit" class="btn btn-secondary btn-block mt-5">Register</button>
			</form>
		</div>
	</div>

    <?php include 'footer.php'; ?>
</body>

</html>

<?php mysqli_close($conn); ?>