<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="phpcss.css">
    <title>User Management</title>
</head>
<body>
    <?php
    session_start();
    include 'menu.php';

    if ($_SESSION['role'] !== 'admin') {
        header("Location: login.php");
        exit;
    }

    $con = mysqli_connect("localhost", "root", "1234", "projectphp");
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit;
    }

  
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
        $userID = mysqli_real_escape_string($con, $_POST['userID']);
        $newRole = mysqli_real_escape_string($con, $_POST['role']);

        $updateQuery = "UPDATE users SET role = '$newRole' WHERE IDuser = '$userID'";
        mysqli_query($con, $updateQuery);
    }

   
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
        $userID = mysqli_real_escape_string($con, $_POST['userID']);

        $deleteQuery = "DELETE FROM users WHERE IDuser = '$userID'";
        mysqli_query($con, $deleteQuery);
    }

  
    $search_query = '';
    if (isset($_GET['search'])) {
        $search_query = mysqli_real_escape_string($con, $_GET['search']);
    }

    $query = "SELECT IDuser, fname, lname, mail, role FROM users WHERE username LIKE '%$search_query%' OR fname LIKE '%$search_query%' OR lname LIKE '%$search_query%' OR IDuser LIKE '%$search_query%' OR mail LIKE '%$search_query%'";
    $result = mysqli_query($con, $query);
    ?>

    <div style="text-align : center">
        <div class="header-container">
            <h2>User Management</h2>

            <form method="GET" action="admin_users.php" class="search-form">
                <input type="text" name="search" placeholder="Search users..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit">Search</button>
            </form>
            
        </div>

      
        <div class="header-container">
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['IDuser']); ?></td>
                            <td><?php echo htmlspecialchars($row['fname']); ?></td>
                            <td><?php echo htmlspecialchars($row['lname']); ?></td>
                            <td><?php echo htmlspecialchars($row['mail']); ?></td>
                            <td>
                                <form method="POST" action="admin_users.php">
                                    <input type="hidden" name="userID" value="<?php echo $row['IDuser']; ?>">
                                    <select name="role">
                                        <option value="user" <?php if ($row['role'] == 'user') echo 'selected'; ?>>User</option>
                                        <option value="admin" <?php if ($row['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                    </select>
                                    <button type="submit" name="update_user">Update</button>
                                </form>
                            </td>
                            <td>
                                <form method="POST" action="admin_users.php" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="userID" value="<?php echo $row['IDuser']; ?>">
                                    <button type="submit" name="delete_user">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
