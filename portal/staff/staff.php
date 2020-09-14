<?php
session_start();
require_once '../config.php';
$parent_page_name = 'staff';
$page_name = basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php');

if (!isset($_SESSION['moov_portal_logged_in']) && $_SESSION['moov_portal_logged_in'] != TRUE) {
    header('location: /moov/portal/login?url=' . urlencode('/moov/portal/'. $page_name));
    
} elseif (isset($_SESSION['moov_portal_staff_role']) && $_SESSION['moov_portal_staff_role'] != 'Admin') {
    header('location: /moov/portal/');
    
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['selectedStaffId']) && !empty($_POST['selectedStaffId'])) {
        if (isset($_POST['reinstateButton'])) {
            $set_staff_account_status = 0;
            $status = 'reinstated';
            
        } elseif (isset($_POST['deactivateButton'])) {
            $set_staff_account_status = 1;
            $status = 'deactivated';
            
        }
        
        $update_staff_account_status_sql = 'UPDATE portal_account SET is_deactivated = ? WHERE account_id = ?';
        $update_staff_account_status_stmt = mysqli_prepare($conn, $update_staff_account_status_sql);
        
        mysqli_stmt_bind_param($update_staff_account_status_stmt, 'ii', $param_staff_account_status, $param_staff_account_id);
        $param_staff_account_status = $set_staff_account_status;
        $param_staff_account_id = $_POST['selectedStaffId'];
        
        if (mysqli_stmt_execute($update_staff_account_status_stmt)) {
            $updated_account_status = TRUE;
            $updated_account_status_name = $_POST['selectedStaffName'];
            
        } else {
            $update_error = TRUE;
            $error_message = mysqli_stmt_error($conn);
            
        }
        
        mysqli_stmt_close($update_staff_account_status_stmt);
        
    }
}
?>

<!DOCTYPE html>
<html>
	
<head>
	<title>Staff Account Management | Moov Portal</title>
	
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

    <div class="fluid-container mx-4 mx-sm-5 px-sm-5 my-3 footer-align-bottom">
		<h1 class="text-center">Staff Account Management</h1>
        
        <?php
        if ($_SESSION['moov_portal_staff_account_updated'] === TRUE) {
            echo '
            <div class="alert alert-success my-4 alert-dismissible fade show" role="alert">
                Staff: ' . $_SESSION['moov_portal_staff_account_name'] . '\'s account is updated successfully.

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ';
        }
        
        unset($_SESSION['moov_portal_staff_account_updated']);
        unset($_SESSION['moov_portal_staff_account_account_name']);
        
        if ($_SESSION['moov_portal_staff_account_password_updated'] === TRUE) {
            echo '
            <div class="alert alert-success my-4 alert-dismissible fade show" role="alert">
                Staff: ' . $_SESSION['moov_portal_staff_account_name'] . '\'s account password updated successfully.

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ';
        }
        
        unset($_SESSION['moov_portal_staff_account_password_updated']);
        
        if ($updated_account_status === TRUE) {
            echo '
            <div class="alert alert-success my-4 alert-dismissible fade show" role="alert">
                Staff: ' . $updated_account_status_name . '\'s account ' . $status . ' successfully.

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ';
        }
        ?>
        
        <form class="form-inline justify-content-center mt-5" action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php'); ?>" method="get" onSubmit="submitButton()" onKeyUp="changeEventButton(this)">
            <label class="sr-only" for="sort">Sort By</label>
            <select class="custom-select mb-3 mb-md-0" id="sort" name="sort">
                <option value="" selected>Sort By</option>
                
                <?php
                $sort_by_array = array('first_name'=>'First Name (Default)', 'last_name'=>'Last Name', 'username'=>'Username', 'email_address'=>'Email Address', 'role'=>'Role', 'is_deactivated'=>'Account Status');
                
                foreach ($sort_by_array as $value => $name) {
                    $selected_sort_by = (isset($_GET['sort']) && $_GET['sort'] == $value ? ' selected="selected"' : '');
                    
                    echo '<option value="' . $value . '" ' . $selected_sort_by . '>' . $name . '</option>';
                    
                }
                ?>
            </select>
            
            <label class="sr-only" for="search">Search</label>
            <div id="searchField" class="input-group ml-md-3 mb-3 mb-md-0">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <img class="w-75 mx-auto" src="/moov/portal/assets/images/search_icon.svg" alt="Search Icon">
                    </div>
                </div>
                
                <input type="text" class="form-control" id="search" name="search" placeholder="Search" value="<?php echo $_GET['search']; ?>" onKeyUp="changeEventButton(this)">
            </div>
            
            <button id="searchSubmitButton" type="submit" class="btn btn-primary ml-md-3">
                <span id="submitButton">Search</span>

                <img id="processingIcon" src="/moov/assets/images/processing_icon.svg" class="processing-icon d-none">
                <span id="processingButton" class="d-none">Processing...</span>
            </button>
        </form>
        
        <?php
        $search_engine_url = '&sort=' . $_GET['sort'] . '&search=' . $_GET['search'];
        
        if (isset($_GET['page'])) {
            $current_page = $_GET['page'];

        } else {
            $current_page = 1;

        }

        $record_number = 1;
        $record_per_page = 5;
        $offset = ($current_page - 1) * $record_per_page;
            
        $get_staff_list_sql = 'SELECT account_id, username, first_name, last_name, email_address, role, is_deactivated FROM portal_account WHERE (first_name LIKE ? OR last_name LIKE ? OR username LIKE ? OR email_address LIKE ?) ORDER BY ' . ((isset($_GET['sort']) && !empty($_GET['sort'])) ? $_GET['sort'] : 'first_name') . ' ASC LIMIT ?, ?';
        $get_staff_list_stmt = mysqli_prepare($conn, $get_staff_list_sql);

        mysqli_stmt_bind_param($get_staff_list_stmt, 'ssssii', $param_search_query, $param_search_query, $param_search_query, $param_search_query, $param_offset, $param_limit);
        $param_search_query = ((isset($_GET['search']) && !empty($_GET['search'])) ? '%' . $_GET['search'] . '%' : '%%');
        $param_offset = $offset;
        $param_limit = $record_per_page;
        
        $get_total_records_sql = 'SELECT COUNT(*) FROM portal_account WHERE first_name LIKE "' . $param_search_query . '" OR last_name LIKE "' . $param_search_query . '" OR username LIKE "' . $param_search_query . '" OR email_address LIKE "' . $param_search_query . '"';
        $get_total_records = mysqli_query($conn, $get_total_records_sql);
        $total_records = mysqli_fetch_array($get_total_records)[0];
        $total_pages = ceil($total_records / $record_per_page);
        ?>
        
        <p class="text-right text-muted mt-4 mb-0 font-italic">Total Records: <?php echo $total_records; ?></p>
        
        <div class="table-responsive-lg">
            <table class="table table-striped table-hover">
                <thead class="thead-light text-center">
                    <tr>
                        <th scope="col" class="align-middle">#</th>
                        <th scope="col" class="align-middle">First Name</th>
                        <th scope="col" class="align-middle">Last Name</th>
                        <th scope="col" class="align-middle">Username</th>
                        <th scope="col" class="align-middle">Email Address</th>
                        <th scope="col" class="align-middle">Role</th>
                        <th scope="col" class="align-middle">Status</th>
                        <th scope="col" class="align-middle">Action</th>
                    </tr>
                </thead>
                
                <tbody class="text-center">
                    <?php
                    if (mysqli_stmt_execute($get_staff_list_stmt)) {
                        $get_staff_list = mysqli_stmt_get_result($get_staff_list_stmt);
                        
                        if ($current_page > 1) {
                            $last_record_number = $record_per_page * $current_page;
                            $first_record_number = $last_record_number - $record_per_page;
                            $record_number = $first_record_number + 1;
                            
                        }
                        
                        while ($staff_list = mysqli_fetch_assoc($get_staff_list)) {
                            if ($staff_list['is_deactivated'] == 0) {
                                $staff_account_status = 'Active';
                                $staff_account_action = 'Deactivate';
                                
                            } elseif ($staff_list['is_deactivated'] == 1) {
                                $staff_account_status = 'Deactivated';
                                $staff_account_action = 'Reinstate';
                                
                            }
                            
                            if ($staff_list['role'] == 1) {
                                $staff_role = 'Admin';
                                
                            } elseif ($staff_list['role'] == 2) {
                                $staff_role = 'Staff';
                                
                            }
                            
                            echo '
                            <tr>
                                <th scope="row" class="text-center align-middle">' . $record_number . '</th>
                                <td class="align-middle">' . $staff_list['first_name'] . '</td>
                                <td class="align-middle">' . $staff_list['last_name'] . '</td>
                                <td class="align-middle">' . $staff_list['username'] . '</td>
                                <td class="align-middle">' . $staff_list['email_address'] . '</td>
                                <td class="align-middle">' . $staff_role . '</td>
                                <td class="align-middle">' . $staff_account_status . '</td>
                                <td class="align-middle">
                                    <a class="btn btn-primary btn-sm" href="modify-staff?id=' . $staff_list['account_id'] . '" role="button">Modify</a>
                                    
                                    <a class="btn btn-warning btn-sm mt-3 mt-xl-0 ml-xl-2" role="button" data-toggle="modal" data-target="#staffAccountDeactivateReinstate" data-id="' . $staff_list['account_id'] . '" data-name="' . $staff_list['first_name'] . '" data-status="' . $staff_account_action . '">' . $staff_account_action .'</a>
                                </td>
                            </tr>
                            ';
                            
                            $record_number++;
                        }
                    }
                    
                    mysqli_stmt_close($get_staff_list_stmt);
                    ?>
                </tbody>
            </table>
        </div>
        
        <form action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php') . '?page=' . $current_page . $search_engine_url; ?>" method="post">
            <div class="modal fade" id="staffAccountDeactivateReinstate" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staffAccountDeactivateReinstateLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title text-warning" id="staffAccountDeactivateReinstateLabel">Warning!</h2>
                        </div>

                        <div class="modal-body">
                            <input type="hidden" class="selectedStaffId" name="selectedStaffId">
                            <input type="hidden" class="selectedStaffName" name="selectedStaffName">
                            
                            You are about to <span class="staffName"></span>'s access to Moov portal. Are you confirm?
                        </div>

                        <div class="modal-footer">
                            <button type="reset" class="btn btn-primary" data-dismiss="modal">Cancel</button>

                            <button type="submit" class="btn btn-warning"> Staff Account</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
            
        <?php
        $previous_page = $current_page - 1;
        $next_page = $current_page + 1;
        ?>
            
        <!-- Pagination -->
        <nav aria-label="Staff Database Page Navigation" class="mt-5">
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
                for ($page_number = max(1, $current_page - 2); $page_number <= min($current_page + 5, $total_pages); $page_number++) {
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
            
            $('#staffAccountDeactivateReinstate').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var name = button.data('name');
                var status = button.data('status');
                var modal = $(this);
                
                modal.find('.modal-body .selectedStaffId').val(id);
                modal.find('.modal-body .selectedStaffName').val(name);
                modal.find('.modal-body .staffName').text(status.toLowerCase() + ' ' + name);
                modal.find('.modal-footer .btn-warning').text(status + ' Staff Account');
                modal.find('.modal-footer .btn-warning').attr('name', status.toLowerCase() + 'Button');
                
            })
        </script>
	</div>

    <?php include '../footer.php'; ?>
</body>
	
</html>

<?php mysqli_close($conn); ?>