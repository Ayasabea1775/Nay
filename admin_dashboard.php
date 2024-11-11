<?php
session_start();
include "menu.php";
require 'functions.php';

$con = mysqli_connect("localhost", "root", "1234", "projectphp");
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit;
}

if (!isAdmin()) {
    header('Location: admin_dashboard.php');
    exit;
}


function getPopularProducts($con) {
    $query = "SELECT p.productName, COUNT(*) as sold_count 
              FROM order_items oi 
              JOIN product p ON oi.productCode = p.productCode 
              GROUP BY p.productName 
              ORDER BY sold_count DESC 
              LIMIT 5";
    $result = mysqli_query($con, $query);
    if (!$result) {
        die("Error executing query: " . mysqli_error($con));
    }
    return $result;
}


function getLowInventoryProducts($con) {
    $query = "SELECT p.productName, p.productCode, ps.size, ps.quantity 
              FROM product p
              JOIN product_sizes ps ON p.productCode = ps.productCode
              WHERE ps.quantity <= 3
              ORDER BY p.productName, ps.size ASC";
    $result = mysqli_query($con, $query);
    if (!$result) {
        die("Error executing query: " . mysqli_error($con));
    }
    return $result;
}


function getSalesSummary($con) {
    $query = "SELECT SUM(totalPrice) as total_sales, COUNT(*) as total_orders 
              FROM orders 
              WHERE orderDate >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
    $result = mysqli_query($con, $query);
    if (!$result) {
        die("Error executing query: " . mysqli_error($con));
    }
    return mysqli_fetch_assoc($result);
}

$popularProductsResult = getPopularProducts($con);
$lowInventoryProductsResult = getLowInventoryProducts($con);
$salesSummary = getSalesSummary($con);

// إعداد بيانات المنتجات الأكثر مبيعًا للرسم البياني
$popularProducts = [];
$soldCounts = [];
while ($row = mysqli_fetch_assoc($popularProductsResult)) {
    $popularProducts[] = $row['productName'];
    $soldCounts[] = $row['sold_count'];
}

?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="phpcss.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    h1, h2 {
        font-family: 'Cinzel', serif;
        color: #A95C68;
    }
    h1 { font-size: 2.2em; margin-bottom: 20px; }
    h2 { font-size: 1.5em; margin-bottom: 10px; }
    .summary, .alert {
        background-color: #F4C7C3;
        color: #FFF5EE;
        margin: 20px auto;
        border-radius: 10px;
        padding: 20px;
        border: 1px solid #E9B7B7;
        width: 80%;
    }
    .alert p {
        font-family: 'Cinzel', serif;
        color: #A95C68;
        text-align: center;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    table, th, td {
        border: 1px solid #E9B7B7;
    }
    th {
        background-color: #A95C68;
        color: #FFF5EE;
        font-size: 1.1em;
    }
    td {
        background-color: #F8E0E0;
        color: #5E3C3C; 
        font-size: 1.1em;
        padding: 10px;
        text-align: center;
    }
</style>


    
</head>
<body>
    <div class="container">
        <h1>Welcome, Admin</h1>

        <div class="summary">
            <h2>Sales Summary</h2>
            <p>Total Sales (Last Month): $<?php echo isset($salesSummary['total_sales']) ? $salesSummary['total_sales'] : '0'; ?></p>
            <p>Total Orders (Last Month): <?php echo isset($salesSummary['total_orders']) ? $salesSummary['total_orders'] : '0'; ?></p>
        </div>

        <h2>Popular Products</h2>
        <div class="chart-container">
            <canvas id="popularProductsChart"></canvas>
        </div>

        <?php if (mysqli_num_rows($lowInventoryProductsResult) > 0): ?>
            <div class="alert">
                <p>The following products have low inventory and need to be reordered:</p>
                <table>
                    <tr>
                        <th>Product Name</th>
                        <th> Code</th>
                        <th>Size</th>
                        <th>Quantity</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($lowInventoryProductsResult)): ?>
                        <tr>
                            <td><?php echo $row['productName']; ?></td>
                            <td><?php echo $row['productCode']; ?></td>
                            <td><?php echo $row['size']; ?></td>
                            <td><?php echo $row['quantity']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        <?php else: ?>
            <p>All products are sufficiently stocked.</p>
        <?php endif; ?>
    </div>

    <script>
        const ctx = document.getElementById('popularProductsChart').getContext('2d');
        const popularProductsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($popularProducts); ?>,
                datasets: [{
                    label: 'Sold Count',
                    data: <?php echo json_encode($soldCounts); ?>,
                    backgroundColor: 'rgba(233, 183, 183, 0.6)',
                    borderColor: 'rgba(169, 92, 104, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>
