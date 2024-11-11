<?php
session_start();

$con = mysqli_connect("localhost", "root", "1234", "projectphp");
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['orderNumber'], $_POST['method'])) {
    $_SESSION['orderNumber'] = $_POST['orderNumber'];
    $_SESSION['method'] = $_POST['method'];
}


if (isset($_SESSION['orderNumber'], $_SESSION['method'])) {
    $orderNumber = $_SESSION['orderNumber'];
    $shippingMethod = $_SESSION['method'];
} else {
    echo "Invalid request.";
    exit;
}

$query = "SELECT o.orderNumber, o.address, o.phone, o.instructions, o.shipping_method
          FROM orders o
          WHERE o.orderNumber = '$orderNumber' AND o.shipping_method = '$shippingMethod'";

$result = mysqli_query($con, $query);
$details = mysqli_fetch_assoc($result);

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <link rel='stylesheet' href='phpcss.css'>
    <title>Shipping Details</title>
</head>
<body>
    <h1>Shipping Details for Order #{$orderNumber}</h1>";

if ($details) {
    if ($shippingMethod == 'delivery') {
        echo "<p>Delivery Address: {$details['address']}</p>
              <p>Phone: {$details['phone']}</p>
              <p>Instructions: {$details['instructions']}</p>";
    } else if ($shippingMethod == 'pickup') {
        echo "<p>Phone for Pickup: {$details['phone']}</p>";
    }
} else {
    echo "<p>No details available for this order.</p>";
}


echo "<div style='text-align: center; margin-top: 20px;'>
        <button onclick='window.history.back()'>Back</button>
      </div>";

echo "</body>
</html>";

mysqli_close($con);
?>
