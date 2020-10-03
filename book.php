<?php
session_start();
require_once 'config.php';
$page_name = 'find-cars';

$today_date = date('d/m/Y');
$next_date = date('d/m/Y', strtotime('+1 day'));
$sample_minute = array('00', '15', '30', '45');
$search_filename = array('- ', ' ', '-', '.');
$replace_filename = array('_', '_', '_', '_');
$search_date_symbol = array('/', '.');
$replace_date_symbol = array('-', '-');

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

if (empty($_GET['bookPickUpDate']) || empty($_GET['bookPickUpTime']) || empty($_GET['bookReturnDate']) || empty($_GET['bookReturnTime'])) {
	header('location: /moov/find-cars');
	
}
?>

<!DOCTYPE html>
<html>
	
<head>
	<title>Find Cars | Moov</title>
	
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

<body id="carResult">
	<?php include 'header.php'; ?>
    
	<div class="container my-3 footer-align-bottom">
		<h1 class="text-center">Find Cars</h1>
        
        
        <div class="row mt-5">
            <!-- Car Filter Function -->
            <div class="col-md-3">
                <form action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php'); ?>" method="get">
                    <input type="hidden" name="bookView" value="<?php echo isset($_GET['bookView']) ? $_GET['bookView'] : 'list'; ?>">
                    
                    <!-- Pick Up Filter -->
                    <label for="bookPickUpDate">Pick-Up</label>
                    
                    <div class="form-row">
                        <div class="col-7">
                            <input type="date" class="form-control <?php echo !empty($book_pick_up_date_err) ? 'border border-danger' : ''; ?>" id="bookPickUpDate" name="bookPickUpDate" placeholder="dd / mm / yyyy" min="<?php echo $today_date; ?>" value="<?php echo !empty($_GET['bookPickUpDate']) ? $_GET['bookPickUpDate'] : $today_date; ?>" onChange="checkDateTime()">
                        </div>
                        
                        <div class="col">
                            <select id="bookPickUpTime" class="form-control <?php echo !empty($book_pick_up_time_err) ? 'border border-danger' : ''; ?>" name="bookPickUpTime" onChange="checkDateTime()">
                                <option value="" selected>Select Pick-Up Time</option>

                                <?php
                                foreach ($select_time_option as $time_option) {
                                    $selected_book_pick_up_time = (isset($_GET['bookPickUpTime']) && $_GET['bookPickUpTime'] == $time_option) ? ' selected="selected"' : '';

                                    echo '<option value="' . $time_option . '" ' . $selected_book_pick_up_time . '>' . $time_option . '</option>';

                                }
                                ?>
                            </select>
                        </div>
						
						<p id="bookPickUpDateErr" class="text-danger mb-0 pl-1"></p>
						<p id="bookPickUpTimeErr" class="text-danger mb-0 pl-1"></p>
                    </div>
                    
                    <!-- Return Filter -->
                    <label for="bookReturnDate" class="mt-4">Return</label>
                    
                    <div class="form-row">
                        <div class="col-7">
                            <input type="date" class="form-control <?php echo !empty($book_return_date_err) ? 'border border-danger' : ''; ?>" id="bookReturnDate" name="bookReturnDate" placeholder="dd / mm / yyyy" min="<?php echo $next_date; ?>" value="<?php echo !empty($_GET['bookReturnDate']) ? $_GET['bookReturnDate'] : $next_date; ?>" onChange="checkDateTime()">
                        </div>
                        
                        <div class="col">
                            <select id="bookReturnTime" class="form-control <?php echo !empty($book_return_time_err) ? 'border border-danger' : ''; ?>" name="bookReturnTime" onChange="checkDateTime()">
                                <option value="" selected>Select Return Time</option>

                                <?php
                                foreach ($select_time_option as $time_option) {
                                    $selected_book_return_time = (isset($_GET['bookReturnTime']) && $_GET['bookReturnTime'] == $time_option) ? ' selected="selected"' : '';

                                    echo '<option value="' . $time_option . '" ' . $selected_book_return_time . '>' . $time_option . '</option>';

                                }
                                ?>
                            </select>
                        </div>
						
						<p id="bookReturnDateErr" class="text-danger mb-0 pl-1"></p>
						<p id="bookReturnTimeErr" class="text-danger mb-0 pl-1"></p>
                    </div>
                    
                    <!-- Search Bar -->
                    <div class="form-group mt-4">
                        <label for="bookSearch">Search Location</label>

                        <input type="text" class="form-control" id="bookSearch" name="bookSearch" value="<?php echo $_GET['bookSearch']; ?>" placeholder="Search">
                   </div>
                    
                    <!-- Sort By -->
                    <div class="form-group mt-4">
                        <label for="bookSort">Sort By</label>

                        <select id="bookSort" class="form-control" name="bookSort" onKeyUp="changeEventButton(this)">
                            <option value="" selected>Sort By</option>

                            <?php
                            $sort_by_array = array('name ASC' => 'Car Name (A-Z)', 'name DESC' => 'Car Name (Z-A)', 'brand' => 'Brand', 'model ASC' => 'Model (A-Z)', 'model DESC' => 'Model (Z-A)', 'price_per_hour ASC' => 'Price (Lowest to Highest)', 'price_per_hour DESC' => 'Price (Highest to Lowest)');

							foreach ($sort_by_array as $sort_value => $sort_name) {
								$selected_sort_by = (isset($_GET['bookSort']) && $_GET['bookSort'] == $sort_value ? ' selected="selected"' : '');

								echo '<option value="' . $sort_value . '" ' . $selected_sort_by . '>' . $sort_name . '</option>';
								
							}
                            ?>
                        </select>
                    </div>
                    
                    <button id="searchSubmitButton" type="submit" class="btn btn-secondary btn-block mt-5">Search</button>
                    
                    <!-- Filter -->
                    <div class="form-group mt-4">
                        <label for="bookFilter">Filter</label>

                        <input type="text" class="form-control" id="bookFilter" name="bookFilter" placeholder="Search">
                    </div>
                </form>
            </div>
            
            <?php
            $search_engine_url = '?bookView=' . $_GET['bookView'] . '&bookPickUpDate=' . $_GET['bookPickUpDate'] . '&bookPickUpTime=' . $_GET['bookPickUpTime'] . '&bookReturnDate=' . $_GET['bookReturnDate'] . '&bookReturnTime=' . $_GET['bookReturnTime'] . '&bookSearch=' . $_GET['bookSearch'] . '&bookSort=' . $_GET['bookSort'];
			$book_view_position = strpos($search_engine_url, 'bookView');
            
            ?>
                
            <!-- Car Search Result -->
            <div class="col-md-9">
                <!-- View Option -->
				<div class="row justify-content-end align-items-center mb-5 mb-md-4 px-3">
					<p class="mb-0 mr-1">View:</p>

					<div class="btn-group" role="group" aria-label="View Option">
						<a role="button" class="btn btn-secondary btn-sm <?php echo ($_GET['bookView'] == 'list' || !isset($_GET['bookView']) || empty($_GET['bookView'])) ? 'active' : ''; ?>" href="<?php echo empty($_GET['bookView']) ? substr_replace($search_engine_url, 'bookView=list', $book_view_position, 9) : str_replace('bookView=grid', 'bookView=list', $search_engine_url); ?>">
							<img class="w-50" title="List" alt="List Icon" src="/moov/assets/images/book_view_list_icon.svg">
						</a>

						<a role="button" class="btn btn-secondary btn-sm <?php echo $_GET['bookView'] == 'grid' ? 'active' : ''; ?>" href="<?php echo empty($_GET['bookView']) ? substr_replace($search_engine_url, 'bookView=grid', $book_view_position, 9) : str_replace('bookView=list', 'bookView=grid', $search_engine_url); ?>">
							<img class="w-50" title="Grid" alt="Grid Icon" src="/moov/assets/images/book_view_grid_icon.svg">
						</a>
					</div>
				</div>
					
                <div class="row">
                    <?php
                    $get_car_result_sql = 'SELECT * FROM moov_portal.car AS car LEFT JOIN moov_portal.car_location ON car.car_id = moov_portal.car_location.car_id WHERE car.car_id NOT IN (SELECT car_id FROM moov.booking WHERE pick_up_date BETWEEN ? AND ? OR return_date BETWEEN ? AND ?) AND (address_1 LIKE ? OR address_2 LIKE ? OR postal_code LIKE ? OR suburb LIKE ?) ORDER BY car.' . (isset($_GET['bookSort']) && !empty($_GET['bookSort']) ? $_GET['bookSort'] : 'car_id');
                    $get_car_result_stmt = mysqli_prepare($conn, $get_car_result_sql);
                    
                    mysqli_stmt_bind_param($get_car_result_stmt, 'ssssssss', $param_booking_start_date, $param_booking_end_date, $param_booking_start_date, $param_booking_end_date, $param_search_query, $param_search_query, $param_search_query, $param_search_query);
                    $param_booking_start_date = date('Y-m-d H:i', strtotime(str_replace($search_date_symbol, $replace_date_symbol, $_GET['bookPickUpDate'] . ' ' . $_GET['bookPickUpTime'])));
                    $param_booking_end_date = date('Y-m-d H:i', strtotime(str_replace($search_date_symbol, $replace_date_symbol, $_GET['bookReturnDate'] . ' ' . $_GET['bookReturnTime'])));
                    $param_search_query = (isset($_GET['bookSearch']) && !empty($_GET['bookSearch']) ? '%' . $_GET['bookSearch'] . '%' : '%%');

                    if (mysqli_stmt_execute($get_car_result_stmt)) {
                        $get_car_result = mysqli_stmt_get_result($get_car_result_stmt);

                        while ($car_result = mysqli_fetch_assoc($get_car_result)) {
                            // Get car brand
                            $get_brand_sql = 'SELECT brand FROM moov_portal.car_brand WHERE brand_id = ?';
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
                            $get_type_sql = 'SELECT type FROM moov_portal.car_type WHERE type_id = ?';
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
                            $get_transmission_type_sql = 'SELECT transmission FROM moov_portal.car_transmission WHERE transmission_id = ?';
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
                            $get_fuel_type_sql = 'SELECT fuel FROM moov_portal.car_fuel WHERE fuel_id = ?';
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
                            
                            $booking_url = 'id=' . $car_result['car_id'] . '&bookPickUpDate=' . $_GET['bookPickUpDate'] . '&bookPickUpTime=' . $_GET['bookPickUpTime'] . '&bookReturnDate=' . $_GET['bookReturnDate'] . '&bookReturnTime=' . $_GET['bookReturnTime'];

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
								';
								
								if (isset($_SESSION['moov_user_logged_in']) && $_SESSION['moov_user_logged_in'] == TRUE) {
                                    echo '<a class="btn btn-secondary btn-block mt-4" role="button" href="/moov/checkout?' . $booking_url . '">Select</a>';
                                    
                                } else {
                                    echo '<a class="btn btn-primary btn-block mt-4" role="button" href="/moov/login?url=' . urlencode('/moov/book' . $search_engine_url) . '">Login to Book</a>';
                                    
                                }
								
								echo '
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
                                ';
                                
                                if (isset($_SESSION['moov_user_logged_in']) && $_SESSION['moov_user_logged_in'] == TRUE) {
                                    echo '<a class="btn btn-secondary btn-block" role="button" href="/moov/checkout?' . $booking_url . '">Select</a>';
                                    
                                } else {
                                    echo '<a class="btn btn-primary btn-block" role="button" href="/moov/login?url=' . urlencode('/moov/book' . $search_engine_url) . '">Login to Book</a>';
                                    
                                }
                                
                                echo '
                                        </div>
                                    </div>

                                    <hr class="my-4">
                                </div>
                                ';

                            }
                        }
                    }

                    mysqli_stmt_close($get_car_result_stmt);
                    ?>
                </div>
            </div>
        </div>
	</div>
    
    <script>
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

    <?php include 'footer.php'; ?>
</body>
	
</html>

<?php mysqli_close($conn); ?>