<?php
session_start();
$con = mysqli_connect("localhost", "root", "1234", "projectphp");

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit;
}
if (isset($_SESSION['userID'], $_POST['prodectCode'])) {
    $userID = $_SESSION['userID'];
    $productCode = $_POST['prodectCode'];

  
    $cartResult = mysqli_query($con, "SELECT IDcart FROM cart WHERE IDuser = '$userID'");
    if ($cartRow = mysqli_fetch_assoc($cartResult)) {
        $cartID = $cartRow['IDcart'];

       
        $removeQuery = "DELETE FROM proudincart WHERE IDcart = '$cartID' AND prodectCode = '$productCode'";
        mysqli_query($con, $removeQuery);
    }

    header('Location: cart.php'); 
    exit;
}
?>
