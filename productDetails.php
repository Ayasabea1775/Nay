<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    
    <link rel="stylesheet" type="text/css" href="css/productDetails.css">
</head>
<body>

<?php
include 'menu.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "projectphp";

$con = new mysqli($servername, $username, $password, $dbname);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

function getUserApproximateSize($chest, $waist, $hip) {
    if ($chest <= 84 && $waist <= 64 && $hip <= 90) return 'XS';
    if ($chest <= 90 && $waist <= 70 && $hip <= 96) return 'S';
    if ($chest <= 96 && $waist <= 76 && $hip <= 102) return 'M';
    if ($chest <= 102 && $waist <= 82 && $hip <= 108) return 'L';
    if ($chest > 108 || $waist > 88 || $hip > 114) return 'XL';
    return null; 
}

if (isset($_SESSION['userID'])) {
    $userID = intval($_SESSION['userID']);
    $userSql = "SELECT height, chest_size, waist_size, hip_size FROM users WHERE IDuser = ?";
    $userStmt = $con->prepare($userSql);
    if ($userStmt) {
        $userStmt->bind_param("i", $userID);
        $userStmt->execute();
        $userResult = $userStmt->get_result();
        if ($userRow = $userResult->fetch_assoc()) {
            $user_approximate_size = getUserApproximateSize($userRow['chest_size'], $userRow['waist_size'], $userRow['hip_size']);
        }
        $userStmt->close();
    }
}

if (isset($_GET['productCode'])) {
    $productCode = intval($_GET['productCode']);
    $sql = "SELECT * FROM product WHERE productCode = ?";
    $stmt = $con->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $productCode);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            echo "<div class='product-container'>";
            echo "<h1>" . htmlspecialchars($row['productName']) . "</h1>";
            echo "<img src='" . htmlspecialchars($row['source']) . "' alt='" . htmlspecialchars($row['productName']) . "' style='max-width: 200px;'>";
            echo "<p><strong>Price:</strong> $" . htmlspecialchars($row['price']) . "</p>";

            $sizeSql = "SELECT size, quantity FROM product_sizes WHERE productCode = ?";
            $sizeStmt = $con->prepare($sizeSql);
            if ($sizeStmt) {
                $sizeStmt->bind_param("i", $productCode);
                $sizeStmt->execute();
                $sizeResult = $sizeStmt->get_result();
                echo "<table><tr><th>Size</th><th>Quantity</th></tr>";
                while ($sizeRow = $sizeResult->fetch_assoc()) {
                    echo "<tr><td>" . htmlspecialchars($sizeRow['size']) . "</td><td>" . $sizeRow['quantity'] . "</td></tr>";
                }
                echo "</table>";
                $sizeStmt->close();
            }

            if ($user_approximate_size) {
                echo "<p><strong>Recommended Size for You:</strong> $user_approximate_size</p>";
            }
            echo "</div>";
        } else {
            echo "<p>Product not found.</p>";
        }
        $stmt->close();
    }
}
$con->close();
?>

</body>
</html>
