<?php
require_once '../db.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$result = $conn->query("SELECT * FROM products");
?>

<h2>Product List</h2>
<table border="1">
    <tr><th>Name</th><th>Price</th><th>Brand</th><th>Stock</th><th>Action</th></tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?= $row['name'] ?></td>
        <td><?= $row['price'] ?></td>
        <td><?= $row['brand'] ?></td>
        <td><?= $row['stock'] ?></td>
        <td>
            <a href="edit_product.php?id=<?= $row['id'] ?>">Edit</a> |
            <a href="delete_product.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete product?')">Delete</a>
        </td>
    </tr>
    <?php } ?>
</table>
