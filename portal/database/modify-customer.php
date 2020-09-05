<?php
session_start();
require_once '../config.php';
$parent_page_name = 'customer';
$page_name = basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php');

if (isset($_SESSION['moov_portal_logged_in']) && $_SESSION['moov_portal_logged_in'] == TRUE) {
    
} else {
    header('location: /moov/portal/login?url=' . urlencode('/moov/portal/database/' . $page_name));
    
}
?>

<!DOCTYPE html>
<html>
	
<head>
	<title>Modify Customer | Moov Portal</title>
	
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

    <div class="fluid-container mx-4 mx-sm-5 px-sm-5 my-3">
		<h1 class="text-center">Modify Customer</h1>
        
        <?php
        if (isset($_GET['page'])) {
            $current_page = $_GET['page'];

        } else {
            $current_page = 1;

        }

        $record_number = 1;
        $record_per_page = 25;
        $offset = ($current_page - 1) * $record_per_page;

        $get_total_records_sql = 'SELECT COUNT(*) FROM moov.account WHERE is_deleted = 0';
        $get_total_records = mysqli_query($conn, $get_total_records_sql);
        $total_records = mysqli_fetch_array($get_total_records)[0];
        $total_pages = ceil($total_records / $record_per_page);

        $get_customer_list_sql = 'SELECT account_id, first_name, last_name, display_name, email_address, contact_number, is_deactivated FROM moov.account WHERE is_deleted = ? ORDER BY first_name, last_name LIMIT ?, ?';
        $get_customer_list_stmt = mysqli_prepare($conn, $get_customer_list_sql);

        mysqli_stmt_bind_param($get_customer_list_stmt, 'iii', $param_account_status, $param_offset, $param_limit);
        $param_account_status = 0;
        $param_offset = $offset;
        $param_limit = $record_per_page;
        ?>
        
        <p class="text-right text-muted mt-4 mb-0 font-italic">Total Customer: <?php echo $total_records; ?></p>
        
        <div class="table-responsive-sm">
            <table class="table table-striped table-hover">
                <thead class="thead-light text-center">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Display Name</th>
                        <th scope="col">Email Address</th>
                        <th scope="col">Contact Number</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                
                <tbody>
                    <?php
                    if (mysqli_stmt_execute($get_customer_list_stmt)) {
                        $get_customer_list = mysqli_stmt_get_result($get_customer_list_stmt);
                        
                        if ($current_page == 1) {
                            $record_number = 1;

                        } else {
                            $record_number = ($current_page * $record_per_page) - 1;
                            
                        }
                        
                        while ($customer_list = mysqli_fetch_assoc($get_customer_list)) {
                            if ($customer_list['is_deactivated'] == 0) {
                                $customer_account_status = 'Active';
                                
                            } elseif ($customer_list['is_deactivated'] == 1) {
                                $customer_account_status = 'Deactivated';
                                
                            }
                            
                            echo '
                            <tr>
                                <th scope="row" class="text-center align-middle">' . $record_number . '</th>
                                <td class="align-middle">' . $customer_list['first_name'] . '</td>
                                <td class="align-middle">' . $customer_list['last_name'] . '</td>
                                <td class="align-middle">' . $customer_list['display_name'] . '</td>
                                <td class="text-center align-middle">' . $customer_list['email_address'] . '</td>
                                <td class="text-center align-middle">' . $customer_list['contact_number'] . '</td>
                                <td class="text-center align-middle">' . $customer_account_status . '</td>
                                <td class="text-center align-middle">
                                    <a class="btn btn-primary" href="edit-customer?id=" role="button">Modify</a>
                                    <a class="btn btn-primary" href="deactivate-customer?id=' . $customer_list['account_id'] . '" role="button">Deactivate</a>
                                </td>
                            </tr>
                            ';
                            
                            $record_number++;
                        }
                    }
                    ?>
                </tbody>
            </table>
            
            <?php
            $previous_page = $current_page - 1;
            $next_page = $current_page + 1;
            ?>
            
            <nav aria-label="Customer Database Page Navigation" class="mt-5">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo ($previous_page == 0) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=1">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    
                    <li class="page-item <?php echo ($previous_page == 0) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo ($previous_page == 0) ? '' : '?page=' . $previous_page; ?>">
                            <span aria-hidden="true">&lt;</span>
                        </a>
                    </li>
                    
                    <?php
                    for ($page_number = 1; $page_number <= $total_pages; $page_number++) {
                        echo '
                        <li class="page-item ' . (($page_number == $current_page) ? 'active' : '') . '">
                            <a class="page-link" href="?page=' . $page_number . '">' . $page_number . '</a>
                        </li>
                        ';
                        
                    }
                    ?>
                    
                    <li class="page-item <?php echo ($current_page == $total_pages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo ($current_page == $total_pages) ? '' : '?page=' . $next_page; ?>">
                            <span aria-hidden="true">&gt;</span>
                        </a>
                    </li>
                    
                    <li class="page-item <?php echo ($current_page == $total_pages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $total_pages; ?>">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
	</div>

    <?php include '../footer.php'; ?>
</body>
	
</html>

<?php mysqli_close($conn); ?>