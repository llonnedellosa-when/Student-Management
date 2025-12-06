<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Index.php");
    exit();
}

if (!isset($_POST['id']) || !isset($_POST['email'])) {
    die("Invalid request.");
}

$id = $_POST['id'];
$email = trim($_POST['email']);

// Update query
$stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
$stmt->bind_param("si", $email, $id);

if ($stmt->execute()) {
    echo "<script>alert('Student updated successfully!'); window.location='student.php';</script>";
} else {
    echo "<script>alert('Error updating student.'); window.location='student.php';</script>";
}
?>
