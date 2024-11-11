<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit;
}

$con = mysqli_connect("localhost", "root", "1234", "projectphp");
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit;
}

if (isset($_POST['productCode']) && isset($_POST['size']) && isset($_POST['quantity'])) {
    $productCode = $_POST['productCode'];
    $size = $_POST['size'];
    $quantity = (int) $_POST['quantity'];
    $userID = $_SESSION['userID'];


    $sizeQuery = $con->prepare("SELECT quantity FROM product_sizes WHERE productCode = ? AND size = ?");
    $sizeQuery->bind_param("is", $productCode, $size);
    $sizeQuery->execute();
    $sizeResult = $sizeQuery->get_result();
    $sizeData = $sizeResult->fetch_assoc();

    if (!$sizeData || $sizeData['quantity'] < $quantity) {
        $_SESSION['cart_notification'] = "Sorry, only " . $sizeData['quantity'] . " items are available for this size.";
        header('Location: product.php?productCode=' . $productCode); 
        exit;
    }

  
    $cartQuery = $con->prepare("SELECT IDcart FROM cart WHERE IDuser = ?");
    $cartQuery->bind_param("i", $userID);
    $cartQuery->execute();
    $cartResult = $cartQuery->get_result();
    if ($cartRow = $cartResult->fetch_assoc()) {
        $cartID = $cartRow['IDcart'];
    } else {
        $createCartQuery = $con->prepare("INSERT INTO cart (IDuser) VALUES (?)");
        $createCartQuery->bind_param("i", $userID);
        $createCartQuery->execute();
        $cartID = $con->insert_id;
    }

    $addToCartQuery = $con->prepare("
        INSERT INTO proudincart (IDcart, prodectCode, size, amount) 
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE amount = amount + ?
    ");
    $addToCartQuery->bind_param("iisii", $cartID, $productCode, $size, $quantity, $quantity);
    $addToCartQuery->execute();


    $_SESSION['cart_notification'] = "Product added to cart successfully!";
    header('Location: product.php?productCode=' . $productCode); 
    exit;
} else {
    echo "Product code, size, or quantity is missing.";
}
?>
