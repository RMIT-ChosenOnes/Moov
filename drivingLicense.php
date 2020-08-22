<?php
session_start();
require_once 'config.php';
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
    <title>Driving License | Moov</title>

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
            <h1>Driving License</h1>
            <div id="registerCard" class="card bg-secondary mx-auto">

                <div class="card-body">
                    <form class="mt-5 mx-lg-5" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <?php
                        // Display error message if form is missing inputs
                        if ($form_input_error == true)
                            echo '<div class="alert alert-warning" role="alert">You have not completely filled the signup form.</div>';
                        // Display error message if passwords do not match
                        elseif ($password_match_error == true)
                            echo '<div class="alert alert-danger" role="alert">Those passwords did not match. Please try again.</div>';
                        // Display error message if email was found in datastore
                        elseif ($email_exists_error == true)
                            echo '<div class="alert alert-warning" role="alert">The email you have entered currently belongs to an account. Please try another email.</div>';
                        // Display error message for password strength
                        elseif ($password_strength_error == true)
                            echo '<div class="alert alert-danger" role="alert">Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.</div>';
                        ?>
                        <div class="form-group row">
                            <label for="fName" class="col-sm-2 col-form-label" style=text-align:left;>First Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="fName" name="fName">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="lName" class="col-sm-2 col-form-label" style=text-align:left;>Last Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="lName" name="lName">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="licenseNumber" class="col-sm-2 col-form-label" style=text-align:left;>License Number</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="licenseNumber" name="licenseNumber">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="dateOfBirth" class="col-sm-2 col-form-label" style=text-align:left;>Date of Birth</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" id="dateOfBirth" name="dateOfBirth">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="dateOfExpiry" class="col-sm-2 col-form-label" style=text-align:left;>Date of Expiry</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" id="dateOfExpiry" name="dateOfExpiry">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="countryOfIssue" class="col-sm-2 col-form-label" style=text-align:left;>Country of Issue</label>
                            <div class="col-sm-10"  class="form-control">
                                <select name="countryOfIssue"  class="form-control">
                                    <?php
                                    $sql = mysqli_query($conn, "SELECT country FROM country");
                                    while ($row = $sql->fetch_assoc()) {
                                        echo "<option value=\"country\">" . $row['country'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="submit" class="btn btn-lg btn-secondary btn-block" name="submit" value="Register">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </container>
    <?php include 'footer.php'; ?>
</body>

</html>

<?php mysqli_close($conn); ?>