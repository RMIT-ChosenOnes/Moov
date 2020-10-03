<?php
session_start();
require_once 'config.php';
$page_name = 'find-cars';

$search_filename = array('- ', ' ', '-', '.');
$replace_filename = array('_', '_', '_', '_');

$checkout_url = 'id=' . $_GET['id'] . '&bookPickUpDate=' . $_GET['bookPickUpDate'] . '&bookPickUpTime=' . $_GET['bookPickUpTime'] . '&bookReturnDate=' . $_GET['bookReturnDate'] . '&bookReturnTime=' . $_GET['bookReturnTime'];

if (empty($_GET['id']) || empty($_GET['bookPickUpDate']) || empty($_GET['bookPickUpTime']) || empty($_GET['bookReturnDate']) || empty($_GET['bookReturnTime'])) {
    header('location: /moov/find-cars');
    
} elseif (!isset($_SESSION['moov_user_logged_in']) || $_SESSION['moov_user_logged_in'] != TRUE) {
    header('location: /moov/login?url=/moov/booking?' . urlencode($checkout_url));
    
}
?>

<!DOCTYPE html>
<html>
	
<head>
	<title>Booking | Moov</title>
	
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
		<h1 class="text-center">Confirm Booking</h1>
		
		<?php
		$get_car_details_sql = 'SELECT * FROM moov_portal.car AS car LEFT JOIN moov_portal.car_location ON car.car_id = moov_portal.car_location.car_id WHERE car.car_id = ?';
		$get_car_details_stmt = mysqli_prepare($conn, $get_car_details_sql);
		
		mysqli_stmt_bind_param($get_car_details_stmt, 'i', $param_car_id);
		$param_car_id = $_GET['id'];
		
		if (mysqli_stmt_execute($get_car_details_stmt)) {
			$get_car_details = mysqli_stmt_get_result($get_car_details_stmt);
			
			while ($car_details = mysqli_fetch_assoc($get_car_details)) {
				// Get car brand
				$get_brand_sql = 'SELECT brand FROM moov_portal.car_brand WHERE brand_id = ?';
				$get_brand_stmt = mysqli_prepare($conn, $get_brand_sql);

				mysqli_stmt_bind_param($get_brand_stmt, 'i', $param_car_brand);
				$param_car_brand = $car_details['brand'];

				if (mysqli_stmt_execute($get_brand_stmt)) {
					$get_brand = mysqli_stmt_get_result($get_brand_stmt);

					while ($brand = mysqli_fetch_assoc($get_brand)) {
						$car_brand = $brand['brand'];

					}
				}
				
				// Get car fuel type
				$get_fuel_type_sql = 'SELECT fuel FROM moov_portal.car_fuel WHERE fuel_id = ?';
				$get_fuel_type_stmt = mysqli_prepare($conn, $get_fuel_type_sql);

				mysqli_stmt_bind_param($get_fuel_type_stmt, 'i', $param_fuel_type);
				$param_fuel_type = $car_details['fuel_type'];

				if (mysqli_stmt_execute($get_fuel_type_stmt)) {
					$get_fuel_type = mysqli_stmt_get_result($get_fuel_type_stmt);

					while ($fuel_type = mysqli_fetch_assoc($get_fuel_type)) {
						$car_fuel_type = $fuel_type['fuel'];

					}
				}
                
                mysqli_stmt_close($get_brand_stmt);
                mysqli_stmt_close($get_fuel_type_stmt);
				
				$car_friendly_name = $car_details['name'];
				$car_model = $car_details['model'];
				$car_seats = $car_details['seat'];
				$car_doors = $car_details['door'];
				$car_price_per_hour = $car_details['price_per_hour'];
				$car_location = $car_details['address_1'] . ',<br/>' . (!empty($car_details['address_2']) ? $car_details['address_2'] . ',<br/>' : '') . $car_details['suburb'] . ' ' . $car_details['postal_code'] . ' ' . strtoupper($car_details['state']);
				$car_location_url = 'https://www.google.com/maps?q=' . $car_details['longitude'] . ',' . $car_details['latitude'];
				
			}
		}
        
        mysqli_stmt_close($get_car_details_stmt);
		
		$car_temp_image_name = strtolower($car_brand . '_' . $car_model . '_' . $car_friendly_name);
		$car_image_name = str_replace($search_filename, $replace_filename, $car_temp_image_name);
		?>
		<div class="row mt-4 mt-md-5">
			<div class="col-md-4">
				<img class="car-image rounded border-0 mt-5" src="/moov/assets/images/transparent_background.png" style="background-image: url('/moov/car-image/<?php echo $car_image_name; ?>.jpg'); height: auto !important;">
			</div>
			
			<div class="col-md-4 mt-5 mt-md-0">
				<h4>Car Details</h4>
				
				<p class="font-weight-bold lead mb-2"><?php echo $car_friendly_name; ?></p>
				
				<p class="mb-0"><?php echo $car_brand . ' ' . $car_model; ?></p>
				<p class="mb-0"><?php echo $car_fuel_type . ' | ' . $car_seats . ' seats | ' . $car_doors . ' doors'; ?></p>
				<p class="mb-2">A$<?php echo number_format($car_price_per_hour, 2, '.', ','); ?> per hour</p>
				
				<p class="mb-2"><?php echo $car_location; ?></p>
				
				<p class="mb-0"><u><a href="<?php echo $car_location_url; ?>" target="_blank">View in Google Maps</a></u></p>
			</div>
			
			<div class="col-md-4 mt-5 mt-md-0">
				<?php
				$pick_up_date = $_GET['bookPickUpDate'] . ', ' . $_GET['bookPickUpTime'];
				$return_date = $_GET['bookReturnDate'] . ', ' . $_GET['bookReturnTime'];
				
				$book_pick_up_date = date_create($pick_up_date);
        		$book_return_date = date_create($return_date);
				
				$duration = date_diff($book_pick_up_date, $book_return_date);
				
				$duration_day = (int)$duration->format('%d');
				$duration_hour = (int)$duration->format('%h');
				$duration_minute = (int)$duration->format('%i');
				
                $total_duration_hour_min = $duration_hour + ($duration_minute * (1 / 60));
				$total_duration = ($duration_day * 24) + $total_duration_hour_min;
				$car_price_per_day = $car_price_per_hour * 6;
				
				if (!empty($duration_day)) {
					if ($duration_day > 1) {
						$duration_day_string = $duration_day . ' days ';
						
					} else {
						$duration_day_string = $duration_day . ' day ';
						
					}
                    
                    // Check if hour is empty and less than 6
                    if (!empty($duration_hour) && $total_duration_hour_min <= 6) {
                        $total_price = ($duration_day * $car_price_per_day) + ($total_duration_hour_min * $car_price_per_hour);
                        $discount = TRUE;
                        
                    } elseif (!empty($duration_hour) && $total_duration_hour_min > 6) { // Check if hour is empty and more than 6
                        $total_price = ($duration_day + 1) * $car_price_per_day;
                        $discount = TRUE;
                        
                    } else {
                        $total_price = $duration_day * $car_price_per_day;
                        $discount = TRUE;
                        
                    }
				} else {
					$duration_day_string = '';
					
                    // Check if hour is more than 6 and day is 0
					if ($duration_hour > 6 && $duration_day == 0) {
						$total_price = $car_price_per_day;
                        $discount = TRUE;
						
					} else {
						$total_price = $total_duration * $car_price_per_hour;
						
                    }
				}
				
				if (!empty($duration_hour)) {
					if ($duration_hour > 1) {
						$duration_hour_string = $duration_hour . ' hours ';
						
					} else {
						$duration_hour_string = $duration_hour . ' hour ';
						
					}
				} else {
					$duration_hour_string = '';
					
				}
				
				if (!empty($duration_minute)) {
					$duration_minute_string = $duration_minute . ' minutes';
					
				} else {
					$duration_minute_string = '';
					
				}
				
				$duration_string = $duration_day_string . $duration_hour_string . $duration_minute_string;
                $discount_price = $total_duration * $car_price_per_hour;
                $discounted_total_price = $discount_price - $total_price;
				?>
				
				<h4>Booking Details</h4>
				
				<p class="mb-2"><span class="small font-weight-bold">Pick-Up:</span> <?php echo $pick_up_date; ?></p>
				
				<p class="mb-2"><span class="small font-weight-bold">Return:</span> <?php echo $return_date; ?></p>
				
				<p><span class="small font-weight-bold">Duration:</span> <?php echo $duration_string; ?></p>
                
                <hr class="my-3">
                
                <p class="mb-0"><b>Subtotal:</b> A$<?php echo number_format($discount_price, 2, '.', ',');?></p>
                
                <?php
                if ($discount == TRUE) {
                    echo '<p class="text-danger mb-0"><b>Discount:</b> A$' . number_format($discounted_total_price, 2, '.', ',') . '</p>';
                    
                }
                ?>
				
                <p class="mt-3"><b>Total Price <span class="text-muted font-italic font-weight-bold small">(Excluded GST)</span>:</b> A$<?php echo number_format($total_price, 2, '.', ',');?></p>
			</div>
		</div>
        
        <div class="row mt-5">
            <div class="col-md-6">
                <a class="btn btn-primary btn-block" href="javascript:history.go(-1)" role="button">Modify Booking</a>
            </div>
            
            <div class="col-md-6 mt-4 mt-md-0">
                <a class="btn btn-secondary btn-block" href="/moov/checkout?<?php echo $checkout_url; ?>" role="button">Checkout</a>
            </div>
        </div>
	</div>

    <?php include 'footer.php'; ?>
</body>
	
</html>

<?php mysqli_close($conn); ?>