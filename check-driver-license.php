<?php
$today_date = date('Y-m-d');

$check_driver_license_expired_sql = 'SELECT driving_license_id, date_of_expiry, is_expired FROM driving_license WHERE account_id = ? ORDER BY driving_license_id DESC LIMIT 1';

if ($check_driver_license_expired_stmt = mysqli_prepare($conn, $check_driver_license_expired_sql)) {
    mysqli_stmt_bind_param($check_driver_license_expired_stmt, 'i', $param_user_account_id);
    
    $param_user_account_id = $user_account_id;
    $param_dl_status = 0;
    
    if (mysqli_stmt_execute($check_driver_license_expired_stmt)) {
        mysqli_stmt_store_result($check_driver_license_expired_stmt);
        
        if (mysqli_stmt_num_rows($check_driver_license_expired_stmt) == 1) {
            mysqli_stmt_bind_result($check_driver_license_expired_stmt, $saved_driver_license_id, $saved_date_of_expiry, $saved_dl_status);
            
            if (mysqli_stmt_fetch($check_driver_license_expired_stmt)) {
                session_start();

                $_SESSION['moov_user_logged_in'] = TRUE;
                $_SESSION['moov_user_account_id'] = $param_user_account_id;
                $_SESSION['moov_user_display_name'] = $user_display_name;
                $_SESSION['moov_user_avatar_status'] = $user_avatar_status;
                $_SESSION['moov_user_avatar_type'] = $user_avatar_type;
                
                if ($saved_dl_status == 0) {
                    if ($saved_date_of_expiry <= $today_date) {
                        $set_dl_expired_sql = 'UPDATE driving_license SET is_expired = ? WHERE driving_license_id = ?';
                        $set_dl_expired_stmt = mysqli_prepare($conn, $set_dl_expired_sql);

                        mysqli_stmt_bind_param($set_dl_expired_stmt, 'ii', $param_dl_expired, $param_dl_id);
                        $param_dl_expired = 1;
                        $param_dl_id = $saved_driver_license_id;

                        if (mysqli_stmt_execute($set_dl_expired_stmt)) {
                            $_SESSION['moov_user_license_expired'] = TRUE;

                            header('location: /moov/my-account?tab=myLicense');

                        } else {
                            $login_error = TRUE;
                            $error_message = mysqli_error($conn);

                        }

                        mysqli_stmt_close($set_dl_expired_stmt);

                    } else {
                        if (!empty($referrer_url)) {
                            header('location: ' . $referrer_url);

                        } else {
                            header('location: /moov/');

                        }
                    }
                } elseif ($saved_dl_status == 1) {
                    $_SESSION['moov_user_license_expired'] = TRUE;

                    header('location: /moov/my-account?tab=myLicense');
                    
                }
            }
        } else {
            $login_error = TRUE;
            $error_message = mysqli_error($conn);
            
        }
    } else {
        $login_error = TRUE;
        $error_message = mysqli_error($conn);
        
    }
}

mysqli_stmt_close($check_driver_license_expired_stmt);
?>