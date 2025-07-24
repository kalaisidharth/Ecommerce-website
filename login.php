<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($user = $res->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'is_admin' => (int)$user['is_admin']
            ];
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid credentials.";
        }
    } else {
        $error = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6 bg-white p-4 rounded shadow">
      <h3 class="text-center mb-4">🔐 Login</h3>
      <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
      <?php if (isset($_SESSION['message'])) { echo "<div class='alert alert-success'>" . $_SESSION['message'] . "</div>"; unset($_SESSION['message']); } ?>
      <form method="POST">
        <input class="form-control my-2" type="email" name="email" placeholder="Email" required>
        <input class="form-control my-2" type="password" name="password" placeholder="Password" required>
        <button class="btn btn-success w-100 mt-2">Login</button>
        <p class="text-center mt-3">Don't have an account? <a href="register.php">Register</a></p>
      </form>
    </div>
  </div>
</div>
</body>
</html>
