<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
}
?>

<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>geolocation</title>
</head>

<body>
	<iframe src="https://www.google.com/maps/d/u/0/embed?mid=1L6jFc5iUkzb9vk5QYm4zHKYaT7yxxZ39" width="640" height="480"></iframe>
	
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
	</form>
</body>
</html>
