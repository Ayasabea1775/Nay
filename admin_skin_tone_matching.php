<?php
session_start();


if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
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


$productQuery = "SELECT productCode, productName FROM product";
$productResult = mysqli_query($con, $productQuery);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_skin_tone'])) {
    $productCode = mysqli_real_escape_string($con, $_POST['productCode']);
    $skinTone = mysqli_real_escape_string($con, $_POST['skinTone']);
    $currentSkinTone = mysqli_real_escape_string($con, $_POST['currentSkinTone']);

  
    $colorQuery = "SELECT color FROM product WHERE productCode = '$productCode'";
    $colorResult = mysqli_query($con, $colorQuery);
    $colorRow = mysqli_fetch_assoc($colorResult);

    if ($colorRow) {
        $color = $colorRow['color'];

  
        $updateQuery = "
            UPDATE color_match_skin 
            SET skin_tone = '$skinTone' 
            WHERE product_color = '$color' AND skin_tone = '$currentSkinTone'
        ";
        mysqli_query($con, $updateQuery);
    }

    header("Location: admin_skin_tone_matching.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['productCode'])) {
    $productCode = mysqli_real_escape_string($con, $_GET['productCode']);

    $response = ['color' => null, 'skin_tones' => []];

 
    $productQuery = "SELECT color FROM product WHERE productCode = '$productCode'";
    $productResult = mysqli_query($con, $productQuery);
    $product = mysqli_fetch_assoc($productResult);

    if ($product) {
        $response['color'] = $product['color'];

      
        $colorMatchQuery = "SELECT skin_tone FROM color_match_skin WHERE product_color = '{$product['color']}'";
        $colorMatchResult = mysqli_query($con, $colorMatchQuery);

        while ($colorMatch = mysqli_fetch_assoc($colorMatchResult)) {
            $response['skin_tones'][] = $colorMatch['skin_tone'];
        }
    }

    echo json_encode($response);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Match Products to Skin Tones</title>
    <link rel="stylesheet" href="phpcss.css"> 
    <script>
        function checkSkinTone() {
            var productCode = document.getElementById("productCode").value;
            if (productCode) {
                fetch(`admin_skin_tone_matching.php?productCode=${productCode}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById("productColor").innerText = data.color ? data.color : "No color found";
                        if (data.skin_tones.length > 0) {
                            document.getElementById("skinToneResult").innerText = "Suitable skin tones: " + data.skin_tones.join(", ");
                            document.getElementById("editSkinToneForm").style.display = "block"; // إظهار نموذج التعديل
                            document.getElementById("addSkinTone").style.display = "none";
                            document.getElementById("hiddenProductCode").value = productCode;
                            document.getElementById("currentSkinTone").value = data.skin_tones[0]; // اختيار أول لون بشرة للتعديل
                        } else {
                            document.getElementById("skinToneResult").innerText = "No matching skin tone found. Please add one below.";
                            document.getElementById("addSkinTone").style.display = "block";
                            document.getElementById("editSkinToneForm").style.display = "none"; // إخفاء نموذج التعديل
                            document.getElementById("hiddenProductCode").value = productCode;
                        }
                    })
                    .catch(error => console.error("Error:", error));
            }
        }
    </script>
</head>
<body>


<?php include 'menu.php'; ?> 

<div class="container">
    <h2>Admin - Match Products to Skin Tones</h2>


    <div style="text-align: center; margin-bottom: 20px;">
        <img src="/labs/project/photo/color.png" alt="Color Image" style="width: 300px; height: auto; border: 1px solid #ddd;">
    </div>

    
    <select name="productCode" id="productCode" required>
        <option value="">Select a product</option>
        <?php while ($product = mysqli_fetch_assoc($productResult)) { ?>
            <option value="<?php echo $product['productCode']; ?>">
                <?php echo htmlspecialchars($product['productName']); ?>
            </option>
        <?php } ?>
    </select>

    <button onclick="checkSkinTone()">Check</button>

    <div class="result-section">
        <p><strong>Product Color:</strong> <span id="productColor"></span></p>
        <p id="skinToneResult"></p>
    </div>

   
    <div class="edit-section" id="editSkinToneForm" style="display: none;">
        <form method="POST" action="admin_skin_tone_matching.php">
            <input type="hidden" name="productCode" id="hiddenProductCode">
            <input type="hidden" name="currentSkinTone" id="currentSkinTone">
            <input type="hidden" name="update_skin_tone" value="1">
            <label for="skinTone">Update Skin Tone:</label>
            <select name="skinTone" id="skinTone" required>
                <option value="Light">Light</option>
                <option value="Medium">Medium</option>
                <option value="Dark">Dark</option>
            </select>
            <button type="submit">Update Skin Tone</button>
        </form>
    </div>

  
    <div class="add-section" id="addSkinTone" style="display: none;">
        <form method="POST" action="admin_skin_tone_matching.php">
            <input type="hidden" name="productCode" id="hiddenProductCode">
            <input type="hidden" name="add_skin_tone" value="1">
            <label for="skinTone">Select Skin Tone:</label>
            <select name="skinTone" id="skinTone" required>
                <option value="Light">Light</option>
                <option value="Medium">Medium</option>
                <option value="Dark">Dark</option>
            </select>
            <button type="submit">Add Skin Tone</button>
        </form>
    </div>
</div>

</body>
</html>

<?php
$con->close();
?>
