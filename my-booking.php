<?php
session_start();
require_once 'config.php';
$page_name = basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php');

$search_filename = array('- ', ' ', '-', '.');
$replace_filename = array('_', '_', '_', '_');

if (!isset($_SESSION['moov_user_logged_in']) || $_SESSION['moov_user_logged_in'] != TRUE) {
    header('location: /moov/login?url=' . urlencode('/moov/my-booking'));
    
}
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
                
                <?php
                $get_current_booking_sql = 'SELECT *, IF(pick_up_date > CURRENT_TIMESTAMP, \'Future\', \'Active\') AS status FROM booking WHERE (pick_up_date >= CURRENT_TIMESTAMP) OR (pick_up_date < CURRENT_TIMESTAMP AND return_date > CURRENT_TIMESTAMP) AND (customer_id = ?)';
                $get_current_booking_stmt = mysqli_prepare($conn, $get_current_booking_sql);

                mysqli_stmt_bind_param($get_current_booking_stmt, 'i', $param_customer_id);
                $param_customer_id = $_SESSION['moov_user_account_id'];
                
                if (mysqli_stmt_execute($get_current_booking_stmt)) {
                    $get_current_booking = mysqli_stmt_get_result($get_current_booking_stmt);
                    
                    while ($current_booking = mysqli_fetch_assoc($get_current_booking)) {
                        $booking_id = $current_booking['booking_id'];
                        $booking_pick_up = $current_booking['pick_up_date'];
                        $booking_return = $current_booking['return_date'];
                        $booking_status = $current_booking['status'];

                        $get_car_details_sql = 'SELECT * FROM moov_portal.car AS car LEFT JOIN moov_portal.car_location ON car.car_id = moov_portal.car_location.car_id WHERE car.car_id = ?';
                        $get_car_details_stmt = mysqli_prepare($conn, $get_car_details_sql);

                        mysqli_stmt_bind_param($get_car_details_stmt, 'i', $param_car_id);
                        $param_car_id = $current_booking['car_id'];

                        if (mysqli_stmt_execute($get_car_details_stmt)) {
                            $get_car_details = mysqli_stmt_get_result($get_car_details_stmt);

                            while ($car_details = mysqli_fetch_assoc($get_car_details)) {
                                $car_friendly_name = $car_details['name'];
                                $car_model = $car_details['model'];
                                $car_registration_number = $car_details['registration_number'];
                                $car_seat = $car_details['seat'];
                                $car_door = $car_details['door'];
                                $car_color = $car_details['color'];
                                $car_location = $car_details['address_1'] . (!empty($car_details['address_2']) ? ', ' . $car_details['address_2'] : '') . ', ' . $car_details['suburb'] . ' ' . $car_details['postal_code'] . ' ' . strtoupper($car_details['state']) . ', Australia';
                                $car_location_longitude = $car_details['longitude'];
                                $car_location_latitude = $car_details['latitude'];

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

                                // Get car type
                                $get_type_sql = 'SELECT type FROM moov_portal.car_type WHERE type_id = ?';
                                $get_type_stmt = mysqli_prepare($conn, $get_type_sql);

                                mysqli_stmt_bind_param($get_type_stmt, 'i', $param_car_type);
                                $param_car_type = $car_details['car_type'];

                                if (mysqli_stmt_execute($get_type_stmt)) {
                                    $get_type = mysqli_stmt_get_result($get_type_stmt);

                                    while ($type = mysqli_fetch_assoc($get_type)) {
                                        $car_type = $type['type'];

                                    }
                                }

                                // Get car transmission type
                                $get_transmission_type_sql = 'SELECT transmission FROM moov_portal.car_transmission WHERE transmission_id = ?';
                                $get_transmission_type_stmt = mysqli_prepare($conn, $get_transmission_type_sql);

                                mysqli_stmt_bind_param($get_transmission_type_stmt, 'i', $param_car_transmission_type);
                                $param_car_transmission_type = $car_details['transmission_type'];

                                if (mysqli_stmt_execute($get_transmission_type_stmt)) {
                                    $get_transmission_type = mysqli_stmt_get_result($get_transmission_type_stmt);

                                    while ($transmission_type = mysqli_fetch_assoc($get_transmission_type)) {
                                        $car_transmission_type = $transmission_type['transmission'];

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
                                mysqli_stmt_close($get_type_stmt);
                                mysqli_stmt_close($get_transmission_type_stmt);
                                mysqli_stmt_close($get_fuel_type_stmt);

                                $car_temp_image_name = strtolower($car_brand . '_' . $car_details['model'] . '_' . $car_details['name']);
                                $car_image_name = str_replace($search_filename, $replace_filename, $car_temp_image_name);

                            }
                        }
                        
                        echo '
                            <div class="row mt-4">
                                <div class="col-md-5">
                                    <img class="car-image rounded border-0" src="/moov/assets/images/transparent_background.png" style="background-image: url(\'/moov/car-image/' . $car_image_name . '.jpg\'); height: auto !important;">
                                </div>

                                <div class="col-md-7 mt-4 mt-md-0">
                                    <p class="lead font-weight-bold">' . $car_friendly_name . ($booking_status == 'Active' ? ' <span class="badge badge-danger align-middle">In Progress...</span>' : '') . '</p>

                                    <p class="mb-2"><b>Booking ID:</b> #' . $booking_id . '</p>

                                    <p class="mb-2"><b>Registration No.:</b> ' . $car_registration_number . '</p>

                                    <p class="mb-2"><b>Pick Up:</b> ' . date('Y-m-d, H:s', strtotime($booking_pick_up)) . '</p>

                                    <p class="mb-2"><b>Return:</b> ' . date('Y-m-d, H:s', strtotime($booking_return)) . '</p>

                                    <button type="button" class="btn btn-secondary mt-4" data-toggle="modal" data-target="#booking' . $booking_id . '">View More</button>

                                    <div class="modal fade" id="booking' . $booking_id . '" tabindex="-1" aria-labelledby="booking' . $booking_id . 'Label" aria-hidden="true">
                                        <div class="modal-lg modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="booking' . $booking_id . 'Label">Booking #' . $booking_id . ($booking_status == 'Active' ? ' <span class="badge badge-danger align-middle">In Progress...</span>' : '') . '</h4>

                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <img class="car-image rounded border-0 mt-4" src="/moov/assets/images/transparent_background.png" style="background-image: url(\'/moov/car-image/' . $car_image_name . '.jpg\'); height: auto !important;">
                                                        </div>

                                                        <div class="col-md-6 mt-4 mt-md-0">
                                                            <p class="lead font-weight-bold">' . $car_friendly_name . '</p>

                                                            <p class="mb-3">' . $car_brand . ' ' . $car_model . ' ('. $car_transmission_type . ')</p>

                                                            <p class="mb-1"><b>Registration No.:</b> ' . $car_registration_number . '</p>
                                                            <p class="mb-1"><b>Fuel:</b> ' . $car_fuel_type . '</p>
                                                            <p class="mb-1"><b>Color:</b> ' . $car_color . '</p>
                                                            <p class="mb-3"><b>Other Features:</b> ' .$car_seat . ' seats | ' . $car_door . ' doors</p>

                                                            <p class="mb-1"><b>Pick Up:</b> ' . date('Y-m-d, H:s', strtotime($booking_pick_up)) . '</p>

                                                            <p class="mb-2"><b>Return:</b> ' . date('Y-m-d, H:s', strtotime($booking_return)) . '</p>
                                                        </div>
                                                    </div>

                                                    <div class="row mt-3 mt-md-4">
                                                        <div class="col-12">
                                                            <p class="mb-3"><b>Parked Location:</b> ' . $car_location . '</p>

                                                            <div class="embed-responsive embed-responsive-16by9 mt-4">
                                                                <iframe class="embed-responsive-item" src="https://www.google.com/maps/embed/v1/search?q=' . $car_location_longitude . ',' . $car_location_latitude . '&key=//AIzaSyASci3zGSQpHleNh10OQUpLzstQuWhvUjQ"></iframe>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                                                    
                                                    <a role="button" href="#" class="btn btn-secondary disabled">Report</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-5">
                            ';
                        
                    }
                }
                ?>

			</div>
			
			<div class="tab-pane fade show" id="past-bookings" role="tabpanel" aria-labelledby="pastBookings">
				
			</div>
		</div>
	</div>

    <?php include 'footer.php'; ?>
</body>
	
</html>

<?php mysqli_close($conn); ?>