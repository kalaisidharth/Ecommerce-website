<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>eCommerce - Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card-img-top { height: 200px; object-fit: cover; }
    .badge-oos { position: absolute; top: 10px; left: 10px; }
  </style>
</head>
<body class="bg-light">

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>🛒 My eCommerce Store</h3>
    <div class="d-flex align-items-center">
      <?php if ($_SESSION['user']['is_admin'] == 1): ?>
        <a href="admin.php" class="btn btn-warning me-2">Admin Panel</a>
      <?php endif; ?>
      <a href="cart.php" class="btn btn-outline-dark me-2">🛒 View Cart</a>
      <span class="me-3">👋 <?= htmlspecialchars($_SESSION['user']['name']); ?></span>
      <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
  </div>

  <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-success text-center"><?= $_SESSION['message']; ?></div>
    <?php unset($_SESSION['message']); ?>
  <?php endif; ?>

  <div class="row row-cols-1 row-cols-md-3 g-4">
    <?php
    $result = $conn->query("SELECT * FROM products");
    while($row = $result->fetch_assoc()):
      $oos = ((int)$row['stock'] <= 0);
    ?>
      <div class="col">
        <div class="card h-100 shadow-sm position-relative">
          <?php if ($oos): ?>
            <span class="badge bg-danger badge-oos">Out of Stock</span>
          <?php endif; ?>
          <img src="images/<?= htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']); ?>">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($row['name']); ?></h5>
            <p class="card-text">
              <strong>Brand:</strong> <?= htmlspecialchars($row['brand']); ?><br>
              <strong>Price:</strong> ₹<?= $row['price']; ?><br>
              <strong>Stock:</strong> <?= (int)$row['stock']; ?>
            </p>

            <?php if (!$oos): ?>
              <form method="POST" action="cart.php">
                <input type="hidden" name="action" value="add_to_cart">
                <input type="hidden" name="product_id" value="<?= $row['id']; ?>">
                <div class="mb-2">
                  <input type="number" name="quantity" value="1" min="1" max="<?= (int)$row['stock'] ?>" class="form-control" />
                </div>
                <button type="submit" class="btn btn-primary w-100">Add to Cart</button>
              </form>
            <?php else: ?>
              <button class="btn btn-secondary w-100" disabled>Out of Stock</button>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>

</body>
</html>
