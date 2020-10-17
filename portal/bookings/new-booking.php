<?php
session_start();
require_once '../config.php';
$parent_page_name = 'bookings';
$page_name = basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php');

$booking_customer_id = $booking_customer_id_err = '';

if (!isset($_SESSION['moov_portal_logged_in']) && $_SESSION['moov_portal_logged_in'] != TRUE) {
    header('location: /moov/portal/login?url=' . urlencode('/moov/portal/' . $parent_page_name . '/' . $page_name));
    
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['bookingCustomerId'])) {
        $booking_customer_id_err = 'Please select a customer account before you proceed to booking page.';
        
    }
    
    if (empty($booking_customer_id_err)) {
        header('location: /moov/portal/bookings/new-booking-customer?id=' . $_POST['bookingCustomerId']);
        
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
    
    <!-- JavaScript from Select2 -->
    <script src='/moov/portal/assets/script/select2.min.js' type='text/javascript'></script>
	
	<!-- CSS from Bootstrap v4.5.2 -->
    <link rel="stylesheet" type="text/css" href="/moov/portal/assets/style/bootstrap.css">
    
    <!-- CSS from Select2 -->
    <link href='/moov/portal/assets/style/select2.min.css' rel='stylesheet' type='text/css'>

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
		<h1 class="text-center">Register New Booking</h1>
        
        <form action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php'); ?>" method="post" onSubmit="submitButton()">
            <div class="form-group row align-items-center mt-5">
                <label for="bookingCustomerId" class="col-sm-2 col-form-label">Customer Account</label>
                
                <div class="col-sm-10">
                    <select id="bookingCustomerId" class="form-control <?php echo !empty($booking_customer_id_err) ? 'border border-danger' : ''; ?>" name="bookingCustomerId" onChange="getAccountInfo(this.value)" onKeyUp="changeEventButton(this)">
                        <option value="" selected>Select Customer Account</option>

                        <?php
                        $get_account_sql = 'SELECT account_id, first_name, last_name, display_name, email_address FROM moov.account ORDER BY first_name ASC';
                        $get_account = mysqli_query($conn, $get_account_sql);

                        if (mysqli_num_rows($get_account) > 0) {
                            while ($account = mysqli_fetch_assoc($get_account)) {
                                $selected_role = (isset($_POST['bookingCustomerId']) && $_POST['bookingCustomerId'] == $account['account_id']) ? ' selected="selected"' : '';

                                echo '<option value="' . $account['account_id'] . '"' . $selected_role . '>' . $account['first_name'] . ' ' . strtoupper($account['last_name']) . ' (' . $account['display_name'] . ') (' . $account['email_address'] . ')</option>';
                            }

                            mysqli_free_result($get_account);
                            
                        }
                        ?>
                    </select>
                    
                    <?php
                    if (isset($booking_customer_id_err) && !empty($booking_customer_id_err)) {
                        echo '<p class="text-danger mb-0">' . $booking_customer_id_err . '</p>';

                    }
                    ?>
                </div>
            </div>
            
            <div id="customerInformation" class="container-fluid px-0">
                <div class="row">
                    <div class="col-md-6 order-last order-md-first">
                        <div class="form-group row mt-4 align-items-center">
                            <label class="col-4 col-form-label">First Name</label>

                            <div class="col-8">
                                <p id="bookingCustomerFirstName" class="form-control-plaintext text-break"></p>
                            </div>
                        </div>
                        
                        <div class="form-group row mt-4 align-items-center">
                            <label class="col-4 col-form-label">Last Name</label>

                            <div class="col-8">
                                <p id="bookingCustomerLastName" class="form-control-plaintext text-break"></p>
                            </div>
                        </div>
						
						<div class="form-group row mt-4 align-items-center">
                            <label class="col-4 col-form-label">Email Address</label>

                            <div class="col-8">
                                <p id="bookingCustomerEmailAddress" class="form-control-plaintext text-break"></p>
                            </div>
                        </div>
						
						<div class="form-group row mt-4 align-items-center">
                            <label class="col-4 col-form-label">Date of Birth</label>

                            <div class="col-8">
                                <p id="bookingCustomerDateOfBirth" class="form-control-plaintext text-break"></p>
                            </div>
                        </div>
						
						<div class="form-group row mt-4 align-items-center">
                            <label class="col-4 col-form-label">Contact Number</label>

                            <div class="col-8">
                                <p id="bookingCustomerContactNumber" class="form-control-plaintext text-break"></p>
                            </div>
                        </div>
						
						<div class="form-group row mt-4 align-items-center">
                            <label class="col-4 col-form-label">License Status</label>

                            <div class="col-8">
                                <p id="bookingCustomerLicenseStatus" class="form-control-plaintext text-break"></p>
                            </div>
                        </div>
                        
                        <div class="form-group row mt-4 align-items-center">
                            <label class="col-4 col-form-label">Account Status</label>

                            <div class="col-8">
                                <p id="bookingCustomerAccountStatus" class="form-control-plaintext text-break"></p>
                            </div>
                        </div>
                    </div>
					
					<div class="col-md-6 text-center mt-4 order-first order-md-last">
						<img id="bookingCustomerAvatar" class="rounded-circle customer-avatar" src="/moov/assets/images/transparent_background.png">
					</div>
                </div>
				
				<div class="row mt-5">
                    <div class="col-12">
                        <button id="bookingSubmitButton" type="submit" class="btn btn-primary btn-block">
                            <span id="submitButton">Continue to Booking</span>

                            <img id="processingIcon" src="/moov/portal/assets/images/processing_icon.svg" class="processing-icon d-none">
                            <span id="processingButton" class="d-none">Processing...</span>
                        </button>
                    </div>
				</div>
            </div>
        </form>
        
        <script>
            $(document).ready(function(){
                $('#bookingCustomerId').select2();
				$('#customerInformation').hide();
                
            });
			
			function getAccountInfo(id) {
				var xhttpAccount, resultAccount, parsedAccount, accountInfo;

				if (id != '') {
					xhttpAccount = new XMLHttpRequest();

					xhttpAccount.onreadystatechange = function() {
						if (this.readyState == 4 && this.status == 200) {
							resultAccount = this.responseText;
							parsedAccount = JSON.parse(resultAccount);
							accountInfo = parsedAccount[0];

							var avatarFileName = '/moov/avatar/' + accountInfo.avatarName;

							document.getElementById('customerInformation').style.display = 'block';

							document.getElementById('bookingCustomerFirstName').innerHTML = accountInfo.firstName;
							document.getElementById('bookingCustomerLastName').innerHTML = accountInfo.lastName;
							document.getElementById('bookingCustomerEmailAddress').innerHTML = accountInfo.emailAddress;
							document.getElementById('bookingCustomerDateOfBirth').innerHTML = accountInfo.dateOfBirth;
							document.getElementById('bookingCustomerContactNumber').innerHTML = '+61' + accountInfo.contactNumber;
							document.getElementById('bookingCustomerLicenseStatus').innerHTML = accountInfo.dlStatus;
							document.getElementById('bookingCustomerAvatar').style.backgroundImage = 'url(' + avatarFileName + ')';
                            document.getElementById('bookingCustomerAccountStatus').innerHTML = accountInfo.accountStatus;
                            
                            document.getElementById('bookingCustomerAccountStatus').classList.remove('text-danger');
                            document.getElementById('bookingCustomerAccountStatus').classList.remove('font-weight-bold');
                            document.getElementById('bookingCustomerLicenseStatus').classList.remove('text-danger');
                            document.getElementById('bookingCustomerLicenseStatus').classList.remove('font-weight-bold');
                            document.getElementById('bookingSubmitButton').disabled = false;
                            
                            if (accountInfo.accountStatus == 'Deactivated' || accountInfo.accountStatus == 'Suspended') {
                                document.getElementById('bookingCustomerAccountStatus').classList.add('text-danger');
                                document.getElementById('bookingCustomerAccountStatus').classList.add('font-weight-bold');
                                document.getElementById('bookingSubmitButton').disabled = true;
                                
                            } else if (accountInfo.dlStatus == 'Expired') {
                                document.getElementById('bookingCustomerLicenseStatus').classList.add('text-danger');
                                document.getElementById('bookingCustomerLicenseStatus').classList.add('font-weight-bold');
                                document.getElementById('bookingSubmitButton').disabled = true;
                                
                            }
						}
					};

					xhttpAccount.open('GET', '/moov/portal/bookings/get-account?id=' + id, true);
					xhttpAccount.send();
					
				} else {
					document.getElementById('customerInformation').style.display = 'none';
					
				}
			}
            
            function submitButton() {
                document.getElementById('bookingSubmitButton').disabled = true;
                document.getElementById('submitButton').classList.add('d-none');
                document.getElementById('processingIcon').classList.add('d-inline-block');
                document.getElementById('processingIcon').classList.remove('d-none');
                document.getElementById('processingButton').classList.remove('d-none');

            }

            function changeEventButton(event) {
                if (event.keyCode == 13) {
                    event.preventDefault;

                    document.getElementById('bookingSubmitButton').disabled = true;
                    document.getElementById('submitButton').classList.add('d-none');
                    document.getElementById('processingIcon').classList.add('d-inline-block');
                    document.getElementById('processingIcon').classList.remove('d-none');
                    document.getElementById('processingButton').classList.remove('d-none');

                }
            }
        </script>
	</div>

    <?php include '../footer.php'; ?>
</body>
	
</html>

<?php mysqli_close($conn); ?>