<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] !== 1) {
    header("Location: index.php");
    exit();
}

$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    foreach ($_POST['stock'] as $product_id => $stock) {
        $pid = (int)$product_id;
        $stk = max(0, (int)$stock);
        $stmt = $conn->prepare("UPDATE products SET stock = ? WHERE id = ?");
        $stmt->bind_param("ii", $stk, $pid);
        $stmt->execute();
    }
    $message = "Stock updated successfully!";
}

$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Manage Stock</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>👑 Admin Panel - Manage Products Stock</h3>
    <div>
      <a href="index.php" class="btn btn-outline-secondary me-2">Back to Shop</a>
      <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
  </div>

  <?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
  <?php endif; ?>

  <form method="POST">
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Image</th>
          <th>Name</th>
          <th>Brand</th>
          <th>Price (₹)</th>
          <th>Stock (0 = OOS)</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($p = $products->fetch_assoc()): ?>
        <tr>
          <td><?= $p['id'] ?></td>
          <td><img src="images/<?= $p['image'] ?>" style="height:50px;object-fit:cover;"></td>
          <td><?= htmlspecialchars($p['name']) ?></td>
          <td><?= htmlspecialchars($p['brand']) ?></td>
          <td><?= $p['price'] ?></td>
          <td style="max-width:100px;">
            <input type="number" class="form-control" name="stock[<?= $p['id'] ?>]" value="<?= (int)$p['stock'] ?>" min="0">
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
    <button class="btn btn-primary" name="update_stock" value="1">Save Stock</button>
  </form>
</div>
</body>
</html>
