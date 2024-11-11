<?php
session_start();
include "menu.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="phpcss.css">
</head>
<body>
<div>
    <form method='post' action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <label>UserName : </label>
        <input type='text' name='username' placeholder="Enter UserName" required />
        <br><br>
        <label>Password : </label>
        <input type='password' name='password' placeholder="Enter Password" required />
        <br>
        <button type='submit' name="login"> <span> Login </span> </button>
        <br>
        <a href="forgetPassword.php" class="forgot-password">Forget Password?</a>
        <br>
        <p style="color : black">Don't have an account yet? </p><a href="register.php" style="text-align:center;">Register Now</a>
    </form>
</div>
</body>
</html>

<?php
$con = mysqli_connect("localhost", "root", "1234", "projectphp");

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']); 

   
    $loginStmt = $con->prepare("SELECT IDuser, role FROM users WHERE username = ? AND password = ?");
    $loginStmt->bind_param("ss", $username, $password);
    $loginStmt->execute();
    $loginResult = $loginStmt->get_result();

    if ($loginResult->num_rows === 1) {
        $user = $loginResult->fetch_assoc();

        
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['userID'] = $user['IDuser'];
        $_SESSION['role'] = $user['role'];

     
        if ($_SESSION['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: home.php");
        }
        exit;
    } else {
        echo "Invalid username or password.";
    }
}
?>
