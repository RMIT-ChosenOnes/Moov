<?php
session_start();
require_once 'config.php';
$page_name = 'find-cars';

if (!isset($_SESSION['moov_user_booking_id']) || empty($_SESSION['moov_user_booking_id'])) {
	header('location: /moov/find-cars');
	
}
?>

<!DOCTYPE html>
<html>
	
<head>
	<title>Booking Confirmed | Moov</title>
	
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
	<link rel="icon" type="image/png" sizes="32x32" href="/moov/assets/favicon/favicon-16x16.png">
</head>

<body>
	<?php include 'header.php'; ?>
    
	<div class="container my-3 footer-align-bottom">
		<div class="jumbotron mt-5">
			<h1 class="text-center">Booking Confirmed!</h1>
			
			<p class="text-right mt-4">Booking ID: #<?php echo $_SESSION['moov_user_booking_id']; ?></p>
			
			<p class="mt-4">Dear <?php echo $_SESSION['moov_user_display_name']; ?>, thanks for driving with Moov! Your booking has confirmed!</p>
			
			<div class="row">
				<div class="col-md-8">
					<ul>
						<li><b>Car:</b> <?php echo $_SESSION['moov_user_booking_car_name']; ?></li>
						<li><b>Model:</b> <?php echo $_SESSION['moov_user_booking_car_model']; ?></li>
						<li><b>Pick Up:</b> <?php echo $_SESSION['moov_user_booking_pick_up']; ?></li>
						<li><b>Return:</b> <?php echo $_SESSION['moov_user_booking_return']; ?></li>
						<li><b>Parked Location:</b> <a href="<?php echo $_SESSION['moov_user_booking_location_url']; ?>" target="_blank"><?php echo $_SESSION['moov_user_booking_location']; ?>, Australia</a></li>
					</ul>
				</div>
				
				<div class="col-md-4 order-first order-md-last mb-4 mb-md-0">
					<img class="car-image rounded border-0 card-img-top" src="/moov/assets/images/transparent_background.png" style="background-image: url('/moov/car-image/<?php echo $_SESSION['moov_user_booking_car_image']; ?>.jpg'); height: auto !important;">
				</div>
			</div>
			
			
			<div class="embed-responsive embed-responsive-16by9 mt-4">
				<iframe class="embed-responsive-item" src="https://www.google.com/maps/embed/v1/search?q=<?php echo $_SESSION['moov_user_booking_location_longitude'] . ',' . $_SESSION['moov_user_booking_location_latitude']; ?>&key=AIzaSyASci3zGSQpHleNh10OQUpLzstQuWhvUjQ"></iframe>
			</div>
			
			<p class="mt-4">Once again, thanks for driving with Moov! If you have any issue, please <a href="/moov/contact-us">contact us</a> via Support page.</p>
		</div>
		
		<?php
		unset($_SESSION['moov_user_booking_id']);
		unset($_SESSION['moov_user_booking_pick_up']);
		unset($_SESSION['moov_user_booking_return']);
		unset($_SESSION['moov_user_booking_location']);
		unset($_SESSION['moov_user_booking_location_url']);
		unset($_SESSION['moov_user_booking_car_image']);
		unset($_SESSION['moov_user_booking_car_name']);
		unset($_SESSION['moov_user_booking_car_model']);
		unset($_SESSION['moov_user_booking_location_longitude']);
		unset($_SESSION['moov_user_booking_location_latitude']);
		?>
	</div>

    <?php include 'footer.php'; ?>
</body>
	
</html>

<?php mysqli_close($conn); ?>