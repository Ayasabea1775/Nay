<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "projectphp";

$con = new mysqli($servername, $username, $password, $dbname);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$user_id = $_SESSION['userID'];
$sql = "SELECT * FROM users WHERE IDuser = $user_id";
$result = mysqli_query($con, $sql);

if ($result) {
    $user = mysqli_fetch_assoc($result);
} else {
    echo "Error retrieving user data: " . mysqli_error($con);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $updateFields = [];

    if (!empty($_POST['username']) && $_POST['username'] !== $user['username']) {
        $updateFields[] = "username = '" . mysqli_real_escape_string($con, $_POST['username']) . "'";
    }
    if (!empty($_POST['Fname']) && $_POST['Fname'] !== $user['Fname']) {
        $updateFields[] = "Fname = '" . mysqli_real_escape_string($con, $_POST['Fname']) . "'";
    }
    if (!empty($_POST['Lname']) && $_POST['Lname'] !== $user['Lname']) {
        $updateFields[] = "Lname = '" . mysqli_real_escape_string($con, $_POST['Lname']) . "'";
    }
    if (!empty($_POST['email']) && $_POST['email'] !== $user['Mail']) {
        $updateFields[] = "Mail = '" . mysqli_real_escape_string($con, $_POST['email']) . "'";
    }
    if (!empty($_POST['date']) && $_POST['date'] !== $user['date_of_birth']) {
        $updateFields[] = "date_of_birth = '" . mysqli_real_escape_string($con, $_POST['date']) . "'";
    }
    if (!empty($_POST['skin_tone']) && $_POST['skin_tone'] !== $user['skin_tone']) {
        $updateFields[] = "skin_tone = '" . mysqli_real_escape_string($con, $_POST['skin_tone']) . "'";
    }
    if (!empty($_POST['body_shape']) && $_POST['body_shape'] !== $user['body_shape']) {
        $updateFields[] = "body_shape = '" . mysqli_real_escape_string($con, $_POST['body_shape']) . "'";
    }
    if (!empty($_POST['height']) && $_POST['height'] !== $user['height']) {
        $updateFields[] = "height = '" . mysqli_real_escape_string($con, $_POST['height']) . "'";
    }
    if (!empty($_POST['chest_size']) && $_POST['chest_size'] !== $user['chest_size']) {
        $updateFields[] = "chest_size = '" . mysqli_real_escape_string($con, $_POST['chest_size']) . "'";
    }
    if (!empty($_POST['waist_size']) && $_POST['waist_size'] !== $user['waist_size']) {
        $updateFields[] = "waist_size = '" . mysqli_real_escape_string($con, $_POST['waist_size']) . "'";
    }
    if (!empty($_POST['hip_size']) && $_POST['hip_size'] !== $user['hip_size']) {
        $updateFields[] = "hip_size = '" . mysqli_real_escape_string($con, $_POST['hip_size']) . "'";
    }

   
    if(!empty($_POST['current_password']) )
    {
        if ($_POST['current_password'] === $user['password']) {
            if (!empty($_POST['new_password']) && $_POST['new_password'] === $_POST['confirm_password']) {
                $updateFields[] = "password = '" . mysqli_real_escape_string($con, $_POST['new_password']) . "'";
            } else {
                echo "New password and confirmation password do not match.";
            }
        } else {
            echo "Current password is incorrect.";
        }

        
    }

   
    if (!empty($updateFields)) {
        $sql = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE IDuser = $user_id";
        if (mysqli_query($con, $sql)) {
            $_SESSION['username'] = $_POST['username'];
            echo "Profile updated successfully!";
        } else {
            echo "Error updating profile: " . mysqli_error($con);
        }
    } else {
        echo "No changes detected.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="phpcss.css"> 
    <meta charset="UTF-8">  
    <title>Edit Profile</title>
</head>
<body>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector("form");
        const newPasswordInput = document.getElementById("new_password");
        const confirmPasswordInput = document.getElementById("confirm_password");

     
        function validatePasswords() {
            if (newPasswordInput.value !== confirmPasswordInput.value) {
                confirmPasswordInput.setCustomValidity("Passwords do not match");
            } else {
                confirmPasswordInput.setCustomValidity("");
            }
        }

       
        newPasswordInput.addEventListener("input", validatePasswords);
        confirmPasswordInput.addEventListener("input", validatePasswords);

        form.addEventListener("submit", function (event) {
           
            const height = document.getElementById("height").value;
            const chestSize = document.getElementById("chest_size").value;
            const waistSize = document.getElementById("waist_size").value;
            const hipSize = document.getElementById("hip_size").value;

            if (height < 0 || chestSize < 0 || waistSize < 0 || hipSize < 0) {
                alert("Measurements cannot be negative values.");
                event.preventDefault();
            }

           
            validatePasswords();
        });
    });
</script>

<?php include 'menu.php'; ?>

<div class="form-container">
    <form method="POST" action="edit_profile.php">
        <div class="form-group">
            <label>Username :</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="form-group">
            <label>Email Address :</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Mail']); ?>" required>
        </div>
        <div class="form-group">
            <label>First Name:</label>
            <input type="text" id="first_name" name="Fname" value="<?php echo htmlspecialchars(isset($user['Fname']) ? $user['Fname'] : ''); ?>" required>
        </div>
        <div class="form-group">
            <label>Last Name:</label>
            <input type="text" id="last_name" name="Lname" value="<?php echo htmlspecialchars(isset($user['Lname']) ? $user['Lname'] : ''); ?>" required>
        </div>
        <div class="form-group">
            <label>Date of Birth:</label>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars(isset($user['date_of_birth']) ? $user['date_of_birth'] : ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="skin_tone">Skin Tone:</label>
            <select id="skin_tone" name="skin_tone">
                <option value="light" <?php if(isset($user['skin_tone']) && $user['skin_tone'] == 'light') echo 'selected'; ?>>Light</option>
                <option value="medium" <?php if(isset($user['skin_tone']) && $user['skin_tone'] == 'medium') echo 'selected'; ?>>Medium</option>
                <option value="dark" <?php if(isset($user['skin_tone']) && $user['skin_tone'] == 'dark') echo 'selected'; ?>>Dark</option>
            </select>
        </div>
        <div class="form-group">
            <label for="body_shape">Body Shape:</label>
            <input type="number" id="body_shape" name="body_shape" value="<?php echo htmlspecialchars(isset($user['body_shape']) ? $user['body_shape'] : ''); ?>" min="0">
        </div>
        <div class="form-group">
            <label for="height">Height (cm):</label>
            <input type="number" id="height" name="height" value="<?php echo htmlspecialchars(isset($user['height']) ? $user['height'] : ''); ?>" min="0">
        </div>
        <div class="form-group">
            <label for="chest_size">Chest Size (cm):</label>
            <input type="number" id="chest_size" name="chest_size" value="<?php echo htmlspecialchars(isset($user['chest_size']) ? $user['chest_size'] : ''); ?>" min="0">
        </div>
        <div class="form-group">
            <label for="waist_size">Waist Size (cm):</label>
            <input type="number" id="waist_size" name="waist_size" value="<?php echo htmlspecialchars(isset($user['waist_size']) ? $user['waist_size'] : ''); ?>" min="0">
        </div>
        <div class="form-group">
            <label for="hip_size">Hip Size (cm):</label>
            <input type="number" id="hip_size" name="hip_size" value="<?php echo htmlspecialchars(isset($user['hip_size']) ? $user['hip_size'] : ''); ?>" min="0">
        </div>
        <div class="form-group">
            <label>Current Password:</label>
            <input type="password" id="current_password" name="current_password" >
        </div>
        <div class="form-group">
            <label>New Password:</label>
            <input type="password" id="new_password" name="new_password">
        </div>
        <div class="form-group">
            <label>Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password">
        </div>
        <button name="update_profile" type="submit"><span>Update Profile</span></button>
    </form>
</div>

</body>
</html>
