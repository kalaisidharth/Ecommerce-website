<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    header('Location: login.php');
    exit();
}

// Handle product addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $rating = intval($_POST['rating']);

    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $img_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $target = 'images/' . $img_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image = $img_name;
        }
    }

    $stmt = $conn->prepare("INSERT INTO products (name, brand, price, stock, rating, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdiss", $name, $brand, $price, $stock, $rating, $image);
    $stmt->execute();
    $_SESSION['message'] = "Product added!";
    header("Location: admin_dashboard.php");
    exit();
}

// Handle product deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM products WHERE id = $id");
    $_SESSION['message'] = "Product deleted!";
    header("Location: admin_dashboard.php");
    exit();
}

// Handle stock update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $id = intval($_POST['product_id']);
    $stock = intval($_POST['new_stock']);
    $stmt = $conn->prepare("UPDATE products SET stock=? WHERE id=?");
    $stmt->bind_param("ii", $stock, $id);
    $stmt->execute();
    $_SESSION['message'] = "Stock updated!";
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch products
$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Manage Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-img-top { height: 120px; object-fit: cover; }
        .table-img { width: 60px; height: 60px; object-fit: cover; }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Admin Product Management</h2>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['message']; ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="card mb-4 shadow">
        <div class="card-header bg-primary text-white">Add New Product</div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" class="row g-3">
                <input type="hidden" name="add_product" value="1">
                <div class="col-md-4">
                    <input type="text" name="name" class="form-control" placeholder="Product Name" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="brand" class="form-control" placeholder="Brand" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="price" class="form-control" placeholder="Price" step="0.01" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="stock" class="form-control" placeholder="Stock" min="0" required>
                </div>
                <div class="col-md-1">
                    <input type="number" name="rating" class="form-control" placeholder="Rating" min="1" max="5" required>
                </div>
                <div class="col-md-1">
                    <input type="file" name="image" class="form-control" accept="image/*" required>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-success">Add Product</button>
                </div>
            </form>
        </div>
    </div>

    <h4 class="mb-3">All Products</h4>
    <table class="table table-bordered table-hover bg-white shadow-sm">
        <thead class="table-primary">
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Brand</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Rating</th>
                <th>Update Stock</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $products->fetch_assoc()): ?>
            <tr>
                <td>
                    <?php if ($row['image']): ?>
                        <img src="images/<?= htmlspecialchars($row['image']); ?>" class="table-img" alt="">
                    <?php else: ?>
                        <span class="text-muted">No image</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td><?= htmlspecialchars($row['brand']); ?></td>
                <td>₹<?= number_format($row['price'], 2); ?></td>
                <td><?= (int)$row['stock']; ?></td>
                <td><?= (int)$row['rating']; ?></td>
                <td>
                    <form method="POST" class="d-flex">
                        <input type="hidden" name="update_stock" value="1">
                        <input type="hidden" name="product_id" value="<?= $row['id']; ?>">
                        <input type="number" name="new_stock" value="<?= (int)$row['stock']; ?>" min="0" max="1000"class="form-control me-2" style="width:80px;">
                        <button type="submit" class="btn btn-sm btn-warning">Update</button>
                    </form>
                </td>
                <td>
                    <a href="admin_dashboard.php?delete=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<script>
function test_delete_product() {
    console.log("Testing: Delete Product");
    driver.get(BASE_URL + "admin_dashboard.php");
    var delete_buttons = driver.find_elements(By.CSS_SELECTOR, "a.btn-danger");
    if (delete_buttons.length > 0) {
        delete_buttons[0].click();
        // Handle JS confirm dialog if present
        try {
            var alert = driver.switch_to.alert;
            alert.accept();
        } catch (e) {
            console.log("No JS confirm dialog present.");
        }
        // Wait for the success alert (skip waiting for URL change)
        wait.until(EC.presence_of_element_located((By.CLASS_NAME, "alert-success")));
        console.log("Delete Product: Success");
    } else {
        console.log("No product found to delete.");
    }
}
</script>