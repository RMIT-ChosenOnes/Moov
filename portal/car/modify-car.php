<?php
session_start();
require_once '../config.php';
$parent_page_name = 'car';
$page_name = basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php');

$car_image = $car_friendly_name = $car_registration_number = $car_brand = $car_model = $car_types = $car_manufacture_year = $car_price_per_hour = $car_transmission_type = $car_fuel_type = $car_color = $car_seat = $car_door = $car_address_1 = $car_address_2 = $car_suburb = $car_temp_postal_code = $car_postal_code = $car_state = $car_longitude = $car_latitude = '';
$car_image_err = $car_friendly_name_err = $car_registration_number_err = $car_brand_err = $car_model_err = $car_type_err = $car_manufacture_year_err = $car_price_per_hour_err = $car_transmission_type_err = $car_fuel_type_err = $car_color_err = $car_seat_err = $car_door_err = $car_features_err = $car_address_1_err = $car_address_2_err = $car_suburb_err = $car_postal_code_err = $car_state_err = $car_longitude_err = $car_latitude_err = '';

$accepted_max_year = date('Y');
$accepted_min_year = $accepted_max_year - 50;
$search_filename = array('- ', ' ', '-', '.');
$replace_filename = array('_', '_', '_', '_');
$car_image_save_directory = '/var/www/html/moov/car-image/';

if (!isset($_GET['id']) || empty($_GET['id'])) {
	header('location: /moov/portal/car/');
	
} else {
	$car_id = $_GET['id'];
	
}

if (isset($_SESSION['moov_portal_logged_in']) && $_SESSION['moov_portal_logged_in'] == TRUE) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    	if (empty(trim($_POST['carFriendlyName']))) {
            $car_friendly_name_err = 'Please enter a friendly name for this car.';
            
        } else {
            if (preg_match('/^[a-zA-Z\s]{3,100}$/', trim($_POST['carFriendlyName']))) {
                $car_friendly_name = ucwords(trim($_POST['carFriendlyName']));

            } else {
                $car_friendly_name_err = 'Please enter a valid friendly name.';

            }
        }
		
		if (empty(trim($_POST['carRegistrationNumber']))) {
            $car_registration_number_err = 'Please enter the car registration number.';
            
        } else {
            $check_registration_number_duplication_sql = 'SELECT car_id FROM car WHERE registration_number = ?';
            $check_registration_number_duplication_stmt = mysqli_prepare($conn, $check_registration_number_duplication_sql);
            
            mysqli_stmt_bind_param($check_registration_number_duplication_stmt, 's', $param_temp_car_registration_number);
            $param_temp_car_registration_number = trim($_POST['carRegistrationNumber']);
            
            if (mysqli_stmt_execute($check_registration_number_duplication_stmt)) {
                mysqli_stmt_store_result($check_registration_number_duplication_stmt);
                
                if (mysqli_stmt_num_rows($check_registration_number_duplication_stmt) > 0) {
					mysqli_stmt_bind_result($check_registration_number_duplication_stmt, $saved_car_id);
					mysqli_stmt_fetch($check_registration_number_duplication_stmt);
					
					if ($saved_car_id == $car_id) {
						if (preg_match('/^[0-9A-Z\.\s]{2,8}$/', strtoupper(trim($_POST['carRegistrationNumber'])))) {
							$car_registration_number = strtoupper(trim($_POST['carRegistrationNumber']));

						} else {
							$car_registration_number_err = 'Please enter a valid car registration number.';

						}
					} else {
						$car_registration_number_err = 'Car registration number is already in use. Please try another registration number.';
						
					}
                } else {
					if (preg_match('/^[0-9A-Z\.\s]{2,8}$/', strtoupper(trim($_POST['carRegistrationNumber'])))) {
						$car_registration_number = strtoupper(trim($_POST['carRegistrationNumber']));

					} else {
						$car_registration_number_err = 'Please enter a valid car registration number.';

					}
				}
            } else {
                $register_error = TRUE;
                $error_message = mysqli_stmt_error($conn);
                
            }
            
            mysqli_stmt_close($check_registration_number_duplication_stmt);
            
        }
		
		if (!isset($_POST['carBrand']) || $_POST['carBrand'] == '') {
            $car_brand_err = 'Please select the car brand.';
            
        } else {
            $car_brand = $_POST['carBrand'];
            
        }
        
        if (empty(trim($_POST['carModel']))) {
            $car_model_err = 'Please enter the car model.';
            
        } else {
            if (preg_match('/^[0-9a-zA-Z\.\s\-]{3,100}$/', trim($_POST['carModel']))) {
                $car_model = ucwords(trim($_POST['carModel']));

            } else {
                $car_model_err = 'Please enter a valid car model.';

            }
        }
        
        if (!isset($_POST['carType']) || $_POST['carType'] == '') {
            $car_type_err = 'Please select the car type.';
            
        } else {
            $car_types = $_POST['carType'];
            
        }
        
        if (empty(trim($_POST['carManufactureYear']))) {
            $car_manufacture_year_err = 'Please enter the car manufacture year.';
            
        } else {
            if (preg_match('/^[0-9]{4}$/', trim($_POST['carManufactureYear']))) {
                if (trim($_POST['carManufactureYear']) >= $accepted_min_year && trim($_POST['carManufactureYear']) <= $accepted_max_year) {
                    $car_manufacture_year = trim($_POST['carManufactureYear']);
                    
                } else {
                    $car_manufacture_year_err = 'Please enter a valid car manufacture year.';
                    
                }
            } else {
                $car_manufacture_year_err = 'Please enter a valid car manufacture year.';

            }
        }
        
        if (empty(trim($_POST['carPricePerHour']))) {
            $car_price_per_hour_err = 'Please enter the price per hour for this car.';
            
        } else {
            if (preg_match('/^[1-9][0-9\.]*$/', trim($_POST['carPricePerHour']))) {
                $car_price_per_hour = trim($_POST['carPricePerHour']);
                
            } else {
                $car_price_per_hour_err = 'Please enter a valid price per hour.';

            }
        }
		
		if (!isset($_POST['carTransmissionType']) || $_POST['carTransmissionType'] == '') {
            $car_transmission_type_err = 'Please select the car transmission type.';
            
        } else {
            $car_transmission_type = $_POST['carTransmissionType'];
            
        }
        
        if (!isset($_POST['carFuelType']) || $_POST['carFuelType'] == '') {
            $car_fuel_type_err = 'Please select the car fuel type.';
            
        } else {
            $car_fuel_type = $_POST['carFuelType'];
            
        }
        
        if (empty(trim($_POST['carColor']))) {
            $car_color_err = 'Please enter the car color.';
            
        } else {
            if (preg_match('/^[a-zA-Z\s]{3,100}$/', trim($_POST['carColor']))) {
                $car_color = ucfirst(trim($_POST['carColor']));

            } else {
                $car_color_err = 'Please enter a valid car color.';

            }
        }
        
        if (empty(trim($_POST['carSeat']))) {
            $car_seat_err = 'Please enter the number of car seat.';
            
        } else {
            if (preg_match('/^[0-9]{1,2}$/', trim($_POST['carSeat']))) {
                $car_seat = trim($_POST['carSeat']);

            } else {
                $car_seat_err = 'Please enter a valid number of car seat.';

            }
        }
        
        if (empty(trim($_POST['carDoor']))) {
            $car_door_err = 'Please enter the number of car door.';
            
            
        } else {
            if (preg_match('/^[0-9]{1}$/', trim($_POST['carDoor']))) {
                $car_door = trim($_POST['carDoor']);

            } else {
                $car_door_err = 'Please enter a valid number of car door.';

            }
        }
        
        if (!isset($_POST['carFeatures'])) {
            $car_features_err = 'Please select at least one car feature.';
            
        } else {
            $check_wd = array(1, 2, 3);
            
            if (count(array_intersect($_POST['carFeatures'], $check_wd)) > 1) {
                $car_features_err = 'You can only select one wheel drive option.';
                
            }
        }
        
        if (empty(trim($_POST['carAddress1']))) {
            $car_address_1_err = 'Please enter the address of the car.';
            
        } else {
            if (preg_match('/^[0-9a-zA-Z\s\.\-\/\,]{5,}$/', trim($_POST['carAddress1']))) {
                $car_address_1 = ucwords(trim($_POST['carAddress1']));

            } else {
                $car_address_1_err = 'Please enter a valid address of the car located.';

            }
        }
        
        if (!empty(trim($_POST['carAddress2']))) {
            if (preg_match('/^[0-9a-zA-Z\s\.\-\/\,]{5,}$/', trim($_POST['carAddress2']))) {
                $car_address_2 = ucwords(trim($_POST['carAddress2']));

            } else {
                $car_address_2_err = 'Please enter a valid address of the car located.';

            }
        } else {
            $car_address_2 = NULL;
            
        }
        
        if (empty(trim($_POST['carSuburb']))) {
            $car_suburb_err = 'Please enter the suburb of the car.';
            
        } else {
            if (preg_match('/^[a-zA-Z\s\-]{5,255}$/', trim($_POST['carSuburb']))) {
                $car_suburb = ucwords(trim($_POST['carSuburb']));

            } else {
                $car_suburb_err = 'Please enter a valid suburb of the car located.';

            }
        }
        
        if (empty(trim($_POST['carPostalCode']))) {
            $car_postal_code_err = 'Please enter the postal code of the car.';
            
        } else {
            if (preg_match('/^[0-9]{4}$/', trim($_POST['carPostalCode']))) {
                $car_temp_postal_code = trim($_POST['carPostalCode']);

            } else {
                $car_postal_code_err = 'Please enter a valid postal code of the car located.';

            }
        }
        
        if (!isset($_POST['carState']) || $_POST['carState'] == '') {
            $car_state_err = 'Please select the state of the car.';
            
        } else {
            $car_state = $_POST['carState'];
            
            if ($car_state == 'act') {
                if (($car_temp_postal_code >= 200 && $car_temp_postal_code <= 299) || ($car_temp_postal_code >= 2600 && $car_temp_postal_code <= 2619) || ($car_temp_postal_code >= 2900 && $car_temp_postal_code <= 2920)) {
                    $car_postal_code = $car_temp_postal_code;
                    
                } else {
                    $car_postal_code_err = 'Please enter a valid postal code for Australian Capital Territory.';
                    
                }
            } elseif ($car_state == 'nsw') {
                if (($car_temp_postal_code >= 1000 && $car_temp_postal_code <= 2599) || ($car_temp_postal_code >= 2620 && $car_temp_postal_code <= 2899) || ($car_temp_postal_code >= 2921 && $car_temp_postal_code <= 2999)) {
                    $car_postal_code = $car_temp_postal_code;
                    
                } else {
                    $car_postal_code_err = 'Please enter a valid postal code for New South Wales.';
                    
                }
            } elseif ($car_state == 'nt') {
                if ($car_temp_postal_code >= 800 && $car_temp_postal_code <= 999) {
                    $car_postal_code = $car_temp_postal_code;
                    
                } else {
                    $car_postal_code_err = 'Please enter a valid postal code for Northern Territory.';
                    
                }
            } elseif ($car_state == 'qld') {
                if (($car_temp_postal_code >= 4000 && $car_temp_postal_code <= 4999) || ($car_temp_postal_code >= 9000 && $car_temp_postal_code <= 9999)) {
                    $car_postal_code = $car_temp_postal_code;
                    
                } else {
                    $car_postal_code_err = 'Please enter a valid postal code for Queensland.';
                    
                }
            } elseif ($car_state == 'sa') {
                if ($car_temp_postal_code >= 5000 && $car_temp_postal_code <= 5999) {
                    $car_postal_code = $car_temp_postal_code;
                    
                } else {
                    $car_postal_code_err = 'Please enter a valid postal code for South Australia.';
                    
                }
            } elseif ($car_state == 'tas') {
                if ($car_temp_postal_code >= 7000 && $car_temp_postal_code <= 7999) {
                    $car_postal_code = $car_temp_postal_code;
                    
                } else {
                    $car_postal_code_err = 'Please enter a valid postal code for Tasmania.';
                    
                }
            } elseif ($car_state == 'vic') {
                if (($car_temp_postal_code >= 3000 && $car_temp_postal_code <= 3999) || ($car_temp_postal_code >= 8000 && $car_temp_postal_code <= 8999)) {
                    $car_postal_code = $car_temp_postal_code;
                    
                } else {
                    $car_postal_code_err = 'Please enter a valid postal code for Victoria.';
                    
                }
            } elseif ($car_state == 'wa') {
                if ($car_temp_postal_code >= 6000 && $car_temp_postal_code <= 6999) {
                    $car_postal_code = $car_temp_postal_code;
                    
                } else {
                    $car_postal_code_err = 'Please enter a valid postal code for Western Australia.';
                    
                }
            }
        }
        
        if (empty(trim($_POST['carLongitude']))) {
            $car_longitude_err = 'Please enter the longitude of the car.';
            
        } else {
            if (preg_match('/^[0-9\.\-]{4,}$/', trim($_POST['carLongitude']))) {
                $car_longitude = trim($_POST['carLongitude']);

            } else {
                $car_longitude_err = 'Please enter a valid longitude of the car located.';

            }
        }
        
        if (empty(trim($_POST['carLatitude']))) {
            $car_latitude_err = 'Please enter the latitude of the car.';
            
        } else {
            if (preg_match('/^[0-9\.\-]{4,}$/', trim($_POST['carLatitude']))) {
                $car_latitude = trim($_POST['carLatitude']);

            } else {
                $car_latitude_err = 'Please enter a valid latitude of the car located.';

            }
        }
		
		if (isset($_FILES['carImage']) && $_FILES['carImage']['name'] != '') {
            $img_file_name = basename($_FILES['carImage']['name']);
            $img_file_type = strtolower(pathinfo($img_file_name, PATHINFO_EXTENSION));

            if ($_FILES['carImage']['size'] > 1000000) {
                $car_image_err = 'Sorry. Your file is too big. Maximum file size is 1MB. Please try again.';

            } elseif ($img_file_type != 'jpg') {
                $car_image_err = 'Sorry. You have uploaded an unsupported file type. Please try again.';

            } else {
				$car_image_update = TRUE;
				
			}
        }
		
		if (empty($car_image_err) && empty($car_friendly_name_err) && empty($car_registration_number_err) && empty($car_brand_err) && empty($car_model_err) && empty($car_type_err) && empty($car_manufacture_year_err) && empty($car_price_per_hour_err) && empty($car_transmission_type_err) && empty($car_fuel_type_err) && empty($car_color_err) && empty($car_seat_err) && empty($car_door_err) && empty($car_features_err) && empty($car_address_1_err) && empty($car_address_2_err) && empty($car_suburb_err) && empty($car_postal_code_err) && empty($car_state_err) && empty($car_longitude_err) && empty($car_latitude_err)) {
            $update_car_sql = 'UPDATE car SET name = ?, brand = ?, model = ?, registration_number = ?, car_type = ?, price_per_hour = ?, seat = ?, manufacture_year = ?, color = ?, door = ?, transmission_type = ?, fuel_type = ? WHERE car_id = ?';
            
            if ($update_car_stmt = mysqli_prepare($conn, $update_car_sql)) {
                mysqli_stmt_bind_param($update_car_stmt, 'sissisissiiii', $param_car_friendly_name, $param_brand_id, $param_car_model, $param_car_registration_number, $param_car_type, $param_car_price_per_hour, $param_car_seat, $param_car_manufacture_year, $param_car_color, $param_car_door, $param_car_transmission_type, $param_car_fuel_type, $param_car_id);
                
                $param_car_friendly_name = $car_friendly_name;
				$param_brand_id = $car_brand;
                $param_car_model = $car_model;
                $param_car_registration_number = $car_registration_number;
                $param_car_type = $car_types;
                $param_car_price_per_hour = $car_price_per_hour;
                $param_car_seat = $car_seat;
                $param_car_manufacture_year = $car_manufacture_year;
                $param_car_color = $car_color;
                $param_car_door = $car_door;
                $param_car_transmission_type = $car_transmission_type;
                $param_car_fuel_type = $car_fuel_type;
				$param_car_id = $car_id;
                
                if (mysqli_stmt_execute($update_car_stmt)) {
                    // Delete all existing car feature
					$delete_all_feature_sql = 'DELETE FROM car_feature WHERE car_id = ?';
					$delete_all_feature_stmt = mysqli_prepare($conn, $delete_all_feature_sql);
					
					mysqli_stmt_bind_param($delete_all_feature_stmt, 'i', $param_car_id);
					mysqli_stmt_execute($delete_all_feature_stmt);
					
					// Insert car feature into database
					foreach ($_POST['carFeatures'] as $selected_features) {
                        $register_car_feature_sql = 'INSERT INTO car_feature (car_id, feature) VALUES (?, ?)';
                        $register_car_feature_stmt = mysqli_prepare($conn, $register_car_feature_sql);

                        mysqli_stmt_bind_param($register_car_feature_stmt, 'ii', $param_car_id, $param_car_feature);
                        $param_car_feature = $selected_features;

                        mysqli_stmt_execute($register_car_feature_stmt);

                    }
					
					$update_car_location_sql = 'UPDATE car_location SET address_1 = ?, address_2 = ?, suburb = ?, postal_code = ?, state = ?, longitude = ?, latitude = ? WHERE car_id = ?';
					$update_car_location_stmt = mysqli_prepare($conn, $update_car_location_sql);
                    
                    mysqli_stmt_bind_param($update_car_location_stmt, 'sssisssi', $param_car_address_1, $param_car_address_2, $param_car_suburb, $param_car_postal_code, $param_car_state, $param_car_longitude, $param_car_latitude, $param_car_id);
                    $param_car_address_1 = $car_address_1;
                    $param_car_address_2 = $car_address_2;
                    $param_car_suburb = $car_suburb;
                    $param_car_postal_code = $car_postal_code;
                    $param_car_state = $car_state;
                    $param_car_longitude = $car_longitude;
                    $param_car_latitude = $car_latitude;
                    
                    if (mysqli_stmt_execute($update_car_location_stmt)) {
						if ($car_image_update == TRUE) {
							// Get car brand
							$get_car_brand_sql = 'SELECT brand FROM car_brand WHERE brand_id = ?';
							$get_car_brand_stmt = mysqli_prepare($conn, $get_car_brand_sql);
							
							mysqli_stmt_bind_param($get_car_brand_stmt, 'i', $param_brand_id);

							if (mysqli_stmt_execute($get_car_brand_stmt)) {
								mysqli_stmt_store_result($get_car_brand_stmt);

								if (mysqli_stmt_num_rows($get_car_brand_stmt) == 1) {
									mysqli_stmt_bind_result($get_car_brand_stmt, $brand);
									mysqli_stmt_fetch($get_car_brand_stmt);

								}
							}

							$car_temp_image_name = strtolower($brand . '_' . $car_model . '_' . $car_friendly_name);
							$car_image_name = str_replace($search_filename, $replace_filename, $car_temp_image_name);
							
							$car_image_file_type = (!empty($img_file_type) ? $img_file_type : $car_image_temp_file_type);
							$car_image_url = $car_image_save_directory . $car_image_name . '.jpg';

							if (move_uploaded_file($_FILES['carImage']['tmp_name'], $car_image_url)) {
								$_SESSION['moov_portal_car_updated_image'] = TRUE;
								unset($_FILES);

							} else {
								$update_error = TRUE;

							}
						}
						
						$_SESSION['moov_portal_car_updated'] = TRUE;
                        $_SESSION['moov_portal_car_updated_name'] = $car_friendly_name;
						unset($_POST);
						
						if ($update_error != TRUE) {
							header('location: /moov/portal/car/');
							
						}
						
                    } else {
                        $update_error = TRUE;
                        $error_message = mysqli_error($conn);
                        
                    }
                    
                    mysqli_stmt_close($register_car_feature_stmt);
                    mysqli_stmt_close($update_car_location_stmt);
					mysqli_stmt_close($delete_all_feature_stmt);
                    
                } else {
                    $update_error = TRUE;
                    $error_message = mysqli_error($conn);
                    
                }
            }
			
			mysqli_stmt_close($update_car_stmt);
			
        }
	}
} else {
	header('location: /moov/portal/login?url=' . urlencode('/moov/portal/' . $parent_page_name . '/' . $page_name . '?id=' . $car_id));
	
}


?>

<!DOCTYPE html>
<html>

<head>
    <title>Modify Car #<?php echo $car_id; ?> | Moov Portal</title>

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

    <div class="container my-3 footer-align-bottom">
        <h1 class="text-center">Modify Car #<?php echo $car_id; ?></h1>

        <?php
		if (isset($update_error) && $update_error === TRUE) {
            echo '
            <div class="alert alert-warning my-4 alert-dismissible fade show" role="alert">
                Oops! There is an error occurred. Please try again later. If you continue to see this error, please contact the administrator.

			' . (!empty($error_message) ? '<br/><br/><b>Error:</b> ' . $error_message : '') . '

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ';
        }
        ?>

        <form class="mt-5" action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php') . '?id=' . $car_id; ?>" method="post" enctype="multipart/form-data" onSubmit="submiButtton()">
			<?php
			// Get car details based on ID
			$get_car_details_sql = 'SELECT * FROM car WHERE car_id = ?';

			if ($get_car_details_stmt = mysqli_prepare($conn, $get_car_details_sql)) {
				mysqli_stmt_bind_param($get_car_details_stmt, 'i', $param_car_id);

				$param_car_id = $car_id;

				if (mysqli_stmt_execute($get_car_details_stmt)) {
					$get_car_details = mysqli_stmt_get_result($get_car_details_stmt);

					while ($car_details = mysqli_fetch_assoc($get_car_details)) {
						// Get car brand
						$get_car_brand_sql = 'SELECT brand FROM car_brand WHERE brand_id = ?';
						$get_car_brand_stmt = mysqli_prepare($conn, $get_car_brand_sql);
						
						mysqli_stmt_bind_param($get_car_brand_stmt, 'i', $param_brand_id);
						$param_brand_id = $car_details['brand'];
						
						if (mysqli_stmt_execute($get_car_brand_stmt)) {
							$get_car_brand = mysqli_stmt_get_result($get_car_brand_stmt);
							
							while ($car_brand = mysqli_fetch_assoc($get_car_brand)) {
								$saved_car_brand = $car_brand['brand'];
								
							}
						}
						
						// Get car type
						$get_car_type_sql = 'SELECT type FROM car_type WHERE type_id = ?';
						$get_car_type_stmt = mysqli_prepare($conn, $get_car_type_sql);
						
						mysqli_stmt_bind_param($get_car_type_stmt, 'i', $param_type_id);
						$param_type_id = $car_details['car_type'];
						
						if (mysqli_stmt_execute($get_car_type_stmt)) {
							$get_car_type = mysqli_stmt_get_result($get_car_type_stmt);
							
							while ($car_type = mysqli_fetch_assoc($get_car_type)) {
								$saved_car_type = $car_type['type'];
								
							}
						}
						
						// Get car transmission type
						$get_car_transmission_type_sql = 'SELECT transmission FROM car_transmission WHERE transmission_id = ?';
						$get_car_transmission_type_stmt = mysqli_prepare($conn, $get_car_transmission_type_sql);
						
						mysqli_stmt_bind_param($get_car_transmission_type_stmt, 'i', $param_transmission_type_id);
						$param_transmission_type_id = $car_details['transmission_type'];
						
						if (mysqli_stmt_execute($get_car_transmission_type_stmt)) {
							$get_car_transmission_type = mysqli_stmt_get_result($get_car_transmission_type_stmt);
							
							while ($car_transmission_type = mysqli_fetch_assoc($get_car_transmission_type)) {
								$saved_car_transmission_type = $car_transmission_type['transmission'];
								
							}
						}
						
						// Get car fuel type
						$get_car_fuel_type_sql = 'SELECT fuel FROM car_fuel WHERE fuel_id = ?';
						$get_car_fuel_type_stmt = mysqli_prepare($conn, $get_car_fuel_type_sql);
						
						mysqli_stmt_bind_param($get_car_fuel_type_stmt, 'i', $param_fuel_type_id);
						$param_fuel_type_id = $car_details['fuel_type'];
						
						if (mysqli_stmt_execute($get_car_fuel_type_stmt)) {
							$get_car_fuel_type = mysqli_stmt_get_result($get_car_fuel_type_stmt);
							
							while ($car_fuel_type = mysqli_fetch_assoc($get_car_fuel_type)) {
								$saved_car_fuel_type = $car_fuel_type['fuel'];
								
							}
						}
						
						// Get car location
						$get_car_location_sql = 'SELECT * FROM car_location WHERE car_id = ?';
						$get_car_location_stmt = mysqli_prepare($conn, $get_car_location_sql);
						
						mysqli_stmt_bind_param($get_car_location_stmt, 'i', $param_car_id);
						
						if (mysqli_stmt_execute($get_car_location_stmt)) {
							$get_car_location = mysqli_stmt_get_result($get_car_location_stmt);
							
							while ($car_location = mysqli_fetch_assoc($get_car_location)) {
								$saved_car_address_1 = $car_location['address_1'];
								$saved_car_address_2 = $car_location['address_2'];
								$saved_car_suburb = $car_location['suburb'];
								$saved_car_postal_code = $car_location['postal_code'];
								$saved_car_state = $car_location['state'];
								$saved_car_longitude = $car_location['longitude'];
								$saved_car_latitude = $car_location['latitude'];
								
							}
						}
						
						// Get car feature
						$get_car_feature_sql = 'SELECT feature FROM car_feature WHERE car_id = ?';
						$get_car_feature_stmt = mysqli_prepare($conn, $get_car_feature_sql);
						
						mysqli_stmt_bind_param($get_car_feature_stmt, 'i', $param_car_id);
						
						if (mysqli_stmt_execute($get_car_feature_stmt)) {
							$get_car_feature = mysqli_stmt_get_result($get_car_feature_stmt);
							
							while ($car_feature = mysqli_fetch_assoc($get_car_feature)) {
								$saved_car_feature[] = $car_feature['feature'];
								
							}
						}
						
						$saved_car_friendly_name = $car_details['name'];
						$saved_car_model = $car_details['model'];
						$saved_car_registration_number = $car_details['registration_number'];
						$saved_car_price_per_hour = number_format($car_details['price_per_hour'], 2, ".", ",");
						$saved_car_seat = $car_details['seat'];
						$saved_car_manufacture_year = $car_details['manufacture_year'];
						$saved_car_color = $car_details['color'];
						$saved_car_door = $car_details['door'];
						$saved_car_brand_id = $car_details['brand'];
						$saved_car_type_id = $car_details['car_type'];
						$saved_car_transmission_type_id = $car_details['transmission_type'];
						$saved_car_fuel_type_id = $car_details['fuel_type'];
						
						mysqli_stmt_close($get_car_brand_stmt);
						mysqli_stmt_close($get_car_type_stmt);
						mysqli_stmt_close($get_car_transmission_type_stmt);
						mysqli_stmt_close($get_car_fuel_type_stmt);
						mysqli_stmt_close($get_car_location_stmt);
						mysqli_stmt_close($get_car_feature_stmt);
						
					}
				}
			}
			
			$car_temp_image_name = strtolower($saved_car_brand . '_' . $saved_car_model . '_' . $saved_car_friendly_name);
            $car_image_name = str_replace($search_filename, $replace_filename, $car_temp_image_name);

			mysqli_stmt_close($get_car_details_stmt);
			?>

			<div class="container-fluid mt-sm-4">
				<div class="row">
					<div class="col-sm-5 mb-5 mb-sm-0">
						<div class="text-center mt-5">
							<img id="carImage" class="car-image rounded border-0" src="/moov/portal/assets/images/transparent_background.png" style="background-image: url('/moov/car-image/<?php echo $car_image_name; ?>.jpg'); height: auto !important;">
						</div>

						<div class="custom-file mt-5 mb-4">
							<input type="file" class="custom-file-input" id="carImage" name="carImage" aria-describedby="carImageFileName" onChange="showUploadImage(event), showUploadFileName(this.value)" onKeyUp="changeEventButton(this)">

							<label id="carImageLabel" class="custom-file-label" for="carImage">Update Car Image</label>

							<small id="carImageFileName" class="form-text text-muted"><?php echo empty($car_image_err) ? 'Max. file size is 1MB. Supported file type: JPG.' : ''; ?></small>

							<?php
							if (isset($car_image_err) && !empty($car_image_err)) {
								echo '<p id="carImageError" class="text-danger mb-0">' . $car_image_err . '</p>';

							}
							?>
						</div>
					</div>

					<!-- Car Legal Details -->
					<div class="col-sm-7 mt-3 mt-sm-0">
						<h4 class="text-center">Car Legal Details</h4>
						
						<div class="form-group mt-4">
							<label for="carFriendlyName">Car Name</label>
							
							<input type="text" class="form-control <?php echo !empty($car_friendly_name_err) ? 'border border-danger' : ''; ?>" id="carFriendlyName" name="carFriendlyName" value="<?php echo isset($_POST['carFriendlyName']) ? $_POST['carFriendlyName'] : $saved_car_friendly_name; ?>">
							
							<?php
							if (isset($car_friendly_name_err) && !empty($car_friendly_name_err)) {
								echo '<p class="text-danger mb-0">' . $car_friendly_name_err . '</p>';

							}
							?>
						</div>

						<div class="form-group mt-4">
							<label for="carRegistrationNumber">Registration Number</label>
							
							<input type="text" class="form-control <?php echo !empty($car_registration_number_err) ? 'border border-danger' : ''; ?>" id="carRegistrationNumber" name="carRegistrationNumber" value="<?php echo isset($_POST['carRegistrationNumber']) ? $_POST['carRegistrationNumber'] : $saved_car_registration_number; ?>">
							
							<?php
							if (isset($car_registration_number_err) && !empty($car_registration_number_err)) {
								echo '<p class="text-danger mb-0">' . $car_registration_number_err . '</p>';

							}
							?>
						</div>
						
						<div class="form-group mt-4">
							<label for="carBrand">Brand</label>

							<select id="carBrand" class="form-control <?php echo !empty($car_brand_err) ? 'border border-danger' : ''; ?>" name="carBrand" onKeyUp="changeEventButton(this)">
								<option value="" selected>Select Car Brand</option>

								<?php
								$get_brand_sql = 'SELECT * FROM car_brand ORDER BY brand ASC';
								$get_brand = mysqli_query($conn, $get_brand_sql);

								if (mysqli_num_rows($get_brand) > 0) {
									while ($brand = mysqli_fetch_assoc($get_brand)) {
										$selected_brand = ((isset($_POST['carBrand']) && $_POST['carBrand'] == $brand['brand_id']) || (!isset($_POST['carBrand']) && $saved_car_brand_id == $brand['brand_id'])) ? ' selected="selected"' : '';

										echo '<option value="' . $brand['brand_id'] . '"' . $selected_brand . '>' . $brand['brand'] . '</option>';

									}

									mysqli_free_result($get_brand);
									
								}
								?>
							</select>

							<?php
							if (isset($car_brand_err) && !empty($car_brand_err)) {
								echo '<p class="text-danger mb-0">' . $car_brand_err . '</p>';

							}
							?>
						</div>
						
						<div class="form-group mt-4">
							<label for="carModel">Model</label>
							
							<input type="text" class="form-control <?php echo !empty($car_model_err) ? 'border border-danger' : ''; ?>" id="carModel" name="carModel" value="<?php echo isset($_POST['carModel']) ? $_POST['carModel'] : $saved_car_model; ?>">
							
							<?php
							if (isset($car_model_err) && !empty($car_model_err)) {
								echo '<p class="text-danger mb-0">' . $car_model_err . '</p>';

							}
							?>
						</div>
						
						<div class="form-group mt-4">
							<label for="carType">Car Type</label>

							<select id="carType" class="form-control <?php echo !empty($car_type_err) ? 'border border-danger' : ''; ?>" name="carType" onKeyUp="changeEventButton(this)">
								<option value="" selected>Select Car Type</option>

								<?php
								$get_type_sql = 'SELECT * FROM car_type ORDER BY type ASC';
								$get_type = mysqli_query($conn, $get_type_sql);

								if (mysqli_num_rows($get_type) > 0) {
									while ($type = mysqli_fetch_assoc($get_type)) {
										$selected_brand = ((isset($_POST['carType']) && $_POST['carType'] == $type['type_id']) || (!isset($_POST['carType']) && $saved_car_type_id == $type['type_id'])) ? ' selected="selected"' : '';

										echo '<option value="' . $type['type_id'] . '"' . $selected_brand . '>' . $type['type'] . '</option>';

									}

									mysqli_free_result($get_type);
									
								}
								?>
							</select>

							<?php
							if (isset($car_type_err) && !empty($car_type_err)) {
								echo '<p class="text-danger mb-0">' . $car_type_err . '</p>';

							}
							?>
						</div>
						
						<div class="form-group mt-4">
							<label for="carManufactureYear">Manufacture Year</label>
							
							<input type="number" step="1" min="<?php echo $accepted_min_year; ?>" max="<?php echo $accepted_max_year; ?>" class="form-control number-hide <?php echo !empty($car_manufacture_year_err) ? 'border border-danger' : ''; ?>" id="carManufactureYear" name="carManufactureYear" value="<?php echo isset($_POST['carManufactureYear']) ? $_POST['carManufactureYear'] : $saved_car_manufacture_year; ?>" onKeyUp="changeEventButton(this)">
							
							<?php
							if (isset($car_manufacture_year_err) && !empty($car_manufacture_year_err)) {
								echo '<p class="text-danger mb-0">' . $car_manufacture_year_err . '</p>';

							}
							?>
						</div>
						
						<div class="form-group mt-4">
							<label for="carPricePerHour">Price per Hour</label>
							
							<div class="input-group">
								<div class="input-group-prepend">
									<div class="input-group-text">A$</div>
								</div>

								<input type="text" class="form-control <?php echo !empty($car_price_per_hour_err) ? 'border border-danger' : ''; ?>" id="carPricePerHour" name="carPricePerHour" value="<?php echo isset($_POST['carPricePerHour']) ? $_POST['carPricePerHour'] : $saved_car_price_per_hour; ?>">

								<div class="input-group-append">
									<div class="input-group-text">per hour</div>
								</div>
							</div>
							
							<?php
							if (isset($car_price_per_hour_err) && !empty($car_price_per_hour_err)) {
								echo '<p class="text-danger mb-0">' . $car_price_per_hour_err . '</p>';

							}
							?>
						</div>
					</div>
				</div>
				
				<div class="row mt-5">
					<div class="col-sm-6">
						<!-- Car Interior Details -->
						<h4 class="text-center">Car Interior Details</h4>
						
						<div class="form-group mt-4">
							<label for="carTransmissionType">Transmission Type</label>

							<select id="carTransmissionType" class="form-control <?php echo !empty($car_transmission_type_err) ? 'border border-danger' : ''; ?>" name="carTransmissionType" onKeyUp="changeEventButton(this)">
								<option value="" selected>Select Transmission Type</option>

								<?php
								$get_transmission_type_sql = 'SELECT * FROM car_transmission ORDER BY transmission ASC';
								$get_transmission_type = mysqli_query($conn, $get_transmission_type_sql);

								if (mysqli_num_rows($get_transmission_type) > 0) {
									while ($tranmission_type = mysqli_fetch_assoc($get_transmission_type)) {
										$selected_transmission_type = ((isset($_POST['carTransmissionType']) && $_POST['carTransmissionType'] == $tranmission_type['transmission_id']) || (!isset($_POST['carTransmissionType']) && $saved_car_transmission_type_id == $tranmission_type['transmission_id'])) ? ' selected="selected"' : '';

										echo '<option value="' . $tranmission_type['transmission_id'] . '"' . $selected_transmission_type . '>' . $tranmission_type['transmission'] . '</option>';

									}

									mysqli_free_result($get_transmission_type);
									
								}
								?>
							</select>

							<?php
							if (isset($car_transmission_type_err) && !empty($car_transmission_type_err)) {
								echo '<p class="text-danger mb-0">' . $car_transmission_type_err . '</p>';

							}
							?>
						</div>
						
						<div class="form-group mt-4">
							<label for="carFuelType">Fuel Type</label>

							<select id="carFuelType" class="form-control <?php echo !empty($car_fuel_type_err) ? 'border border-danger' : ''; ?>" name="carFuelType" onKeyUp="changeEventButton(this)">
								<option value="" selected>Select Fuel Type</option>

								<?php
								$get_fuel_type_sql = 'SELECT * FROM car_fuel ORDER BY fuel ASC';
								$get_fuel_type = mysqli_query($conn, $get_fuel_type_sql);

								if (mysqli_num_rows($get_fuel_type) > 0) {
									while ($fuel_type = mysqli_fetch_assoc($get_fuel_type)) {
										$selected_fuel_type = ((isset($_POST['carFuelType']) && $_POST['carFuelType'] == $fuel_type['fuel_id']) || (!isset($_POST['carFuelType']) && $saved_car_fuel_type_id == $fuel_type['fuel_id'])) ? ' selected="selected"' : '';

										echo '<option value="' . $fuel_type['fuel_id'] . '"' . $selected_fuel_type . '>' . $fuel_type['fuel'] . '</option>';

									}

									mysqli_free_result($get_fuel_type);
									
								}
								?>
							</select>

							<?php
							if (isset($car_fuel_type_err) && !empty($car_fuel_type_err)) {
								echo '<p class="text-danger mb-0">' . $car_fuel_type_err . '</p>';

							}
							?>
						</div>
						
						<div class="form-group mt-4">
							<label for="carColor">Color</label>
							
							<input type="text" class="form-control <?php echo !empty($car_color_err) ? 'border border-danger' : ''; ?>" id="carColor" name="carColor" value="<?php echo isset($_POST['carColor']) ? $_POST['carColor'] : $saved_car_color; ?>">
							
							<?php
							if (isset($car_color_err) && !empty($car_color_err)) {
								echo '<p class="text-danger mb-0">' . $car_color_err . '</p>';

							}
							?>
						</div>
						
						<div class="row mt-4">
							<div class="col-sm-6">
								<div class="form-group">
									<label for="carSeat">Seat</label>
									
									<div class="input-group">
										<input type="text" class="form-control <?php echo !empty($car_seat_err) ? 'border border-danger' : ''; ?>" id="carSeat" name="carSeat" value="<?php echo isset($_POST['carSeat']) ? $_POST['carSeat'] : $saved_car_seat; ?>" onKeyUp="changeEventButton(this)">

										<div class="input-group-append">
											<div class="input-group-text"><?php echo $saved_car_seat > 1 ? 'seats' : 'seat'; ?></div>
										</div>
									</div>

									<?php
									if (isset($car_seat_err) && !empty($car_seat_err)) {
										echo '<p class="text-danger mb-0">' . $car_seat_err . '</p>';

									}
									?>
								</div>
							</div>

							<div class="col-sm-6">
								<div class="form-group">
									<label for="carDoor">Door</label>

									<div class="input-group">
										<input type="text" class="form-control <?php echo !empty($car_door_err) ? 'border border-danger' : ''; ?>" id="carDoor" name="carDoor" value="<?php echo isset($_POST['carDoor']) ? $_POST['carDoor'] : $saved_car_door; ?>" onKeyUp="changeEventButton(this)">

										<div class="input-group-append">
											<div class="input-group-text"><?php echo $saved_car_door > 1 ? 'doors' : 'door'; ?></div>
										</div>
									</div>

									<?php
									if (isset($car_door_err) && !empty($car_door_err)) {
										echo '<p class="text-danger mb-0">' . $car_door_err . '</p>';

									}
									?>
								</div>
							</div>
						</div>
						
						<!-- Car Features -->
						<h4 class="text-center mt-5">Car Features</h4>
						
						<div class="form-group mt-4">
							<label for="carFeatures">Features</label>

							<select multiple size="5" class="form-control <?php echo !empty($car_features_err) ? 'border border-danger' : ''; ?>" id="carFeatures" name="carFeatures[]" aria-describedby="carFeatureSelect" onKeyUp="changeEventButton(this)">
								<?php
								$get_feature_list_sql = 'SELECT * FROM car_feature_list ORDER BY feature ASC';
								$get_feature_list = mysqli_query($conn, $get_feature_list_sql);

								if (mysqli_num_rows($get_feature_list) > 0) {
									while ($feature_list = mysqli_fetch_assoc($get_feature_list)) {
										$selected_feature_list = (isset($_POST['carFeatures']) && in_array($feature_list['feature_list_id'], $_POST['carFeatures'])) || (!isset($_POST['carFeatures']) && in_array($feature_list['feature_list_id'], $saved_car_feature)) ? ' selected="selected"' : '';

										echo '<option value="' . $feature_list['feature_list_id'] . '"' . $selected_feature_list . '>' . $feature_list['feature'] . '</option>';

									}

									mysqli_free_result($get_feature_list);
								}
								?>
							</select>

							<?php
							if (isset($car_features_err) && !empty($car_features_err)) {
								echo '<p class="text-danger mb-0">' . $car_features_err . '</p>';

							} else {
								echo '<small id="carFeatureSelect" class="form-text text-muted d-none d-sm-block">Hold down "CTRL" on Windows or "CMD" on Mac to edit features.</small>';

							}
							?>
						</div>
					</div>
					
					<div class="col-sm-6">
						<!-- Car Location -->
						<h4 class="text-center mt-5 mt-sm-0">Car Location</h4>
						
						<div class="form-group mt-4">
							<label for="carAddress1">Address 1</label>
							
							<input type="text" class="form-control <?php echo !empty($car_address_1_err) ? 'border border-danger' : ''; ?>" id="carAddress1" name="carAddress1" value="<?php echo isset($_POST['carAddress1']) ? $_POST['carAddress1'] : $saved_car_address_1; ?>">
							
							<?php
							if (isset($car_address_1_err) && !empty($car_address_1_err)) {
								echo '<p class="text-danger mb-0">' . $car_address_1_err . '</p>';

							}
							?>
						</div>
						
						<div class="form-group mt-4">
							<label for="carAddress2">Address 2</label>
							
							<input type="text" class="form-control <?php echo !empty($car_address_2_err) ? 'border border-danger' : ''; ?>" id="carAddress2" name="carAddress2" value="<?php echo isset($_POST['carAddress2']) ? $_POST['carAddress2'] : $saved_car_address_2; ?>">
							
							<?php
							if (isset($car_address_2_err) && !empty($car_address_2_err)) {
								echo '<p class="text-danger mb-0">' . $car_address_2_err . '</p>';

							}
							?>
						</div>
						
						<div class="form-group mt-4">
							<label for="carSuburb">Suburb</label>
							
							<input type="text" class="form-control <?php echo !empty($car_suburb_err) ? 'border border-danger' : ''; ?>" id="carSuburb" name="carSuburb" value="<?php echo isset($_POST['carSuburb']) ? $_POST['carSuburb'] : $saved_car_suburb; ?>">
							
							<?php
							if (isset($car_suburb_err) && !empty($car_suburb_err)) {
								echo '<p class="text-danger mb-0">' . $car_suburb_err . '</p>';

							}
							?>
						</div>
						
						<div class="form-group mt-4">
							<label for="carPostalCode">Postal Code</label>
							
							<input type="text" class="form-control <?php echo !empty($car_postal_code_err) ? 'border border-danger' : ''; ?>" id="carPostalCode" name="carPostalCode" value="<?php echo isset($_POST['carPostalCode']) ? $_POST['carPostalCode'] : $saved_car_postal_code; ?>">
							
							<?php
							if (isset($car_postal_code_err) && !empty($car_postal_code_err)) {
								echo '<p class="text-danger mb-0">' . $car_postal_code_err . '</p>';

							}
							?>
						</div>
						
						<div class="row">
							<div class="col-sm-8">
								<div class="form-group mt-4">
									<label for="carState">State</label>

									<select id="carState" class="form-control <?php echo !empty($car_state_err) ? 'border border-danger' : ''; ?>" name="carState" onKeyUp="changeEventButton(this)">
										<option value="" selected>Select State</option>

										<?php
										$get_state_sql = 'SELECT * FROM moov.australia_state ORDER BY state ASC';
										$get_state = mysqli_query($conn, $get_state_sql);

										if (mysqli_num_rows($get_state) > 0) {
											while ($state = mysqli_fetch_assoc($get_state)) {
												$selected_state = ((isset($_POST['carState']) && $_POST['carState'] == $state['state_id']) || (!isset($_POST['carState']) && $saved_car_state == $state['state_id'])) ? ' selected="selected"' : '';

												echo '<option value="' . $state['state_id'] . '"' . $selected_state . '>' . $state['state'] . '</option>';

											}

											mysqli_free_result($get_state);

										}
										?>
									</select>

									<?php
									if (isset($car_state_err) && !empty($car_state_err)) {
										echo '<p class="text-danger mb-0">' . $car_state_err . '</p>';

									}
									?>
								</div>
							</div>
							
							<div class="col-sm-4 d-none d-sm-block">
								<div class="form-group mt-4">
									<label for="carCountry">Country</label>

									<input type="text" class="form-control" id="carCountry" name="carCountry" value="Australia" readonly>
								</div>
							</div>
						</div>

						<div class="form-group mt-4">
							<label for="carLongitude">Longitude</label>
							
							<input type="text" class="form-control <?php echo !empty($car_longitude_err) ? 'border border-danger' : ''; ?>" id="carLongitude" name="carLongitude" value="<?php echo isset($_POST['carLongitude']) ? $_POST['carLongitude'] : $saved_car_longitude; ?>">
							
							<?php
							if (isset($car_longitude_err) && !empty($car_longitude_err)) {
								echo '<p class="text-danger mb-0">' . $car_longitude_err . '</p>';

							}
							?>
						</div>
						
						<div class="form-group mt-4">
							<label for="carLatitude">Latitude</label>
							
							<input type="text" class="form-control <?php echo !empty($car_latitude_err) ? 'border border-danger' : ''; ?>" id="carLatitude" name="carLatitude" value="<?php echo isset($_POST['carLatitude']) ? $_POST['carLatitude'] : $saved_car_latitude; ?>">
							
							<?php
							if (isset($car_latitude_err) && !empty($car_latitude_err)) {
								echo '<p class="text-danger mb-0">' . $car_latitude_err . '</p>';

							}
							?>
						</div>
					</div>
				</div>
				
				<div class="row mt-5">
					<div class="col-sm-6">
						<a class="btn btn-secondary btn-block" href="/moov/portal/car/" role="button">Cancel</a>
					</div>

					<div class="col-sm-6 mt-4 mt-sm-0">
						<button id="suspendSubmitButton" type="submit" class="btn btn-primary btn-block">
							<span id="submitButton">Update</span>

							<img id="processingIcon" src="/moov/portal/assets/images/processing_icon.svg" class="processing-icon d-none">
							<span id="processingButton" class="d-none">Processing...</span>
						</button>
					</div>
				</div>
			</div>
        </form>
		
		<script>
            function submitButton() {
                document.getElementById('registerSubmitButton').disabled = true;
                document.getElementById('submitButton').classList.add('d-none');
                document.getElementById('processingIcon').classList.add('d-inline-block');
                document.getElementById('processingIcon').classList.remove('d-none');
                document.getElementById('processingButton').classList.remove('d-none');

            }

            function changeEventButton(event) {
                if (event.keyCode == 13) {
                    event.preventDefault;

                    document.getElementById('registerSubmitButton').disabled = true;
                    document.getElementById('submitButton').classList.add('d-none');
                    document.getElementById('processingIcon').classList.add('d-inline-block');
                    document.getElementById('processingIcon').classList.remove('d-none');
                    document.getElementById('processingButton').classList.remove('d-none');

                }
            }

            function showUploadImage(event) {
                var output = document.getElementById('carImage');
                var temp_image_url = URL.createObjectURL(event.target.files[0]);

                output.style.backgroundImage = 'url("' + temp_image_url + '")';
                output.onload = function() {
                    URL.revokeObjectURL(output.style.backgroundImage);

                }
            }

            function showUploadFileName(filename) {
                document.getElementById('carImageFileName').innerHTML = 'File: ' + filename.split("\\").pop();
                document.getElementById('carImageLabel').innerHTML = 'File uploaded successfully.';
                document.getElementById('carImageError').innerHTML = '';

            }

        </script>
    </div>
	
    <?php include '../footer.php'; ?>
	
</body>

</html>

<?php mysqli_close($conn); ?>