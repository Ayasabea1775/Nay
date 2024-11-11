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


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_compatibility'])) {
    $color = mysqli_real_escape_string($con, $_POST['color']);
    $matchingColor = mysqli_real_escape_string($con, $_POST['matching_color']);

 
    $checkColorQuery = "SELECT COUNT(*) AS count FROM color_match WHERE color = '$color' AND matching_color = '$matchingColor'";
    $checkColorResult = mysqli_query($con, $checkColorQuery);
    $countRow = mysqli_fetch_assoc($checkColorResult);

    if ($countRow['count'] == 0) {
      
        $insertQuery = "INSERT INTO color_match (color, matching_color) VALUES ('$color', '$matchingColor')";
        if (mysqli_query($con, $insertQuery)) {
            $message = "Color compatibility added successfully!";
        } else {
            $message = "Error: " . mysqli_error($con);
        }
    } else {
        $message = "This color compatibility already exists.";
    }
}


if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($con, $_GET['delete']);
    $deleteQuery = "DELETE FROM color_match WHERE id = '$id'";
    if (mysqli_query($con, $deleteQuery)) {
        $message = "Color compatibility deleted successfully!";
    } else {
        $message = "Error deleting compatibility: " . mysqli_error($con);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_compatibility'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $color = mysqli_real_escape_string($con, $_POST['color']);
    $matchingColor = mysqli_real_escape_string($con, $_POST['matching_color']);

    $updateQuery = "UPDATE color_match SET color = '$color', matching_color = '$matchingColor' WHERE id = '$id'";
    if (mysqli_query($con, $updateQuery)) {
        $message = "Color compatibility updated successfully!";
    } else {
        $message = "Error updating compatibility: " . mysqli_error($con);
    }
}

// معالجة البحث
$searchQuery = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchQuery = mysqli_real_escape_string($con, $_GET['search']);
    $fetchQuery = "SELECT * FROM color_match WHERE color LIKE '%$searchQuery%' OR matching_color LIKE '%$searchQuery%' ORDER BY color ASC";
} else {
    $fetchQuery = "SELECT * FROM color_match ORDER BY color ASC";
}
$fetchResult = mysqli_query($con, $fetchQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Color Compatibility</title>
    <link rel="stylesheet" href="phpcss.css">
</head>
<body>


<?php include 'menu.php'; ?>

<div class="container">
    <h2>Admin - Manage Color Compatibility</h2>

  
    <?php if (isset($message)) { echo "<p>$message</p>"; } ?>

  
    <form method="POST" action="admin_matching_color.php">
        <label for="color">Color:</label>
        <input type="text" id="color" name="color" required>

        <label for="matching_color">Matching Color:</label>
        <input type="text" id="matching_color" name="matching_color" required>

        <button type="submit" name="add_compatibility">Add Compatibility</button>
    </form>

   
    <form method="GET" action="admin_matching_color.php" style="margin-top: 20px;">
        <input type="text" name="search" placeholder="Search for a color..." value="<?php echo htmlspecialchars($searchQuery); ?>">
        <button type="submit">Search</button>
    </form>

    <h3>Current Color Compatibility</h3>
    <table>
        <tr>
            <th>Color</th>
            <th>Matching Color</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($fetchResult)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['color']); ?></td>
                <td><?php echo htmlspecialchars($row['matching_color']); ?></td>
                <td>
                    <a href="admin_matching_color.php?edit=<?php echo $row['id']; ?>">Edit</a> |
                    <a href="admin_matching_color.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this compatibility?');">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>

  
    <?php if (isset($_GET['edit'])): 
        $editId = mysqli_real_escape_string($con, $_GET['edit']);
        $editQuery = "SELECT * FROM color_match WHERE id = '$editId'";
        $editResult = mysqli_query($con, $editQuery);
        $editRow = mysqli_fetch_assoc($editResult);
    ?>
        <h3>Edit Color Compatibility</h3>
        <form method="POST" action="admin_matching_color.php">
            <input type="hidden" name="id" value="<?php echo $editRow['id']; ?>">
            <label for="edit_color">Color:</label>
            <input type="text" id="edit_color" name="color" value="<?php echo htmlspecialchars($editRow['color']); ?>" required>

            <label for="edit_matching_color">Matching Color:</label>
            <input type="text" id="edit_matching_color" name="matching_color" value="<?php echo htmlspecialchars($editRow['matching_color']); ?>" required>

            <button type="submit" name="edit_compatibility">Update Compatibility</button>
        </form>
    <?php endif; ?>

</div>

</body>
</html>

<?php
$con->close();
?>
