<?php
session_start();
require_once 'config.php';
$page_name = basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php');

$report_comment = $report_file_name = $report_file_type = $report_file_status = '';
$report_comment_err = $report_file_err = '';

$search_filename = array('- ', ' ', '-', '.');
$replace_filename = array('_', '_', '_', '_');
$report_image_save_directory = '/var/www/html/moov/car-report/';

if (!isset($_GET['id']) || empty($_GET['id'])) {
	header('location: /moov/my-booking');
	
} elseif (!isset($_SESSION['moov_user_logged_in']) || $_SESSION['moov_user_logged_in'] != TRUE) {
    header('location: /moov/login?url=' . urlencode('/moov/reports?id=' . $_GET['id']));

} else {
	$check_customer_id_sql = 'SELECT customer_id FROM booking WHERE booking_id = ?';
	$check_customer_id_stmt = mysqli_prepare($conn, $check_customer_id_sql);
	
	mysqli_stmt_bind_param($check_customer_id_stmt, 'i', $param_booking_id);
	$param_booking_id = $_GET['id'];
	
	mysqli_stmt_execute($check_customer_id_stmt);
	mysqli_stmt_store_result($check_customer_id_stmt);
	mysqli_stmt_bind_result($check_customer_id_stmt, $booking_customer_id);
	mysqli_stmt_fetch($check_customer_id_stmt);
	
	if ($booking_customer_id != $_SESSION['moov_user_account_id']) {
		header('location: /moov/my-booking');

	}
	
	mysqli_stmt_close($check_customer_id_stmt);
	
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (empty(trim($_POST['reportComment']))) {
		$report_comment_err = 'Please enter your feedbacks / comments / complains.';
		
	} else {
		if (strlen(trim($_POST['reportComment'])) > 5) {
			$report_comment = trim($_POST['reportComment']);

		} else {
			$report_comment_err = 'Please enter a valid feedback / comment / complain.';

		}
	}
	
	if (isset($_FILES['reportFile']) && $_FILES['reportFile']['name'] != '') {
		$report_file_name = basename($_FILES['reportFile']['name']);
		$report_file_type = strtolower(pathinfo($report_file_name, PATHINFO_EXTENSION));

		if ($_FILES['reportFile']['size'] > 1000000) {
			$report_file_err = 'Sorry. Your file is too big. Maximum file size is 1MB. Please try again.';
			$report_file_status = 0;

		} elseif ($report_file_type != 'jpg' && $report_file_type != 'jpeg' && $report_file_type != 'png' && $report_file_type != 'pdf') {
			$report_file_err = 'Sorry. You have uploaded an unsupported file type. Please try again.';
			$report_file_status = 0;

		} else {
			$report_file_status = 1;
			
		}
	} else {
		$report_file_status = 0;
		
	}
	
	if (empty($report_comment_err) && empty($report_file_err)) {
		$register_new_report_sql = 'INSERT INTO booking_report (booking_id, comment, upload_file, status) VALUES (?, ?, ?, ?)';
		$register_new_report_stmt = mysqli_prepare($conn, $register_new_report_sql);
		
		mysqli_stmt_bind_param($register_new_report_stmt, 'isis', $param_booking_id, $param_report_comment, $param_report_upload_file_status, $param_report_status);
		$param_report_comment = $report_comment;
		$param_report_upload_file_status = $report_file_status;
		$param_report_status = 'Waiting to Assign';
		
		if (mysqli_stmt_execute($register_new_report_stmt)) {
			$report_id = mysqli_insert_id($conn);
			
			if ($report_file_status == 1) {
				$report_file_name_url = $report_image_save_directory . 'report_file_' . $report_id . '.' . $report_file_type;
				
				if (move_uploaded_file($_FILES['reportFile']['tmp_name'], $report_file_name_url)) {
					$uploaded = TRUE;

				} else {
					$upload_error = TRUE;

				}
				
				unset($_FILES);
				
			}
			
			$registered = TRUE;
			unset($_POST);

		} else {
			$register_error = TRUE;
			$error_message = mysqli_error($conn);
			
		}
		
		mysqli_stmt_close($register_new_report_stmt);
		
	}
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Report | Moov</title>

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

<body id="report">
    <?php include 'header.php'; ?>

    <div class="container my-3 footer-align-bottom">
        <h1 class="text-center">Report Car</h1>
        
        <?php
		if ((isset($register_error) && $register_error === TRUE) || (isset($upload_error) && $upload_error === TRUE)) {
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
		
        $get_booking_details_sql = 'SELECT * FROM booking WHERE booking_id = ?';
        $get_booking_details_stmt = mysqli_prepare($conn, $get_booking_details_sql);
        
        mysqli_stmt_bind_param($get_booking_details_stmt, 'i', $param_booking_id);
        
        if (mysqli_stmt_execute($get_booking_details_stmt)) {
            $get_booking_details = mysqli_stmt_get_result($get_booking_details_stmt);
            
            while ($booking_details = mysqli_fetch_assoc($get_booking_details)) {
                $get_car_details_sql = 'SELECT * FROM moov_portal.car WHERE car_id = ?';
                $get_car_details_stmt = mysqli_prepare($conn, $get_car_details_sql);
                
                mysqli_stmt_bind_param($get_car_details_stmt, 'i', $param_car_id);
                $param_car_id = $booking_details['car_id'];
				
				if (mysqli_stmt_execute($get_car_details_stmt)) {
					$get_car_details = mysqli_stmt_get_result($get_car_details_stmt);
					
					while ($car_details = mysqli_fetch_assoc($get_car_details)) {
						$get_car_brand_sql = 'SELECT * FROM moov_portal.car_brand WHERE brand_id = ?';
						$get_car_brand_stmt = mysqli_prepare($conn, $get_car_brand_sql);
						
						mysqli_stmt_bind_param($get_car_brand_stmt, 'i', $param_car_brand_id);
						$param_car_brand_id = $car_details['brand'];
						
						if (mysqli_stmt_execute($get_car_brand_stmt)) {
							$get_car_brand = mysqli_stmt_get_result($get_car_brand_stmt);
							
							while ($car_brand = mysqli_fetch_assoc($get_car_brand)) {
								$booking_car_brand = $car_brand['brand'];
								
							}
						}
						
						mysqli_stmt_close($get_car_brand_stmt);
						
						$booking_car_name = $car_details['name'];
						$booking_car_model = $car_details['model'];

					}
				}
				
				mysqli_stmt_close($get_car_details_stmt);
				
				$booking_pick_up_date = date('Y-m-d, H:i', strtotime($booking_details['pick_up_date']));
				$booking_return_date = date('Y-m-d, H:i', strtotime($booking_details['return_date']));
				$car_temp_image_name = strtolower($booking_car_brand . '_' . $booking_car_model . '_' . $booking_car_name);
				$booking_car_image = str_replace($search_filename, $replace_filename, $car_temp_image_name);
				
            }
        }
		
		mysqli_stmt_close($get_booking_details_stmt);
        ?>
		
		<div class="fluid-container text-center">
            <img class="car-image rounded border-0 card-img-top mt-5" src="/moov/assets/images/transparent_background.png" style="background-image: url('/moov/car-image/<?php echo $booking_car_image; ?>.jpg'); height: auto !important;">
        </div>
        
        <form action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php') . '?id=' . $_GET['id']; ?>" enctype="multipart/form-data" method="post" onSubmit="submitButton()">
            <div class="row mt-5">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h3>Car Details</h3>

                            <p class="mb-2"><?php echo $booking_car_name; ?></p>

                            <p class="mb-2"><?php echo $booking_car_brand . ' ' . $booking_car_model; ?></p>

                            <hr class="my-4">

                            <h3>Booking Details</h3>

							<p class="mb-2"><b>Booking:</b> #<?php echo $param_booking_id; ?></p>

                            <p class="mb-2"><b>Pick-Up:</b> <?php echo $booking_pick_up_date; ?></p>

                            <p class="mb-2"><b>Return:</b> <?php echo $booking_return_date; ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-8 mt-4 mt-md-0">
                    <div class="form-group">
                        <label for="reportComment">Feedbacks / Comments / Complains</label>
                        
                        <textarea class="form-control <?php echo !empty($report_comment_err) ? 'border border-danger' : ''; ?>" id="reportComment" name="reportComment" rows="8" onKeyUp="characterCount(this.value)"><?php echo $_POST['reportComment']; ?></textarea>

						<?php
						if (isset($report_comment_err) && !empty($report_comment_err)) {
							echo '
								<div class="row">
									<div class="col-sm-8">
										<p class="text-danger mb-0">' . $report_comment_err . '</p>
									</div>

									<div class="col-sm-4">
										<small id="commentInfo" class="form-text text-muted text-right font-italic">Character Count: 0</small>
									</div>
								</div>
							';

						} else {
							echo '<small id="commentInfo" class="form-text text-muted text-right font-italic">Character Count: 0</small>';
							
						}
						?>
                    </div>
                    
                    <div class="form-group custom-file mt-4">
                        <input type="file" class="custom-file-input" id="reportFile" name="reportFile" aria-describedby="reportFileName" onChange="showUploadFileName(this.value)" onKeyUp="changeEventButton(this)">

                        <label id="reportFileLabel" class="custom-file-label" for="reportFile">Upload Supporting Document (Optional)</label>
                        
                        <small id="reportFileName" class="form-text text-muted"><?php echo empty($report_file_err) ? 'Max. file size is 1MB. Supported file type: JPG, JPEG, PNG, PDF.' : ''; ?></small>
						
						<?php
						if (isset($report_file_err) && !empty($report_file_err)) {
							echo '<p id="reportFileError" class="text-danger mb-0">' . $report_file_err . '</p>';

						}
						?>
                    </div>
                </div>
            </div>
            
            <button id="reportSubmitButton" type="submit" class="btn btn-primary btn-block mt-5">
                <span id="submitButton">Report Now</span>

                <img id="processingIcon" src="/moov/assets/images/processing_icon.svg" class="processing-icon d-none">
                <span id="processingButton" class="d-none">Processing...</span>
            </button>
        </form>
    </div>
	
	<div class="modal fade" data-backdrop="static" data-keyboard="false" id="registeredModal" aria-labelledby="registeredModalLabel" aria-hidden="true" tabindex="-1">
		<div class="modal-dialog modal-dialog-centered modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h2 class="modal-title" id="registeredModalLabel">Thank You!</h2>
					
					<a href="/moov/my-booking" role="button" class="close" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</a>
				</div>
				
				<div class="modal-body text-center">
					<p class="lead font-weight-bold">Report #<?php echo $report_id; ?> for booking #<?php echo $param_booking_id; ?> reported successfully.</p>
					
					<p class="mt-4">One of our staff will review your case within 5 business day, and will contact you if we require more information.</p>
				</div>
			</div>
		</div>
	</div>
    
    <script>
		document.getElementById('report').onload = function () {
			var comment = document.getElementById('reportComment').value;
			characterCount(comment);
			
            var showModal = <?php echo $registered; ?>;
			
            if (showModal == 1) {
                $('#registeredModal').modal('show');
                
            }

		}
		
        function characterCount(value) {
            var nLength = value.length;
            
            document.getElementById('commentInfo').innerHTML = 'Character Count: ' + nLength;
            
        }
        
        function submitButton() {
            document.getElementById('reportSubmitButton').disabled = true;
            document.getElementById('submitButton').classList.add('d-none');
            document.getElementById('processingIcon').classList.add('d-inline-block');
            document.getElementById('processingIcon').classList.remove('d-none');
            document.getElementById('processingButton').classList.remove('d-none');

        }
        
        function changeEventButton(event) {
            if (event.keyCode == 13) {
                event.preventDefault;

                document.getElementById('reportSubmitButton').disabled = true;
                document.getElementById('submitButton').classList.add('d-none');
                document.getElementById('processingIcon').classList.add('d-inline-block');
                document.getElementById('processingIcon').classList.remove('d-none');
                document.getElementById('processingButton').classList.remove('d-none');

            }
        }

        function showUploadFileName(filename) {
            document.getElementById('reportFileName').innerHTML = 'File: ' + filename.split("\\").pop();
            document.getElementById('reportFileLabel').innerHTML = 'File uploaded successfully.';
            document.getElementById('reportFileError').innerHTML = '';

        }
    </script>

    <?php include 'footer.php'; ?>

</body>

<?php mysqli_close($conn); ?>