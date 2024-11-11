<?php
session_start();
include "menu.php";

$con = mysqli_connect("localhost", "root", "1234", "projectphp");
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit;
}

if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit;
}

if (isset($_POST['completeOrder']) && isset($_SESSION['cartId'])) {
    $userID = $_SESSION['userID'];
    $cartId = $_SESSION['cartId'];
    $orderDate = date('Y-m-d H:i:s');

    mysqli_begin_transaction($con);

    try {
        $cartItemsQuery = "SELECT pi.prodectCode, pi.size, pi.amount, p.productName FROM proudincart pi JOIN product p ON pi.prodectCode = p.productCode WHERE pi.IDcart = ?";
        $stmtCart = mysqli_prepare($con, $cartItemsQuery);
        mysqli_stmt_bind_param($stmtCart, 'i', $cartId);
        mysqli_stmt_execute($stmtCart);
        $cartItemsResult = mysqli_stmt_get_result($stmtCart);

        $totalPrice = 0;
        $productCodesToUpdate = [];
        $outOfStockItems = [];

        while ($item = mysqli_fetch_assoc($cartItemsResult)) {
            $productCode = $item['prodectCode'];
            $size = $item['size'];
            $quantityOrdered = $item['amount'];
            $productName = $item['productName'];

            $sizeQuery = "SELECT quantity FROM product_sizes WHERE productCode = ? AND size = ?";
            $stmtSize = mysqli_prepare($con, $sizeQuery);
            mysqli_stmt_bind_param($stmtSize, 'is', $productCode, $size);
            mysqli_stmt_execute($stmtSize);
            $sizeResult = mysqli_stmt_get_result($stmtSize);
            $sizeData = mysqli_fetch_assoc($sizeResult);

            if (!$sizeData || $sizeData['quantity'] < $quantityOrdered) {
                $outOfStockItems[] = "$productName (Size: $size)";
            } else {
                $productCodesToUpdate[] = ['productCode' => $productCode, 'size' => $size];
                $productPriceQuery = "SELECT price FROM product WHERE productCode = ?";
                $stmtProductPrice = mysqli_prepare($con, $productPriceQuery);
                mysqli_stmt_bind_param($stmtProductPrice, 'i', $productCode);
                mysqli_stmt_execute($stmtProductPrice);
                $resultPrice = mysqli_stmt_get_result($stmtProductPrice);
                $productPrice = mysqli_fetch_assoc($resultPrice)['price'];
                $totalPrice += $productPrice * $quantityOrdered;
            }
        }

        if (!empty($outOfStockItems)) {
            foreach ($outOfStockItems as $item) {
                $deleteCartItemQuery = "DELETE FROM proudincart WHERE IDcart = ? AND prodectCode = ? AND size = ?";
                $stmtDeleteCartItem = mysqli_prepare($con, $deleteCartItemQuery);
                preg_match('/^(.*?) \(Size: (.*?)\)$/', $item, $matches);
                $productName = $matches[1];
                $size = $matches[2];

                $productCodeQuery = "SELECT productCode FROM product WHERE productName = ?";
                $stmtProductCode = mysqli_prepare($con, $productCodeQuery);
                mysqli_stmt_bind_param($stmtProductCode, 's', $productName);
                mysqli_stmt_execute($stmtProductCode);
                $resultProductCode = mysqli_stmt_get_result($stmtProductCode);
                $productCodeRow = mysqli_fetch_assoc($resultProductCode);
                $productCode = $productCodeRow['productCode'];

                mysqli_stmt_bind_param($stmtDeleteCartItem, 'iis', $cartId, $productCode, $size);
                mysqli_stmt_execute($stmtDeleteCartItem);
            }

            throw new Exception("Some items in your cart are out of stock: " . implode(", ", $outOfStockItems));
        }

        $insertOrderQuery = "INSERT INTO orders (orderDate, userID, totalPrice, address, phone, instructions, shipping_method) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtOrder = mysqli_prepare($con, $insertOrderQuery);
        mysqli_stmt_bind_param($stmtOrder, 'sidssss', $orderDate, $userID, $totalPrice, $_POST['address'], $_POST['phone'], $_POST['instructions'], $_POST['shippingMethod']);
        mysqli_stmt_execute($stmtOrder);

        $orderNumber = mysqli_insert_id($con);

        mysqli_data_seek($cartItemsResult, 0);
        while ($item = mysqli_fetch_assoc($cartItemsResult)) {
            $productCode = $item['prodectCode'];
            $size = $item['size'];
            $quantityOrdered = $item['amount'];

            $insertOrderItemQuery = "INSERT INTO order_items (orderNumber, productCode, quantity, size) VALUES (?, ?, ?, ?)";
            $stmtOrderItem = mysqli_prepare($con, $insertOrderItemQuery);
            mysqli_stmt_bind_param($stmtOrderItem, 'iiis', $orderNumber, $productCode, $quantityOrdered, $size);
            mysqli_stmt_execute($stmtOrderItem);

            $updateQuantityQuery = "UPDATE product_sizes SET quantity = quantity - ? WHERE productCode = ? AND size = ?";
            $stmtUpdateQty = mysqli_prepare($con, $updateQuantityQuery);
            mysqli_stmt_bind_param($stmtUpdateQty, 'iis', $quantityOrdered, $productCode, $size);
            mysqli_stmt_execute($stmtUpdateQty);
        }

        foreach ($productCodesToUpdate as $productData) {
            $productCode = $productData['productCode'];
            $size = $productData['size'];
            
            $totalQuantityQuery = "SELECT SUM(quantity) AS quantity FROM product_sizes WHERE productCode = ?";
            $stmtTotal = mysqli_prepare($con, $totalQuantityQuery);
            mysqli_stmt_bind_param($stmtTotal, 'i', $productCode);
            mysqli_stmt_execute($stmtTotal);
            $resultTotal = mysqli_stmt_get_result($stmtTotal);
            $totalQuantityRow = mysqli_fetch_assoc($resultTotal);
            $totalQuantity = $totalQuantityRow['quantity'];

            $updateProductQuery = "UPDATE product SET quantity = ? WHERE productCode = ?";
            $stmtUpdateProduct = mysqli_prepare($con, $updateProductQuery);
            mysqli_stmt_bind_param($stmtUpdateProduct, 'ii', $totalQuantity, $productCode);
            mysqli_stmt_execute($stmtUpdateProduct);
        }

        $deleteCartItemsQuery = "DELETE FROM proudincart WHERE IDcart = ?";
        $stmtDeleteCart = mysqli_prepare($con, $deleteCartItemsQuery);
        mysqli_stmt_bind_param($stmtDeleteCart, 'i', $cartId);
        mysqli_stmt_execute($stmtDeleteCart);

        $deleteCartQuery = "DELETE FROM cart WHERE IDcart = ?";
        $stmtDeleteCartTable = mysqli_prepare($con, $deleteCartQuery);
        mysqli_stmt_bind_param($stmtDeleteCartTable, 'i', $cartId);
        mysqli_stmt_execute($stmtDeleteCartTable);

        mysqli_commit($con);

        echo "Your order has been successfully placed. Your order number is: $orderNumber.";

    } catch (Exception $e) {
        mysqli_rollback($con);
        echo $e->getMessage();
    }
} else {
    echo "An error occurred while processing the order. Please try again.";
}

mysqli_close($con);
?>
