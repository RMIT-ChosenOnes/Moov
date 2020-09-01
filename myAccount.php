<!DOCTYPE html>
<html>

<head>
    <title>My Account | Moov</title>

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
    <link rel="icon" type="image/png" sizes="16x16" href="/moov/assets/favicon/favicon-16x16.png">
</head>

<body>

    <?php include 'header.php'; ?>

    <div class="container my-3">
        <h1 class="text-center">My Account</h1>
        <ul class="nav nav-tabs" id="myTab" role="tablist">

            <li class="nav-item">
                <a class="nav-link active" id="my_account-tab" data-toggle="tab" href="#my_account" role="tab" aria-controls="home" aria-selected="true"><h3>My Account</h3></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="My_license-tab" data-toggle="tab" href="#My_license" role="tab" aria-controls="contact" aria-selected="false"><h3>My License</h3></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="payment_method-tab" data-toggle="tab" href="#payment_method" role="tab" aria-controls="contact" aria-selected="false"><h3>Payment Method</h3></a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="my_account" role="tabpanel" aria-labelledby="my_account-tab">
                <?php include 'includes/locations.php' ?>
                <div class="tab-pane fade" id="My_license" role="tabpanel" aria-labelledby="My_license-tab">
                    <?php include 'includes/tours.php' ?>
                    <div class="tab-pane fade" id="payment_method" role="tabpanel" aria-labelledby="payment_method-tab">
                        <?php include 'includes/tour_type.php' ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>

<?php mysqli_close($conn); ?>