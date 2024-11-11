<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="phpcss.css">
    <title>Add New User</title>
</head>
<body>
    <?php
    session_start();
    include 'menu.php';

    if ($_SESSION['role'] !== 'admin') {
        header("Location: login.php");
        exit;
    }

    $con = mysqli_connect("localhost", "root", "1234", "projectphp");
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
        $newIDuser = mysqli_real_escape_string($con, $_POST['IDuser']);
        $newFname = mysqli_real_escape_string($con, $_POST['fname']);
        $newLname = mysqli_real_escape_string($con, $_POST['lname']);
        $newMail = mysqli_real_escape_string($con, $_POST['mail']);
        $newRole = mysqli_real_escape_string($con, $_POST['role']);
        $newUsername = mysqli_real_escape_string($con, $_POST['username']);
        $newPassword = password_hash('defaultPassword', PASSWORD_DEFAULT);
        $login_attempts = 0;
        $randomPassword = mysqli_real_escape_string($con, $_POST['randomPassword']);
        $is_blocked = 0;
        $failed_attempts = 0;
        $lock_time = mysqli_real_escape_string($con, $_POST['lock_time']);
        $skin_tone = mysqli_real_escape_string($con, $_POST['skin_tone']);
        $date_of_birth = mysqli_real_escape_string($con, $_POST['date_of_birth']);
        $body_shape = mysqli_real_escape_string($con, $_POST['body_shape']);
        $height = mysqli_real_escape_string($con, $_POST['height']);
        $chest_size = mysqli_real_escape_string($con, $_POST['chest_size']);
        $waist_size = mysqli_real_escape_string($con, $_POST['waist_size']);
        $hip_size = mysqli_real_escape_string($con, $_POST['hip_size']);

        $insertQuery = "INSERT INTO users (IDuser, fname, lname, mail, role, username, password, login_attempts, randomPassword, is_blocked, failed_attempts, lock_time, skin_tone, date_of_birth, body_shape, height, chest_size, waist_size, hip_size) 
                        VALUES ('$newIDuser', '$newFname', '$newLname', '$newMail', '$newRole', '$newUsername', '$newPassword', '$login_attempts', '$randomPassword', '$is_blocked', '$failed_attempts', '$lock_time', '$skin_tone', '$date_of_birth', '$body_shape', '$height', '$chest_size', '$waist_size', '$hip_size')";

        if (!mysqli_query($con, $insertQuery)) {
            die("Error adding new user: " . mysqli_error($con));
        }

        header("Location: admin_users.php");
        exit;
    }
    ?>

    <div style="text-align: center">
        <h2>Add New User</h2>
        <form method="POST" action="add_user.php">
            <input type="text" name="IDuser" placeholder="User ID" required>
            <input type="text" name="fname" placeholder="First Name" required>
            <input type="text" name="lname" placeholder="Last Name" required>
            <input type="email" name="mail" placeholder="Email" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="text" name="randomPassword" placeholder="Random Password">
            <input type="datetime-local" name="lock_time" placeholder="Lock Time">
            <input type="text" name="skin_tone" placeholder="Skin Tone">
            <input type="date" name="date_of_birth" placeholder="Date of Birth">
            <input type="text" name="body_shape" placeholder="Body Shape">
            <input type="number" name="height" placeholder="Height (cm)">
            <input type="number" name="chest_size" placeholder="Chest Size (cm)">
            <input type="number" name="waist_size" placeholder="Waist Size (cm)">
            <input type="number" name="hip_size" placeholder="Hip Size (cm)">
            <select name="role">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit" name="add_user">Add User</button>
        </form>
        <br>
        <a href="admin_users.php">Back to User Management</a>
    </div>
</body>
</html>
