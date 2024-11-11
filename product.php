<?php
session_start();
include 'menu.php';

$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "projectphp";

$con = new mysqli($servername, $username, $password, $dbname);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}



echo '<div class="container" style="display: flex;">';

echo '<div class="sidebar" style="width: 200px; padding: 20px; background-color: #F3CFC6; border-radius: 8px; margin-right: 20px;">';
echo '<h3>Categories</h3>';
echo '<ul style="list-style-type: none; padding: 0;">';
echo '<li><a href="product.php">All</a></li>';

$categoryQuery = "SELECT DISTINCT category, subcategory FROM product ORDER BY category, subcategory";
$categoryResult = mysqli_query($con, $categoryQuery);

$categories = [];
while ($row = mysqli_fetch_assoc($categoryResult)) {
    if (!empty($row['subcategory'])) {
        $categories[$row['category']][] = $row['subcategory'];
    }
}

foreach ($categories as $category => $subcategories) {
    foreach ($subcategories as $subcategory) {
        echo '<li style="margin-bottom: 5px;"><a href="product.php?subcategory=' . urlencode($subcategory) . '">' . ucwords(htmlspecialchars($subcategory)) . '</a></li>';
    }
}

echo '</ul>';
echo '</div>';

echo '<div class="content" style="flex-grow: 1; padding: 20px;">';
echo '<div style="display: flex; justify-content: flex-start; align-items: center; margin-bottom: 20px;">';
echo '<form action="product.php" method="GET" style="margin-right: 10px;">';
echo '<input type="text" name="search_query" placeholder="Search for a product...">';
echo '<button type="submit">Search</button>';
echo '</form>';

if (isset($_SESSION['userID'])) {
    echo '<form method="POST" action="recommendations.php">';
    echo '<button type="submit" name="recommend">Show clothes suitable for your skin tone & body shape</button>';
    echo '</form>';
} else {
    echo '<button onclick="location.href=\'login.php\'">Show clothes suitable for your skin tone</button>';
}
echo '</div>';

echo '<div class="product-list" style="display: flex; flex-wrap: wrap; gap: 20px;">';

$category = isset($_GET['category']) ? $_GET['category'] : '';
$subcategory = isset($_GET['subcategory']) ? $_GET['subcategory'] : '';
$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';

$sql = "SELECT * FROM product WHERE 1=1";
if ($category) {
    $sql .= " AND category = '". mysqli_real_escape_string($con, $category) ."'";
}
if ($subcategory) {
    $sql .= " AND subcategory = '". mysqli_real_escape_string($con, $subcategory) ."'";
}
if ($search_query) {
    $sql .= " AND productName LIKE '%". mysqli_real_escape_string($con, $search_query) ."%'"; 
}
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        displayProduct($row, $con);
    }
} else {
    echo "<p>No products found.</p>";
}

echo '</div>'; 
echo '</div>'; 
echo '</div>'; 

function displayProduct($row, $con) {
    echo "<div class='product-item' style='width: 22%; box-sizing: border-box; text-align: center;'>";

    echo "<a href='productDetails.php?productCode=" . htmlspecialchars($row['productCode']) . "'>";
    echo "<img class='product-img' src='" . htmlspecialchars($row['source']) . "' alt='" . htmlspecialchars($row['productName']) . "' style='width: 100%; height: 200px; object-fit: cover; margin-bottom: 10px;'>";
    echo "</a>";
    
    echo "<h2 style='font-size: 16px; color: #333;'>" . htmlspecialchars($row['productName']) . "</h2>";
    echo "<p style='color: #A95C68;'>$" . htmlspecialchars($row['price']) . "</p>";
    
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
        echo "<input type='hidden' name='productCode' value='" . $row['productCode'] . "'>";
        
        echo "<div style='display: flex; flex-direction: column; align-items: center; margin-bottom: 10px;'>";

        echo "<select name='size' id='size-select-" . $row['productCode'] . "' required style='width: 80%; margin-bottom: 5px;' onchange='updateQuantityOptions(this)'>";
        foreach ($sizeOptions as $option) {
            echo "<option value='" . htmlspecialchars($option['size']) . "' data-quantity='" . $option['quantity'] . "'>" . htmlspecialchars($option['size']) . "</option>";
        }
        echo "</select>";

        echo "<select name='quantity' id='quantity-select-" . $row['productCode'] . "' required style='width: 80%; margin-bottom: 5px;'></select>";

        echo "</div>";

        echo "<script>const sizeOptions_" . $row['productCode'] . " = " . json_encode($sizeOptions) . ";</script>";

        echo "<button type='submit' style='width: 80%; margin-bottom: 10px;'>Add to Cart</button>";
        echo "</form>";

        echo "<button onclick='showMatchingProducts(\"" . $row['productCode'] . "\")' style='width: 80%;'>See Matching Products</button>";
    } else {
        echo "<button onclick='location.href=\"login.php\"'>Login to add products</button>";
    }

    echo "</div>";
}
?>

<div id="popupNotification" style="display: none; position: fixed; top: 20%; left: 50%; transform: translate(-50%, -50%); background: #984a55; color: #fff; padding: 15px 25px; border-radius: 8px; font-weight: bold; z-index: 1000;">Product added to cart!</div>

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

function showMatchingProducts(productCode) {
    if (confirm('Would you like to see matching products for this item?')) {
        window.location.href = 'matching_products.php?productCode=' + productCode;
    }
}
</script>
