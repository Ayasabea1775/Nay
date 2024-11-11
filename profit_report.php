<?php
include 'db_connection.php'; 

function getProfitReport() {
    $con = mysqli_connect("localhost", "root", "1234", "projectphp");
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit;
    }
    
    $query = "SELECT productName, SUM(price * quantity) as totalProfit FROM orders GROUP BY productName ORDER BY totalProfit DESC";
    $result = mysqli_query($con, $query);
    
    echo "<table>";
    echo "<tr><th>מוצר</th><th>רווח כולל</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>{$row['productName']}</td><td>{$row['totalProfit']}</td></tr>";
    }
    echo "</table>";
    
    mysqli_close($con);
}

getProfitReport();
?>
