<?php
session_start();

$con = mysqli_connect("localhost", "root", "1234", "projectphp");

if (mysqli_connect_errno()) {
    $_SESSION['cart_notification'] = "Failed to connect to the database.";
    header('Location: cart.php');
    exit;
}

if (isset($_SESSION['userID'], $_POST['prodectCode'], $_POST['quantity'], $_POST['size'])) {
    $userID = $_SESSION['userID'];
    $productCode = $_POST['prodectCode'];
    $quantity = intval($_POST['quantity']);
    $size = $_POST['size'];

  
    $sizeQuery = $con->prepare("SELECT quantity FROM product_sizes WHERE productCode = ? AND size = ?");
    $sizeQuery->bind_param("is", $productCode, $size);
    $sizeQuery->execute();
    $sizeResult = $sizeQuery->get_result();

    if ($sizeRow = $sizeResult->fetch_assoc()) {
        $availableQuantity = $sizeRow['quantity'];

        if ($quantity > $availableQuantity) {
            $_SESSION['cart_notification'] = "Requested quantity exceeds available stock of $availableQuantity for size $size.";
        } else {
           
            $cartResult = $con->prepare("SELECT IDcart FROM cart WHERE IDuser = ?");
            $cartResult->bind_param("i", $userID);
            $cartResult->execute();
            $cartRow = $cartResult->get_result()->fetch_assoc();

            if ($cartRow) {
                $cartID = $cartRow['IDcart'];

              
                $updateQuery = $con->prepare("UPDATE proudincart SET amount = ? WHERE IDcart = ? AND prodectCode = ? AND size = ?");
                $updateQuery->bind_param("iiis", $quantity, $cartID, $productCode, $size);

                if ($updateQuery->execute()) {
                    $_SESSION['cart_notification'] = "Cart updated successfully.";
                } else {
                    $_SESSION['cart_notification'] = "Error updating cart: " . $con->error;
                }
            } else {
                $_SESSION['cart_notification'] = "Cart not found.";
            }
        }
    } else {
        $_SESSION['cart_notification'] = "Product size not found.";
    }
} else {
    $_SESSION['cart_notification'] = "Required data not provided.";
}

header('Location: cart.php');
exit;
?>
