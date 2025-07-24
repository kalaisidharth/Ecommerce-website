<?php
require_once '../db.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM products WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

header('Location: manage_products.php');
