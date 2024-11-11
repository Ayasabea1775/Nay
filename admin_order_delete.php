<?php
session_start();

$con = mysqli_connect("localhost", "root", "1234", "projectphp");
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteOrder'])) {
    $orderNumber = $_POST['deleteOrder'];

 
    $query = "SELECT productCode, quantity, size FROM order_items WHERE orderNumber = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $orderNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $productCode = $row['productCode'];
        $quantity = $row['quantity'];
        $size = $row['size'];

      
        $updateSizeStockQuery = "UPDATE product_sizes SET quantity = quantity + ? WHERE productCode = ? AND size = ?";
        $updateSizeStmt = $con->prepare($updateSizeStockQuery);
        $updateSizeStmt->bind_param("iis", $quantity, $productCode, $size);
        $updateSizeStmt->execute();
        $updateSizeStmt->close();

       
        $updateProductStockQuery = "UPDATE product SET quantity = quantity + ? WHERE productCode = ?";
        $updateProductStmt = $con->prepare($updateProductStockQuery);
        $updateProductStmt->bind_param("ii", $quantity, $productCode);
        $updateProductStmt->execute();
        $updateProductStmt->close();
    }
    $stmt->close();

  
    $deleteOrderItemsQuery = "DELETE FROM order_items WHERE orderNumber = ?";
    $stmtItems = $con->prepare($deleteOrderItemsQuery);
    $stmtItems->bind_param("i", $orderNumber);
    $stmtItems->execute();
    $stmtItems->close();

  
    $deleteOrderQuery = "DELETE FROM orders WHERE orderNumber = ?";
    $stmt = $con->prepare($deleteOrderQuery);
    $stmt->bind_param("i", $orderNumber);
    $stmt->execute();
    $stmt->close();

    $_SESSION['message'] = "Order deleted successfully and stock updated.";

    header("Location: admin_orders.php");
    exit;
}
?>
