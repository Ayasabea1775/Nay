<?php
session_start();
include 'menu.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "projectphp";

$con = new mysqli($servername, $username, $password, $dbname);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$productCode = $_GET['productCode'] ?? '';

$productQuery = "SELECT * FROM product WHERE productCode = '$productCode'";
$productResult = $con->query($productQuery);
$product = $productResult->fetch_assoc();

$complementaryCategories = [
    'top' => ['bottom', 'accessories', 'shoes'],
    'bottom' => ['top', 'accessories', 'shoes'],
    'dress' => ['shoes', 'accessories'],
    'shoes' => ['top', 'bottom', 'dress'],
    'accessories' => ['top', 'bottom', 'dress', 'shoes'],
];

$category = strtolower($product['category']);
$color = strtolower($product['color']);

$matchingCategories = $complementaryCategories[$category] ?? [];
$escapedCategories = array_map([$con, 'real_escape_string'], $matchingCategories);
$categoryList = implode("','", $escapedCategories);

$colorQuery = "SELECT matching_color FROM color_match WHERE color = '$color'";
$colorResult = $con->query($colorQuery);
$matchingColors = [];
while ($row = $colorResult->fetch_assoc()) {
    $matchingColors[] = $row['matching_color'];
}

$escapedColors = array_map([$con, 'real_escape_string'], $matchingColors);
$colorList = implode("','", $escapedColors);

$matchingQuery = "SELECT * FROM product WHERE LOWER(category) IN ('$categoryList') AND LOWER(color) IN ('$colorList') AND productCode != '$productCode'";
$matchingResult = $con->query($matchingQuery);

echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Matching Products</title>";
echo "</head>";
echo "<body>";
echo "<h1>Matching Products for " . htmlspecialchars($product['productName']) . "</h1>";
echo "<div class='products-container'>";
echo "<button onclick='history.back()'>Go Back</button>";

if ($matchingResult->num_rows > 0) {
    while ($row = $matchingResult->fetch_assoc()) {
        echo "<div class='product-item'>";
        echo "<h2>" . htmlspecialchars($row['productName']) . "</h2>";
        $imageSrc = htmlspecialchars($row['source']);
        echo "<img src='" . $imageSrc . "' alt='" . htmlspecialchars($row['productName']) . "' style='width: 100px; height: 100px;'>";

        echo "<p>Price: $" . htmlspecialchars($row['price']) . "</p>";
        
        $sizesSql = "SELECT size, quantity FROM product_sizes WHERE productCode = " . intval($row['productCode']);
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
        } else if (isset($_SESSION['userID'])) {
            echo "<form id='addToCartForm-" . $row['productCode'] . "' onsubmit='return addToCart(" . $row['productCode'] . ");'>";
            echo "<input type='hidden' name='productCode' value='" . htmlspecialchars($row['productCode']) . "'>";

            echo "<div style='display: flex; gap: 10px;'>";
            
            
            echo "<select name='size' id='size-select-" . $row['productCode'] . "' required onchange='updateQuantityOptions(this)'>";
            echo "<option value=''>Select Size</option>";
            foreach ($sizeOptions as $option) {
                echo "<option value='" . htmlspecialchars($option['size']) . "' data-quantity='" . $option['quantity'] . "'>" . htmlspecialchars($option['size']) . "</option>";
            }
            echo "</select>";

          
            echo "<select name='quantity' id='quantity-select-" . $row['productCode'] . "' required>";
            echo "<option value=''>Qty</option>";
            echo "</select>";

            echo "</div>";
            echo "<button type='submit'>Add to Cart</button>";
            echo "</form>";
        } else {
            echo "<button onclick='location.href=\"login.php\"'>Login to add products</button>";
        }

        echo "</div>";
        echo "<script>const sizeOptions_" . $row['productCode'] . " = " . json_encode($sizeOptions) . ";</script>";
    }
} else {
    echo "<p>No matching products found.</p>";
}
echo "</div>";
echo "<div id='popupNotification' style='display: none; position: fixed; top: 20%; left: 50%; transform: translate(-50%, -50%); background: #984a55; color: #fff; padding: 15px 25px; border-radius: 8px; font-weight: bold; z-index: 1000;'>Product added to cart!</div>";
echo "</body>";
echo "</html>";

$con->close();
?>

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

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[id^="size-select-"]').forEach(selectElement => {
        updateQuantityOptions(selectElement);
    });
});

function addToCart(productCode) {
    const form = document.getElementById('addToCartForm-' + productCode);
    const formData = new FormData(form);

    fetch('addToCart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        showPopupNotification();
    })
    .catch(error => {
        console.error('Error:', error);
    });

    return false;
}

function showPopupNotification() {
    const popup = document.getElementById('popupNotification');
    popup.style.display = 'block';
    setTimeout(() => {
        popup.style.display = 'none';
    }, 3000);
}
</script>
