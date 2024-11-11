<?php
session_start();
$con = mysqli_connect("localhost", "root", "1234", "projectphp");

if (!$con) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}


if (isset($_GET['username']) && isset($_GET['token'])) {
    $username = mysqli_real_escape_string($con, $_GET['username']);
    $token = mysqli_real_escape_string($con, $_GET['token']);

  
    $result = mysqli_query($con, "SELECT * FROM users WHERE username = '$username' AND randomPassword = '$token'");
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        die("Invalid or expired reset token.");
    }
} else {
    die("Username or token not provided.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password === $confirmPassword) {
       
        $updateQuery = "UPDATE users SET password = '$password', randomPassword = '' WHERE username = '$username'";
        if (mysqli_query($con, $updateQuery)) {
            echo "<script>alert('Password updated successfully.');</script>";
            header("Location: login.php");
            exit();
        } else {
            echo "Error updating password: " . mysqli_error($con);
        }
    } else {
        echo "Passwords do not match.";
    }
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Password Reset</title>
    <link rel="stylesheet" href="phpcss.css">
</head>
<body>
    <h1 style="text-align:center;">Password Reset</h1>
    <div style="text-align:center;">
        <form method="post">
            <label for="password">Enter a new Password:</label>
            <input type="password" name="password" id="password" required>
            <br><br>
            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" name="confirmPassword" id="confirmPassword" required>
            <br><br>
            <button type="submit">Confirm</button>
        </form>
    </div>
</body>
</html>
