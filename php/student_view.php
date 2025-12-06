<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Index.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("Invalid student ID.");
}

$id = $_GET['id'];

$query = "SELECT id, email, role, created_at FROM users WHERE id = ? AND role = 'student'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Student not found.");
}

$student = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Details</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="p-10">

<a href="student.php" class="text-blue-600">⬅ Back to Student List</a>

<div class="mt-6 border p-6 rounded shadow w-[400px] bg-white">
    <h2 class="text-xl font-bold mb-4">Student Information</h2>

    <p><strong>ID:</strong> <?= htmlspecialchars($student['id']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></p>
    <p><strong>Role:</strong> <?= htmlspecialchars($student['role']) ?></p>
    <p><strong>Created At:</strong> <?= htmlspecialchars($student['created_at']) ?></p>
</div>

</body>
</html>
