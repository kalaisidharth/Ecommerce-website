<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

/**
 * ADD TO CART
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $id  = (int)$_POST['product_id'];
    $qty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    $qty = max(1, $qty);

    // Validate stock
    $res = $conn->query("SELECT stock FROM products WHERE id = $id");
    if ($res && $res->num_rows) {
        $stock = (int)$res->fetch_assoc()['stock'];
        if ($stock <= 0) {
            $_SESSION['message'] = "Item is out of stock!";
            header("Location: index.php");
            exit();
        }
        if ($qty > $stock) $qty = $stock;
    }

    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qty;
    // Cap by stock
    if (isset($stock)) {
        $_SESSION['cart'][$id] = min($_SESSION['cart'][$id], $stock);
    }
    $_SESSION['message'] = "Item added to cart!";
    header("Location: index.php");
    exit();
}

/**
 * UPDATE CART QUANTITIES
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $id => $qty) {
            $id  = (int)$id;
            $qty = max(0, (int)$qty);

            // Check stock
            $res = $conn->query("SELECT stock FROM products WHERE id = $id");
            if ($res && $res->num_rows) {
                $stock = (int)$res->fetch_assoc()['stock'];
                if ($qty > $stock) $qty = $stock;
            }

            if ($qty <= 0) {
                unset($_SESSION['cart'][$id]);
            } else {
                $_SESSION['cart'][$id] = $qty;
            }
        }
    }
    $_SESSION['message'] = "Cart updated.";
    header("Location: cart.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .container { margin-top: 50px; }
    .table img { height: 60px; object-fit: cover; }
    .total-box { background: #f8f9fa; padding: 20px; border-radius: 8px; }
  </style>
</head>
<body>

<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="text-center">🛒 Your Shopping Cart</h2>
    <div>
      <a href="index.php" class="btn btn-outline-secondary">← Continue Shopping</a>
    </div>
  </div>

  <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-success text-center"><?= $_SESSION['message']; ?></div>
    <?php unset($_SESSION['message']); ?>
  <?php endif; ?>

  <?php if (empty($_SESSION['cart'])): ?>
    <div class="alert alert-info text-center">
      Your cart is currently empty.
    </div>
  <?php else: ?>
    <form method="POST">
      <input type="hidden" name="update_cart" value="1">
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
          <thead class="table-dark">
            <tr>
              <th>Image</th>
              <th>Product</th>
              <th>Brand</th>
              <th>Price</th>
              <th style="width:120px;">Qty</th>
              <th>Subtotal</th>
              <th>Remove</th>
            </tr>
          </thead>
          <tbody>
          <?php
            $grandTotal = 0;
            foreach ($_SESSION['cart'] as $id => $qty):
              $res = $conn->query("SELECT * FROM products WHERE id = $id");
              if ($row = $res->fetch_assoc()):
                $maxStock = (int)$row['stock'];
                if ($qty > $maxStock) {
                    $qty = $_SESSION['cart'][$id] = $maxStock; // Adjust if stock decreased
                }
                $total = $row['price'] * $qty;
                $grandTotal += $total;
          ?>
            <tr>
              <td><img src="images/<?= htmlspecialchars($row['image']); ?>" alt=""></td>
              <td><?= htmlspecialchars($row['name']); ?></td>
              <td><?= htmlspecialchars($row['brand']); ?></td>
              <td>₹<?= $row['price']; ?></td>
              <td>
                <input type="number" name="quantities[<?= $id; ?>]" value="<?= $qty; ?>" min="0" max="<?= $maxStock ?>" class="form-control">
              </td>
              <td>₹<?= $total; ?></td>
              <td>
                <a href="remove.php?id=<?= $id; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Remove item from cart?');">Remove</a>
              </td>
            </tr>
          <?php endif; endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="row mt-4">
        <div class="col-md-6 offset-md-3">
          <div class="total-box text-center shadow-sm">
            <h4>Total: ₹<?= $grandTotal; ?></h4>
            <button type="submit" class="btn btn-primary mt-2 w-100">Update Cart</button>
            <a href="checkout.php" class="btn btn-success mt-2 w-100">Proceed to Checkout</a>
          </div>
        </div>
      </div>
    </form>
  <?php endif; ?>
</div>

</body>
</html>
