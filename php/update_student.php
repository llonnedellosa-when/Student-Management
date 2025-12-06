<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Index.php");
    exit();
}

// Must come from POST
if (!isset($_POST['id'])) {
    die("Invalid request.");
}

$id = $_POST['id'];

// Get the student info
$stmt = $conn->prepare("SELECT id, email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    die("Student not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="p-10">

<h1 class="text-2xl font-bold mb-6">Update Student Information</h1>

<form action="update_student_process.php" method="POST" class="w-96 space-y-4">

    <input type="hidden" name="id" value="<?= $student['id'] ?>">

    <div>
        <label>Email:</label>
        <input type="text" name="email"
               value="<?= htmlspecialchars($student['email']) ?>"
               class="w-full px-3 py-2 border rounded">
    </div>

    <div>
        <label>Role:</label>
        <input type="text" name="role"
               value="<?= htmlspecialchars($student['role']) ?>"
               class="w-full px-3 py-2 border rounded"
               readonly>
        <p class="text-xs text-gray-500">* Role cannot be changed.</p>
    </div>

    <button class="bg-blue-600 text-white px-4 py-2 rounded">Save Changes</button>
</form>

</body>
</html>
