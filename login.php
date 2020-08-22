<?php
session_start();
require_once 'config.php';
// SIGNUP FORM
// Receive input from client side
$email = strtolower($_POST['email']);
$password = $_POST['password'];
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Prepare error message triggers
$form_input_error = false;
$login_fail_error = false;

// Verify email uniqueness
if ($_POST['submit']) {
    // Validate form inputs are not empty
    if ($email == NULL || $password == NULL) {
        $form_input_error = true;
    } 
    else {
        // Search datastore for if email input exists
        $query = "select * from account where email_address = '$email'";
        $record = mysqli_query($conn, $query) or die(mysqli_error($conn));
        $row_count = mysqli_num_rows($record);
        if ($row_count == 0) {
            // Trigger error alert for email existing in datastore
            $login_fail_error = true;
        } else {
            //verify password
            $query = "select * from account where email_address = '$email'";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_array($result)) {
                    if (password_verify($password, $row["password"])) {
                        //return true;  
                        $_SESSION["email"] = $email;
                        header("location:index.php");
                    } else {
                        // Trigger error alert for passwords not matching
                        $login_fail_error = true;
                    }
                }
            }
        }
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

    <container id="userLogin" class="d-flex m-0 p-0 vh-100">

        <div class="container m-auto text-center">
            <h1>LOGIN</h1>
            <div id="registerCard" class="card bg-secondary mx-auto">

                <div class="card-body">
                    <form class="mt-5 mx-lg-5" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <?php
                        // Display error message if form is missing inputs
                        if ($form_input_error == true)
                            echo '<div class="alert alert-warning" role="alert">You have not completely filled the signup form.</div>';
                        elseif ($login_fail_error == true)
                            echo '<div class="alert alert-danger" role="alert">Your details were incorrect and we could not log you in. Please try again.</div>';
                        ?>
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

                        <div class="form-group">
                            <input type="submit" class="btn btn-lg btn-primary btn-block" name="submit" value="Login">
                        </div>
                    </form>
                </div>
                <div class="py-2 text-logo">
                    <p>Dont have an account? <a href="register">Register now</a>.</p>
                </div>
            </div>
        </div>
    </container>
    <?php include 'footer.php'; ?>
</body>

</html>

<?php mysqli_close($conn); ?>