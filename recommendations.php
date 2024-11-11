<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

include 'menu.php';
include('recommendation.php');

$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "projectphp";

$con = new mysqli($servername, $username, $password, $dbname);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$user_id = $_SESSION['userID'];

$skinToneRecommendations = recommend_clothes($user_id, $con);
$bodyShapeRecommendations = recommend_clothes_by_body_shape($user_id, $con);

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="phpcss.css">
    <title>Recommendations</title>
</head>
<body>

<div class="recommendations">
    <h2>Recommended for You Based on Skin Tone</h2>
    <?php
    if (!empty($skinToneRecommendations)) {
        foreach ($skinToneRecommendations as $item) {
            echo "<div class='product-item'>";
            echo "<h2>" . htmlspecialchars($item['productName']) . "</h2>";
            echo "<img class='product-img' src='" . htmlspecialchars($item['source']) . "' alt='" . htmlspecialchars($item['productName']) . "'>";
            echo "<p>$" . htmlspecialchars($item['price']) . "</p>";

            $sizesSql = "SELECT size, quantity FROM product_sizes WHERE productCode = " . intval($item['productCode']);
            $sizesResult = mysqli_query($con, $sizesSql);

            $isOutOfStock = true;
            $sizeOptions = [];
            while ($sizeRow = mysqli_fetch_assoc($sizesResult)) {
                if ($sizeRow['quantity'] > 0) {
                    $isOutOfStock = false;
                    $sizeOptions[] = ['size' => $sizeRow['size'], 'quantity' => $sizeRow['quantity']];
                }
            }

            if ($isOutOfStock) {
                echo "<p style='color: gray;'>Out of Stock</p>";
            } else {
               
                echo "<form id='addToCartForm-" . $item['productCode'] . "' onsubmit='return addToCart(" . $item['productCode'] . ");'>";
                echo "<input type='hidden' name='productCode' value='" . htmlspecialchars($item['productCode']) . "'>";

                echo "<div style='display: flex; gap: 10px;'>";
                echo "<select name='size' id='size-select-" . $item['productCode'] . "' required onchange='updateQuantityOptions(this)'>";
                echo "<option value=''>Select Size</option>";
                foreach ($sizeOptions as $option) {
                    echo "<option value='" . htmlspecialchars($option['size']) . "' data-quantity='" . $option['quantity'] . "'>" . htmlspecialchars($option['size']) . "</option>";
                }
                echo "</select>";

                echo "<select name='quantity' id='quantity-select-" . $item['productCode'] . "' required>";
                echo "<option value=''>Qty</option>";
                echo "</select>";

                echo "</div>";
                echo "<button type='submit'>Add to Cart</button>";
                echo "</form>";
            }

            echo "</div>";
            echo "<script>const sizeOptions_" . $item['productCode'] . " = " . json_encode($sizeOptions) . ";</script>";
        }
    } else {
        echo "<p>No recommendations found based on your skin tone.</p>";
    }
    ?>

    <h2>Recommended for You Based on Body Shape</h2>
    <?php
    if (!empty($bodyShapeRecommendations)) {
        foreach ($bodyShapeRecommendations as $item) {
            echo "<div class='product-item'>";
            echo "<h2>" . htmlspecialchars($item['productName']) . "</h2>";
            echo "<img class='product-img' src='" . htmlspecialchars($item['source']) . "' alt='" . htmlspecialchars($item['productName']) . "'>";
            echo "<p>$" . htmlspecialchars($item['price']) . "</p>";

            // استعلام للحصول على الأحجام والكمية المتوفرة
            $sizesSql = "SELECT size, quantity FROM product_sizes WHERE productCode = " . intval($item['productCode']);
            $sizesResult = mysqli_query($con, $sizesSql);

            $isOutOfStock = true;
            $sizeOptions = [];
            while ($sizeRow = mysqli_fetch_assoc($sizesResult)) {
                if ($sizeRow['quantity'] > 0) {
                    $isOutOfStock = false;
                    $sizeOptions[] = ['size' => $sizeRow['size'], 'quantity' => $sizeRow['quantity']];
                }
            }

            if ($isOutOfStock) {
                echo "<p style='color: gray;'>Out of Stock</p>";
            } else {
                
                echo "<form id='addToCartForm-" . $item['productCode'] . "' onsubmit='return addToCart(" . $item['productCode'] . ");'>";
                echo "<input type='hidden' name='productCode' value='" . htmlspecialchars($item['productCode']) . "'>";

                echo "<div style='display: flex; gap: 10px;'>";
                echo "<select name='size' id='size-select-" . $item['productCode'] . "' required onchange='updateQuantityOptions(this)'>";
                echo "<option value=''>Select Size</option>";
                foreach ($sizeOptions as $option) {
                    echo "<option value='" . htmlspecialchars($option['size']) . "' data-quantity='" . $option['quantity'] . "'>" . htmlspecialchars($option['size']) . "</option>";
                }
                echo "</select>";

                echo "<select name='quantity' id='quantity-select-" . $item['productCode'] . "' required>";
                echo "<option value=''>Qty</option>";
                echo "</select>";

                echo "</div>";
                echo "<button type='submit'>Add to Cart</button>";
                echo "</form>";
            }

            echo "</div>";
            echo "<script>const sizeOptions_" . $item['productCode'] . " = " . json_encode($sizeOptions) . ";</script>";
        }
    } else {
        echo "<p>No recommendations found based on your body shape.</p>";
    }
    ?>
</div>

</body>
</html>

<script>
function updateQuantityOptions(sizeSelect) {
    const productCode = sizeSelect.id.split('-')[2];
    const quantitySelect = document.getElementById('quantity-select-' + productCode);
    const selectedSizeOption = sizeSelect.options[sizeSelect.selectedIndex];
    const availableQuantity = parseInt(selectedSizeOption.getAttribute('data-quantity'));

    quantitySelect.innerHTML = '';
    for (let i = 1; i <= availableQuantity; i++) {
        const option = document.createElement('option');
        option.value = i;
        option.textContent = i;
        quantitySelect.appendChild(option);
    }
}

function addToCart(productCode) {
    const form = document.getElementById('addToCartForm-' + productCode);
    const formData = new FormData(form);

    fetch('addToCart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert("Product added to cart!");
    })
    .catch(error => {
        console.error('Error:', error);
    });

    return false;
}
</script>

<?php
$con->close();
?>
