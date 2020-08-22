<?php
$servername = 'localhost';
$username = 'pp';
$password = 'IEFo7NyYSR8tkPse';
$database = 'moov';

$conn = new mysqli($servername, $username, $password, $database);

if (!conn) {
    die('Connection failed: ' . mysqli_connect_error());
}
?>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-171692999-2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-171692999-2');
</script>
