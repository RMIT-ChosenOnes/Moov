<?php
session_start();
require_once 'config.php';
$page_name = basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php');

$search_filename = array('- ', ' ', '-', '.');
$replace_filename = array('_', '_', '_', '_');

if (!isset($_SESSION['moov_user_logged_in']) || $_SESSION['moov_user_logged_in'] != TRUE) {
    header('location: /moov/login?url=' . urlencode('/moov/my-booking'));

}

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

$get_current_booking_sql = 'SELECT *, IF(pick_up_date > CURRENT_TIMESTAMP, \'Future\', \'Active\') AS status FROM booking WHERE ((pick_up_date >= CURRENT_TIMESTAMP) OR (pick_up_date < CURRENT_TIMESTAMP AND return_date > CURRENT_TIMESTAMP)) AND (customer_id = '.$_SESSION['moov_user_account_id'].')';


$bookingId = $_REQUEST['booking_id'];

$get_car_details_sql = 'SELECT * FROM moov.booking AS a 
LEFT JOIN moov_portal.car as b ON a.car_id = b.car_id 
LEFT JOIN moov_portal.car_brand as c ON b.brand = c.brand_id 
LEFT JOIN moov.account as d ON a.customer_id = d.account_id 
WHERE a.booking_id = '.$bookingId;

$list = select($get_car_details_sql);

$info = $list[0];

$car_temp_image_name = strtolower($info['brand'] . '_' . $info['model'] . '_' . $info['name']);
$car_image_name = str_replace($search_filename, $replace_filename, $car_temp_image_name);

?>
<!DOCTYPE html>
<html>

<head>
    <title>My Booking | Moov</title>

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
    <script src="./script/jquery.min.js"></script>
   <!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>-->
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
    <STYLE>
        .font-weight-bold{
            font-family: 'Fish', sans-serif;
        }
    </STYLE>
</head>

<body>
<?php include 'header.php'; ?>

<div class="container my-3 footer-align-bottom">
    <h1 class="text-center">Reports</h1>
    <form action="reports_form.php" method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-7 mt-4 mt-md-0">
                <img class="car-image rounded border-0 mt-4" src="/moov/assets/images/transparent_background.png" style="background-image: url(<?='/moov/car-image/'.$car_image_name.'.jpg'?>); height: auto !important;">

                <h3 class="font-weight-bold" style="margin: 30px 0 "><?=$info['name']?></h3>

                <p class="mb-2"><b>Booking ID:</b> #<?=$info['booking_id']?></p>
                <p class="mb-2"><b>Start Booking:</b> <?=$info['created_at']?></p>
                <p class="mb-2"><b>End Booking:</b> <?=$info['pick_up_date']?></p>

                <div></div>

            </div>
            <div class="col-md-5 mt-4 mt-md-0">
                <h4 class="font-weight-bold"> What want wrong ?</h4>
                <div>
                    <textarea rows="12" name="content" placeholder="Input ..." style="width: 100%" class="form-control" > </textarea>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-7">
                <div class="text-center offset-md-1">
                    <img src="assets/logo/moov_logo_100x50.png" class="d-inline-block align-top" alt="Moov" loading="lazy" style="width: 300px;margin: 30px 0 ">
                </div>
                <div>
                    <p class="mb-2"><b>Display Name:</b> <?=$info['display_name']?></p>
                    <p class="mb-2"><b>Full Legal Name :</b> <?=$info['first_name']?> <?=$info['last_name']?></p>
                    <p class="mb-2"><b>Account E-mail:</b> <?=$info['email_address']?></p>
                    <p class="mb-2"><b>Date of Birth:</b> <?=$info['date_of_birth']?></p>
                    <p class="mb-2"><b>Mobile Number:</b> <?=$info['contact_number']?></p>
                </div>
            </div>
            <div class="col-md-5 mt-4 mt-md-0">
                <h4 class="font-weight-bold"> Evidence</h4>
                <div class="custom-file mb-3">
                    <div class="text-center mt-1">
                        <img id="carImage" class="car-image rounded border-0" src="/moov/portal/assets/images/transparent_background.png" style="background-image: url(<?='/moov/car-image/'.$car_image_name.'.jpg'?>); height: auto !important;">
                    </div>
                    <div style="position: relative;margin-top: 15px">
                        <input type="file" class="custom-file-input" id="carImage" name="file" aria-describedby="carImageFileName" onChange="showUploadImage(event), showUploadFileName(this.value)" onKeyUp="changeEventButton(this)">

                        <label id="carImageLabel" class="custom-file-label" for="carImage">Update Car Image</label>
                        <input type="hidden" value="<?=$bookingId?>" name="booking_id">
                        <small id="carImageFileName" class="form-text text-muted"><?php echo empty($car_image_err) ? 'Max. file size is 1MB. Supported file type: JPG.' : ''; ?></small>
                    </div>

                </div>

                <button type="submit" class="btn btn-primary" data-dismiss="modal">Send A Reply</button>
            </div>
        </div>


    </form>
</div>

<?php include 'footer.php'; ?>
<script>

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
</body>

<?php mysqli_close($conn); ?>