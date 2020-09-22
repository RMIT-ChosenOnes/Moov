<?php
session_start();
require_once '../config.php';
$parent_page_name = 'car';
$page_name = basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php');

if (!isset($_SESSION['moov_portal_logged_in']) && $_SESSION['moov_portal_logged_in'] != TRUE) {
    header('location: /moov/portal/login?url=' . urlencode('/moov/portal/' . $parent_page_name));
	
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Car Database | Moov Portal</title>

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

    <div class="fluid-container mx-4 mx-sm-5 px-sm-5 my-3 footer-align-bottom">
        <h1 class="text-center">Car Database</h1>

        <?php
        if (isset($_SESSION['moov_portal_car_updated']) && $_SESSION['moov_portal_car_updated'] === TRUE) {
            echo '
            <div class="alert alert-success my-4 alert-dismissible fade show" role="alert">
                Car: ' . $_SESSION['moov_portal_car_updated_name'] . ' is updated successfully.

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ';
            
            unset($_SESSION['moov_portal_car_updated']);
            unset($_SESSION['moov_portal_car_updated_name']);
            
        }

        if (isset($_SESSION['moov_portal_car_updated_image']) && $_SESSION['moov_portal_car_updated_image'] === TRUE) {
            echo '
            <div class="alert alert-success my-4 alert-dismissible fade show" role="alert">
                Car image updated successfully.

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ';
            
            unset($_SESSION['moov_portal_car_updated_image']);
            
        }
        ?>

        <form class="mt-5" action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php'); ?>" method="get" onSubmit="submitButton()" onKeyUp="changeEventButton(this)">
			<div class="container-fluid">
				<div class="row">
					<!-- Search Engine -->
					<div class="col">
						<label class="sr-only" for="search">Search</label>

						<div class="input-group mb-3 mb-md-0">
							<div class="input-group-prepend">
								<div class="input-group-text">
									<img class="w-50 mx-auto" src="/moov/portal/assets/images/search_icon.svg" alt="Search Icon">
								</div>
							</div>

							<input type="text" class="form-control form-control-sm" id="search" name="search" placeholder="Search" value="<?php echo $_GET['search']; ?>" onKeyUp="changeEventButton(this)">
						</div>
					</div>
				</div>
				
				<div class="row mt-3">
					<!-- Sort By -->
					<div class="col-md">
						<label class="sr-only" for="sort">Sort By</label>

						<select class="form-control form-control-sm mb-3" id="sort" name="sort">
							<option value="" selected>Sort By</option>

							<?php
							$sort_by_array = array('car_id' => 'Car ID (Default)', 'name' => 'Car Name', 'brand' => 'Brand', 'model' => 'Model', 'car_type' => 'Type', 'suburb' => 'Suburb');

							foreach ($sort_by_array as $sort_value => $sort_name) {
								$selected_sort_by = (isset($_GET['sort']) && $_GET['sort'] == $sort_value ? ' selected="selected"' : '');

								echo '<option value="' . $sort_value . '" ' . $selected_sort_by . '>' . $sort_name . '</option>';
								
							}
							?>
						</select>
					</div>
					
					<!-- Car Brand Filter -->
					<div class="col-md">
						<label class="sr-only" for="brand">Brand</label>
            
						<select class="form-control form-control-sm mb-3" id="brand" name="brand">
							<option value="" selected>Brand</option>

							<?php
							$get_car_brand_list_sql = 'SELECT * FROM car_brand ORDER BY brand ASC';
							$get_car_brand_list = mysqli_query($conn, $get_car_brand_list_sql);
							
							if (mysqli_num_rows($get_car_brand_list) > 0) {
								while ($car_brand_list = mysqli_fetch_assoc($get_car_brand_list)) {
									$selected_car_brand = (isset($_GET['brand']) && $_GET['brand'] == $car_brand_list['brand_id'] ? ' selected="selected"' : '');

									echo '<option value="' . $car_brand_list['brand_id'] . '" ' . $selected_car_brand . '>' . $car_brand_list['brand'] . '</option>';
									
								}
								
								mysqli_free_result($get_car_brand_list);
								
							}
							?>
						</select>
					</div>
					
					<!--  Car Type Filter -->
					<div class="col-md">
						<label class="sr-only" for="type">Type</label>
            
						<select class="form-control form-control-sm mb-3" id="type" name="type">
							<option value="" selected>Type</option>

							<?php
							$get_car_type_list_sql = 'SELECT * FROM car_type ORDER BY type ASC';
							$get_car_type_list = mysqli_query($conn, $get_car_type_list_sql);
							
							if (mysqli_num_rows($get_car_type_list) > 0) {
								while ($car_type_list = mysqli_fetch_assoc($get_car_type_list)) {
									$selected_car_type = (isset($_GET['type']) && $_GET['type'] == $car_type_list['type_id'] ? ' selected="selected"' : '');

									echo '<option value="' . $car_type_list['type_id'] . '" ' . $selected_car_type . '>' . $car_type_list['type'] . '</option>';
									
								}
								
								mysqli_free_result($get_car_type_list);
								
							}
							?>
						</select>
					</div>
					
					<!--  Car State Filter -->
					<div class="col-md">
						<label class="sr-only" for="state">State</label>
            
						<select class="form-control form-control-sm mb-3" id="state" name="state">
							<option value="" selected>State</option>

							<?php
							$get_car_state_list_sql = 'SELECT * FROM moov.australia_state ORDER BY state ASC';
							$get_car_state_list = mysqli_query($conn, $get_car_state_list_sql);
							
							if (mysqli_num_rows($get_car_state_list) > 0) {
								while ($car_state_list = mysqli_fetch_assoc($get_car_state_list)) {
									$selected_car_state = (isset($_GET['state']) && $_GET['state'] == $car_state_list['state_id'] ? ' selected="selected"' : '');

									echo '<option value="' . $car_state_list['state_id'] . '" ' . $selected_car_state . '>' . $car_state_list['state'] . '</option>';
									
								}
								
								mysqli_free_result($get_car_state_list);
								
							}
							?>
						</select>
					</div>
					
					<div class="col-md">
						<button id="searchSubmitButton" type="submit" class="btn btn-primary btn-sm btn-block">
							<span id="submitButton">Search</span>

							<img id="processingIcon" src="/moov/portal/assets/images/processing_icon.svg" class="processing-icon d-none">
							<span id="processingButton" class="d-none">Processing...</span>
						</button>
					</div>
				</div>
			</div>
        </form>

        <?php
        $search_engine_url = '&search=' . $_GET['search'] . '&sort=' . $_GET['sort'] . '&brand=' . $_GET['brand'] . '&type=' . $_GET['type'] . '&state=' . $_GET['state'];

        if (isset($_GET['page'])) {
            $current_page = $_GET['page'];
			
        } else {
            $current_page = 1;
			
        }

        $record_per_page = 20;
        $offset = ($current_page - 1) * $record_per_page;

        $get_car_list_sql = 'SELECT * FROM car WHERE name LIKE ? OR model LIKE ? OR brand = ? OR car_type = ? ORDER BY ' . (isset($_GET['sort']) && !empty($_GET['sort']) ? $_GET['sort'] : 'car_id') . ' ASC';
        $get_car_list_stmt = mysqli_prepare($conn, $get_car_list_sql);

        mysqli_stmt_bind_param($get_car_list_stmt, 'ssii', $param_search_query, $param_search_query, $param_brand_query, $param_type_query);
        $param_search_query = ((isset($_GET['search']) && !empty($_GET['search'])) ? '%' . $_GET['search'] . '%' : '%%');
		$param_brand_query = ((isset($_GET['brand']) && !empty($_GET['brand'])) ? $_GET['brand'] : '');
		$param_type_query = ((isset($_GET['type']) && !empty($_GET['type'])) ? $_GET['type'] : '');
        $param_offset = $offset;
        $param_limit = $record_per_page;
		
		echo($get_car_list_stmt->fullQuery);
		echo mysqli_stmt_error($conn);
		echo mysqli_error($conn);

        /*$get_total_records_sql = 'SELECT COUNT(*) FROM moov_portal.car WHERE (name LIKE "' . $param_search_query . '")';
        $get_total_records = mysqli_query($conn, $get_total_records_sql);
        $total_records = mysqli_fetch_array($get_total_records)[0];
        $total_pages = ceil($total_records / $record_per_page);*/
        ?>

        <p class="text-right text-muted mt-4 mb-0 font-italic">Total Records: <?php echo $total_records; ?></p>

        <div class="table-responsive-lg">
            <table class="table table-striped table-hover">
                <thead class="thead-light text-center">
                    <tr>
                        <th scope="col" class="align-middle">#</th>
                        <th scope="col" class="align-middle">Car Name</th>
                        <th scope="col" class="align-middle">Brand</th>
                        <th scope="col" class="align-middle">Model</th>
                        <th scope="col" class="align-middle">Type</th>
                        <th scope="col" class="align-middle">Suburb</th>
                        <th scope="col" class="align-middle">Status</th>
                        <th scope="col" class="align-middle">Action</th>
                    </tr>
                </thead>

                <tbody class="text-center">
                    <?php
                    if (mysqli_stmt_execute($get_car_list_stmt)) {
                        $get_car_list = mysqli_stmt_get_result($get_car_list_stmt);

                        while ($car_list = mysqli_fetch_assoc($get_car_list)) {
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
                            
                            echo '
                            <tr>
                                <th scope="row" class="text-center align-middle">' . $car_list['car_id'] . '</th>
                                <td class="align-middle">' . $car_list['name'] . '</td>
                                <td class="align-middle">' . $car_brand . '</td>
                                <td class="align-middle">' . $car_list['model'] . '</td>
                                <td class="align-middle">' . $car_type . '</td>
                                <td class="align-middle">' . $car_suburb . '</td>
                                <td class="align-middle">Active</td>
                                <td class="text-center align-middle">
                                    <a class="btn btn-primary btn-sm" href="modify-car?id=' . $car_list['car_id'] . '" role="button">Modify</a>       
                                </td>
                            </tr>
                            ';
                            
                        }
                    }

                    mysqli_stmt_close($get_car_list_stmt);
                    ?>
                </tbody>
            </table>
        </div>

        <?php
        $previous_page = $current_page - 1;
        $next_page = $current_page + 1;
        ?>

        <!-- Pagination -->
        <nav aria-label="Customer Database Page Navigation" class="mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo ($previous_page == 0) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=1<?php echo $search_engine_url; ?>">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                <li class="page-item <?php echo ($previous_page == 0) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?php echo ($previous_page == 0) ? '' : '?page=' . $previous_page . $search_engine_url; ?>">
                        <span aria-hidden="true">&lt;</span>
                    </a>
                </li>

                <?php
                for ($page_number = max(1, min($current_page - 2, $total_pages - 4)); $page_number <= min(max($current_page + 2, 5), $total_pages); $page_number++) {
                    echo '
                    <li class="page-item ' . (($page_number == $current_page) ? 'active' : '') . '">
                        <a class="page-link" href="?page=' . $page_number . $search_engine_url . '">' . $page_number . '</a>
                    </li>
                    ';
                }
                ?>

                <li class="page-item <?php echo ($current_page == $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?php echo ($current_page == $total_pages) ? '' : '?page=' . $next_page . $search_engine_url; ?>">
                        <span aria-hidden="true">&gt;</span>
                    </a>
                </li>

                <li class="page-item <?php echo ($current_page == $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $total_pages . $search_engine_url; ?>">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>

        <script>
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
        </script>
    </div>

    <?php include '../footer.php'; ?>
</body>

</html>

<?php mysqli_close($conn); ?>