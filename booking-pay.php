<?php
session_start();
require_once 'config.php';
$page_name = 'find-cars';

$booking_car_id = $booking_pick_up = $booking_return = $booking_duration = $booking_amount = $booking_location = $booking_location_url = $billing_first_name = $billing_last_name = $billing_email_address = $billing_contact_number = $billing_address_1 = $billing_address_2 = $billing_suburb = $billing_postal_code = $billing_state = $billing_country = $billing_payment_method = $billing_card_number = $billing_temp_card_number = $billing_card_expiry_date = $billing_card_name = $billing_card_cvv = '';
$billing_first_name_err = $billing_last_name_err = $billing_email_address_err = $billing_contact_number_err = $billing_address_1_err = $billing_address_2_err = $billing_suburb_err = $billing_postal_code_err = $billing_state_err = $billing_country_err = $billing_payment_method_err = $billing_err = '';
$billing_card_number_err = $billing_card_expiry_date_err = $billing_card_name_err = $billing_card_cvv_err = FALSE;

$today_date = date('m-y');
$search_filename = array('- ', ' ', '-', '.');
$replace_filename = array('_', '_', '_', '_');
$search_contact_number_symbol = array('-', ' ');
$replace_contact_number_symbol = array('', '');
$search_date_symbol = array('/', '.');
$replace_date_symbol = array('-', '-');

if (empty($_GET['token'])) {
    header('location: /moov/find-cars');

}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = $_POST['bookingId'];
	$booking_location = str_replace('<br/>', ' ', $_POST['bookingLocation']);
	$booking_location_url = $_POST['bookingLocationUrl'];
    
    if (empty(trim($_POST['billingFirstName']))) {
        $billing_first_name_err = 'Please enter your first name.';
        
    } else {
        if (preg_match('/^[a-zA-zw\-\s]{3,100}$/', trim($_POST['billingFirstName']))) {
            $billing_first_name = ucwords(trim($_POST['billingFirstName']));

        } else {
            $billing_first_name_err = 'Please enter a valid first name.';

        }
    }
    
    if (empty(trim($_POST['billingLastName']))) {
        $billing_last_name_err = 'Please enter your last name.';
        
    } else {
        if (preg_match('/^[a-zA-zw\-\s]{2,100}$/', trim($_POST['billingLastName']))) {
            $billing_last_name = ucwords(trim($_POST['billingLastName']));

        } else {
            $billing_last_name_err = 'Please enter a valid last name.';

        }
    }
    
    if (empty(trim($_POST['billingEmailAddress']))) {
        $billing_email_address_err = 'Please enter your email address.';
        
    } else {
        $billing_email_address = trim($_POST['billingEmailAddress']);
        
    }
    
    if (empty(trim($_POST['billingContactNumber']))) {
        $billing_contact_number_err = 'Please enter your contact number.';
        
    } else {
        $temp_contact_number = trim($_POST['billingContactNumber']);
				
        $replace_temp_cn = str_replace($search_contact_number_symbol, $replace_contact_number_symbol, $temp_contact_number);

        if (substr($replace_temp_cn, 0, 1) == 0) {
            $replace_temp_cn = substr($replace_temp_cn, 1);
        }

        if (preg_match('/^(0)?(4){1}[0-9]{8}$/', $replace_temp_cn)) {
            $billing_contact_number = $replace_temp_cn;

        } else {
            $billing_contact_number_err = 'Please enter a valid Australian contact number.';

        }
    }
    
    if (empty(trim($_POST['billingAddress1']))) {
        $billing_address_1_err = 'Please enter your billing address.';

    } else {
        if (preg_match('/^[0-9a-zA-Z\s\.\-\/\,]{5,}$/', trim($_POST['billingAddress1']))) {
            $billing_address_1 = ucwords(trim($_POST['billingAddress1']));

        } else {
            $billing_address_1_err = 'Please enter a valid address.';

        }
    }

    if (!empty(trim($_POST['billingAddress2']))) {
        if (preg_match('/^[0-9a-zA-Z\s\.\-\/\,]{5,}$/', trim($_POST['billingAddress2']))) {
            $billing_address_2 = ucwords(trim($_POST['billingAddress2']));

        } else {
            $billing_address_2_err = 'Please enter a valid address.';

        }
    } else {
        $billing_address_2 = NULL;

    }
    
    if (empty(trim($_POST['billingSuburb']))) {
        $billing_suburb_err = 'Please enter your billing suburb.';

    } else {
        if (preg_match('/^[a-zA-Z\s\-]{3,255}$/', trim($_POST['billingSuburb']))) {
            $billing_suburb = ucwords(trim($_POST['billingSuburb']));

        } else {
            $billing_suburb_err = 'Please enter a valid suburb.';

        }
    }

    if (empty(trim($_POST['billingPostalCode']))) {
        $billing_postal_code_err = 'Please enter your billing postal code.';

    } else {
        if (preg_match('/^[0-9]{3,10}$/', trim($_POST['billingPostalCode']))) {
            $billing_postal_code = trim($_POST['billingPostalCode']);

        } else {
            $billing_postal_code_err = 'Please enter a valid postal code.';

        }
    }
    
    if (empty(trim($_POST['billingState']))) {
        $billing_state_err = 'Please enter your billing state.';

    } else {
        if (preg_match('/^[0-9a-zA-Z\s\-]{3,50}$/', trim($_POST['billingState']))) {
            $billing_state = trim($_POST['billingState']);

        } else {
            $billing_state_err = 'Please enter a valid state.';

        }
    }
    
    if (!isset($_POST['billingCountry']) || $_POST['billingCountry'] == '') {
        $billing_country_err = 'Please select your billing country.';

    } else {
        $billing_country = $_POST['billingCountry'];
        
    }
    
    if (!isset($_POST['billingPaymentMethod']) || $_POST['billingPaymentMethod'] == '') {
        $billing_payment_method_err = 'Please select your payment method.';

    } else {
        $billing_payment_method = $_POST['billingPaymentMethod'];
        
    }
    
    if (empty($_POST['billingCardNumber'])) {
        $billing_card_number_err = TRUE;
        $billing_err = 'Please enter your card number. ';
        
    } else {
        $billing_temp_card_number = str_replace(' ', '', $_POST['billingCardNumber']);
        
        if ($billing_payment_method == 'american_express') {
            if (preg_match('/^[0-9]{15}$/', $billing_temp_card_number)) {
                $billing_card_number = $billing_temp_card_number;

            } else {
                $billing_card_number_err = TRUE;
                $billing_err = 'Please enter a valid American Express card number. ';

            }
        } elseif ($billing_payment_method == 'mastercard') {
            if (preg_match('/^[0-9]{16}$/', $billing_temp_card_number)) {
                $billing_card_number = $billing_temp_card_number;

            } else {
                $billing_card_number_err = TRUE;
                $billing_err = 'Please enter a valid Mastercard card number. ';

            }
        } elseif ($billing_payment_method == 'visa') {
            if (preg_match('/^[0-9]{16}$/', $billing_temp_card_number)) {
                $billing_card_number = $billing_temp_card_number;

            } else {
                $billing_card_number_err = TRUE;
                $billing_err = 'Please enter a valid Visa card number. ';

            }
        }
    }
    
    if (empty(trim($_POST['billingCardExpiryDate']))) {
        $billing_card_expiry_date_err = TRUE;
        $billing_err .= 'Please enter your card expiry date. ';
        
    } else {
        $billing_temp_card_expiry_date = str_replace($search_date_symbol, $replace_date_symbol, trim($_POST['billingCardExpiryDate']));
        $temp_month_year = explode('-', $billing_temp_card_expiry_date);
        
        $month = $temp_month_year[0];
        $year = $temp_month_year[1];
        
        if ($month <= 0 || $month > 12 || $year < 20 || $year > (date('y') + 10)) {
            $billing_card_expiry_date_err = TRUE;
            $billing_err .= 'Please enter a valid card expiry date. Format: mm / yy ';
            
        } elseif ($billing_temp_card_expiry_date > $today_date) {
            $billing_card_expiry_date_err = TRUE;
            $billing_err .= 'Your card has expired. Please try with a different card. ';
            
        } else {
            $billing_card_expiry_date = $billing_temp_card_expiry_date;
            
        }
    }
    
    if (empty(trim($_POST['billingCardName']))) {
        $billing_card_name_err = TRUE;
        $billing_err .= 'Please enter your name on card. ';
        
    } else {
        if (preg_match('/^[a-zA-zw\-\s]{3,100}$/', trim($_POST['billingCardName']))) {
            $billing_card_name = ucwords(trim($_POST['billingCardName']));

        } else {
            $billing_card_name_err = TRUE;
            $billing_err .= 'Please enter a valid name on card.';

        }
    }
    
    if (empty(trim($_POST['billingCardCvv']))) {
        $billing_card_cvv_err = TRUE;
        $billing_err .= 'Please enter your card CVV.';
        
    } else {
         if (preg_match('/^[0-9]{3}$/', trim($_POST['billingCardCvv']))) {
            $billing_card_cvv = trim($_POST['billingCardCvv']);

        } else {
            $billing_card_cvv_err = TRUE;
            $billing_err .= 'Please enter a valid card CVV.';

        }
    }
    
    if (empty($billing_first_name_err) && empty($billing_last_name_err) && empty($billing_email_address_err) && empty($billing_contact_number_err) && empty($billing_address_1_err) && empty($billing_address_2_err) && empty($billing_suburb_err) && empty($billing_postal_code_err) && empty($billing_state_err) && empty($billing_country_err) && empty($billing_payment_method_err) && empty($billing_err)) {
        $register_new_billing_sql = 'INSERT INTO booking_billing (first_name, last_name, email_address, contact_number, address_1, address_2, suburb, postal_code, state, country) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $register_new_billing_stmt = mysqli_prepare($conn, $register_new_billing_sql);
        
        mysqli_stmt_bind_param($register_new_billing_stmt, 'sssssssssi', $param_billing_first_name, $param_billing_last_name, $param_billing_email_address, $param_billing_contact_number, $param_billing_address_1, $param_billing_address_2, $param_billing_suburb, $param_billing_postal_code, $param_billing_state, $param_billing_country);
        $param_billing_first_name = $billing_first_name;
        $param_billing_last_name = $billing_last_name;
        $param_billing_email_address = $billing_email_address;
        $param_billing_contact_number = $billing_contact_number;
        $param_billing_address_1 = $billing_address_1;
        $param_billing_address_2 = $billing_address_2;
        $param_billing_suburb = $billing_suburb;
        $param_billing_postal_code = $billing_postal_code;
        $param_billing_state = $billing_state;
        $param_billing_country = $billing_country;
        
        if (mysqli_stmt_execute($register_new_billing_stmt)) {
            $billing_id = mysqli_insert_id($conn);
            
            $register_new_payment_sql = 'INSERT INTO booking_payment (payment_method, card_number, name_on_card, expiry_date, cvv) VALUES (?, ?, ?, ?, ?)';
            $register_new_payment_stmt = mysqli_prepare($conn, $register_new_payment_sql);
            
            mysqli_stmt_bind_param($register_new_payment_stmt, 'ssssi', $param_billing_payment_method, $param_billing_card_number, $param_billing_card_name, $param_billing_card_expiry_date, $param_billing_card_cvv);
            $param_billing_payment_method = $billing_payment_method;
            $param_billing_card_number = $billing_card_number;
            $param_billing_card_name = $billing_card_name;
            $param_billing_card_expiry_date = $billing_card_expiry_date;
            $param_billing_card_cvv = $billing_card_cvv;
            
            if (mysqli_stmt_execute($register_new_payment_stmt)) {
                $payment_id = mysqli_insert_id($conn);
                
                $register_new_booking_sql = 'UPDATE booking SET billing_id = ?, payment_id = ?, status = ? WHERE booking_id = ?';
                $register_new_booking_stmt = mysqli_prepare($conn, $register_new_booking_sql);
                
                mysqli_stmt_bind_param($register_new_booking_stmt, 'iisi', $billing_id, $payment_id, $param_booking_status, $booking_id);
				$param_booking_status = 'Paid';
                
                if (mysqli_stmt_execute($register_new_booking_stmt)) {
                    $mail_email = $_SESSION['moov_user_email_address'];
					$mail_name = $_SESSION['moov_user_display_name'];
					$mail_subject = '[Moov] #' . $booking_id . ' Booking Confirmed';
					$mail_body = '<h1>Dear ' . $_SESSION['moov_user_display_name'] . ',</h1><p class="my-4 text-left">Your booking, #' . $booking_id . ' is now confirmed. Please find your booking details below.</p><ul class="my-4 text-left"><li><b>Car:</b> ' . $_POST['bookingCarName'] . '</li><li><b>Model:</b> ' . $_POST['bookingCarModel'] . '</li><li><b>Pick Up:</b> ' . $_POST['bookingPickUpDate'] . '</li><li><b>Return:</b> ' . $_POST['bookingReturnDate'] . '</li><li><b>Parked Location:</b> <a href="' . $booking_location_url . '">' . $booking_location . '</a></li></ul><p class="my-4 text-left">Thanks for driving with Moov. Safe trip!</p><p class="my-4 text-left">Kind Regards,<br/>Moov Admin</p>';

					require_once 'mail/mail-customer.php';
					
					$_SESSION['moov_user_booking_id'] = $booking_id;
					$_SESSION['moov_user_booking_pick_up'] = $_POST['bookingPickUpDate'];
					$_SESSION['moov_user_booking_return'] = $_POST['bookingReturnDate'];
					$_SESSION['moov_user_booking_location'] = $booking_location;
					$_SESSION['moov_user_booking_location_url'] = $booking_location_url;
					$_SESSION['moov_user_booking_car_image'] = $_POST['bookingCarImage'];
					$_SESSION['moov_user_booking_car_name'] = $_POST['bookingCarName'];
					$_SESSION['moov_user_booking_car_model'] = $_POST['bookingCarModel'];
					$_SESSION['moov_user_booking_location_longitude'] = $_POST['bookingLocationLongitude'];
					$_SESSION['moov_user_booking_location_latitude'] = $_POST['bookingLocationLatitude'];
					
					sleep(10);
					
					unset($_POST);
					header('location: /moov/booking-confirmed');

                } else {
                    $booking_error = TRUE;
					$error_message = mysqli_error($conn);
					
                }
				
				mysqli_stmt_close($register_new_booking_stmt);
				
            } else {
                $booking_error = TRUE;
				$error_message = mysqli_error($conn);
                
            }
			
			mysqli_stmt_close($register_new_payment_stmt);
			
        } else {
			$booking_error = TRUE;
			$error_message = mysqli_error($conn);
			
		}
		
		mysqli_stmt_close($register_new_billing_stmt);
		
    }
}
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

<body id="checkout">
	<?php include 'header.php'; ?>
	
	<div class="container my-3 footer-align-bottom">
	
	<?php
	if (!isset($_SESSION['moov_user_logged_in']) || $_SESSION['moov_user_logged_in'] != TRUE) {
		header('location: /moov/login?url=' . urlencode('/moov/booking-pay?token=' . $_GET['token']));

	} else {
		$booking_id_decode = hex2bin($_GET['token']);
		
		$get_booking_details_sql = 'SELECT customer_id, car_id, pick_up_date, return_date, status FROM booking WHERE booking_id = ?';
		$get_booking_details_stmt = mysqli_prepare($conn, $get_booking_details_sql);
		
		mysqli_stmt_bind_param($get_booking_details_stmt, 'i', $param_booking_id);
		$param_booking_id = $booking_id_decode;
		
		if (mysqli_stmt_execute($get_booking_details_stmt)) {
			$get_booking_details = mysqli_stmt_get_result($get_booking_details_stmt);
			
			while ($booking_details = mysqli_fetch_assoc($get_booking_details)) {
				$saved_customer_id = $booking_details['customer_id'];
				$saved_car_id = $booking_details['car_id'];
				$saved_pick_up_date = $booking_details['pick_up_date'];
				$saved_return_date = $booking_details['return_date'];
				$saved_status = $booking_details['status'];
				
			}
		}
		
		if ($saved_customer_id != $_SESSION['moov_user_account_id']) {
			echo '
				<div class="jumbotron bg-info text-white text-center">
					<h1 class="display-3">Oops!</h1>
					
					<p class="lead mt-5">Looks like you don\'t have the access to this booking page. Please continue to <a class="text-white" href="/moov/find-cars"><u>drive with us here</u></a>.</p>
					
					<p>If you think there is an error, please contact us immediately.</p>
				</div>
			';
			
		} else if($saved_status === 'Paid') {
			echo '
				<div class="jumbotron bg-success text-white text-center">
					<h1 class="display-3">Oops!</h1>
					
					<p class="lead mt-5">Looks like you have already paid for this booking. Please check your booking status at <a class="text-white" href="/moov/my-booking"><u>here</u></a>.</p>
					
					<p>If you think there is an error, please contact us immediately.</p>
				</div>
			';
			
		} else {
			echo '<h1 class="text-center">Checkout</h1>';
        
			if ($booking_error === TRUE) {
				echo '
				<div class="alert alert-warning my-4 alert-dismissible fade show" role="alert">
					Oops! There is an error occurred. Please try again later. If you continue to see this error, please contact us immediately.

				' . (!empty($error_message) ? '<br/><br/><b>Error:</b> ' . $error_message : '') . '

					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				';
			}

			$get_car_details_sql = 'SELECT * FROM moov_portal.car AS car LEFT JOIN moov_portal.car_location ON car.car_id = moov_portal.car_location.car_id WHERE car.car_id = ?';
			$get_car_details_stmt = mysqli_prepare($conn, $get_car_details_sql);

			mysqli_stmt_bind_param($get_car_details_stmt, 'i', $saved_car_id);

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
					$car_longitude = $car_details['longitude'];
					$car_latitude = $car_details['latitude'];

				}
			}

			mysqli_stmt_close($get_car_details_stmt);

			$get_driver_profile_sql = 'SELECT * FROM account WHERE account_id = ?';
			$get_driver_profile_stmt = mysqli_prepare($conn, $get_driver_profile_sql);

			mysqli_stmt_bind_param($get_driver_profile_stmt, 'i', $saved_customer_id);

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

			$pick_up_date = date('Y-m-d, H:i', strtotime($saved_pick_up_date));
			$return_date = date('Y-m-d, H:i', strtotime($saved_return_date));

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
			
			echo '
				<div class="row mt-5">
					<div class="col-md-4">
						<div class="card bg-light rounded">
							<img class="car-image rounded border-0 card-img-top" src="/moov/assets/images/transparent_background.png" style="background-image: url(\'/moov/car-image/' . $car_image_name . '.jpg\'); height: auto !important;">

							<div class="card-body">
								<!-- Car -->
								<h4 class="card-title">Car Details</h4>

								<p class="font-weight-bold lead mb-2 card-text">' . $car_friendly_name . '</p>

								<p class="mb-2 card-text">' . $car_brand . ' ' . $car_model . '</p>

								<p class="card-text">' . $car_location . '</p>

								<hr class="my-4 border-dark">

								<!-- Booking -->
								<h4 class="card-title">Booking Details</h4>

								<p class="mb-2 card-text"><span class="small font-weight-bold">Pick-Up:</span> ' . $pick_up_date . '</p>

								<p class="mb-2 card-text"><span class="small font-weight-bold">Return:</span> ' . $return_date . '</p>

								<p><span class="small font-weight-bold">Duration:</span> ' . $duration_string . '</p>

								<hr class="my-4 border-dark">

								<!-- Driver -->
								<h4 class="card-title">Driver Details</h4>

								<p class="mb-2 card-text">' . $driver_first_name . ' ' . $driver_last_name . '</p>

								<p class="mb-2 card-text">' . $driver_email_address . '</p>

								<p class="card-text">+61' . $driver_contact_number . '</p>

								<hr class="my-4 border-dark">

								<!-- Pricing -->
								<h4 class="card-title">Pricing Details</h4>

								<p class="mb-2 card-text"><b>Subtotal:</b> A$' . number_format($discount_price, 2, '.', ',') . '</p>

								' . ($discount == TRUE ? '<p class="text-danger mb-2 card-text"><b>Discount:</b> A$' . number_format($discounted_total_price, 2, '.', ',') . '</p>' : '') . '

								<p class="mb-2 card-text"><b>GST:</b> A$' . number_format($gst, 2, '.', ',') . '</p>

								<p class="mt-3 card-text"><b>Total Price <span class="text-muted font-italic font-weight-bold small">(Incl. GST)</span>:</b> A$' . number_format($total_price + $gst, 2, '.', ',') . '</p>
							</div>
						</div>
					</div>

					<div class="col-md-8 mt-4 mt-md-0">
						<form action="' . basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php') . '?token=' . $_GET['token'] . '" method="post" onSubmit="submitButton()">
							<input type="hidden" id="bookingId" name="bookingId" value="' . $booking_id_decode . '">
							<input type="hidden" id="bookingPickUpDate" name="bookingPickUpDate" value="' . $pick_up_date . '">
							<input type="hidden" id="bookingReturnDate" name="bookingReturnDate" value="' . $return_date . '">
							<input type="hidden" id="bookingLocation" name="bookingLocation" value="' . $car_location . '">
							<input type="hidden" id="bookingLocationUrl" name="bookingLocationUrl" value="' . $car_location_url . '">
							<input type="hidden" id="bookingCarName" name="bookingCarName" value="' . $car_friendly_name . '">
							<input type="hidden" id="bookingCarModel" name="bookingCarModel" value="' . $car_brand . ' ' . $car_model . '">
							<input type="hidden" id="bookingCarImage" name="bookingCarImage" value="' . $car_image_name . '">
							<input type="hidden" id="bookingLocationLongitude" name="bookingLocationLongitude" value="' . $car_longitude . '">
							<input type="hidden" id="bookingLocationLatitude" name="bookingLocationLatitude" value="' . $car_latitude . '">

							<h4>Billing Details</h4>

							<div class="row mt-4">
								<div class="col-md-6">
									<label for="billingFirstName">First Name</label>

									<input type="text" id="billingFirstName" name="billingFirstName" class="form-control ' . (!empty($billing_first_name_err) ? 'border border-danger' : '') . '" value="' . (isset($_POST['billingFirstName']) ? $_POST['billingFirstName'] : $driver_first_name) . '" onKeyUp="changeEventButton(this)">

									' . (isset($billing_first_name_err) && !empty($billing_first_name_err) ? '<p class="text-danger mb-0">' . $billing_first_name_err . '</p>' : '') . '
								</div>

								<div class="col-md-6 mt-4 mt-md-0">
									<label for="billingLastName">Last Name</label>

									<input type="text" id="billingLastName" name="billingLastName" class="form-control ' . (!empty($billing_last_name_err) ? 'border border-danger' : '') . '" value="' . (isset($_POST['billingLastName']) ? $_POST['billingLastName'] : $driver_last_name) . '" onKeyUp="changeEventButton(this)">

									' . (isset($billing_last_name_err) && !empty($billing_last_name_err) ? '<p class="text-danger mb-0">' . $billing_last_name_err . '</p>' : '') . '
								</div>
							</div>

							<div class="row mt-4">
								<div class="col-md-6">
									<label for="billingEmailAddress">Email Address</label>

									<input type="email" id="billingEmailAddress" name="billingEmailAddress" class="form-control ' . (!empty($billing_email_address_err) ? 'border border-danger' : '') . '" value="' . (isset($_POST['billingEmailAddress']) ? $_POST['billingEmailAddress'] : $driver_email_address) . '" onKeyUp="changeEventButton(this)">

									' . (isset($billing_email_address_err) && !empty($billing_email_address_err) ? '<p class="text-danger mb-0">' . $billing_email_address_err . '</p>' : '') . '
								</div>

								<div class="col-md-6 mt-4 mt-md-0">
									<label for="billingContactNumber">Contact Number</label>

									<input type="text" id="billingContactNumber" name="billingContactNumber" class="form-control ' . (!empty($billing_contact_number_err) ? 'border border-danger' : '') . '" value="' . (isset($_POST['billingContactNumber']) ? $_POST['billingContactNumber'] : $driver_contact_number) . '" onKeyUp="changeEventButton(this)">

									' . (isset($billing_contact_number_err) && !empty($billing_contact_number_err) ? '<p class="text-danger mb-0">' . $billing_contact_number_err . '</p>' : '') . '
								</div>
							</div>

							<div class="row mt-4">
								<div class="col-md-6">
									<label for="billingAddress1">Address 1</label>

									<input type="text" id="billingAddress1" name="billingAddress1" class="form-control ' . (!empty($billing_address_1_err) ? 'border border-danger' : '') . '" value="' . $_POST['billingAddress1'] . '" onKeyUp="changeEventButton(this)">

									' . (isset($billing_address_1_err) && !empty($billing_address_1_err) ? '<p class="text-danger mb-0">' . $billing_address_1_err . '</p>' : '') . '
								</div>

								<div class="col-md-6 mt-4 mt-md-0">
									<label for="billingAddress2">Address 2</label>

									<input type="text" id="billingAddress2" name="billingAddress2" class="form-control ' . (!empty($billing_address_2_err) ? 'border border-danger' : '') . '" value="' . $_POST['billingAddress2'] . '" onKeyUp="changeEventButton(this)">

									' . (isset($billing_address_2_err) && !empty($billing_address_2_err) ? '<p class="text-danger mb-0">' . $billing_address_2_err . '</p>' : '') . '
								</div>
							</div>

							<div class="form-row mt-4">
								<div class="col-md-3">
									<label for="billingSuburb">Suburb</label>

									<input type="text" id="billingSuburb" name="billingSuburb" class="form-control ' . (!empty($billing_suburb_err) ? 'border border-danger' : '') . '" value="' . $_POST['billingSuburb'] . '" onKeyUp="changeEventButton(this)">

									' . (isset($billing_suburb_err) && !empty($billing_suburb_err) ? '<p class="text-danger mb-0">' . $billing_suburb_err . '</p>' : '') . '
								</div>

								<div class="col-md-3 mt-4 mt-md-0">
									<label for="billingPostalCode">Postal Code</label>

									<input type="text" id="billingPostalCode" name="billingPostalCode" class="form-control ' . (!empty($billing_postal_code_err) ? 'border border-danger' : '') . '" value="' . $_POST['billingPostalCode'] . '" onKeyUp="changeEventButton(this)">

									' . (isset($billing_postal_code_err) && !empty($billing_postal_code_err) ? '<p class="text-danger mb-0">' . $billing_postal_code_err . '</p>' : '') . '
								</div>

								<div class="col-md-3 mt-4 mt-md-0">
									<label for="billingState">State</label>

									<input type="text" id="billingState" name="billingState" class="form-control ' . (!empty($billing_state_err) ? 'border border-danger' : '') . '" value="' . $_POST['billingState'] . '" onKeyUp="changeEventButton(this)">

									' . (isset($billing_state_err) && !empty($billing_state_err) ? '<p class="text-danger mb-0">' . $billing_state_err . '</p>' : '') . '
								</div>

								<div class="col-md-3 mt-4 mt-md-0">
									<label for="billingCountry">Country</label>

									<select id="billingCountry" class="form-control ' . (!empty($billing_country_err) ? 'border border-danger' : '') . '" name="billingCountry" onKeyUp="changeEventButton(this)">
										<option value="" selected>Select Country</option>
									
			';

			$get_country_list_sql = 'SELECT * FROM country ORDER BY country ASC';
			$get_country_list = mysqli_query($conn, $get_country_list_sql);

			if (mysqli_num_rows($get_country_list) > 0) {
				while ($country_list = mysqli_fetch_assoc($get_country_list)) {
					$selected_country = (isset($_POST['billingCountry']) && $_POST['billingCountry'] == $country_list['country_id']) ? ' selected="selected"' : '';

					echo '<option value="' . $country_list['country_id'] . '"' . $selected_country . '>' . $country_list['country'] . '</option>';
				}

				mysqli_free_result($get_country_list);

			}
		
			echo '
									</select>

									' . (isset($billing_country_err) && !empty($billing_country_err) ? '<p class="text-danger mb-0">' . $billing_country_err . '</p>' : '') . '
								</div>
							</div>

							<hr class="my-5">

							<h4>Payment Details</h4>

							<div class="row form-group mt-4 align-items-center">
								<label for="billingPaymentMethod" class="col-md-3">Payment Method</label>

								<div class="col-md-9">
									<select id="billingPaymentMethod" class="form-control ' . (!empty($billing_payment_method_err) ? 'border border-danger' : '') . '" name="billingPaymentMethod" onChange="showPaymentIcon(this.value)" onKeyUp="changeEventButton(this)">
										<option value="" selected>Select Payment Method</option>
			';

			$payment_method_array = array('american_express' => 'American Express', 'mastercard' => 'MasterCard', 'visa' => 'Visa');

			foreach ($payment_method_array as $method_value => $method_name) {
				$selected_payment_method = (isset($_POST['billingPaymentMethod']) && $_POST['billingPaymentMethod'] == $method_value ? ' selected="selected"' : '');

				echo '<option value="' . $method_value . '" ' . $selected_payment_method . '>' . $method_name . '</option>';

			}
			
			echo '
									</select>

									' . (isset($billing_payment_method_err) && !empty($billing_payment_method_err) ? '<p class="text-danger mb-0">' . $billing_payment_method_err . '</p>' : '') . '
								</div>
							</div>

							<div id="paymentCard" class="row mt-4 ' . (!empty($billing_err) && empty($billing_payment_method_err) ? 'd-flex' : '') . '">
								<!-- Credit Card Front -->
								<div class="col-lg-6">
									<div class="card bg-dark text-white">
										<div class="card-body mt-5 px-4">
											<label for="billingCardNumber" class="sr-only">Card Number</label>

											<input type="text" class="form-control-sm w-100 ' . ($billing_card_number_err == TRUE && empty($billing_payment_method_err) ? 'border border-danger' : 'border-0') . '" id="billingCardNumber" name="billingCardNumber" placeholder="Card Number" value="' . $_POST['billingCardNumber'] . '" onKeyUp="changeEventButton(this)">

											<div class="row justify-content-md-center mt-3">
												<div class="col-md-auto ml-5 mw-100">
													<label for="billingCardExpiryDate" class="small ml-5 pl-3">Valid Thru</label>

													<input type="text" id="billingCardExpiryDate" name="billingCardExpiryDate" class="form-control-sm w-25 ' . ($billing_card_expiry_date_err == TRUE && empty($billing_payment_method_err) ? 'border border-danger' : 'border-0') . '" placeholder="mm / yy" value="' . $_POST['billingCardExpiryDate'] . '" onKeyUp="changeEventButton(this)">
												</div>
											</div>

											<div class="row mt-3 align-items-end">
												<div class="col-7">
													<label for="billingCardName" class="sr-only">Name on Card</label>

													<input type="text" id="billingCardName" name="billingCardName" class="form-control-sm w-100 ' .( $billing_card_name_err == TRUE && empty($billing_payment_method_err) ? 'border border-danger' : 'border-0') . '" placeholder="Name on Card" value="' . $_POST['billingCardName'] . '" onKeyUp="changeEventButton(this)">
												</div>

												<div class="col-5">
													<img id="paymentIcon" class="float-right w-75 mw-100">
												</div>
											</div>
										</div>
									</div>
								</div>

								<!-- Credit Card Rear -->
								<div class="col-lg-6 mt-4 mt-lg-0">
									<div class="card bg-dark text-white">
										<span class="row mx-0 bg-light mt-4" style="height: 35px;"></span>

										<div class="card-body px-4">
											<div class="row justify-content-md-center">
												<div class="col-md-auto">
													<label for="billingCardCvv" class="sr-only">CVV</label>

													<input type="number" min="000" max="999" id="billingCardCvv" name="billingCardCvv" class="number-hide w-25 mr-4 float-right form-control-sm ' . ($billing_card_cvv_err == TRUE && empty($billing_payment_method_err) ? 'border border-danger' : 'border-0') . '" placeholder="CCV" value="' . $_POST['billingCardCvv'] . '" onKeyUp="changeEventButton(this)">
												</div>
											</div>

											<span class="row mt-5" style="height: 53px;"></span>
										</div>
									</div>
								</div>
							</div>

							' . (isset($billing_err) && !empty($billing_err) && empty($billing_payment_method_err) ? '<p class="text-danger mb-0">' . $billing_err . '</p>' : '') . '

							<button id="paySubmitButton" type="submit" class="mt-5 btn btn-secondary btn-block">
								<span id="submitButton">Pay</span>

								<img id="processingIcon" src="/moov/assets/images/processing_icon.svg" class="processing-icon d-none">
								<span id="processingButton" class="d-none">Processing...</span>
							</button>
						</form>

						<script>
							document.getElementById(\'checkout\').onload = function () {
								document.getElementById(\'paymentCard\').style.display = \'none\';

							}

							function showPaymentIcon(selectedPaymentMethod) {
								document.getElementById(\'paymentIcon\').src = \'/moov/assets/images/payment_\' + selectedPaymentMethod + \'_icon.svg\';
								document.getElementById(\'paymentCard\').style.display = \'flex\';

							}

							function submitButton() {
								document.getElementById(\'paySubmitButton\').disabled = true;
								document.getElementById(\'submitButton\').classList.add(\'d-none\');
								document.getElementById(\'processingIcon\').classList.add(\'d-inline-block\');
								document.getElementById(\'processingIcon\').classList.remove(\'d-none\');
								document.getElementById(\'processingButton\').classList.remove(\'d-none\');

							}

							function changeEventButton(event) {
								if (event.keyCode == 13) {
									event.preventDefault;

									document.getElementById(\'paySubmitButton\').disabled = true;
									document.getElementById(\'submitButton\').classList.add(\'d-none\');
									document.getElementById(\'processingIcon\').classList.add(\'d-inline-block\');
									document.getElementById(\'processingIcon\').classList.remove(\'d-none\');
									document.getElementById(\'processingButton\').classList.remove(\'d-none\');

								}
							}
						</script>
					</div>
				</div>
			';
			
		}
	}
	?>
		
	</div>

    <?php include 'footer.php'; ?>
</body>
	
</html>

<?php mysqli_close($conn); ?>