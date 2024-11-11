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

$productCode = intval($_GET['edit']);
$productQuery = "SELECT * FROM product WHERE productCode = $productCode";
$productResult = mysqli_query($con, $productQuery);
$product = mysqli_fetch_assoc($productResult);

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
    
    $target_file = $product['source']; 
    if (!empty($_FILES['image']['name'])) {
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
    }

    if (!empty($new_subcategory)) {
        $subcategory = $new_subcategory;
    }
    if (!empty($new_category)) {
        $category = $new_category;
    }

    $updateSql = "UPDATE product SET 
                    productName = '$productName', 
                    price = $price, 
                    color = '$color', 
                    source = '$target_file', 
                    subcategory = '$subcategory', 
                    category = '$category' 
                  WHERE productCode = $productCode";

    if (mysqli_query($con, $updateSql)) {
        header("Location: view_products.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="phpcss.css">
</head>
<body>
<?php include 'menu.php'; ?>
<div class="container">
    <h2>Edit Product</h2>
    <form method="POST" enctype="multipart/form-data">
        <div>
            <label>Product Name:</label>
            <input type="text" name="productName" value="<?php echo htmlspecialchars($product['productName']); ?>" required>
        </div>
        <div>
            <label>Price:</label>
            <input type="number" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
        </div>
        <div>
            <label>Color:</label>
            <input type="text" name="color" value="<?php echo htmlspecialchars($product['color']); ?>" required>
        </div>
        <div>
            <label>Category:</label>
            <select name="category" onchange="toggleNewCategoryInput(this)">
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php if ($product['category'] == $cat) echo 'selected'; ?>><?php echo htmlspecialchars($cat); ?></option>
                <?php endforeach; ?>
                <option value="other">Other</option>
            </select>
            <input type="text" name="new_category" id="new_category" placeholder="Enter new category" style="display:none;">
        </div>
        <div>
            <label>Subcategory:</label>
            <select name="subcategory" onchange="toggleNewSubcategoryInput(this)">
                <?php foreach ($subcategories as $subcat): ?>
                    <option value="<?php echo htmlspecialchars($subcat); ?>" <?php if ($product['subcategory'] == $subcat) echo 'selected'; ?>><?php echo htmlspecialchars($subcat); ?></option>
                <?php endforeach; ?>
                <option value="other">Other</option>
            </select>
            <input type="text" name="new_subcategory" id="new_subcategory" placeholder="Enter new subcategory" style="display:none;">
        </div>
        <div>
            <label>Product Image:</label>
            <input type="file" name="image" accept="image/*">
            <p>Current Image: <img src="<?php echo htmlspecialchars($product['source']); ?>" alt="Product Image" style="width:50px;height:50px;"></p>
        </div>
        <button type="submit">Update Product</button>
    </form>
</div>

<script>
function toggleNewCategoryInput(selectElement) {
    const newCategoryInput = document.getElementById('new_category');
    if (selectElement.value === 'other') {
        newCategoryInput.style.display = 'block';
    } else {
        newCategoryInput.style.display = 'none';
    }
}

function toggleNewSubcategoryInput(selectElement) {
    const newSubcategoryInput = document.getElementById('new_subcategory');
    if (selectElement.value === 'other') {
        newSubcategoryInput.style.display = 'block';
    } else {
        newSubcategoryInput.style.display = 'none';
    }
}
</script>
</body>
</html>

<?php mysqli_close($con); ?>
