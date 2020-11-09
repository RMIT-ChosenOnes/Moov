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
                $get_current_booking_sql = 'SELECT *, IF(pick_up_date > CURRENT_TIMESTAMP, \'Future\', \'Active\') AS status FROM booking WHERE ((pick_up_date >= CURRENT_TIMESTAMP) OR (pick_up_date < CURRENT_TIMESTAMP AND return_date > CURRENT_TIMESTAMP)) AND (customer_id = ?) AND (status = ?)';
                $get_current_booking_stmt = mysqli_prepare($conn, $get_current_booking_sql);

                mysqli_stmt_bind_param($get_current_booking_stmt, 'is', $param_customer_id, $param_booking_status);
                $param_customer_id = $_SESSION['moov_user_account_id'];
				$param_booking_status = 'Paid';
                
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
                        
                        mysqli_stmt_close($get_car_details_stmt);
                        
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

								<div class="row mt-4 mx-auto">
									<button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#booking' . $booking_id . '">View More</button>
								
									' . ($booking_status != 'Active' ? '' : '<a href="/moov/reports?id=' . $booking_id . '" role="button" class="btn btn-primary ml-3">Report This Booking</a>') . '
								</div>

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
                                                        <p class="mb-3"><b>Parked Location:</b> <a href="https://www.google.com/maps?q=' . $car_location_longitude . ',' . $car_location_latitude . '" target="_blank">' . $car_location . '</a></p>

                                                        <div class="embed-responsive embed-responsive-16by9 mt-4">
                                                            <iframe class="embed-responsive-item" src="https://www.google.com/maps/embed/v1/search?q=' . $car_location_longitude . ',' . $car_location_latitude . '&key=AIzaSyASci3zGSQpHleNh10OQUpLzstQuWhvUjQ"></iframe>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

												' . ($booking_status != 'Active' ? '' : '<a href="/moov/reports?id=' . $booking_id . '" role="button" class="btn btn-primary">Report This Booking</a>') . '
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-5">
                        ';
                        
                    }
                    
                    if (mysqli_num_rows($get_current_booking) == 0) {
                        echo '
                        <div class="jumbotron mt-4">
                            <h4 class="text-center display-4">You do not have any active or upcoming booking.</h4>
                        </div>
                        ';
                        
                    }
                }
                
                mysqli_stmt_close($get_current_booking_stmt);
                
                ?>
			</div>
			
			<div class="tab-pane fade show" id="past-bookings" role="tabpanel" aria-labelledby="pastBookings">
				<?php
                $get_past_booking_sql = 'SELECT * FROM booking WHERE pick_up_date < CURRENT_TIMESTAMP AND return_date < CURRENT_TIMESTAMP AND customer_id = ? AND status = ? ORDER BY pick_up_date DESC';
                $get_past_booking_stmt = mysqli_prepare($conn, $get_past_booking_sql);

                mysqli_stmt_bind_param($get_past_booking_stmt, 'is', $param_customer_id, $param_booking_status);
                
                if (mysqli_stmt_execute($get_past_booking_stmt)) {
                    $get_past_booking = mysqli_stmt_get_result($get_past_booking_stmt);
					
                    while ($past_booking = mysqli_fetch_assoc($get_past_booking)) {
                        $past_booking_id = $past_booking['booking_id'];
                        $past_booking_pick_up = $past_booking['pick_up_date'];
                        $past_booking_return = $past_booking['return_date'];
                        $past_booking_amount = $past_booking['amount'];

                        $get_past_car_details_sql = 'SELECT * FROM moov_portal.car AS car LEFT JOIN moov_portal.car_location ON car.car_id = moov_portal.car_location.car_id WHERE car.car_id = ?';
                        $get_past_car_details_stmt = mysqli_prepare($conn, $get_past_car_details_sql);

                        mysqli_stmt_bind_param($get_past_car_details_stmt, 'i', $param_car_id);
                        $param_car_id = $past_booking['car_id'];

                        if (mysqli_stmt_execute($get_past_car_details_stmt)) {
                            $get_past_car_details = mysqli_stmt_get_result($get_past_car_details_stmt);

                            while ($past_car_details = mysqli_fetch_assoc($get_past_car_details)) {
                                $past_car_friendly_name = $past_car_details['name'];
                                $past_car_model = $past_car_details['model'];
                                $past_car_location = $past_car_details['address_1'] . (!empty($past_car_details['address_2']) ? ', ' . $past_car_details['address_2'] : '') . ', ' . $past_car_details['suburb'] . ' ' . $past_car_details['postal_code'] . ' ' . strtoupper($past_car_details['state']) . ', Australia';
                                $past_car_price_per_hour = $past_car_details['price_per_hour'];

                                // Get car brand
                                $get_past_brand_sql = 'SELECT brand FROM moov_portal.car_brand WHERE brand_id = ?';
                                $get_past_brand_stmt = mysqli_prepare($conn, $get_past_brand_sql);

                                mysqli_stmt_bind_param($get_past_brand_stmt, 'i', $param_past_car_brand);
                                $param_past_car_brand = $past_car_details['brand'];

                                if (mysqli_stmt_execute($get_past_brand_stmt)) {
                                    $get_past_brand = mysqli_stmt_get_result($get_past_brand_stmt);

                                    while ($past_brand = mysqli_fetch_assoc($get_past_brand)) {
                                        $past_car_brand = $past_brand['brand'];

                                    }
                                }

                                mysqli_stmt_close($get_past_brand_stmt);

                                $past_car_temp_image_name = strtolower($past_car_brand . '_' . $past_car_details['model'] . '_' . $past_car_details['name']);
                                $past_car_image_name = str_replace($search_filename, $replace_filename, $past_car_temp_image_name);

                            }
                        }
                        
                        mysqli_stmt_close($get_past_car_details_stmt);
                        
                        $get_past_payment_details_sql = 'SELECT * FROM booking_payment WHERE payment_id = ?';
                        $get_past_payment_details_stmt = mysqli_prepare($conn, $get_past_payment_details_sql);
                        
                        mysqli_stmt_bind_param($get_past_payment_details_stmt, 'i', $param_past_payment_id);
                        $param_past_payment_id = $past_booking['payment_id'];
                        
                        if (mysqli_stmt_execute($get_past_payment_details_stmt)) {
                            $get_past_payment_details = mysqli_stmt_get_result($get_past_payment_details_stmt);
                            
                            while ($past_payment_details = mysqli_fetch_assoc($get_past_payment_details)) {
                                $past_payment_method = ucwords(str_replace('_', ' ', $past_payment_details['payment_method']));
                                $past_payment_card_number = substr($past_payment_details['card_number'], -4);
                                
                            }
                        }
						
						mysqli_stmt_close($get_past_payment_details_stmt);
                        
                        $date_diff = date_diff(date_create($past_booking_return), date_create(date('Y-m-d, H:i')));
                        $date_diff_days = (int)$date_diff->format('%a');
                        
                        echo '
                        <div class="row mt-4 align-items-center">
                            <div class="col-md-5">
                                <img class="car-image rounded border-0" src="/moov/assets/images/transparent_background.png" style="background-image: url(\'/moov/car-image/' . $past_car_image_name . '.jpg\'); height: auto !important;">
                            </div>

                            <div class="col-md-7 mt-4 mt-md-0">
                                <p class="lead font-weight-bold">' . date('Y-m-d, H:s', strtotime($past_booking_pick_up)) . ' - ' . date('Y-m-d, H:s', strtotime($past_booking_return)) . '</p>

                                <p class="mb-2"><b>' . $past_car_friendly_name . '</b> (Booking #' . $past_booking_id . '), ' . $past_car_brand . ' ' . $past_car_model . '</p>

                                <p class="mb-2"><b>Parked Location:</b> ' . $past_car_location . '</p>

                                <p class="mb-2"><b>Price per Hour:</b> A$' . number_format($past_car_price_per_hour, 2, '.', ',') . '</p>

                                <p class="mb-2"><b>Payment Method:</b> ' . $past_payment_method . ' <span class="font-italic">(ending with ' . $past_payment_card_number . ')</span></p>

                                <p class="mb-0"><b>Total Paid Amount <span class="small font-italic">(Incl. GST)</span>:</b> A$' . number_format($past_booking_amount, 2, '.', ',') . '</p>
								
								' . ($date_diff_days > 20 ? '' : '<a href="/moov/reports?id=' . $past_booking_id . '" role="button" class="btn btn-primary mt-4">Report This Booking</a>') . '
                            </div>
                        </div>

                        <hr class="my-5">
                        ';
						
						
                        
                        //echo ;
                        
                    }
					
					if (mysqli_num_rows($get_past_booking) == 0) {
						echo '
						<div class="jumbotron mt-4">
							<h4 class="text-center display-4">You do not have any past booking.</h4>
						</div>
						';

					}
                }
                
                mysqli_stmt_close($get_past_booking_stmt);
                
                ?>
			</div>
		</div>
	</div>

    <?php include 'footer.php'; ?>
</body>
	
</html>

<?php mysqli_close($conn); ?>