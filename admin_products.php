<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "projectphp";

$con = new mysqli($servername, $username, $password, $dbname);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$subcategoryQuery = "SELECT DISTINCT subcategory FROM product WHERE subcategory IS NOT NULL";
$subcategoryResult = mysqli_query($con, $subcategoryQuery);

$categoryQuery = "SELECT DISTINCT category FROM product WHERE category IS NOT NULL";
$categoryResult = mysqli_query($con, $categoryQuery);

$subcategories = [];
$categories = [];
while ($row = mysqli_fetch_assoc($subcategoryResult)) {
    $subcategories[] = $row['subcategory'];
}
while ($row = mysqli_fetch_assoc($categoryResult)) {
    $categories[] = $row['category'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = mysqli_real_escape_string($con, $_POST['productName']);
    $price = (int)mysqli_real_escape_string($con, $_POST['price']);
    $color = mysqli_real_escape_string($con, $_POST['color']);
    $subcategory = mysqli_real_escape_string($con, $_POST['subcategory']);
    $new_subcategory = mysqli_real_escape_string($con, $_POST['new_subcategory']);
    $category = mysqli_real_escape_string($con, $_POST['category']);
    $new_category = mysqli_real_escape_string($con, $_POST['new_category']);
    
    $target_dir = "photo/";
    $imageName = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $imageName;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        die("File is not an image.");
    }

    if ($_FILES["image"]["size"] > 5000000) {
        die("Sorry, your file is too large.");
    }

    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        die("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
    }

    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        die("Sorry, there was an error uploading your file.");
    }

    if (!empty($new_subcategory)) {
        $subcategory = $new_subcategory;
    }
    if (!empty($new_category)) {
        $category = $new_category;
    }

    $sql = "INSERT INTO product (productName, quantity, price, color, source, subcategory, category) 
            VALUES ('$productName', 0, $price, '$color', '$target_file', '$subcategory', '$category')";

    if (mysqli_query($con, $sql)) {
        $productID = mysqli_insert_id($con);

        $totalQuantity = 0;
        if (!empty($_POST['sizes']) && !empty($_POST['quantities'])) {
            $sizes = $_POST['sizes'];
            $quantities = $_POST['quantities'];

            for ($i = 0; $i < count($sizes); $i++) {
                $size = mysqli_real_escape_string($con, $sizes[$i]);
                $quantitySize = (int)mysqli_real_escape_string($con, $quantities[$i]);
                $totalQuantity += $quantitySize;

                $sizeSql = "INSERT INTO product_sizes (productCode, size, quantity) VALUES ($productID, '$size', $quantitySize)";
                if (!mysqli_query($con, $sizeSql)) {
                    echo "Error inserting size: " . mysqli_error($con);
                }
            }

            $updateQuantitySql = "UPDATE product SET quantity = $totalQuantity WHERE productCode = $productID";
            mysqli_query($con, $updateQuantitySql);
        }

        echo "New product added successfully!";
    } else {
        echo "Error: " . mysqli_error($con);
    }
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="phpcss.css">
    <script>
        function toggleNewInput(selectElement, inputId) {
            const newInput = document.getElementById(inputId);
            if (selectElement.value === "other") {
                newInput.classList.add("show");
            } else {
                newInput.classList.remove("show");
            }
        }

        function addSizeEntry() {
            const container = document.getElementById('sizes-container');
            const newEntry = document.createElement('div');
            newEntry.classList.add('size-entry');
            newEntry.innerHTML = `
                <label>Size:</label>
                <input type="text" name="sizes[]" placeholder="Enter size (e.g., S, M, 38)" required>
                <label>Quantity:</label>
                <input type="number" name="quantities[]" min="0" placeholder="Enter quantity" required>
            `;
            container.appendChild(newEntry);
        }
    </script>
</head>
<body>
<?php include 'menu.php'; ?>
<div class="form-container">
    <h2>Add New Product</h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <div>
            <label for="productName">Product Name:</label>
            <input type="text" id="productName" name="productName" placeholder="Enter product name..." required>
        </div>
        <div>
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" placeholder="Enter price..." min="0" required>
        </div>
        <div>
            <label for="color">Color:</label>
            <input type="text" id="color" name="color" placeholder="Enter color..." required>
        </div>
        <div>
            <label for="image">Product Image:</label>
            <input type="file" id="image" name="image" required>
        </div>
        <div>
            <label for="subcategory">Subcategory:</label>
            <select id="subcategory" name="subcategory" onchange="toggleNewInput(this, 'new_subcategory_input')">
                <option value="">--Select Subcategory--</option>
                <?php foreach ($subcategories as $subcategory): ?>
                    <option value="<?php echo htmlspecialchars($subcategory); ?>"><?php echo htmlspecialchars($subcategory); ?></option>
                <?php endforeach; ?>
                <option value="other">Other</option>
            </select>
        </div>
        <div id="new_subcategory_input" class="toggle-input">
            <label for="new_subcategory">New Subcategory:</label>
            <input type="text" id="new_subcategory" name="new_subcategory" placeholder="Enter new subcategory...">
        </div>
        <div>
            <label for="category">Category:</label>
            <select id="category" name="category" onchange="toggleNewInput(this, 'new_category_input')">
                <option value="">--Select Category--</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></option>
                <?php endforeach; ?>
                <option value="other">Other</option>
            </select>
        </div>
        <div id="new_category_input" class="toggle-input">
            <label for="new_category">New Category:</label>
            <input type="text" id="new_category" name="new_category" placeholder="Enter new category...">
        </div>
        
        <div id="sizes-container">
            <h3>Sizes and Quantities</h3>
            <div class="size-entry">
                <label>Size:</label>
                <input type="text" name="sizes[]" placeholder="Enter size (e.g., S, M, 38)" required>
                <label>Quantity:</label>
                <input type="number" name="quantities[]" min="0" placeholder="Enter quantity" required>
            </div>
            <button type="button" onclick="addSizeEntry()">Add Another Size</button>
        </div>

        <button type="submit">Add Product</button>
    </form>
</div>
</body>
</html>
