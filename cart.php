<?php
session_start();
include "menu.php";
$con = mysqli_connect("localhost", "root", "1234", "projectphp");

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit;
}

if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];

    $cartResult = mysqli_query($con, "SELECT IDcart FROM cart WHERE IDuser = '$userID'");
    if ($cartRow = mysqli_fetch_assoc($cartResult)) {
        $cartID = $cartRow['IDcart'];
        $_SESSION['cartId'] = $cartID;

       
        function checkAndUpdateAvailability($con, $productCode, $size, &$quantity) {
            $query = "SELECT quantity FROM product_sizes WHERE productCode = ? AND size = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("is", $productCode, $size);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            if ($data) {
                if ($data['quantity'] < $quantity) {
                    $quantity = $data['quantity'];
                    return "Note: Quantity updated to available stock ({$data['quantity']}).";
                } elseif ($data['quantity'] == 0) {
                    return "Out of stock";
                }
            }
            return null;
        }

     
        $productsInCartQuery = "
            SELECT p.productName, pi.prodectCode, pi.size, pi.amount, p.price, (pi.amount * p.price) AS itemTotal, p.source
            FROM proudincart pi 
            JOIN product p ON pi.prodectCode = p.productCode 
            WHERE pi.IDcart = '$cartID'";

        $productsInCartResult = mysqli_query($con, $productsInCartQuery);

        if (mysqli_num_rows($productsInCartResult) > 0) {
            $totalPrice = 0;

            echo "<div class='cart-container'>";
            echo "<h2>Your Shopping Cart</h2>";
            echo "<table class='cart-table'>";
            echo "<tr>
                <th>Product Image</th>
                <th>Product Name</th>
                <th>Size</th>
                <th>Quantity</th>
                <th>Price per Item</th>
                <th>Total Price</th>
                <th>Remove</th>
                </tr>";

            while ($row = mysqli_fetch_assoc($productsInCartResult)) {
                $productCode = $row['prodectCode'];
                $size = $row['size'];
                $quantity = $row['amount'];

              
                $availabilityMessage = checkAndUpdateAvailability($con, $productCode, $size, $quantity);
                if ($availabilityMessage === "Out of stock") {
                 
                    $deleteQuery = "DELETE FROM proudincart WHERE IDcart = ? AND prodectCode = ? AND size = ?";
                    $stmt = $con->prepare($deleteQuery);
                    $stmt->bind_param("iis", $cartID, $productCode, $size);
                    $stmt->execute();
                    continue; 
                } else {
                  
                    $updateCartQuery = "UPDATE proudincart SET amount = ? WHERE IDcart = ? AND prodectCode = ? AND size = ?";
                    $stmt = $con->prepare($updateCartQuery);
                    $stmt->bind_param("iiis", $quantity, $cartID, $productCode, $size);
                    $stmt->execute();
                }

                echo "<tr>";
                echo "<td><img src='" . htmlspecialchars($row['source']) . "' alt='" . htmlspecialchars($row['productName']) . "' style='width: 150px; height: 150px;'></td>";
                echo "<td>" . htmlspecialchars($row['productName']) . "</td>";
                echo "<td>" . htmlspecialchars($row['size']) . "</td>";

           
                echo "<td class='quantity-input'>
                        <form action='update_cart.php' method='POST' style='display: inline;'>
                            <input type='hidden' name='prodectCode' value='" . htmlspecialchars($row['prodectCode']) . "'>
                            <input type='hidden' name='size' value='" . htmlspecialchars($row['size']) . "'>
                            <input type='number' name='quantity' value='" . htmlspecialchars($quantity) . "' min='1' required style='width: 50px;'>
                            <button type='submit' style='margin-left: 5px;'>Update</button>
                        </form>
                    </td>";

                echo "<td>$" . htmlspecialchars($row['price']) . "</td>";
                echo "<td>$" . htmlspecialchars($quantity * $row['price']) . "</td>";
                echo "<td><form action='remove_from_cart.php' method='POST'><input type='hidden' name='prodectCode' value='" . $row['prodectCode'] . "' /><input type='hidden' name='size' value='" . $row['size'] . "' /><button type='submit' class='remove-button'>Remove</button></form></td>";
                $totalPrice += $quantity * $row['price'];
                echo "</tr>";

                
                if ($availabilityMessage && $availabilityMessage !== "Out of stock") {
                    echo "<tr><td colspan='7' style='color: #00796b; font-size: 12px;'>" . htmlspecialchars($availabilityMessage) . " <button onclick='dismissNotification(this)'>Dismiss</button></td></tr>";
                }
            }

            echo "<tr><td colspan='5' class='cart-total'>Cart Total: $$totalPrice</td><td colspan='2'></td></tr>";
            echo "</table>";

            echo "<div class='checkout-container'>";
            echo "<form action='checkout.php' method='POST'><button type='submit' name='checkout' class='checkout-button'>Proceed to Checkout</button></form>";
            echo "<form action='clear_cart.php' method='POST' style='display:inline;'><button type='submit' name='clear_cart' class='clear-cart-button'>Clear Cart</button></form>";
            echo "<button onclick='window.history.back()' class='back-button'>Go Back</button>";
            echo "</div>";
            echo "</div>";

        } else {
            echo "<p>Your cart is empty.</p>";
        }
    } else {
        echo "<p>You do not have a cart yet.</p>";
    }
} else {
    header('Location: login.php');
    exit;
}
?>

<script>
function dismissNotification(button) {
    button.closest('tr').style.display = 'none';
}
</script>
