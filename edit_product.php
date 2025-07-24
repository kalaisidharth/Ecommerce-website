<?php
require_once '../db.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $brand = $_POST['brand'];
    $rating = $_POST['rating'];
    $stock = $_POST['stock'];

    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, brand=?, rating=?, stock=? WHERE id=?");
    $stmt->bind_param("sdsdii", $name, $price, $brand, $rating, $stock, $id);
    $stmt->execute();
    header('Location: manage_products.php');
} else {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
}
?>

<form method="POST">
    <input type="text" name="name" value="<?= $product['name'] ?>" required><br>
    <input type="number" name="price" value="<?= $product['price'] ?>" step="0.01" required><br>
    <input type="text" name="brand" value="<?= $product['brand'] ?>" required><br>
    <input type="number" name="rating" value="<?= $product['rating'] ?>" required><br>
    <input type="number" name="stock" value="<?= $product['stock'] ?>" required><br>
    <input type="submit" value="Update Product">
</form>
