<?php
session_start();
include "menu.php";

$con = mysqli_connect("localhost", "root", "1234", "projectphp");
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit;
}


if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['orderNumber'], $_POST['status'])) {
    $orderNumber = $_POST['orderNumber'];
    $status = $_POST['status'];
    $updateQuery = "UPDATE orders SET status = ? WHERE orderNumber = ?";
    $stmt = $con->prepare($updateQuery);
    if ($stmt) {
        $stmt->bind_param("si", $status, $orderNumber);
        $stmt->execute();
        $stmt->close();
    }
}

$filter = isset($_POST['filter']) ? $_POST['filter'] : null;
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';


$query = "SELECT o.orderNumber, o.orderDate, o.userID, u.Fname, u.Lname, o.totalPrice, o.status, o.shipping_method
          FROM orders o
          JOIN users u ON o.userID = u.IDuser";

$conditions = [];
if ($filter === 'delivery') {
    $conditions[] = "o.shipping_method = 'delivery'";
} elseif ($filter === 'pickup') {
    $conditions[] = "o.shipping_method = 'pickup'";
} elseif ($filter === 'pending') {
    $conditions[] = "o.status = 'Order is being prepared'";
} elseif ($filter === 'completed') {
    $conditions[] = "o.status = 'Order is completed'";
}


if (!empty($searchQuery)) {
    $conditions[] = "(o.orderNumber LIKE '%$searchQuery%' OR u.Fname LIKE '%$searchQuery%' OR u.Lname LIKE '%$searchQuery%')";
}


if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$result = mysqli_query($con, $query);

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <link rel='stylesheet' href='phpcss.css'>
    <title>Admin Order Manager</title>
</head>
<body>
<div class='container'>
    <h1>Admin Order Manager</h1>
    <div class='search-bar'>
        <form method='GET' action=''>
            <input type='text' name='search' placeholder='Search orders...' value='" . htmlspecialchars($searchQuery) . "'>
            <button type='submit'>Search</button>
        </form>
    </div>
    <div class='filter-buttons'>
        <form method='POST' action=''>
            <button type='submit' name='filter' value='delivery'>Delivery Orders</button>
            <button type='submit' name='filter' value='pickup'>Pickup Orders</button>
            <button type='submit' name='filter' value='all'>All Orders</button>
            <button type='submit' name='filter' value='pending'>Orders in Preparation</button>
            <button type='submit' name='filter' value='completed'>Completed Orders</button>
        </form>
    </div>
    <table class='order-table'>
        <tr>
            <th>Order Number</th>
            <th>Order Date</th>
            <th>User ID</th>
            <th>User Name</th>
            <th>Total Price</th>
            <th>Status</th>
            <th>Shipping Method</th>
            <th>View Details</th>
            <th>Delete</th>
        </tr>";


while ($row = mysqli_fetch_assoc($result)) {
    $statusOptions = '';
    if ($row['shipping_method'] == 'delivery') {
        $statusOptions = "
            <option value='Order is being prepared'" . ($row['status'] == 'Order is being prepared' ? " selected" : "") . ">Order is being prepared</option>
            <option value='Order has been delivered to the courier'" . ($row['status'] == 'Order has been delivered to the courier' ? " selected" : "") . ">Order has been delivered to the courier</option>
            <option value='Order is completed'" . ($row['status'] == 'Order is completed' ? " selected" : "") . ">Order is completed</option>";
    } elseif ($row['shipping_method'] == 'pickup') {
        $statusOptions = "
            <option value='Order is being prepared'" . ($row['status'] == 'Order is being prepared' ? " selected" : "") . ">Order is being prepared</option>
            <option value='Order is ready for pickup'" . ($row['status'] == 'Order is ready for pickup' ? " selected" : "") . ">Order is ready for pickup</option>
            <option value='Order is completed'" . ($row['status'] == 'Order is completed' ? " selected" : "") . ">Order is completed</option>";
    }

    echo "<tr>
            <td>{$row['orderNumber']}</td>
            <td>{$row['orderDate']}</td>
            <td>{$row['userID']}</td>
            <td>{$row['Fname']} {$row['Lname']}</td>
            <td>\${$row['totalPrice']}</td>
            <td>
                <form method='POST' action=''>
                    <input type='hidden' name='orderNumber' value='{$row['orderNumber']}'>
                    <select name='status' onchange='this.form.submit()'>
                        $statusOptions
                    </select>
                </form>
            </td>
            <td>
                <form method='POST' action='order_shipping_details.php'>
                    <input type='hidden' name='orderNumber' value='{$row['orderNumber']}'>
                    <input type='hidden' name='method' value='{$row['shipping_method']}'>
                    <button type='submit'>" . ucfirst($row['shipping_method']) . "</button>
                </form>
            </td>
            <td>
                <form method='POST' action='order_details_admin.php'>
                    <input type='hidden' name='orderNumber' value='{$row['orderNumber']}'>
                    <button type='submit'>View Details</button>
                </form>
            </td>
            <td>
           
            <form method='POST' action='admin_order_delete.php' onsubmit=\"return confirm('Are you sure you want to delete this order?');\">
                <input type='hidden' name='deleteOrder' value='{$row['orderNumber']}'>
                <button type='submit'>Delete</button>
            </form>
       
        
            </td>
          </tr>";
}

echo "</table>
</div>
</body>
</html>";
?>
