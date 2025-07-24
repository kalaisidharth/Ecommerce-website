<?php
session_start();
$_SESSION['cart'] = []; // Clear cart
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Thank You</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container text-center mt-5">
  <div class="bg-success text-white p-5 rounded shadow">
    <h2>🎉 Thank You!</h2>
    <p>Your order has been placed successfully.</p>
    <a href="index.php" class="btn btn-light mt-3">Continue Shopping</a>
  </div>
</div>

</body>
</html>
