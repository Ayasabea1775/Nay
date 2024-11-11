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

$userID = $_SESSION['userID'];


$cartResult = mysqli_query($con, "SELECT IDcart FROM cart WHERE IDuser = '$userID'");
if ($cartRow = mysqli_fetch_assoc($cartResult)) {
    $cartID = $cartRow['IDcart'];

 
    $clearCartQuery = "DELETE FROM proudincart WHERE IDcart = '$cartID'";
    mysqli_query($con, $clearCartQuery);

    echo "<script>alert('Your cart has been cleared.'); window.location.href='cart.php';</script>";
} else {
    echo "<script>alert('Your cart is already empty.'); window.location.href='cart.php';</script>";
}

mysqli_close($con);
?>
