<!DOCTYPE html>
<html lang="en">
<head>
<head>
       <link rel="stylesheet" href="phpcss.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .navbar img {
            width: 50px; 
            height: auto;
            margin-right: 10px;
            vertical-align: middle;
        }
    </style>
    <script>
        function validateForm() {
            const height = document.getElementById("height").value;
            const chestSize = document.getElementById("chest_size").value;
            const waistSize = document.getElementById("waist_size").value;
            const hipSize = document.getElementById("hip_size").value;

            if (height < 0 || chestSize < 0 || waistSize < 0 || hipSize < 0) {
                alert("Measurements cannot be negative.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
<?php include 'menu.php'; ?>
<div class="form-container">
    <h2>Register</h2>
    <form method="POST" action="register_table.php" onsubmit="return validateForm()">
        <div>
            <label>Username :</label>
            <input type="text" id="username" name="username" placeholder="Enter Your Username..." required>
        </div>
        <div>
            <label>Email Address :</label>
            <input type="email" id="email" name="email" placeholder="Enter Your Email Address..." required>
        </div>
        <div>
            <label>First Name:</label>
            <input type="text" id="first_name" name="fname" placeholder="Enter Your First Name..." required>
        </div>
        <div>
            <label>Last Name:</label>
            <input type="text" id="last_name" name="lname" placeholder="Enter Your Last Name..." required>
        </div>
        <div>
            <label>ID:</label>
            <input type="text" name="id" placeholder="Enter Your ID..." required>
        </div>
        <div>
            <label>Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter Password..." required>
        </div>
        <div>
            <label>Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password..." required>
        </div>
        <div>
            <label>Date of Birth:</label>
            <input style="text-align: left" type="date" id="date" name="date" required>
        </div>
        <div>
            <label for="skin_tone">Skin Tone (Optional):</label>
            <select id="skin_tone" name="skin_tone">
                <option value="">--Select Skin Tone--</option>
                <option value="light">Light</option>
                <option value="medium">Medium</option>
                <option value="dark">Dark</option>
            </select>
            <button type="button" onclick="alert('Choosing your skin tone helps us recommend clothes that match your skin tone.')">Why provide skin tone?</button>
        </div>
        <div>
            <label>Height (cm):</label>
            <input type="number" id="height" name="height" placeholder="Enter your height in cm..." min="0" >
        </div>
        <div>
            <label>Chest Size (cm):</label>
            <input type="number" id="chest_size" name="chest_size" placeholder="Enter your chest size in cm..." min="0" >
        </div>
        <div>
            <label>Waist Size (cm):</label>
            <input type="number" id="waist_size" name="waist_size" placeholder="Enter your waist size in cm..." min="0" >
        </div>
        <div>
            <label>Hip Size (cm):</label>
            <input type="number" id="hip_size" name="hip_size" placeholder="Enter your hip size in cm..." min="0" >
        </div>
        <div>
            <label>Body Shape:</label>
            <img src="/labs/project/photo/a.jpg" alt="Body Shapes">
            <select id="body_shape" name="body_shape" >
                <option value="">--Select Body Shape--</option>
                <option value="1">Hourglass</option>
                <option value="2">Inverted Triangle</option>
                <option value="3">Rectangle</option>
                <option value="4">Pear</option>
                <option value="5">Apple</option>
            </select>
        </div>
        <button name="register" type="submit"><span>Register</span></button>
    </form>
</div>
</body>
</html>

