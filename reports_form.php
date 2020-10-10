<?php
require_once 'config.php';

$page_name = basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php');

function upload($file)
{
    if(empty($file)) {
        return '';
    } else {
        $path = './uploads/';

        $ext =  pathinfo($file['name'], PATHINFO_EXTENSION);
        $upname = $path.time().'.'.$ext ;
        if(move_uploaded_file($file['tmp_name'],$upname)){
            return $upname ;
        }else{
            return '';
        }
    }
}

$fileName = upload($_FILES['file']);

$content = isset($_REQUEST['content']) ? $_REQUEST['content'] : '';
$bookingId = isset($_REQUEST['booking_id']) ? $_REQUEST['booking_id'] : '';

if (empty($bookingId) || empty($content)) {
    echo "<script>alert('content is not empty ');history.back();</script>";
    return;
}

$sql = "INSERT INTO `booking_report` ( `booking_id`, `reply_content`, `reply_file`, `status`,`resolved_portal_account_id`) VALUES ( '".$bookingId."', '".$content."', '".$fileName."', 0,0)";

$res = $conn->query($sql);

echo "<script>alert('successfully !');history.back();</script>";