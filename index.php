<?php
session_start();
require_once 'config.php';
$page_name = 'index';
?>

<!DOCTYPE html>
<html>

<head>
	<title>Moov</title>

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

	<div id="landing-page">
		<div id="hero">
			<div class="container">
				<div id="hero-detail">
					<h1>The cars we drive say a lot about us</h1>
					<h3>Discover what Moov has to offer</h3>
				</div>
				<a class="btn btn-lg btn-primary" href="register.php">Sign up today</a>
			</div>
		</div>
		<div id="about">
			<div class="container">
				<div class="row">
					<div class="col">
						<h2>How It Works</h2>
					</div>
				</div>
				<div class="row fluid">
					<div class="about-item col-md-4 col-sm-12 col-xs-12">
						<i class="fas fa-file-image fa-4x"></i>
						<h3>Find a car</h3>
						<p>Find a car that is the most nearest to you and suits for your needs </p>
					</div>
					<div class="about-item col-md-4 col-sm-12 col-xs-12">
						<i class="fas fa-cloud-upload-alt fa-4x"></i>
						<h3>Book the car</h3>
						<p>Book with us with for most affordable prices and variety of cars</p>
					</div>
					<div class="about-item col-md-4 col-sm-12 col-xs-12">
						<i class="fas fa-globe-americas fa-4x"></i>
						<h3>Pick/Drop the car</h3>
						<p>Pick up the car from provided location and drop it back to the same location</p>
					</div>
				</div>
			</div>
		</div>
		<div id="features">
			<div class="container">
				<div class="row">
					<div class="col">
						<h2>Main Heading</h2>
					</div>
				</div>
				<div class="row feature-row">
					<div class="col-sm feature-image">
						<img src="assets/images/landing_page_image.jpg" alt="Image 1">
					</div>
					<div class="col-sm feature-detail">
						<h3>Heading 1</h3>
						<p>More content</p>
					</div>
				</div>
				<div class="row feature-row">
					<div class="col-sm feature-detail">
						<h3>Heading 2</h3>
						<p>More content</p>
					</div>
					<div class="col-sm feature-image">
						<img src="assets/images/landing_page_image.jpg" alt="Image 2">
					</div>
				</div>
				<div class="row feature-row">
					<div class="col-sm feature-image">
						<img src="assets/images/landing_page_image.jpg" alt="Image 3">
					</div>
					<div class="col-sm feature-detail">
						<h3>Heading 3</h3>
						<p>More content</p>
					</div>
				</div>
				<div class="row feature-row">
					<div class="col-sm feature-detail">
						<h3>Heading 4</h3>
						<p>	More content</p>
					</div>
					<div class="col-sm feature-image">
						<img src="assets/images/landing_page_image.jpg" alt="Image 4">
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php include 'footer.php'; ?>
</body>

</html>

<?php mysqli_close($conn); ?>