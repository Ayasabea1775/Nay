<?php
$con = mysqli_connect("localhost", "root", "1234", "projectphp");

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}


$username = mysqli_real_escape_string($con, $_POST['username']);
$fname = mysqli_real_escape_string($con, $_POST['fname']);
$lname = mysqli_real_escape_string($con, $_POST['lname']);
$password = mysqli_real_escape_string($con, $_POST['password']);
$IDuser = mysqli_real_escape_string($con, $_POST['id']);
$email = mysqli_real_escape_string($con, $_POST['email']);
$date = mysqli_real_escape_string($con, $_POST['date']);
$skin_tone = mysqli_real_escape_string($con, $_POST['skin_tone']);
$height = mysqli_real_escape_string($con, $_POST['height']);
$chest_size = mysqli_real_escape_string($con, $_POST['chest_size']);
$waist_size = mysqli_real_escape_string($con, $_POST['waist_size']);
$hip_size = mysqli_real_escape_string($con, $_POST['hip_size']);
$body_shape = mysqli_real_escape_string($con, $_POST['body_shape']);


$checkQuery = "SELECT * FROM users WHERE username = '$username' OR IDuser = '$IDuser' OR Mail = '$email'";
$checkResult = mysqli_query($con, $checkQuery);

if (mysqli_num_rows($checkResult) > 0) {
    
    echo "<script>alert('Username, ID, or email already exists. Please use different credentials.'); window.location.href = 'register.php';</script>";
} else {
    
    $sql = "INSERT INTO users (username, Fname, Lname, password, IDuser, Mail, date_of_birth, skin_tone, height, chest_size, waist_size, hip_size, body_shape) 
            VALUES ('$username', '$fname', '$lname', '$password', '$IDuser', '$email', '$date', '$skin_tone', '$height', '$chest_size', '$waist_size', '$hip_size', '$body_shape')";

    if (mysqli_query($con, $sql)) {
        header("Location: login.php"); 
    } else {
        echo "Error: " . mysqli_error($con); 
    }
}

mysqli_close($con);
?>
