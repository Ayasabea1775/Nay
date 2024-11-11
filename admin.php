<html>
    <head>
    <link rel="stylesheet" href="phpcss.css">
    <title>Home</title>
    </head>
    <body>
</body>

</html>

<?php
session_start();
require 'functions.php'; 

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}


$con = mysqli_connect("localhost", "root", "1234", "projectphp");


$ordersQuery = "SELECT * FROM orders";
$ordersResult = mysqli_query($con, $ordersQuery);


$customersQuery = "SELECT * FROM users WHERE role = 'user'";
$customersResult = mysqli_query($con, $customersQuery);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Admin Dashboard</h1>
    
    <h2>All Orders</h2>
    <table>
        <tr>
            <th>Order Number</th>
            <th>Order Date</th>
            <th>User ID</th>
            <th>Total Price</th>
        </tr>
        <?php while($order = mysqli_fetch_assoc($ordersResult)): ?>
            <tr>
                <td><?= htmlspecialchars($order['orderNumber']) ?></td>
                <td><?= htmlspecialchars($order['orderDate']) ?></td>
                <td><?= htmlspecialchars($order['userID']) ?></td>
                <td><?= htmlspecialchars($order['totalPrice']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <h2>All Customers</h2>
    <table>
        <tr>
            <th>Username</th>
            <th>Full Name</th>
            <th>Email</th>
        </tr>
        <?php while($customer = mysqli_fetch_assoc($customersResult)): ?>
            <tr>
                <td><?= htmlspecialchars($customer['username']) ?></td>
                <td><?= htmlspecialchars($customer['Fname'] . " " . $customer['Lname']) ?></td>
                <td><?= htmlspecialchars($customer['Mail']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
