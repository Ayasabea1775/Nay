<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="phpcss.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&display=swap" rel="stylesheet">
    <title>Checkout Page</title>
    <script>
        function toggleFormFields() {
            const shippingMethod = document.querySelector('select[name="shippingMethod"]').value;
            const addressInput = document.getElementById('address');
            const addressInputGroup = document.getElementById('addressInputGroup');
            const phoneInputGroup = document.getElementById('phoneInputGroup');
            const instructionsInputGroup = document.getElementById('instructionsInputGroup');

            if (shippingMethod === 'pickup') {
                addressInputGroup.style.display = 'none';
                instructionsInputGroup.style.display = 'none';
                phoneInputGroup.style.display = 'block';
                addressInput.removeAttribute('required');
            } else {
                addressInputGroup.style.display = 'block';
                instructionsInputGroup.style.display = 'block';
                phoneInputGroup.style.display = 'block';
                addressInput.setAttribute('required', 'required');
            }
        }

        window.onload = toggleFormFields; 
    </script>
</head>
<body>
    <?php 
    include "menu.php"; 

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $con = mysqli_connect("localhost", "root", "1234", "projectphp");
    if (!$con) {
        die("Failed to connect to MySQL: " . mysqli_connect_error());
    }

    if (!isset($_SESSION['userID'])) {
        header('Location: login.php');
        exit;
    }
    ?>

    <div class="flex">
        <h1>Order Summary</h1>

        <form action='process_order.php' method='POST' class="order-form">
            <div class="input-group">
                <label for="shippingMethod">Shipping Method:</label>
                <select name='shippingMethod' class="select-style" onchange="toggleFormFields()">
                    <option value='delivery'>Delivery</option>
                    <option value='pickup'>Pickup</option>
                </select>
            </div>

            <div class="input-group" id="addressInputGroup">
                <label for="address">Delivery Address:</label>
                <input type="text" id="address" name="address" required>
            </div>

            <div class="input-group" id="instructionsInputGroup">
                <label for="instructions">Special Delivery Instructions:</label>
                <textarea id="instructions" name="instructions"></textarea>
            </div>

            <div class="input-group" id="phoneInputGroup" style="display: none;">
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" required pattern="[0-9]{10}" title="Please enter a 10-digit phone number">
            </div>

           

            <button type='submit' name='completeOrder' class="submit-btn">Complete Order</button>
        </form>
    </div>
</body>
</html>
