<?php
session_start();
require_once 'config.php';
$parent_page_name = '';
$page_name = '';
?>

<!DOCTYPE html>
<html>
	
<head>
	<title>My Booking | Moov</title>
	
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
		<h1 class="text-center">My Bookings</h1>
		
		<ul class="nav nav-tabs" id="myBookings" role="tablist">
			<li class="nav-item" role="presentation">
				<a class="nav-link active" id="currentBookings" data-toggle="tab" href="#current-bookings" role="tab" aria-controls="current-bookings" aria-selected="true"><h4>Current Bookings</h4></a>
			</li>
			
			<li class="nav-item" role="presentation">
				<a class="nav-link" id="pastBookings" data-toggle="tab" href="#past-bookings" role="tab" aria-controls="past-bookings" aria-selected="false"><h4>Past Bookings</h4></a>
			</li>
		</ul>
		
		<div class="tab-content" id="myAccountContent">
			<!-- Current Bookings -->
			<div class="tab-pane fade show active" id="current-bookings" role="tabpanel" aria-labelledby="currentBookings">
				<div class="row mt-4">
					<div class="col-md-5">
						<img class="car-image rounded border-0 card-img-top" src="/moov/assets/images/transparent_background.png" style="background-image: url('/moov/car-image/<?php echo $car_image_name; ?>.jpg'); height: auto !important;">
					</div>
					
					<div class="col-md-7">
						<p class="lead font-weight-bold">Kelvin</p>
						
						<p class="mb-2"><b>Booking ID:</b> #1001</p>
						
						<p class="mb-2"><b>Registration No.:</b> 123AB</p>
						
						<p class="mb-2"><b>Pick Up:</b> 2020-10-02, 04:00</p>
						
						<p class="mb-2"><b>Return:</b> 2020-10-02, 04:00</p>
						
						<button type="button" class="btn btn-secondary mt-4" data-toggle="modal" data-target="#booking1001">View More</button>
						
						<div class="modal fade" id="booking1001" tabindex="-1" aria-labelledby="booking1001Label" aria-hidden="true">
							<div class="modal-dialog modal-dialog-centered">
								<div class="modal-content">
									<div class="modal-header">
										<h4 class="modal-title" id="booking1001Label">Booking #1001</h4>
										
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									
									<div class="modal-body">
										<div class="row">
											
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<hr class="my-5">
			</div>
			
			<div class="tab-pane fade show" id="past-bookings" role="tabpanel" aria-labelledby="pastBookings">
				
			</div>
		</div>
	</div>

    <?php include 'footer.php'; ?>
</body>
	
</html>

<?php mysqli_close($conn); ?>