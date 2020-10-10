<?php
session_start();
require_once '../config.php';
$parent_page_name = 'report';
$page_name = basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php');

if (!isset($_SESSION['moov_portal_logged_in']) && $_SESSION['moov_portal_logged_in'] != TRUE) {
    header('location: /moov/portal/login?url=' . urlencode('/moov/portal/' . $parent_page_name));
}

$sort = empty($_GET['sort']) ? 'report_id' : $_GET['sort'];
$keyword = empty($_GET['keyword']) ? '' : $_GET['keyword'];

function select($sql)
{

    global $conn;

    $results = mysqli_query($conn,$sql);

    $list = Array();

    if (!$results) {
        printf("Error: %s\n", mysqli_error($conn));
        exit();
    }

    while ($row = mysqli_fetch_assoc($results))
    {
        $list[]=$row;
    }

    return $list;
}

$sql = "SELECT a.*,b.*,c.*,d.*,e.username as resolved_username  FROM moov.booking_report AS a 
LEFT JOIN moov.booking as b ON a.booking_id = b.booking_id
LEFT JOIN moov_portal.car as c ON b.car_id = c.car_id 
LEFT JOIN moov.account as d ON b.customer_id = d.account_id 
LEFT JOIN moov_portal.portal_account as e ON e.account_id = a.resolved_portal_account_id";

if(!empty($keyword)) $sql .= " where a.booking_id like '%".$keyword."%' ";
if ('car_id' == $sort) $sql .= " order by b.".$sort." desc";
else $sql .= " order by a.".$sort." desc";

$list = select($sql);

?>
<!DOCTYPE html>
<html>

<head>
    <title> Report History | Moov Portal</title>

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
   <!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>-->
    <script src="../../script/jquery.min.js"></script>
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
     <style>
         .modal h4{
             font-family:fantasy;
         }
     </style>
</head>

<body>
<?php include '../header.php'; ?>
<div>

    <div class="fluid-container mx-4 mx-sm-5 px-sm-5 my-3 footer-align-bottom">
        <h1 class="text-center">Reports History</h1>
        <form >
            <div class="row">
                <!-- Sort By -->
                <div class="col-sm-3">
                    <label class="sr-only" for="sort">Sort By</label>

                    <select class="form-control form-control-sm mb-3" id="sort" name="sort">
                        <option value="" selected>Sort By</option>

                        <?php
                        $sort_by_array = array('report_id' => 'Report ID (Default)', 'booking_id' => 'Booking ID', 'status' => 'status');

                        foreach ($sort_by_array as $sort_value => $sort_name) {
                            $selected_sort_by = (isset($_GET['sort']) && $_GET['sort'] == $sort_value ? ' selected="selected"' : '');

                            echo '<option value="' . $sort_value . '" ' . $selected_sort_by . '>' . $sort_name . '</option>';

                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-3">
                    <div class="input-group mb-3 input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text">&nbsp&nbsp&nbsp&nbsp</span>
                        </div>
                        <input type="text" class="form-control" placeholder="Search" name="keyword" value="<?php echo $keyword; ?>">
                    </div>
                </div>
                <div class="col-sm-3">
                    <button type="submit" class="btn btn-primary btn-sm">Search</button>
                </div>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>ReportID</th>
                <th>BookingID</th>
                <th>Car Name</th>
                <th>User E-mail</th>
                <th>Status</th>
                <th>Resolved By</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
                <?php foreach ($list as $val) {?>
                    <tr>
                    <th><?=$val['report_id']?></th>
                    <th><?=$val['booking_id']?></th>
                    <th><?=$val['name']?></th>
                    <th><?=$val['email_address']?></th>
                    <?php if (empty($val['status'])) { ?>
                        <th>Unresolved</th>
                        <th>-</th>
                        <th>
                            <a href="/moov/portal/report/report-data?id=<?=$val['report_id']?>">
                                <button class="btn btn-primary">Resolve</button>
                            </a>
                        </th>
                    <?php } else { ?>
                        <th>Resolved</th>
                        <th>Fisher <?=$val['resolved_username']?></th>
                        <th><a><button class="btn btn-primary" onclick='viewBookingModel(<?php echo json_encode($val); ?>)'>View</button></a></th>
                    <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="myModal">
        <div class="modal-dialog" style="max-width: 800px">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Report # <b id="report_id"></b></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                   <div>
                       <h4 class="card-title">Booking Info </h4>
                       <div>
                           <table class="table table-bordered">
                               <tr>
                                   <th>BookingID</th>
                                    <td><p id="booking_id"></p></td>
                               </tr>
                               <tr>
                                   <th>Start date</th>
                                   <td><p id="created_at"></p></td>
                               </tr>
                               <tr>
                                   <th>Amount</th>
                                   <td><p id="amount"></p></td>
                               </tr>
                               <tr>
                                   <th>Pick Up Date</th>
                                   <td><p id="pick_up_date"></p></td>
                               </tr>
                               <tr>
                                   <th>Card Name</th>
                                   <td><p id="name"></p></td>
                               </tr>
                           </table>
                       </div>
                   </div>
                    <div>
                        <h4 class="card-title">User Info </h4>
                        <div>
                            <table class="table table-bordered">
                                <tr>
                                    <th>First Name</th>
                                    <td><p id="first_name"></p></td>
                                </tr>
                                <tr>
                                    <th>Last Name</th>
                                    <td><p id="last_name"></p></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><p id="email_address"></p></td>
                                </tr>

                            </table>
                        </div>
                        <div>
                            <h4 class="card-title">Resolved Info </h4>
                            <div>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Content</th>
                                        <td><p id="reply_content"></p></td>
                                    </tr>
                                    <tr>
                                        <th>File</th>
                                        <td><img id="reply_file" style="max-width: 400px"></td>
                                    </tr>
                                </table>
                            </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
</div>
<?php include '../footer.php'; ?>
<script>
    function viewBookingModel($info)
    {
        console.log($info);
        $("#report_id").html($info.report_id);
        $("#created_at").html($info.created_at);
        $("#amount").html($info.amount);
        $("#pick_up_date").html($info.pick_up_date);
        $("#name").html($info.name);
        $("#first_name").html($info.first_name);
        $("#last_name").html($info.last_name);
        $("#email_address").html($info.email_address);
        $("#reply_content").html($info.reply_content);
        $("#reply_file").attr('src','/moov/'+$info.reply_file);
        $('#myModal').modal('show');
    }
</script>
</body>

</html>

<?php mysqli_close($conn); ?>
