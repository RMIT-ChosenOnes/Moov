<?php
session_start();
require_once 'config.php';
$page_name = 'find-cars';

$search_filename = array('- ', ' ', '-', '.');
$replace_filename = array('_', '_', '_', '_');
?>

<!DOCTYPE html>
<html>
	
<head>
	<title>Checkout | Moov</title>
	
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
		<h1 class="text-center">Checkout</h1>
        
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
                
                mysqli_stmt_close($get_brand_stmt);
				
				$car_friendly_name = $car_details['name'];
				$car_model = $car_details['model'];
				$car_price_per_hour = $car_details['price_per_hour'];
				$car_location = $car_details['address_1'] . ',<br/>' . (!empty($car_details['address_2']) ? $car_details['address_2'] . ',<br/>' : '') . $car_details['suburb'] . ' ' . $car_details['postal_code'] . ' ' . strtoupper($car_details['state']);
				$car_location_url = 'https://www.google.com/maps?q=' . $car_details['longitude'] . ',' . $car_details['latitude'];
				
			}
		}
        
        mysqli_stmt_close($get_car_details_stmt);
        
        $get_driver_profile_sql = 'SELECT * FROM account WHERE account_id = ?';
        $get_driver_profile_stmt = mysqli_prepare($conn, $get_driver_profile_sql);
        
        mysqli_stmt_bind_param($get_driver_profile_stmt, 'i', $param_user_account_id);
        $param_user_account_id = $_SESSION['moov_user_account_id'];
        
        if (mysqli_stmt_execute($get_driver_profile_stmt)) {
            $get_driver_profile = mysqli_stmt_get_result($get_driver_profile_stmt);
            
            while ($driver_profile = mysqli_fetch_assoc($get_driver_profile)) {
                $driver_first_name = $driver_profile['first_name'];
                $driver_last_name = $driver_profile['last_name'];
                $driver_email_address = $driver_profile['email_address'];
                $driver_contact_number = $driver_profile['contact_number'];
                
            }
        }
		
		$car_temp_image_name = strtolower($car_brand . '_' . $car_model . '_' . $car_friendly_name);
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
        $gst = $total_price * 0.10;
		?>
        <div class="row mt-5">
            <div class="col-md-4">
                <div class="card bg-light rounded">
                    <img class="car-image rounded border-0 card-img-top" src="/moov/assets/images/transparent_background.png" style="background-image: url('/moov/car-image/<?php echo $car_image_name; ?>.jpg'); height: auto !important;">
                    
                    <div class="card-body">
                        <!-- Car -->
                        <h4 class="card-title">Car Details</h4>
                        
                        <p class="font-weight-bold lead mb-2 card-text"><?php echo $car_friendly_name; ?></p>
                
                        <p class="mb-2 card-text"><?php echo $car_brand . ' ' . $car_model; ?></p>

                        <p class="card-text"><?php echo $car_location; ?></p>
                        
                        <hr class="my-4 border-dark">
                        
                        <!-- Booking -->
                        <h4 class="card-title">Booking Details</h4>
                        
                        <p class="mb-2 card-text"><span class="small font-weight-bold">Pick-Up:</span> <?php echo $pick_up_date; ?></p>
				
                        <p class="mb-2 card-text"><span class="small font-weight-bold">Return:</span> <?php echo $return_date; ?></p>

                        <p><span class="small font-weight-bold">Duration:</span> <?php echo $duration_string; ?></p>
                        
                        <hr class="my-4 border-dark">
                        
                        <!-- Driver -->
                        <h4 class="card-title">Driver Details</h4>
                        
                        <p class="mb-2 card-text"><?php echo $driver_first_name . ' ' . $driver_last_name; ?></p>
				
                        <p class="mb-2 card-text"><?php echo $driver_email_address; ?></p>

                        <p class="card-text">+61<?php echo $driver_contact_number; ?></p>
                        
                        <hr class="my-4 border-dark">
                        
                        <!-- Pricing -->
                        <h4 class="card-title">Pricing Details</h4>
                        
                        <p class="mb-2 card-text"><b>Subtotal:</b> A$<?php echo number_format($discount_price, 2, '.', ',');?></p>
                
                        <?php
                        if ($discount == TRUE) {
                            echo '<p class="text-danger mb-2 card-text"><b>Discount:</b> A$' . number_format($discounted_total_price, 2, '.', ',') . '</p>';

                        }
                        ?>
                        
                        <p class="mb-2 card-text"><b>GST:</b> A$<?php echo number_format($gst, 2, '.', ',');?></p>

                        <p class="mt-3 card-text"><b>Total Price <span class="text-muted font-italic font-weight-bold small">(Incl. GST)</span>:</b> A$<?php echo number_format($total_price + $gst, 2, '.', ',');?></p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <form action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php'); ?>" method="get" onSubmit="submitButton()">
                    <h4>Billing Details</h4>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label for="billingFirstName">First Name</label>
                            
                            <input type="text" id="billingFirstName" name="billingFirstName" class="form-control <?php echo !empty($billing_first_name_err) ? 'border border-danger' : ''; ?>" value="<?php echo !empty($_POST['billingFirstName']) ? $_POST['billingFirstName'] : $driver_first_name; ?>">
                            
                            <?php
                            if (isset($billing_first_name_err) && !empty($billing_first_name_err)) {
                                echo '<p class="text-danger mb-0">' . $billing_first_name_err . '</p>';

                            }
                            ?>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="billingLastName">Last Name</label>
                            
                            <input type="text" id="billingLastName" name="billingLastName" class="form-control <?php echo !empty($billing_last_name_err) ? 'border border-danger' : ''; ?>" value="<?php echo !empty($_POST['billingLastName']) ? $_POST['billingLastName'] : $driver_last_name; ?>">
                            
                            <?php
                            if (isset($billing_last_name_err) && !empty($billing_last_name_err)) {
                                echo '<p class="text-danger mb-0">' . $billing_last_name_err . '</p>';

                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label for="billingEmailAddress">Email Address</label>
                            
                            <input type="text" id="billingEmailAddress" name="billingEmailAddress" class="form-control <?php echo !empty($billing_email_address_err) ? 'border border-danger' : ''; ?>" value="<?php echo !empty($_POST['billingEmailAddress']) ? $_POST['billingEmailAddress'] : $driver_email_address; ?>">
                            
                            <?php
                            if (isset($billing_email_address_err) && !empty($billing_email_address_err)) {
                                echo '<p class="text-danger mb-0">' . $billing_email_address_err . '</p>';

                            }
                            ?>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="billingContactNumber">Contact Number</label>
                            
                            <input type="text" id="billingContactNumber" name="billingContactNumber" class="form-control <?php echo !empty($billing_contact_number_err) ? 'border border-danger' : ''; ?>" value="<?php echo !empty($_POST['billingContactNumber']) ? $_POST['billingContactNumber'] : $driver_contact_number; ?>">
                            
                            <?php
                            if (isset($billing_contact_number_err) && !empty($billing_contact_number_err)) {
                                echo '<p class="text-danger mb-0">' . $billing_contact_number_err . '</p>';

                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label for="billingAddress1">Address 1</label>
                            
                            <input type="text" id="billingAddress1" name="billingAddress1" class="form-control <?php echo !empty($billing_address_1_err) ? 'border border-danger' : ''; ?>" value="<?php echo $_POST['billingAddress1']; ?>">
                            
                            <?php
                            if (isset($billing_address_1_err) && !empty($billing_address_1_err)) {
                                echo '<p class="text-danger mb-0">' . $billing_address_1_err . '</p>';

                            }
                            ?>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="billingAddress2">Address 2</label>
                            
                            <input type="text" id="billingAddress2" name="billingAddress2" class="form-control <?php echo !empty($billing_address_2_err) ? 'border border-danger' : ''; ?>" value="<?php echo $_POST['billingAddress2']; ?>">
                            
                            <?php
                            if (isset($billing_address_2_err) && !empty($billing_address_2_err)) {
                                echo '<p class="text-danger mb-0">' . $billing_address_2_err . '</p>';

                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="form-row mt-4">
                        <div class="col-md-3">
                            <label for="billingSuburb">Suburb</label>
                            
                            <input type="text" id="billingSuburb" name="billingSuburb" class="form-control <?php echo !empty($billing_suburb_err) ? 'border border-danger' : ''; ?>" value="<?php echo $_POST['billingSuburb']; ?>">
                            
                            <?php
                            if (isset($billing_suburb_err) && !empty($billing_suburb_err)) {
                                echo '<p class="text-danger mb-0">' . $billing_suburb_err . '</p>';

                            }
                            ?>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="billingPostalCode">Postal Code</label>
                            
                            <input type="text" id="billingPostalCode" name="billingPostalCode" class="form-control <?php echo !empty($billing_postal_code_err) ? 'border border-danger' : ''; ?>" value="<?php echo $_POST['billingPostalCode']; ?>">
                            
                            <?php
                            if (isset($billing_postal_code_err) && !empty($billing_postal_code_err)) {
                                echo '<p class="text-danger mb-0">' . $billing_postal_code_err . '</p>';

                            }
                            ?>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="billingState">State</label>
                            
                            <input type="text" id="billingState" name="billingState" class="form-control <?php echo !empty($billing_state_err) ? 'border border-danger' : ''; ?>" value="<?php echo $_POST['billingState']; ?>">
                            
                            <?php
                            if (isset($billing_state_err) && !empty($billing_state_err)) {
                                echo '<p class="text-danger mb-0">' . $billing_state_err . '</p>';

                            }
                            ?>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="billingCountry">Country</label>
                            
                            <select id="billingCountry" class="form-control <?php echo !empty($billing_country_err) ? 'border border-danger' : ''; ?>" name="billingCountry">
                                <option value="" selected>Select Country</option>

                                <?php
                                $get_country_list_sql = 'SELECT * FROM country ORDER BY country ASC';
                                $get_country_list = mysqli_query($conn, $get_country_list_sql);

                                if (mysqli_num_rows($get_country_list) > 0) {
                                    while ($country_list = mysqli_fetch_assoc($get_country_list)) {
                                        $selected_country = (isset($_POST['billingCountry']) && $_POST['billingCountry'] == $country_list['country_id']) ? ' selected="selected"' : '';

                                        echo '<option value="' . $country_list['country_id'] . '"' . $selected_country . '>' . $country_list['country'] . '</option>';
                                    }

                                    mysqli_free_result($get_country_list);
                                    
                                }
                                ?>
                            </select>
                            
                            <?php
                            if (isset($billing_country_err) && !empty($billing_country_err)) {
                                echo '<p class="text-danger mb-0">' . $billing_country_err . '</p>';

                            }
                            ?>
                        </div>
                    </div>
                    
                    <hr class="my-5">
                    
                    <h4>Payment Details</h4>
					
					<div class="row form-group align-items-center">
						<label for="billingPaymentMethod" class="col-md-3">Payment Method</label>
						
						<div class="col-md-9">
							<select id="billingPaymentMethod" class="form-control <?php echo !empty($billing_payment_method_err) ? 'border border-danger' : ''; ?>" name="billingPaymentMethod" onChange="showPaymentIcon(this.value)">
                                <option value="" selected>Select Payment Method</option>

                                <?php
                                $payment_method_array = array('american_express' => 'American Express', 'mastercard' => 'MasterCard', 'visa' => 'Visa');

								foreach ($payment_method_array as $method_value => $method_name) {
									$selected_payment_method = (isset($_GET['billingPaymentMethod']) && $_GET['billingPaymentMethod'] == $method_value ? ' selected="selected"' : '');

									echo '<option value="' . $method_value . '" ' . $selected_payment_method . '>' . $method_name . '</option>';

								}
                                ?>
                            </select>
                            
                            <?php
                            if (isset($billing_payment_method_err) && !empty($billing_payment_method_err)) {
                                echo '<p class="text-danger mb-0">' . $billing_payment_method_err . '</p>';

                            }
                            ?>
						</div>
					</div>
                    
                    <div class="row mt-4">
                        <div class="col-lg-6">
                            <div class="card bg-dark text-white">
                                <div class="card-body mt-5 px-4">
									<input type="text" class="form-control-sm w-100 border-0" placeholder="Card Number">
                                    <!--<p class="card-text lead">1234 5678 9012 3456</p>-->
                                    
                                    <div class="row justify-content-md-center mt-3">
                                        <div class="col-md-auto ml-5 mw-100">
											<label for="" class="small ml-5 pl-3">Valid Thru</label>
											
											<input type="text" class="form-control-sm w-25 border-0" placeholder="mm / yy">
                                            <!--<p class="mb-0 card-text"><span class="small">Valid Thru</span> <span id="">01/20</span></p>-->
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-3 align-items-end">
                                        <div class="col-7">
											<input type="text" class="form-control-sm w-100 border-0" placeholder="Name on Card">
                                            <!--<p class="card-text text-uppercase">Xiao Yu Lim</p>-->
                                        </div>
                                        
                                        <div class="col-5">
                                            <img id="paymentIcon" class="float-right w-75 mw-100" src="/moov/assets/payment_mastercard_icon.svg">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 mt-4 mt-lg-0">
                            <div class="card bg-dark text-white">
								<span class="row mx-0 bg-light mt-4" style="height: 35px;"></span>
								
                                <div class="card-body px-4">
                                    <div class="row justify-content-md-center">
                                        <div class="col-md-auto">
											<input type="number" min="000" class="number-hide mr-4 float-right form-control-sm w-25 border-0" placeholder="CCV">
                                            <!--<p class="mb-0 ml-5 card-text"><span id="">003</span></p>-->
                                        </div>
                                    </div>
                                    
                                    <span class="row mt-5" style="height: 47px;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
				
				<script>
					function showPaymentIcon(selectedPaymentMethod) {
						document.getElementById('paymentIcon').src = '/moov/assets/payment_' + selectedPaymentMethod + '_icon.svg';
					}
				</script>
            </div>
        </div>
	</div>

    <?php include 'footer.php'; ?>
</body>
	
</html>

<?php mysqli_close($conn); ?>