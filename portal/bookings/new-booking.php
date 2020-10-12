<?php
session_start();
require_once '../config.php';
$parent_page_name = '';
$page_name = '';
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
        
        <form action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php'); ?>" method="post">
            <div class="form-group row align-items-center mt-5">
                <label for="bookingCustomerId" class="col-sm-2 col-form-label">Customer Account</label>
                
                <div class="col-sm-10">
                    <select id="bookingCustomerId" class="form-control <?php echo !empty($book_pick_up_time_err) || !empty($book_err) ? 'border border-danger' : ''; ?>" name="bookingCustomerId">
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
                </div>
            </div>
            
            <div id="" class="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">First Name</label>

                            <div class="col-sm-8">
                                <input id="bookingCustomerFirstName" type="text" class="form-control-plaintext" value="Kelvin" disabled>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Last Name</label>

                            <div class="col-sm-8">
                                <input id="bookingCustomerFirstName" type="text" class="form-control-plaintext" value="Ng" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        
        <script>
            $(document).ready(function(){
                // Initialize select2
                $("#bookingCustomerId").select2();
                
            });
        </script>
	</div>

    <?php include '../footer.php'; ?>
</body>
	
</html>

<?php mysqli_close($conn); ?>