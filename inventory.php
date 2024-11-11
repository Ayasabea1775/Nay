<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include "menu.php";
require 'functions.php'; 

$con = mysqli_connect("localhost", "root", "1234", "projectphp");

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}


$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($con, $_GET['search']);
}

$query = "SELECT productName, quantity, price, color, subcategory, productCode FROM product WHERE productName LIKE '%$search%' OR color LIKE '%$search%'";
$result = mysqli_query($con, $query);
if (!$result) {
    die("Error executing query: " . mysqli_error($con));
}

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    
    $sizesQuery = "SELECT DISTINCT size, quantity FROM product_sizes WHERE productCode = '" . $row['productCode'] . "'";
    $sizesResult = mysqli_query($con, $sizesQuery);

    $sizes = [];
    while ($sizeRow = mysqli_fetch_assoc($sizesResult)) {
        $sizes[] = $sizeRow;
    }

    $row['sizes'] = $sizes;
    $products[] = $row;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST['sizes'] as $productCode => $sizeData) {
        $totalQuantity = 0; 

        foreach ($sizeData as $size => $quantity) {
           
            if ($quantity < 0) {
                echo "<p style='color: red;'>Error: Quantity for size $size of product $productCode cannot be negative.</p>";
                continue; 
            }

          
            $updateQuery = "UPDATE product_sizes SET quantity = ? WHERE productCode = ? AND size = ?";
            $stmt = $con->prepare($updateQuery);
            $stmt->bind_param("iss", $quantity, $productCode, $size);
            $stmt->execute();

            $totalQuantity += $quantity; 
        }

      
        if ($totalQuantity >= 0) {
            $updateProductQuery = "UPDATE product SET quantity = ? WHERE productCode = ?";
            $stmtProduct = $con->prepare($updateProductQuery);
            $stmtProduct->bind_param("is", $totalQuantity, $productCode);
            $stmtProduct->execute();
        }
    }
   
    header("Location: inventory.php"); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="phpcss.css">
    <title>Inventory</title>
    <style>
        h2 {
            margin-top: 20px;
            text-align: center;
            color: #A95C68;
        }
        .inventory-table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }
        .inventory-table th, .inventory-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .inventory-table th {
            background-color: #C9A9A6; 
            color: #2c3e50;
        }
        .inventory-table tr:nth-child(even) {
            background-color: #F3CFC6;
        }
        .size-input {
            display: flex;
            align-items: center; 
            margin-bottom: 10px; 
        }
        .size-input input {
            width: 50px; 
            padding: 5px; 
            margin-right: 10px; 
        }
        .size-input button {
            padding: 5px; 
            background-color: #A95C68; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            transition: background-color 0.3s; 
        }
        .size-input button:hover {
            background-color: #673147; 
        }
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Inventory</h2>
        
        <!-- شريط البحث -->
        <div class="search-bar">
            <form method="GET" action="inventory.php">
                <input type="text" name="search" placeholder="Search by product name or color" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <form method="POST" action="">
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Sizes</th>
                        <th>Price</th>
                        <th>Color</th>
                        <th>Category</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['productName']); ?></td>
                        <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                        <td>
                            <?php if (!empty($product['sizes'])): ?>
                                <?php foreach ($product['sizes'] as $size): ?>
                                    <div class="size-input">
                                        <label><?php echo htmlspecialchars($size['size']); ?></label>
                                        <input type="number" name="sizes[<?php echo $product['productCode']; ?>][<?php echo htmlspecialchars($size['size']); ?>]" value="<?php echo htmlspecialchars($size['quantity']); ?>" min="0">
                                        <button type="submit">Update</button> 
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($product['price']); ?></td>
                        <td><?php echo htmlspecialchars($product['color']); ?></td>
                        <td><?php echo htmlspecialchars($product['subcategory']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
    </div>
</body>
</html>

<?php
$con->close();
?>
