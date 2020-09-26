<?php
session_start();
require_once 'config.php';
$page_name = basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php');

$today_date = date('d/m/Y');
$next_date = date('d/m/Y', strtotime('+1 day'));
$sample_minute = array('00', '15', '30', '45');

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

<body id="book">
	<?php include 'header.php'; ?>
    
	<div class="container my-3 footer-align-bottom d-flex">
		<div id="find-car-card" class="card m-auto py-5 px-4">
            <h1 class="text-center">Find Cars</h1>
            
            <form action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php'); ?>" method="post" onSubmit="submitButton()">
                <div class="form-row mt-4">
                    <label for="bookPickUpDate" class="col-sm-2 col-form-label">Pick-Up</label>
                    
                    <div class="col">
                        <input type="date" class="form-control <?php echo !empty($book_pick_up_date_err) ? 'border border-danger' : ''; ?>" id="bookPickUpDate" name="bookPickUpDate" placeholder="dd / mm / yyyy" min="<?php echo $today_date; ?>" value="<?php echo !empty($_POST['bookPickUpDate']) ? $_POST['bookPickUpDate'] : $today_date; ?>" onKeyUp="changeEventButton(this)">
                        
                        <?php
						if (isset($book_pick_up_date_err) && !empty($book_pick_up_date_err)) {
							echo '<p class="text-danger mb-0">' . $book_pick_up_date_err . '</p>';

						}
						?>
                    </div>
                    
                    <div class="col">
                        <select id="bookPickUpTime" class="form-control <?php echo !empty($book_pick_up_time_err) ? 'border border-danger' : ''; ?>" name="bookPickUpTime" onKeyUp="changeEventButton(this)">
                            <option value="" selected>Select Pick-Up Time</option>
                            
                            <?php
                            foreach ($select_time_option as $time_option) {
                                $selected_book_pick_up_time = (isset($_POST['bookPickUpTime']) && $_POST['bookPickUpTime'] == $time_option) ? ' selected="selected"' : '';
                                
                                echo '<option value="' . $time_option . '" ' . $selected_book_pick_up_time . '>' . $time_option . '</option>';
                                
                            }
                            ?>
                        </select>
                        
                        <?php
						if (isset($book_pick_up_time_err) && !empty($book_pick_up_time_err)) {
							echo '<p class="text-danger mb-0">' . $book_pick_up_time_err . '</p>';

						}
						?>
                    </div>
                </div>
                
                <div class="form-row mt-3">
                    <label for="bookReturnDate" class="col-sm-2 col-form-label">Return</label>
                    
                    <div class="col">
                        <input type="date" class="form-control <?php echo !empty($book_return_date_err) ? 'border border-danger' : ''; ?>" id="bookReturnDate" name="bookReturnDate" placeholder="dd / mm / yyyy" min="<?php echo $today_date; ?>" value="<?php echo !empty($_POST['bookReturnDate']) ? $_POST['bookReturnDate'] : $next_date; ?>" onKeyUp="changeEventButton(this)">
                        
                        <?php
						if (isset($book_return_date_err) && !empty($book_return_date_err)) {
							echo '<p class="text-danger mb-0">' . $book_return_date_err . '</p>';

						}
						?>
                    </div>
                    
                    <div class="col">
                        <select id="bookReturnTime" class="form-control <?php echo !empty($book_return_time_err) ? 'border border-danger' : ''; ?>" name="bookReturnTime" onKeyUp="changeEventButton(this)">
                            <option value="" selected>Select Return Time</option>
                            
                            <?php
                            foreach ($select_time_option as $time_option) {
                                $selected_book_pick_up_time = (isset($_POST['bookReturnTime']) && $_POST['bookReturnTime'] == $time_option) ? ' selected="selected"' : '';
                                
                                echo '<option value="' . $time_option . '" ' . $selected_book_pick_up_time . '>' . $time_option . '</option>';
                                
                            }
                            ?>
                        </select>
                        
                        <?php
						if (isset($book_return_time_err) && !empty($book_return_time_err)) {
							echo '<p class="text-danger mb-0">' . $book_return_time_err . '</p>';

						}
						?>
                    </div>
                </div>
                
                <button id="searchSubmitButton" type="submit" class="btn btn-secondary btn-block mt-5">
					<span id="submitButton">Find</span>
					
					<img id="processingIcon" src="/moov/assets/images/processing_icon.svg" class="processing-icon d-none">
					<span id="processingButton" class="d-none">Processing...</span>
				</button>
            </form>
        </div>
	</div>

    <?php include 'footer.php'; ?>
</body>
	
</html>

<?php mysqli_close($conn); ?>