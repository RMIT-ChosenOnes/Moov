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

<body id="carResult">
	<?php include 'header.php'; ?>
    
	<div class="container my-3 footer-align-bottom">
		<h1 class="text-center">Find Cars</h1>
        
        <form action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php'); ?>" method="get" onSubmit="submitButton()">
            <div class="row mt-5">
                <!-- Car Filter Function -->
                <div class="col-sm-3">
                    <!-- Pick Up Filter -->
                    <label for="bookPickUpDate">Pick-Up</label>
                    
                    <div class="form-row">
                        <div class="col">
                            <input type="date" class="form-control <?php echo !empty($book_pick_up_date_err) ? 'border border-danger' : ''; ?>" id="bookPickUpDate" name="bookPickUpDate" placeholder="dd / mm / yyyy" min="<?php echo $today_date; ?>" value="<?php echo !empty($_POST['bookPickUpDate']) ? $_POST['bookPickUpDate'] : $today_date; ?>" onKeyUp="changeEventButton(this)">
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
                        </div>
                    </div>
                    
                    <!-- Return Filter -->
                    <label for="bookReturnDate" class="mt-4">Return</label>
                    
                    <div class="form-row">
                        <div class="col">
                            <input type="date" class="form-control <?php echo !empty($book_return_date_err) ? 'border border-danger' : ''; ?>" id="bookReturnDate" name="bookReturnDate" placeholder="dd / mm / yyyy" min="<?php echo $next_date; ?>" value="<?php echo !empty($_POST['bookReturnDate']) ? $_POST['bookReturnDate'] : $today_date; ?>" onKeyUp="changeEventButton(this)">
                        </div>
                        
                        <div class="col">
                            <select id="bookReturnTime" class="form-control <?php echo !empty($book_return_time_err) ? 'border border-danger' : ''; ?>" name="bookReturnTime" onKeyUp="changeEventButton(this)">
                                <option value="" selected>Select Return Time</option>

                                <?php
                                foreach ($select_time_option as $time_option) {
                                    $selected_book_return_time = (isset($_POST['bookReturnTime']) && $_POST['bookReturnTime'] == $time_option) ? ' selected="selected"' : '';

                                    echo '<option value="' . $time_option . '" ' . $selected_book_return_time . '>' . $time_option . '</option>';

                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Search Bar -->
                    <div class="form-group mt-4">
                        <label for="bookSearch">Search</label>

                        <input type="text" class="form-control" id="bookSearch" name="bookSearch" placeholder="Search">
                   </div>
                    
                    <!-- Sort By -->
                    <div class="form-group mt-4">
                        <label for="bookSort">Sort By</label>

                        <select id="bookSort" class="form-control" name="bookSort" onKeyUp="changeEventButton(this)">
                            <option value="" selected>Sort By</option>

                            <?php
                            $sort_by_array = array('name' => 'Car Name', 'brand' => 'Brand', 'model' => 'Model', 'car_type' => 'Type');

							foreach ($sort_by_array as $sort_value => $sort_name) {
								$selected_sort_by = (isset($_GET['bookSort']) && $_GET['bookSort'] == $sort_value ? ' selected="selected"' : '');

								echo '<option value="' . $sort_value . '" ' . $selected_sort_by . '>' . $sort_name . '</option>';
								
							}
                            ?>
                        </select>
                    </div>
                    
                    <button id="searchSubmitButton" type="submit" class="btn btn-secondary btn-block mt-5">
                        <span id="submitButton">Search</span>

                        <img id="processingIcon" src="/moov/assets/images/processing_icon.svg" class="processing-icon d-none">
                        <span id="processingButton" class="d-none">Processing...</span>
                    </button>
                    
                    <!-- Filter -->
                    <div class="form-group mt-4">
                        <label for="bookFilter">Filter</label>

                        <input type="text" class="form-control" id="bookSort" name="bookSort" placeholder="Search">
                    </div>
                </div>

                <?php
                $get_car_result_sql = 'SELECT * FROM car';
                $get_car_result_stmt = mysqli_prepare($conn, $get_car_result_sql);
                
                if (mysqli_stmt_execute($get_car_result_stmt)) {
                    $get_car_result = mysqli_stmt_get_result($get_car_result_stmt);

                    while ($car_list = mysqli_fetch_assoc($get_car_result)) {
                        $get_brand_sql = 'SELECT brand FROM car_brand WHERE brand_id = ?';
                        $get_brand_stmt = mysqli_prepare($conn, $get_brand_sql);

                        mysqli_stmt_bind_param($get_brand_stmt, 'i', $param_car_brand);
                        $param_car_brand = $car_list['brand'];

                        if (mysqli_stmt_execute($get_brand_stmt)) {
                            $get_brand = mysqli_stmt_get_result($get_brand_stmt);

                            while ($brand = mysqli_fetch_assoc($get_brand)) {
                                $car_brand = $brand['brand'];

                            }
                        }

                        $get_type_sql = 'SELECT type FROM car_type WHERE type_id = ?';
                        $get_type_stmt = mysqli_prepare($conn, $get_type_sql);

                        mysqli_stmt_bind_param($get_type_stmt, 'i', $param_car_type);
                        $param_car_type = $car_list['car_type'];

                        if (mysqli_stmt_execute($get_type_stmt)) {
                            $get_type = mysqli_stmt_get_result($get_type_stmt);

                            while ($type = mysqli_fetch_assoc($get_type)) {
                                $car_type = $type['type'];

                            }
                        }

                        $get_suburb_sql = 'SELECT suburb FROM car_location WHERE car_id = ?';
                        $get_suburb_stmt = mysqli_prepare($conn, $get_suburb_sql);

                        mysqli_stmt_bind_param($get_suburb_stmt, 'i', $param_car_id);
                        $param_car_id = $car_list['car_id'];

                        if (mysqli_stmt_execute($get_suburb_stmt)) {
                            $get_suburb = mysqli_stmt_get_result($get_suburb_stmt);

                            while ($suburb = mysqli_fetch_assoc($get_suburb)) {
                                $car_suburb = $suburb['suburb'];

                            }
                        }

                        mysqli_stmt_close($get_suburb_stmt);
                        mysqli_stmt_close($get_type_stmt);
                        mysqli_stmt_close($get_brand_stmt);
                ?>
                  
                
                
                <!-- Car Search Result -->
                <div class="col-sm-9">
                    <!-- View Option -->
                    <div class="float-right mb-3">
                        <p class="mb-0">View:

                            <span class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                                <label id="listButton" class="btn btn-secondary active">
                                    <img class="w-50" alt="List Icon" src="/moov/assets/list_icon.svg" onClick="changeView('list')">
                                </label>

                                <label id="gridButton" class="btn btn-secondary">
                                    <img class="w-50" alt="Grid Icon" src="/moov/assets/grid_icon.svg" onClick="changeView('grid')">
                                </label>
                            </span>
                        </p>
                    </div>
                    
                <?php
                        echo '
                        <div class="row">
                            <div class="col-4">
                                <img class="car-image rounded border-0" src="/moov/assets/images/transparent_background.png" style="background-image: url("/moov/car-image/toyota_c_hr_koba_kelvin.jpg"); height: auto !important;">
                            </div>

                            <div class="col-sm-6">
                                <p class="font-weight-bold lead mb-2">' . $car_list['name'] . '</p>

                                <p class="mb-0">' . $car_brand . ' ' . $car_list['model'] . ' (' . $car_list['transmission_type'] . ')</p>

                                <p class="mb-0">' . $car_fuel_type . ' | ' . $car_list['seat'] . ' seats | ' . $car_list['door'] . ' doors</p>

                                <p class="mb-2">A$' . $car_list['price_per_hour'] . ' per hour</p>

                                <p class="mb-0"><b>Location:</b> 43 Guinane Avenue</p>
                            </div>

                            <div class="col-sm-2">
                                <button class="btn btn-secondary">Select</button>
                            </div>
                        </div>

                        <hr class="my-5">
                        ';

                    }
                }

                mysqli_stmt_close($get_car_list_stmt);
                ?>

                    <!-- Grid View -->
                    <div id="grid">
                        <div class="row text-center">
                            <div class="col-4 border">
                                <img class="car-image rounded border-0" src="/moov/assets/images/transparent_background.png" style="background-image: url('/moov/car-image/toyota_c_hr_koba_kelvin.jpg'); height: auto !important;">

                                <p class="font-weight-bold lead mb-2">Kelvin</p>

                                <p class="mb-0">Toyota C-HR Koba (Automatic)</p>

                                <p class="mb-0">E10 or Unleaded 91 | 7 seats | 4 doors</p>

                                <p class="mb-2">A$17.00 per hour</p>

                                <p class="mb-0"><b>Location:</b> 43 Guinane Avenue</p>

                                <button class="btn btn-secondary">Select</button>
                            </div>

                            <div class="col-4 border">
                                <img class="car-image rounded border-0" src="/moov/assets/images/transparent_background.png" style="background-image: url('/moov/car-image/toyota_c_hr_koba_kelvin.jpg'); height: auto !important;">

                                <p class="font-weight-bold lead mb-2">Kelvin</p>

                                <p class="mb-0">Toyota C-HR Koba (Automatic)</p>

                                <p class="mb-0">E10 or Unleaded 91 | 7 seats | 4 doors</p>

                                <p class="mb-2">A$17.00 per hour</p>

                                <p class="mb-0"><b>Location:</b> 43 Guinane Avenue</p>

                                <button class="btn btn-secondary">Select</button>
                            </div>

                            <div class="col-4 border">
                                <img class="car-image rounded border-0" src="/moov/assets/images/transparent_background.png" style="background-image: url('/moov/car-image/toyota_c_hr_koba_kelvin.jpg'); height: auto !important;">

                                <p class="font-weight-bold lead mb-2">Kelvin</p>

                                <p class="mb-0">Toyota C-HR Koba (Automatic)</p>

                                <p class="mb-0">E10 or Unleaded 91 | 7 seats | 4 doors</p>

                                <p class="mb-2">A$17.00 per hour</p>

                                <p class="mb-0"><b>Location:</b> 43 Guinane Avenue</p>

                                <button class="btn btn-secondary">Select</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
	</div>
    
    <script>
        document.getElementById('carResult').onload = function() {
            //document.getElementById('grid').style.display = 'none';
            
        }
        
        function changeView(selectedView) {
            document.getElementById('listButton').classList.remove('active');
            document.getElementById('gridButton').classList.remove('active');
            document.getElementById('list').style.display = 'none';
            document.getElementById('grid').style.display = 'none';
            
            document.getElementById(selectedView).style.display = 'block';
            
        }
    </script>

    <?php include 'footer.php'; ?>
</body>
	
</html>

<?php mysqli_close($conn); ?>