<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="phpcss.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .navbar img {
            width: 50px; 
            height: auto;
            margin-right: 10px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
<div class="navbar">
    <nav>
        <ul>
            <li><a href="your_link_here"><img src="/labs/project/photo/n.png" alt="Icon"></a></li>
            <li><a href="product.php"><i class="fas fa-store"></i> Products</a></li>
            
            <?php if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'): ?>
                <li><a href="home.php"><i class="fas fa-home"></i> Home</a></li>
             
            <?php endif; ?>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Home</a></li>
                <li><a href="inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
                <li><a href="admin_users.php"><i class="fas fa-users-cog"></i> Manage Users</a></li>
                <li><a href="admin_orders.php"><i class="fas fa-receipt"></i> View All Orders</a></li>
                <li><a href="admin_products.php"><i class="fas fa-plus-circle"></i> Add Product</a></li> 
                <li><a href="view_products.php"><i class="fas fa-list"></i> View Products</a></li>
                <li><a href="admin_skin_tone_matching.php"><i class="fas fa-adjust"></i> Match Products to Skin Tones</a></li>
                <li><a href="admin_matching_color.php"><i class="fas fa-palette"></i> Manage Color Matching</a></li> 
                <li><a href="admin_update_body_shape.php"><i class="fas fa-edit"></i> Update Body Shape Details</a></li>
            <?php endif; ?>

            <?php if (isset($_SESSION['username']) && isset($_SESSION['loggedin'])): ?>
                <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                <li><a href="edit_profile.php"><i class="fas fa-user-edit"></i> Edit Profile</a></li>
            <?php endif; ?>

            <?php if (isset($_SESSION['username']) && $_SESSION['role'] !== 'admin'): ?>
                <li><a href="my_orders.php"><i class="fas fa-box-open"></i> My Orders</a></li>
                <li><a href="recommendations.php"><i class="fas fa-star"></i> Recommendations</a></li>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['username']) && isset($_SESSION['loggedin'])): ?>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            <?php else: ?>
                <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                <li><a href="register.php"><i class="fas fa-user-plus"></i> Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>
</body>
</html>
