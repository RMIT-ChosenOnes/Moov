<?php
$title = 'Upload';
require_once 'config.php';

// Receive input from client side
$fname = mysqli_real_escape_string($conn, $_POST['fname']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$subject = mysqli_real_escape_string($conn, $_POST['subject']);
$inquiry = mysqli_real_escape_string($conn, $_POST['inquiry']);

// Prepare error message trigger
$form_input_error = false;
$form_success_message = false;
if ($_POST['submit']) {
    // Validate form inputs are not empty
    if ($fname == NULL || $email == NULL || $subject == NULL || $inquiry == NULL) {
        $form_input_error = true;
    } else {
        // Make query and attempt to insert into database
        $query = "INSERT INTO contact_us (full_name, email_address, subject, inquiry) VALUES ('$fname', '$email', '$subject', '$inquiry')";
        $success = mysqli_query($conn, $query);
        if (isset($success)) {
            $form_success_message = true;
        } else {
            echo 'SQL query error: ' . mysqli_error($conn);
            mysqli_close($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Contact us | Moov</title>

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

        function gtag() {
            dataLayer.push(arguments);
        }
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
    <link rel="icon" type="image/png" sizes="32x32" href="/moov/assets/favicon/favicon-16x16.png">
</head>

<body id="contact-us">
    <?php include 'header.php'; ?>
    <div class="container my-3 footer-align-bottom d-flex">
        <div id="find-car-card" class="card m-auto py-5 px-4">
            <form class="content-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <h1 class="text-center">Contact Us</h1>
                <?php
                if ($form_input_error == true)
                    echo '<div class="alert alert-warning" role="alert">You have not completely filled the form.</div>';
                if($form_success_message == true)
                    echo '<div class="alert alert-success" role="alert">Your support request has been submitted. One of our team members will be in contact with you in 3-5 days.</div>';
                
                ?>
                <div class="form-group">
                    <input type="text" class="form-control" name="fname" id="fname" placeholder="Full Name" required="required">
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" name="email" id="email" placeholder="Email Address" required="required">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject" required="required">
                </div>
                <div class="form-group">
                    <textarea type="text" class="form-control" name="inquiry" id="inquiry" placeholder="Please type your inquiry here.." rows="4"></textarea>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-lg btn-primary btn-block" name="submit" value="Submit">
                </div>
            </form>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>