<?php


function recommend_clothes($user_id, $conn) {
    
    $userQuery = "SELECT skin_tone FROM users WHERE IDuser = $user_id";
    if (!$userResult = mysqli_query($conn, $userQuery)) {
        die('Error fetching user details: ' . mysqli_error($conn));
    }

    $user = mysqli_fetch_assoc($userResult);
    $skin_tone = $user['skin_tone'];

    
    $colorQuery = "
        SELECT DISTINCT product.productCode, product.productName, product.quantity, product.price, product.color, product.source, product.subcategory, product.category 
        FROM product
        JOIN color_match_skin ON product.color = color_match_skin.product_color
        WHERE color_match_skin.skin_tone = '$skin_tone'
    ";

    if (!$result = mysqli_query($conn, $colorQuery)) {
        die('Error fetching recommendations: ' . mysqli_error($conn));
    }

    $recommendations = [];
    $seenProducts = []; 

    while ($row = mysqli_fetch_assoc($result)) {
        if (!in_array($row['productCode'], $seenProducts)) {
            $recommendations[] = $row;
            $seenProducts[] = $row['productCode']; 
        }
    }

    return $recommendations;
}


function recommend_clothes_by_body_shape($user_id, $conn) {
   
    $userQuery = "SELECT body_shape FROM users WHERE IDuser = $user_id";
    if (!$userResult = mysqli_query($conn, $userQuery)) {
        die('Error fetching user details: ' . mysqli_error($conn));
    }

    $user = mysqli_fetch_assoc($userResult);
    $user_body_shape = $user['body_shape'];

   
    $shapeQuery = "SELECT shape_id FROM body_shapes WHERE shape_id = $user_body_shape";
    if (!$shapeResult = mysqli_query($conn, $shapeQuery)) {
        die('Error fetching body shape details: ' . mysqli_error($conn));
    }

    $shapeRow = mysqli_fetch_assoc($shapeResult);
    $shape_id = $shapeRow['shape_id'];

    $featureQuery = "
        SELECT feature_name, feature_value 
        FROM compatibility_rules 
        WHERE shape_id = $shape_id
    ";
    if (!$featureResult = mysqli_query($conn, $featureQuery)) {
        die('Error fetching compatibility rules: ' . mysqli_error($conn));
    }

    $compatibleFeatures = [];
    while ($feature = mysqli_fetch_assoc($featureResult)) {
        $compatibleFeatures[] = $feature;
    }

   
    $productRecommendations = [];
    $seenProducts = []; 

    foreach ($compatibleFeatures as $feature) {
        $featureName = mysqli_real_escape_string($conn, $feature['feature_name']);
        $featureValue = mysqli_real_escape_string($conn, $feature['feature_value']);

        $productQuery = "
            SELECT DISTINCT product.productCode, product.productName, product.quantity, product.price, product.color, product.source, product.subcategory, product.category 
            FROM product
            JOIN product_features ON product.productCode = product_features.productCode
            WHERE product_features.feature_name = '$featureName' AND product_features.feature_value = '$featureValue'
        ";
        
        $productResult = mysqli_query($conn, $productQuery);
        if (!$productResult) {
            die('Error fetching products: ' . mysqli_error($conn));
        }

        while ($product = mysqli_fetch_assoc($productResult)) {
            if (!in_array($product['productCode'], $seenProducts)) {
                $productRecommendations[] = $product;
                $seenProducts[] = $product['productCode'];
            }
        }
    }

    return $productRecommendations;
}
?>
