<?php
session_start();
require_once '../config.php';
$parent_page_name = 'bookings';
$page_name = 'new-booking';

$today_date = date('Y-m-d');
$next_date = date('Y-m-d', strtotime('+1 day'));
$sample_minute = array('00', '15', '30', '45');
$search_filename = array('- ', ' ', '-', '.');
$replace_filename = array('_', '_', '_', '_');
$search_date_symbol = array('/', '.');
$replace_date_symbol = array('-', '-');
$current_time = date('H:i', strtotime('+30 minutes'));

for ($i = 0; $i < 24; $i++) {
    foreach ($sample_minute as $minute) {
        if ($i < 10) {
            $time = '0' . $i;
            
        } else {
            $time = $i;
            
        }
        
        $select_time_option[] = $time . ':' . $minute;
        
    }
}

if (!isset($_SESSION['moov_portal_logged_in']) && $_SESSION['moov_portal_logged_in'] != TRUE) {
    header('location: /moov/portal/login?url=' . urlencode('/moov/portal/' . $parent_page_name . '/' . $page_name));
    
}

$search_engine_url = '?id=' . $_GET['id'] . '&bookView=' . $_GET['bookView'] . '&bookPickUpDate=' . $_GET['bookPickUpDate'] . '&bookPickUpTime=' . $_GET['bookPickUpTime'] . '&bookReturnDate=' . $_GET['bookReturnDate'] . '&bookReturnTime=' . $_GET['bookReturnTime'] . '&bookSearch=' . $_GET['bookSearch'] . '&bookSort=' . $_GET['bookSort'];
$book_view_position = strpos($search_engine_url, 'bookView');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	// Check pick up date is older than return date
	if (trim($_GET['bookPickUpDate']) > trim($_GET['bookReturnDate'])) {
		$booking_err = TRUE;
		$book_pick_up_date_err = $book_return_date_err = 'Return date is older than pick-up date.';

	} elseif (trim($_GET['bookPickUpDate']) < $today_date) { // Check pick up date has past
		$booking_err = TRUE;
		$book_pick_up_date_err = 'Pick-up date has passed. Please try again.';

	} else {
		$book_temp_pick_up_date = trim($_GET['bookPickUpDate']);
		$book_temp_return_date = trim($_GET['bookReturnDate']);

	}
    
	if ($book_temp_pick_up_date == $today_date) {
		// Check pick up time has past if pick up date is today
		if ($_GET['bookPickUpTime'] < $current_time) {
			$booking_err = TRUE;
			$book_pick_up_time_err = 'You have selected either a past or too close to current time for your booking. Please try another pick-up time.';

		} else {
			$book_pick_up_time = $_GET['bookPickUpTime'];

		}
	} else {
		$book_pick_up_time = $_GET['bookPickUpTime'];

	}
	
	$book_return_time = $_GET['bookReturnTime'];
    
    if (empty($book_pick_up_date_err) && empty($book_pick_up_time_err) && empty($book_return_date_err) && empty($book_return_time_err)) {
        $book_pick_up_date = date('Y-m-d H:i', strtotime(str_replace($search_date_symbol, $replace_date_symbol, $book_temp_pick_up_date . ' ' . $book_pick_up_time)));
        $book_return_date = date('Y-m-d H:i', strtotime(str_replace($search_date_symbol, $replace_date_symbol, $book_temp_return_date . ' ' . $book_return_time)));
		$accepted_return_date = date('Y-m-d H:i', strtotime($book_pick_up_date . '+30 minutes'));
        
        if ($book_pick_up_date > $book_return_date) {
			$booking_err = TRUE;
            $book_err = 'Return time is older than pick-up time.';
            
        } elseif ($book_return_date < $accepted_return_date) {
			$booking_err = TRUE;
			$book_err = 'Minimum booking duration is 30 minutes.';
			
		}
    }
}
?>

<!DOCTYPE html>
<html>
	
<head>
	<title>New Booking | Moov Portal</title>
	
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
    <link rel="stylesheet" type="text/css" href="/moov/portal/assets/style/bootstrap.css">

    <!-- Self Defined CSS -->
    <link rel="stylesheet" type="text/css" href="/moov/portal/assets/style/style.css?<?php echo date('l jS \of F Y h:i:s A'); ?>">

    <!-- Favicon -->
	<link rel="icon" type="image/png" sizes="96x96" href="/moov/portal/assets/favicon/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/moov/portal/assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/moov/portal/assets/favicon/favicon-16x16.png">
</head>

<body>
	<?php include '../header.php'; ?>
	
	
	<?php
	if (!isset($_GET['bookPickUpDate']) || !isset($_GET['bookPickUpTime']) || !isset($_GET['bookReturnDate']) || !isset($_GET['bookReturnTime'])) {
		echo '
		<div class="container my-3 footer-align-bottom d-flex">
			<div id="find-car-card" class="card m-auto py-5 px-4">
				<h1 class="text-center">Find Cars</h1>

				<form action="' . basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php') . '" method="get" onSubmit="submitButton()">
					<input type="hidden" name="id" value="' . $_GET['id'] . '">
					
					<div class="form-row mt-4">
						<label for="bookPickUpDate" class="col-sm-2 col-form-label">Pick-Up</label>

						<div class="col">
							<input type="date" class="form-control ' . (!empty($book_pick_up_date_err) || !empty($book_err) ? 'border border-danger' : '') . '" id="bookPickUpDate" name="bookPickUpDate" placeholder="dd / mm / yyyy" min="' . $today_date . '" value="' . (!empty($_GET['bookPickUpDate']) ? $_GET['bookPickUpDate'] : $today_date) . '" onChange="checkDateTime()" onKeyUp="changeEventButton(this)">

							<p id="bookPickUpDateErr" class="text-danger mb-0">' . $book_pick_up_date_err . '</p>' . '
						</div>

						<div class="col">
							<select id="bookPickUpTime" class="form-control ' . (!empty($book_pick_up_time_err) || !empty($book_err) ? 'border border-danger' : '') . '" name="bookPickUpTime" onChange="checkDateTime()" onKeyUp="changeEventButton(this)">
		';

								foreach ($select_time_option as $time_option) {
									$selected_book_pick_up_time = (isset($_POST['bookPickUpTime']) && $_POST['bookPickUpTime'] == $time_option) ? ' selected="selected"' : '';

									echo '<option value="' . $time_option . '" ' . $selected_book_pick_up_time . '>' . $time_option . '</option>';

								}
		
		echo '
							</select>

							<p id="bookPickUpTimeErr" class="text-danger mb-0">' . $book_pick_up_time_err . '</p>' . '
						</div>
					</div>

					<div class="form-row mt-3">
						<label for="bookReturnDate" class="col-sm-2 col-form-label">Return</label>

						<div class="col">
							<input type="date" class="form-control ' . (!empty($book_return_date_err) || !empty($book_err) ? 'border border-danger' : '') . '" id="bookReturnDate" name="bookReturnDate" placeholder="dd / mm / yyyy" min="' . $today_date . '" value="' . (!empty($_POST['bookReturnDate']) ? $_POST['bookReturnDate'] : $next_date) . '" onKeyUp="changeEventButton(this)" onChange="checkDateTime()">
							
							<p id="bookReturnDateErr" class="text-danger mb-0">' . $book_return_date_err . '</p>' . '
						</div>

						<div class="col">
							<select id="bookReturnTime" class="form-control ' . (!empty($book_return_time_err) || !empty($book_err) ? 'border border-danger' : '') . '" name="bookReturnTime" onKeyUp="changeEventButton(this)" onChange="checkDateTime()">
		';

								foreach ($select_time_option as $time_option) {
									$selected_book_pick_up_time = (isset($_POST['bookReturnTime']) && $_POST['bookReturnTime'] == $time_option) ? ' selected="selected"' : '';

									echo '<option value="' . $time_option . '" ' . $selected_book_pick_up_time . '>' . $time_option . '</option>';

								}
		
		echo '
							</select>
							
							<p id="bookReturnTimeErr" class="text-danger mb-0">' . $book_return_time_err . '</p>' . '
						</div>
					</div>
					
					' . ((isset($book_err) && !empty($book_err)) ? '<p class="text-danger mb-0 mt-4">' . $book_err . '</p>' : '') . '

					<button id="searchSubmitButton" type="submit" class="btn btn-secondary btn-block mt-5">
						<span id="submitButton">Find</span>

						<img id="processingIcon" src="/moov/assets/images/processing_icon.svg" class="processing-icon d-none">
						<span id="processingButton" class="d-none">Processing...</span>
					</button>
				</form>
			</div>
		</div>
		';
		
	} else {
		echo '
		<div class="container my-3 footer-align-bottom">
			<h1 class="text-center">Register New Booking</h1>

			<div class="row mt-5">
				<!-- Car Filter Function -->
				<div class="col-md-3">
					<form action="' . basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php') . '" method="get" onSubmit="submitButton()">
						<input type="hidden" name="id" value="' . $_GET['id'] . '">
						<input type="hidden" name="bookView" value="' . (isset($_GET['bookView']) ? $_GET['bookView'] : 'list') . '">

						<!-- Pick Up Filter -->
						<label for="bookPickUpDate">Pick-Up</label>

						<div class="form-row">
							<div class="col-7">
								<input type="date" class="form-control ' . (!empty($book_pick_up_date_err) || !empty($book_err) ? 'border border-danger' : '') . '" id="bookPickUpDate" name="bookPickUpDate" placeholder="dd / mm / yyyy" min="' . $today_date . '" value="' . (!empty($_GET['bookPickUpDate']) ? $_GET['bookPickUpDate'] : $today_date) . '" onChange="checkDateTime()">
							</div>

							<div class="col">
								<select id="bookPickUpTime" class="form-control ' . (!empty($book_pick_up_time_err) || !empty($book_err) ? 'border border-danger' : '') . '" name="bookPickUpTime" onChange="checkDateTime()">
									<option value="" selected>Select Pick-Up Time</option>
		';

									foreach ($select_time_option as $time_option) {
										$selected_book_pick_up_time = (isset($_GET['bookPickUpTime']) && $_GET['bookPickUpTime'] == $time_option) ? ' selected="selected"' : '';

										echo '<option value="' . $time_option . '" ' . $selected_book_pick_up_time . '>' . $time_option . '</option>';

									}
		
		echo '
								</select>
							</div>

							<p id="bookPickUpDateErr" class="text-danger mb-0 pl-1">' . $book_pick_up_date_err . $book_err . '</p>
							<p id="bookPickUpTimeErr" class="text-danger mb-0 pl-1">' . $book_pick_up_time_err . '</p>
						</div>

						<!-- Return Filter -->
						<label for="bookReturnDate" class="mt-4">Return</label>

						<div class="form-row">
							<div class="col-7">
								<input type="date" class="form-control ' . (!empty($book_return_date_err) || !empty($book_err) ? 'border border-danger' : '') . '" id="bookReturnDate" name="bookReturnDate" placeholder="dd / mm / yyyy" min="' . $today_date . '" value="' . (!empty($_GET['bookReturnDate']) ? $_GET['bookReturnDate'] : $next_date) . '" onChange="checkDateTime()">
							</div>

							<div class="col">
								<select id="bookReturnTime" class="form-control ' . (!empty($book_return_time_err) || !empty($book_err) ? 'border border-danger' : '') . '" name="bookReturnTime" onChange="checkDateTime()">
									<option value="" selected>Select Return Time</option>
		';

									foreach ($select_time_option as $time_option) {
										$selected_book_return_time = (isset($_GET['bookReturnTime']) && $_GET['bookReturnTime'] == $time_option) ? ' selected="selected"' : '';

										echo '<option value="' . $time_option . '" ' . $selected_book_return_time . '>' . $time_option . '</option>';

									}
		
		echo '
								</select>
							</div>

							<p id="bookReturnDateErr" class="text-danger mb-0 pl-1">' . $book_return_date_err . $book_err . '</p>
							<p id="bookReturnTimeErr" class="text-danger mb-0 pl-1">' . $book_return_time_err . '</p>
						</div>

						<!-- Search Bar -->
						<div class="form-group mt-4">
							<label for="bookSearch">Search Location</label>

							<input type="text" class="form-control" id="bookSearch" name="bookSearch" value="' . $_GET['bookSearch'] . '" placeholder="Search" onKeyDown="searchButton()">
					   </div>

						<!-- Sort By -->
						<div class="form-group mt-4">
							<label for="bookSort">Sort By</label>

							<select id="bookSort" class="form-control" name="bookSort" onKeyUp="changeEventButton(this)" onChange="searchButton()">
								<option value="" selected>Sort By</option>
		';

								$sort_by_array = array('name ASC' => 'Car Name (A-Z)', 'name DESC' => 'Car Name (Z-A)', 'brand' => 'Brand', 'model ASC' => 'Model (A-Z)', 'model DESC' => 'Model (Z-A)', 'price_per_hour ASC' => 'Price (Lowest to Highest)', 'price_per_hour DESC' => 'Price (Highest to Lowest)');

								foreach ($sort_by_array as $sort_value => $sort_name) {
									$selected_sort_by = (isset($_GET['bookSort']) && $_GET['bookSort'] == $sort_value ? ' selected="selected"' : '');

									echo '<option value="' . $sort_value . '" ' . $selected_sort_by . '>' . $sort_name . '</option>';

								}
		
		echo '
							</select>
						</div>

						<button id="searchSubmitButton" type="submit" class="btn btn-secondary btn-block mt-5">
							<span id="submitButton">Search</span>

							<img id="processingIcon" src="/moov/assets/images/processing_icon.svg" class="processing-icon d-none">
							<span id="processingButton" class="d-none">Processing...</span>
						</button>
					</form>
				</div>

				<!-- Car Search Result -->
				<div class="col-md-9 mt-4 mt-md-0">
					<!-- View Option -->
					<div class="row justify-content-end align-items-center mb-5 mb-md-4 px-3">
						<p class="mb-0 mr-1">View:</p>

						<div class="btn-group" role="group" aria-label="View Option">
							<a role="button" class="btn btn-secondary btn-sm ' . (($_GET['bookView'] == 'list' || !isset($_GET['bookView']) || empty($_GET['bookView'])) ? 'active' : '') . '" href="' . (empty($_GET['bookView']) ? substr_replace($search_engine_url, 'bookView=list', $book_view_position, 9) : str_replace('bookView=grid', 'bookView=list', $search_engine_url)) . '">
								<img class="w-50" title="List" alt="List Icon" src="/moov/assets/images/book_view_list_icon.svg">
							</a>

							<a role="button" class="btn btn-secondary btn-sm ' . ($_GET['bookView'] == 'grid' ? 'active' : '') . '" href="' . (empty($_GET['bookView']) ? substr_replace($search_engine_url, 'bookView=grid', $book_view_position, 9) : str_replace('bookView=list', 'bookView=grid', $search_engine_url)) . '">
								<img class="w-50" title="Grid" alt="Grid Icon" src="/moov/assets/images/book_view_grid_icon.svg">
							</a>
						</div>
					</div>

					<div class="row">
		';

						$get_car_result_sql = 'SELECT * FROM car AS car LEFT JOIN car_location ON car.car_id = car_location.car_id WHERE car.car_id NOT IN (SELECT car_id FROM moov.booking WHERE pick_up_date BETWEEN ? AND ? OR return_date BETWEEN ? AND ?) AND (address_1 LIKE ? OR address_2 LIKE ? OR postal_code LIKE ? OR suburb LIKE ?) ORDER BY car.' . (isset($_GET['bookSort']) && !empty($_GET['bookSort']) ? $_GET['bookSort'] : 'car_id');
						$get_car_result_stmt = mysqli_prepare($conn, $get_car_result_sql);

						mysqli_stmt_bind_param($get_car_result_stmt, 'ssssssss', $param_booking_start_date, $param_booking_end_date, $param_booking_start_date, $param_booking_end_date, $param_search_query, $param_search_query, $param_search_query, $param_search_query);
						$param_booking_start_date = date('Y-m-d H:i', strtotime(str_replace($search_date_symbol, $replace_date_symbol, $_GET['bookPickUpDate'] . ' ' . $_GET['bookPickUpTime'])));
						$param_booking_end_date = date('Y-m-d H:i', strtotime(str_replace($search_date_symbol, $replace_date_symbol, $_GET['bookReturnDate'] . ' ' . $_GET['bookReturnTime'])));
						$param_search_query = (isset($_GET['bookSearch']) && !empty($_GET['bookSearch']) ? '%' . $_GET['bookSearch'] . '%' : '%%');

						if (mysqli_stmt_execute($get_car_result_stmt)) {
							$get_car_result = mysqli_stmt_get_result($get_car_result_stmt);

							while ($car_result = mysqli_fetch_assoc($get_car_result)) {
								// Get car brand
								$get_brand_sql = 'SELECT brand FROM car_brand WHERE brand_id = ?';
								$get_brand_stmt = mysqli_prepare($conn, $get_brand_sql);

								mysqli_stmt_bind_param($get_brand_stmt, 'i', $param_car_brand);
								$param_car_brand = $car_result['brand'];

								if (mysqli_stmt_execute($get_brand_stmt)) {
									$get_brand = mysqli_stmt_get_result($get_brand_stmt);

									while ($brand = mysqli_fetch_assoc($get_brand)) {
										$car_brand = $brand['brand'];

									}
								}

								// Get car type
								$get_type_sql = 'SELECT type FROM car_type WHERE type_id = ?';
								$get_type_stmt = mysqli_prepare($conn, $get_type_sql);

								mysqli_stmt_bind_param($get_type_stmt, 'i', $param_car_type);
								$param_car_type = $car_result['car_type'];

								if (mysqli_stmt_execute($get_type_stmt)) {
									$get_type = mysqli_stmt_get_result($get_type_stmt);

									while ($type = mysqli_fetch_assoc($get_type)) {
										$car_type = $type['type'];

									}
								}

								// Get car transmission type
								$get_transmission_type_sql = 'SELECT transmission FROM car_transmission WHERE transmission_id = ?';
								$get_transmission_type_stmt = mysqli_prepare($conn, $get_transmission_type_sql);

								mysqli_stmt_bind_param($get_transmission_type_stmt, 'i', $param_car_transmission_type);
								$param_car_transmission_type = $car_result['transmission_type'];

								if (mysqli_stmt_execute($get_transmission_type_stmt)) {
									$get_transmission_type = mysqli_stmt_get_result($get_transmission_type_stmt);

									while ($transmission_type = mysqli_fetch_assoc($get_transmission_type)) {
										$car_transmission_type = $transmission_type['transmission'];

									}
								}

								// Get car fuel type
								$get_fuel_type_sql = 'SELECT fuel FROM car_fuel WHERE fuel_id = ?';
								$get_fuel_type_stmt = mysqli_prepare($conn, $get_fuel_type_sql);

								mysqli_stmt_bind_param($get_fuel_type_stmt, 'i', $param_fuel_type);
								$param_fuel_type = $car_result['fuel_type'];

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

								$car_temp_image_name = strtolower($car_brand . '_' . $car_result['model'] . '_' . $car_result['name']);
								$car_image_name = str_replace($search_filename, $replace_filename, $car_temp_image_name);
								
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
								$car_price_per_hour = $car_result['price_per_hour'];
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
									if ($duration_hour > 5 && $duration_day == 0) {
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
								$gst = $total_price * 0.10;

								if (isset($_GET['bookView']) && $_GET['bookView'] == 'grid') {
									echo '
									<div class="col-sm-4 text-center mb-5">
										<div class="card">
											<img class="car-image pt-4 px-3 rounded border-0" src="/moov/assets/images/transparent_background.png" style="background-image: url(\'/moov/car-image/' . $car_image_name . '.jpg\'); height: auto !important;">

											<div class="card-body mt-4 mt-md-0">
												<p class="font-weight-bold lead mb-2">' . $car_result['name'] . '</p>

												<p class="mb-2">' . $car_brand . ' ' . $car_result['model'] . ' (' . $car_transmission_type. ')</p>

												<p class="mb-0">' . $car_fuel_type . '</p>

												<p class="mb-0">' . $car_result['seat'] . ' seats | ' . $car_result['door'] . ' doors</p>

												<p class="mb-2">A$' . number_format($car_result['price_per_hour'], 2, '.', ',') . ' per hour</p>

												<p class="mb-0"><b>Location:</b> ' . $car_result['address_1'] . ', ' . $car_result['suburb'] . ' ' . $car_result['postal_code'] . ' ' . strtoupper($car_result['state']) . '</p>

												<a class="btn btn-secondary btn-block mt-4 ' . ($booking_err == TRUE ? 'disabled' : '') . '" role="button" href="/moov/checkout?' . $booking_url . '">Select</a>
											</div>
										</div>
									</div>
									';

								} else {
									echo '
									<div class="col-12">
										<div class="row align-items-center car-search-result">
											<div class="col-md-4">
												<img class="car-image rounded border-0" src="/moov/assets/images/transparent_background.png" style="background-image: url(\'/moov/car-image/' . $car_image_name . '.jpg\'); height: auto !important;">
											</div>

											<div class="col-md-6 mt-4 mt-md-0">
												<p class="font-weight-bold lead mb-2">' . $car_result['name'] . '</p>

												<p class="mb-0">' . $car_brand . ' ' . $car_result['model'] . ' (' . $car_transmission_type . ')</p>

												<p class="mb-0">' . $car_fuel_type . ' | ' . $car_result['seat'] . ' seats | ' . $car_result['door'] . ' doors</p>

												<p class="mb-2">A$' . number_format($car_result['price_per_hour'], 2, '.', ',') . ' per hour</p>

												<p class="mb-0"><b>Location:</b> ' . $car_result['address_1'] . ', ' . $car_result['suburb'] . ' ' . $car_result['postal_code'] . ' ' . strtoupper($car_result['state']) . '</p>
											</div>

											<div class="col-md-2 mt-4 mt-md-0">
												<button class="btn btn-secondary btn-block ' . ($booking_err == TRUE ? 'disabled' : '') . '" type="button" data-toggle="modal" data-target="#bookingConfirmation" data-imagename="' . $car_image_name . '" data-carid="' . $car_result['car_id'] . '" data-carname="' . $car_result['name'] . '" data-carmodel="' . $car_brand . ' ' . $car_result['model'] . '" data-pickup="' . $pick_up_date . '" data-return="' . $return_date . '" data-duration="' . $duration_string . '" data-subtotal="' . number_format($discount_price, 2, '.', ',') . '" data-discount="' . number_format($discounted_total_price, 2, '.', ',') . '" data-gst="' . number_format($gst, 2, '.', ',') . '" data-total="' . number_format($total_price + $gst, 2, '.', ',') . '">Select</button>
											</div>
										</div>

										<hr class="my-4">
									</div>
									';

								}
							}
						}

						mysqli_stmt_close($get_car_result_stmt);

		echo '
						<div class="modal fade" data-backdrop="static" data-keyboard="false" id="bookingConfirmation" tabindex="-1" aria-labelledby="bookingConfirmationLabel" aria-hidden="true">
							<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
								<div class="modal-content">
									<div class="modal-header">
										<h4 class="modal-title" id="bookingConfirmationLabel">Confirm Booking</h4>
										
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</buton>
									</div>
									
									<div class="modal-body">
										<div class="row">
											<div class="col-5">
												<img class="car-image rounded border-0" src="/moov/assets/images/transparent_background.png" style="height: auto !important;">
											</div>

											<div class="col-7">
												<p id="carName" class="font-weight-bold lead mb-1"></p>

												<p id="carModel" class="mb-2"></p>

												<p class="mb-0"><b>Pick-Up:</b> <span id="pickUp"></span></p>

												<p class="mb-0"><b>Return:</b> <span id="return"></span></p>

												<p class="mb-2"><b>Duration:</b> <span id="duration"></p>

												<p class="mb-0 card-text"><b>Subtotal:</b> A$<span id="subtotal"></span></p>

												<p id="discountLabel" class="text-danger mb-0 card-text"><b>Discount:</b> A$<span id="discount"></span>

												<p class="mb-2 card-text"><b>GST:</b> A$<span id="gst"></span></p>

												<p class="mt-3 card-text"><b>Total Price <span class="text-muted font-italic font-weight-bold small">(Incl. GST)</span>:</b> A$<span id="total"></span></p>
											</div>
										</div>
									</div>

									<div class="modal-footer">
										<form action="' . basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php') . '" method="get" onSubmit="submitButton()">
											<input type="hidden" name="bookingCustomerId" value="' . $_GET['id'] . '">
											<input type="hidden" id="bookingCarId" name="bookingCarId">
											<input type="hidden" id="bookingPickUp" name="bookingPickUp">
											<input type="hidden" id="bookingReturn" name="bookingReturn">
											<input type="hidden" id="bookingDuration" name="bookingDuration">
											<input type="hidden" id="bookingAmount" name="bookingAmount">
											<input type="hidden" name="bookingStatus" value="">
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		';
	}
	?>
            
	<script>
		$(document).ready(function(){
			var browser = navigator.userAgent;
			
			if (browser.includes('Safari')) {
				$('#searchSubmitButton').prop('disabled', false);
				
			} else {
				$('#searchSubmitButton').prop('disabled', true);
				
			}

		});
		
		$('#bookingConfirmation').on('show.bs.modal', function (event) {
			var button = $(event.relatedTarget)
			var modal = $(this)
			
			var imageName = button.data('imagename')
			var carId = button.data('carid')
			var carName = button.data('carname')
			var carModel = button.data('carmodel')
			var pickUpDate = button.data('pickup')
			var returnDate = button.data('return')
			var duration = button.data('duration')
			var subtotal = button.data('subtotal')
			var discount = button.data('discount')
			var gst = button.data('gst')
			var total = button.data('total')
			
			var imageUrl = '/moov/car-image/' + imageName + '.jpg'
			
			modal.find('.car-image').css('background-image', 'url(' + imageUrl + ')');
			modal.find('#carName').text(carName)
			modal.find('#carModel').text(carModel)
			modal.find('#pickUp').text(pickUpDate)
			modal.find('#return').text(returnDate)
			modal.find('#duration').text(duration)
			modal.find('#subtotal').text(subtotal)
			modal.find('#gst').text(gst)
			modal.find('#total').text(total)
			
			modal.find('#bookingCarId').val(carId)
			
			if (discount == '0.00') {
				modal.find('#discountLabel').hide()
				
			} else {
				modal.find('#discountLabel').show()
				modal.find('#discount').text(discount)
				
			}
			
		})
		
		function submitButton() {
			document.getElementById('searchSubmitButton').disabled = true;
			document.getElementById('submitButton').classList.add('d-none');
			document.getElementById('processingIcon').classList.add('d-inline-block');
			document.getElementById('processingIcon').classList.remove('d-none');
			document.getElementById('processingButton').classList.remove('d-none');

		}

		function changeEventButton(event) {
			if (event.keyCode == 13) {
				event.preventDefault;

				document.getElementById('searchSubmitButton').disabled = true;
				document.getElementById('submitButton').classList.add('d-none');
				document.getElementById('processingIcon').classList.add('d-inline-block');
				document.getElementById('processingIcon').classList.remove('d-none');
				document.getElementById('processingButton').classList.remove('d-none');

			}
		}
		
		function searchButton() {
			document.getElementById('searchSubmitButton').disabled = false;
			
		}
		
		function checkDateTime() {
			var pickUpDateValue = document.getElementById('bookPickUpDate');
			var pickUpTimeValue = document.getElementById('bookPickUpTime');
			var returnDateValue = document.getElementById('bookReturnDate');
			var returnTimeValue = document.getElementById('bookReturnTime');
			var pickUpDateErr = document.getElementById('bookPickUpDateErr');
			var pickUpTimeErr = document.getElementById('bookPickUpTimeErr');
			var returnDateErr = document.getElementById('bookReturnDateErr');
			var returnTimeErr = document.getElementById('bookReturnTimeErr');
			var searchButton = document.getElementById('searchSubmitButton');
			
			var pickUpDateError = false;
			var pickUpTimeError = false;
			var returnDateError = false;
			var returnTimeError = false;
			var todayDateTime = new Date().toISOString();
			var todayDateTimeMins = new Date(todayDateTime);
			
			// Check if pick up date is empty
			if (pickUpDateValue.value == '') {
				pickUpDateValue.classList.add('border-danger');
				pickUpDateErr.innerHTML = 'Pick-up date cannot be empty.';
				pickUpDateError = true;
				
			} else {
				var pickUpDateTimeValue = pickUpDateValue.value + ' ' + pickUpTimeValue.value;
				var pickUpDateTimeString = new Date(pickUpDateTimeValue);
				var pickUpDateTime = new Date(pickUpDateTimeValue).toISOString();
				var acceptedBookingDateTime = new Date(todayDateTimeMins.getTime() + 1800000).toISOString();
				
				pickUpDateValue.classList.remove('border-danger');
				pickUpDateErr.innerHTML = '';
				pickUpDateError = false;
				
			}
			
			// Check if return date is empty
			if (returnDateValue.value == '') {
				returnDateValue.classList.add('border-danger');
				returnDateErr.innerHTML = 'Return date cannot be empty.';
				returnDateError = true;
				
			} else {
				var returnDateTimeValue = returnDateValue.value + ' ' + returnTimeValue.value;
				var returnDateTime = new Date(returnDateTimeValue).toISOString();
				var acceptedReturnDateTime = new Date(pickUpDateTimeString.getTime() + 1800000).toISOString();
				
				returnDateValue.classList.remove('border-danger');
				returnDateErr.innerHTML = '';
				returnDateError = false;
				
			}
			
			// Check pick-up date time has past
			if (pickUpDateTime <= todayDateTime) {
				pickUpTimeValue.classList.add('border-danger');
				pickUpTimeErr.innerHTML = 'Pick-up time has passed. Please try again.';
				pickUpTimeError = true;
				
			} else if (pickUpDateTime <= acceptedBookingDateTime) { // Check pick up date time is too close to current time
				pickUpTimeValue.classList.add('border-danger');
				pickUpTimeErr.innerHTML = 'Pick-up time must be at least 30 minutes from now.';
				pickUpTimeError = true;
				
			} else {
				pickUpTimeValue.classList.remove('border-danger');
				pickUpTimeErr.innerHTML = '';
				pickUpTimeError = false;
				
			}
			
			// Check return date time has past
			if (returnDateTime <= pickUpDateTime) {
				returnTimeValue.classList.add('border-danger');
				returnTimeErr.innerHTML = 'Return time is older than pick-up time.';
				returnTimeError = true;
				
			} else if (returnDateTime < acceptedReturnDateTime) { // Check return date time is too close to pick-up date time
				returnTimeValue.classList.add('border-danger');
				returnTimeErr.innerHTML = 'Minimum booking duration is 30 minutes.';
				returnTimeError = true;
				
			} else {
				returnTimeValue.classList.remove('border-danger');
				returnTimeErr.innerHTML = '';
				returnTimeError = false;
				
			}
			
			// Disabled search button if contains error
			if (pickUpDateError == true || pickUpTimeError == true || returnDateError == true || returnTimeError == true) {
				searchButton.disabled = true;
				
			} else {
				searchButton.disabled = false;
				
			}
		}
	</script>

    <?php include '../footer.php'; ?>
</body>
	
</html>

<?php mysqli_close($conn); ?>