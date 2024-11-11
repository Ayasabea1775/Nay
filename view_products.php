\<?php
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


if (isset($_GET['remove'])) {
    $productCode = intval($_GET['remove']);
    $removeSql = "DELETE FROM product WHERE productCode = $productCode";
    if (mysqli_query($con, $removeSql)) {
        echo "<script>alert('Product removed successfully'); window.location.href='view_products.php';</script>";
    } else {
        echo "<script>alert('Error removing product');</script>";
    }
}


$searchQuery = "";
if (isset($_GET['search'])) {
    $searchQuery = mysqli_real_escape_string($con, $_GET['search']);
}


$productQuery = "SELECT * FROM product WHERE productName LIKE '%$searchQuery%' OR color LIKE '%$searchQuery%'";
$productResult = mysqli_query($con, $productQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products</title>
    <link rel="stylesheet" href="phpcss.css">
    <style>
        .search-bar {
            text-align: center;
            margin-bottom: 20px;
        }
        .search-bar input[type="text"] {
            padding: 8px;
            width: 250px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 10px;
        }
        .search-bar button {
            padding: 8px 16px;
            font-size: 16px;
            background-color: #A95C68;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .search-bar button:hover {
            background-color: #673147;
        }
        .product-table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }
        .product-table th, .product-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .product-table th {
            background-color: #C9A9A6; 
            color: #2c3e50;
        }
        .product-table tr:nth-child(even) {
            background-color: #F3CFC6;
        }
        .action-buttons a {
            margin: 0 5px;
        }
    </style>
</head>
<body>
<?php include 'menu.php'; ?>

<div class="container">
    <h2>All Products</h2>

   
    <div class="search-bar">
        <form method="GET" action="view_products.php">
            <input type="text" name="search" placeholder="Search by product name or color" value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <table class="product-table">
        <thead>
            <tr>
                <th>Product Code</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Color</th>
                <th>Subcategory</th>
                <th>Category</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($productResult) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($productResult)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['productCode']); ?></td>
                        <td><?php echo htmlspecialchars($row['productName']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['price']); ?></td>
                        <td><?php echo htmlspecialchars($row['color']); ?></td>
                        <td><?php echo htmlspecialchars($row['subcategory']); ?></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><img src="<?php echo htmlspecialchars($row['source']); ?>" alt="Product Image" style="width:50px;height:50px;"></td>
                        <td class="action-buttons">
                            <a href="edit_product.php?edit=<?php echo $row['productCode']; ?>" class="edit-button">Edit</a>
                            <a href="view_products.php?remove=<?php echo $row['productCode']; ?>" class="remove-button" onclick="return confirm('Are you sure you want to remove this product?');">Remove</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">No products found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php mysqli_close($con); ?>
