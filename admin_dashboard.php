<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    header('Location: ../login.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial; background-color: #f4f4f4; padding: 20px; }
        h1 { color: #333; }
        .admin-links a {
            display: block;
            margin: 10px 0;
            font-size: 18px;
            color: #007BFF;
            text-decoration: none;
        }
        .admin-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Welcome Admin, <?= htmlspecialchars($_SESSION['user']['name']) ?>!</h1>
    <div class="admin-links">
        <a href="add_product.php">➕ Add New Product</a>
        <a href="manage_products.php">📦 Manage Products</a>
        <a href="view_orders.php">📑 View Orders</a>
        <a href="view_users.php">👥 View Users</a>
        <a href="../logout.php">🚪 Logout</a>
    </div>
</body>
</html>
