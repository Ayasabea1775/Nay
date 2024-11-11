<?php
session_start();
include "menu.php";

$con = mysqli_connect("localhost", "root", "1234", "projectphp");
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit;
}

$userId = $_SESSION['userID']; 
$query = "SELECT orderNumber, orderDate, totalPrice, status FROM orders WHERE userID = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <link rel='stylesheet' href='phpcss.css'>
    <title>My Orders</title>
</head>
<body>
<div class='container'>
    <h1>My Orders</h1>
    <table class='order-table'>
        <tr>
            <th>Order Number</th>
            <th>Order Date</th>
            <th>Total Price</th>
            <th>Status</th>
            <th>View Details</th>
        </tr>";


while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['orderNumber']}</td>
            <td>{$row['orderDate']}</td>
            <td>\${$row['totalPrice']}</td>
            <td>{$row['status']}</td>
            <td>
                <form method='POST' action='order_details.php'>
                    <input type='hidden' name='orderNumber' value='{$row['orderNumber']}'>
                    <button type='submit'>View Details</button>
                </form>
            </td>
          </tr>";
}

echo "</table>
</div>
</body>
</html>";

$stmt->close();
$con->close();
?>
