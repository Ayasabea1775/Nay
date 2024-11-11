<?php

$con = mysqli_connect("localhost", "root", "1234", "projectphp");


if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit;
}


if (isset($_GET['search_query'])) {
    $search_query = $_GET['search_query'];
    $search_query = mysqli_real_escape_string($con, $search_query);  

 
    $sql = "SELECT * FROM product WHERE productName LIKE '%$search_query%'";
    $result = mysqli_query($con, $sql);

   
    if (mysqli_num_rows($result) > 0) {
        echo "<div class='products'>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='product'>";
            echo "<p>" . htmlspecialchars($row['productName']) . "</p>"; 
            echo "<img src='" . htmlspecialchars($row['source']) . "' alt='" . htmlspecialchars($row['productName']) . "' style='width:100px; height:100px;'>"; // Product image
            echo "<p>$" . htmlspecialchars($row['price']) . "</p>"; 
            echo "<button onclick='location.href=\"addToCart.php?productCode=" . $row['productCode'] . "\"'>Add to Cart</button>"; // Add to Cart button
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "No results found.";
    }
}

mysqli_close($con);
?>
