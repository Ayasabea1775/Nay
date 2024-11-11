<?php
include "menu.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="phpcss.css">
    <title>Forget Password</title>
</head>
<body>
<div style="text-align:center; color:black;">
    <h3>Reset your password here:</h3>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <label>Username: </label>
        <input type="text" name="username" id="username" placeholder="Enter Username" required />
        <br><br>
        <label>Email: </label>
        <input type="email" name="email" id="email" placeholder="Enter Email" required />
        <br><br>
        <button type="submit" name="reset">Reset</button>
    </form>
</div>
</body>
</html>

<?php

function resetPassword() {   
   
    $con = mysqli_connect("localhost", "root", "1234", "projectphp");
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

   
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $email = mysqli_real_escape_string($con, $_POST['email']);

  
    $query = "SELECT * FROM users WHERE username = '$username' AND Mail = '$email'";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
       
        $newPassword = randomPassword();
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

      
        $updateQuery = "UPDATE users SET password = '$hashedPassword' WHERE username = '$username'";
        if (mysqli_query($con, $updateQuery)) {
           
            $_SESSION['email'] = $email;
            $_SESSION['randomPass'] = $newPassword;
            $_SESSION['username'] = $username;
            
          
            header('Location: sendmail.php');
            exit();
        } else {
            echo "<div>Error updating password. Please try again later.</div>";
        }
    } else {
        echo "<div>User or email not found.</div>";
    }
    
    
    mysqli_close($con);
}


function randomPassword() {
    $length = 8;
    $keySpace = "qwertyuiopasdfghjklzxcvbnm123456789";
    $max = mb_strlen($keySpace, '8bit') - 1;
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $keySpace[random_int(0, $max)];
    }
    return $password;
}


if (isset($_POST['reset'])) {
    resetPassword();
}
?>
