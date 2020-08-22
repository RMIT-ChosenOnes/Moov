<?php
require_once 'config.php';
$query = "SELECT * FROM account";
$result = mysqli_query($conn, $query);
$row_count = mysqli_num_rows($result);

// returning rows from db
if ($row_count == 0) {
    echo 'Nothing';
} else {
    for ($i = 0; $i < $row_count; $i++)
        $item[$i] = mysqli_fetch_array($result);
    foreach ($item as $next) {
        echo '<div class="col-sm-10 col-md-4 col-lg-3">';
        echo $next;
        echo '</div>';
    }
}
