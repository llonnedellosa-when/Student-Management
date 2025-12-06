<?php
session_start();
require 'config.php';

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: Home.php");
    exit();
}

// Get inputs
$user_id = $_SESSION['user_id'];
$service = trim($_POST['service']);
$preferred_date = trim($_POST['date']); // make sure form uses 'date' as input name
$notes = trim($_POST['notes']);

// Validate required fields
if (empty($service) || empty($preferred_date)) {
    header("Location: Home.php?error=missing_fields");
    exit();
}

// Insert into DB (email is NOT stored here)
$stmt = $conn->prepare("
    INSERT INTO service_requests (user_id, service, preferred_date, notes)
    VALUES (?, ?, ?, ?)
");

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("isss", $user_id, $service, $preferred_date, $notes);

if ($stmt->execute()) {
    header("Location: Home.php?success=service_requested");
    exit();
} else {
    die("Execute failed: " . $stmt->error);
}
