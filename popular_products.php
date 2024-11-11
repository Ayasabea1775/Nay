<?php
include 'db_connection.php'; 

function getPopularProducts() {
    $con = mysqli_connect("localhost", "root", "1234", "projectphp");
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit;
    }
    
    $query = "SELECT productName, COUNT(*) as sold FROM orders GROUP BY productName ORDER BY sold DESC LIMIT 10";
    $result = mysqli_query($con, $query);
    
    echo "<table>";
    echo "<tr><th>מוצר</th><th>כמות מכירות</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>{$row['productName']}</td><td>{$row['sold']}</td></tr>";
    }
    echo "</table>";
    
    mysqli_close($con);
}

getPopularProducts();
?>
