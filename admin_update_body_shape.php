<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'menu.php';

$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "projectphp";

$con = new mysqli($servername, $username, $password, $dbname);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['productCode']) && isset($_POST['feature_name']) && isset($_POST['feature_value'])) {
    $productCode = mysqli_real_escape_string($con, $_POST['productCode']);
    $feature_name = mysqli_real_escape_string($con, $_POST['feature_name']);
    $feature_value = mysqli_real_escape_string($con, $_POST['feature_value']);

    $checkQuery = "SELECT * FROM product_features WHERE productCode = '$productCode' AND feature_name = '$feature_name'";
    $checkResult = mysqli_query($con, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $updateQuery = "
            UPDATE product_features 
            SET feature_value = '$feature_value' 
            WHERE productCode = '$productCode' AND feature_name = '$feature_name'
        ";
        
        if (mysqli_query($con, $updateQuery)) {
            echo "<p style='color: #007bff;'>Product feature updated successfully.</p>";
        } else {
            echo "<p style='color: red;'>Error updating product feature: " . mysqli_error($con) . "</p>";
        }
    } else {
        $insertQuery = "
            INSERT INTO product_features (productCode, feature_name, feature_value) 
            VALUES ('$productCode', '$feature_name', '$feature_value')
        ";
        
        if (mysqli_query($con, $insertQuery)) {
            echo "<p style='color: #007bff;'>Product feature added successfully.</p>";
        } else {
            echo "<p style='color: red;'>Error adding product feature: " . mysqli_error($con) . "</p>";
        }
    }
}

$productQuery = "SELECT productCode, productName, subcategory FROM product";
$productResult = mysqli_query($con, $productQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="phpcss.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Update Body Shape Features</title>
    <style>
        .container {
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        .container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
    <script>
        function updateFeatureOptions() {
            const featureNameSelect = document.getElementById("feature_name");
            const productSelect = document.getElementById("productCode");
            const selectedProduct = productSelect.options[productSelect.selectedIndex];
            const subcategory = selectedProduct.getAttribute("data-subcategory");

            featureNameSelect.innerHTML = "";

            let options = [];

            if (subcategory === "jackets") {
                options = ["Fit", "Length", "Material"];
            } else if (subcategory === "pants") {
                options = ["Length", "Pattern", "Fit"];
            } else if (subcategory === "dresses") {
                options = ["Neckline", "Length", "Fit"];
            } else if (subcategory === "tops") {
                options = ["Straps", "Pattern", "Neckline"];
            } else if (subcategory === "shoes") {
                options = ["Size", "Color", "Material"];
            } else if (subcategory === "Accessories") {
                options = ["Material", "Color", "Size"];
            }

            options.forEach(option => {
                const opt = document.createElement("option");
                opt.value = option;
                opt.textContent = option;
                featureNameSelect.appendChild(opt);
            });

            updateFeatureValueOptions();
        }

        function updateFeatureValueOptions() {
            const featureValueSelect = document.getElementById("feature_value");
            const featureNameSelect = document.getElementById("feature_name");
            const selectedFeature = featureNameSelect.value;

            featureValueSelect.innerHTML = "";

            let values = [];

            if (selectedFeature === "Fit") {
                values = ["Bodycon", "Slim", "Loose", "Tailored"];
            } else if (selectedFeature === "Length") {
                values = ["Short", "Midi", "Knee-Length", "Long"];
            } else if (selectedFeature === "Material") {
                values = ["Cotton", "Leather", "Denim", "Polyester"];
            } else if (selectedFeature === "Pattern") {
                values = ["Solid", "Striped", "Plaid", "Zebra Print"];
            } else if (selectedFeature === "Neckline") {
                values = ["High Neck", "Collared", "V-Neck", "Strapless"];
            } else if (selectedFeature === "Straps") {
                values = ["Thin", "Wide", "Adjustable"];
            } else if (selectedFeature === "Size") {
                values = ["Small", "Medium", "Large", "Extra Large"];
            } else if (selectedFeature === "Color") {
                values = ["Red", "Blue", "Black", "White", "Green"];
            }

            values.forEach(value => {
                const opt = document.createElement("option");
                opt.value = value;
                opt.textContent = value;
                featureValueSelect.appendChild(opt);
            });
        }
    </script>
</head>
<body>

<div class="container">
    <h2>Update Product Features for Body Shape</h2>
    <form method="POST" action="admin_update_body_shape.php">
        <label for="productCode">Select Product:</label>
        <select name="productCode" id="productCode" onchange="updateFeatureOptions()" required>
            <option value="">Select Product</option>
            <?php while ($product = mysqli_fetch_assoc($productResult)): ?>
                <option value="<?= htmlspecialchars($product['productCode']) ?>" data-subcategory="<?= htmlspecialchars($product['subcategory']) ?>">
                    <?= htmlspecialchars($product['productName']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="feature_name">Feature Name:</label>
        <select name="feature_name" id="feature_name" onchange="updateFeatureValueOptions()" required>
        </select>

        <label for="feature_value">Feature Value:</label>
        <select name="feature_value" id="feature_value" required>
        </select>

        <button type="submit">Update Feature</button>
    </form>
</div>

</body>
</html>

<?php
$con->close();
?>
