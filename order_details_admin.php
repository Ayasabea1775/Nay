<?php
session_start();
include "menu.php";

$con = mysqli_connect("localhost", "root", "1234", "projectphp");
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['orderNumber'])) {
    $_SESSION['orderNumber'] = $_POST['orderNumber'];
}


$orderNumber = isset($_SESSION['orderNumber']) ? $_SESSION['orderNumber'] : null;

if (!$orderNumber) {
    echo "Order number is missing.";
    exit;
}


$query = "SELECT p.productName, p.price, oi.quantity, oi.size, p.source AS image
          FROM order_items oi
          JOIN product p ON oi.productCode = p.productCode
          WHERE oi.orderNumber = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $orderNumber);
$stmt->execute();
$result = $stmt->get_result();

$totalOrderPrice = 0;

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <link rel='stylesheet' href='phpcss.css'>
    <title>Order Details</title>
    <style>
        .product-image {
            width: 30px;
            height: 30px;
        }
    </style>
</head>
<body>
<div class='container'>
    <h1>Order Details for Order Number: $orderNumber</h1>
    <table class='order-table'>
        <tr>
            <th>Image</th>
            <th>Product Name</th>
            <th>Size</th>
            <th>Quantity</th>
            <th>Price per Item</th>
            <th>Total Price per Item</th>
        </tr>";

while ($row = $result->fetch_assoc()) {
    $productName = $row['productName'];
    $pricePerItem = $row['price'];
    $quantity = $row['quantity'];
    $image = $row['image'];
    $size = $row['size'];
    $totalPricePerItem = $pricePerItem * $quantity;

    $totalOrderPrice += $totalPricePerItem;

    echo "<tr>
            <td><img src='$image' alt='$productName' class='product-image'></td>
            <td>$productName</td>
            <td>$size</td>
            <td>$quantity</td>
            <td>\$$pricePerItem</td>
            <td>\$$totalPricePerItem</td>
          </tr>";
}

echo "<tr>
        <td colspan='5' style='text-align:right; font-weight:bold;'>Total Price for Order:</td>
        <td style='font-weight:bold;'>\$$totalOrderPrice</td>
      </tr>";

echo "</table>
    <div style='text-align: center; margin-top: 20px;'>
        <button onclick='window.history.back()'>Back</button>
    </div>
</div>
</body>
</html>";

$stmt->close();
$con->close();
?>
