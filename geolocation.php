<?php

/*if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	print_r($_FILES["fileToUpload"]);
	
$target_dir = "account-image/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	
	echo $target_file;

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
  $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
  if($check !== false) {
    echo "File is an image - " . $check["mime"] . ".";
    $uploadOk = 1;
  } else {
    echo "File is not an image.";
    $uploadOk = 0;
  }
}

// Check if file already exists
if (file_exists($target_file)) {
  echo "Sorry, file already exists.";
  $uploadOk = 0;
}

// Check file size
if ($_FILES["fileToUpload"]["size"] > 6000000) {
  echo "Sorry, your file is too large.";
  $uploadOk = 0;
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
  echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
  } else {
    echo "Sorry, there was an error uploading your file.";
  }
}
}*/
?>

<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>geolocation</title>

    <!-- JavaScript from Bootstrap -->
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
    
    <script src='assets/select2.min.js' type='text/javascript'></script>

    <!-- CSS -->
    <link href='assets/select2.min.css' rel='stylesheet' type='text/css'>

	<!-- CSS from Bootstrap v4.5.2 -->
	<link rel="stylesheet" type="text/css" href="/moov/assets/style/bootstrap.css">

	<!-- Self Defined CSS -->
	<link rel="stylesheet" type="text/css" href="/moov/assets/style/style.css?<?php echo date('l jS \of F Y h:i:s A'); ?>">
    
</head>

<body>
    <!--<iframe src="https://www.google.com/maps/d/u/0/embed?mid=1L6jFc5iUkzb9vk5QYm4zHKYaT7yxxZ39" width="640" height="480"></iframe>
	
	<iframe title="Test" aria-label="chart" id="datawrapper-chart-7dQWc" src="https://datawrapper.dwcdn.net/7dQWc/1/" scrolling="no" frameborder="0" style="border: none;" width="550" height="481"></iframe>
	
	<button onClick="getLocation()">Locate Me</button>
	
	<p id="test"></p>
	
	<script>
		var x = document.getElementById('test');
		
		function getLocation() {
			if (navigator.geolocation) {
				// get current position only
				//navigator.geolocation.getCurrentPosition(showPosition, showError);
				
				// update position once user moved
				navigator.geolocation.watchPosition(showPosition, showError);
			} else {
				x.innerHTML = "Error";
			}
		}

		function showPosition(position) {
			x.innerHTML = position.coords.latitude + "<br/>" + position.coords.longitude;
		}
		
		function showError(error) {
			switch(error.code) {
				case error.PERMISSION_DENIED:
					x.innerHTML = "User denied!";
					break;
				case error.POSITION_UNAVAILABLE:
					x.innerHTML = "Location N/A!";
					break;
				case error.TIMEOUT:
					x.innerHTML = "Timed out!";
					break;
				case error.UNKNOWN_ERROR:
					x.innerHTML = "Unknown!";
					break;
			}
		}
	</script>
	
	<form action="geolocation.php" method="post" enctype="multipart/form-data">
	  Select image to upload:
	  <input type="file" name="fileToUpload" id="fileToUpload">
	  <input type="submit" value="Upload Image" name="submit">
	</form>-->

<form action="<?php echo basename(htmlspecialchars($_SERVER['PHP_SELF']), '.php'); ?>" method="post" onSubmit="submitButton()">
    <select id='selUser' class="form-control" style='width: 200px;'>
  <option value='0'>Select User</option> 
  <option value='1'>Yogesh singh</option> 
  <option value='2'>Sonarika Bhadoria</option> 
  <option value='3'>Anil Singh</option> 
  <option value='4'>Vishal Sahu</option> 
  <option value='5'>Mayank Patidar</option> 
  <option value='6'>Vijay Mourya</option> 
  <option value='7'>Rakesh sahu</option> 
</select>

<input type='button' class="btn btn-primary" value='Seleted option' id='but_read'>
    </form>

<br/>
<div id='result'></div>
    
    <script>
    $(document).ready(function(){
 
  // Initialize select2
  $("#selUser").select2();

  // Read selected option
  $('#but_read').click(function(){
    var username = $('#selUser option:selected').text();
    var userid = $('#selUser').val();

    $('#result').html("id : " + userid + ", name : " + username);

  });
});
    </script>

</body>
</html>