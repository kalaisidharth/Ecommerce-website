<?php
session_start();
include 'db.php';

if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
  <h2 class="text-center mb-4">🧾 Checkout</h2>

  <div class="row">
    <div class="col-md-6">
      <h5>Shipping Information</h5>
      <form>
        <input class="form-control my-2" type="text" placeholder="Full Name" required>
        <input class="form-control my-2" type="text" placeholder="Address" required>
        <input class="form-control my-2" type="text" placeholder="City" required>
        <input class="form-control my-2" type="text" placeholder="Zip Code" required>
        <input class="form-control my-2" type="email" placeholder="Email" required>
      </form>
    </div>

    <div class="col-md-6">
      <div class="p-4 bg-light rounded shadow-sm">
        <h5>Order Summary</h5>
        <ul class="list-group mb-3">
          <?php
          $total = 0;
          foreach ($_SESSION['cart'] as $id => $qty) {
              $res = $conn->query("SELECT * FROM products WHERE id = $id");
              if ($row = $res->fetch_assoc()) {
                  $subtotal = $row['price'] * $qty;
                  $total += $subtotal;
                  echo "<li class='list-group-item d-flex justify-content-between'>
                          <span>{$row['name']} × $qty</span>
                          <strong>₹$subtotal</strong>
                        </li>";
              }
          }
          ?>
          <li class="list-group-item d-flex justify-content-between bg-light">
            <strong>Total</strong>
            <strong>₹<?= $total ?></strong>
          </li>
        </ul>
        <form action="thankyou.php" method="POST">
          <button class="btn btn-success w-100" type="submit">Place Order</button>
        </form>
      </div>
    </div>
  </div>
</div>

</body>
</html>
