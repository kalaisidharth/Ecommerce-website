<?php
require_once '../db.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $brand = $_POST['brand'];
    $rating = $_POST['rating'];
    $stock = $_POST['stock'];

    $stmt = $conn->prepare("INSERT INTO products (name, price, brand, rating, stock) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sdsdi", $name, $price, $brand, $rating, $stock);
    $stmt->execute();
    header('Location: manage_products.php');
}
?>

<form method="POST">
    <input type="text" name="name" placeholder="Product Name" required><br>
    <input type="number" name="price" step="0.01" placeholder="Price" required><br>
    <input type="text" name="brand" placeholder="Brand" required><br>
    <input type="number" name="rating" placeholder="Rating (1-5)" required><br>
    <input type="number" name="stock" placeholder="Stock" required><br>
    <input type="submit" value="Add Product">
</form>
