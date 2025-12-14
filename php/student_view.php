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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Student Details</title>
</head>
<body class="bg-gray-100 min-h-screen">

<!-- HEADER -->
<header class="fixed top-0 left-0 w-full z-10">
    <nav class="bg-[#253b80] border-b border-white h-20">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center text-white h-full">
            <div class="flex items-center space-x-3">
                <img src="../images/BUp.png" alt="BU Logo" class="h-16 w-auto" />
                <h1 class="text-xl font-bold">BICOL UNIVERSITY POLANGUI CLINIC - ADMIN - STUDENT DETAILS</h1>
            </div>
            <!-- No search needed for view page -->
        </div>
    </nav>
</header>

<!-- SIDEBAR -->
<div class="group fixed top-20 left-0 h-[calc(100vh-80px)] w-14 hover:w-48 bg-[#111] transition-all duration-300 overflow-x-hidden z-20 flex flex-col">
    <a href="Admin.php" class="flex items-center gap-4 px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-800 transition">
        <i class="fa fa-home text-lg w-6"></i>
        <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition">Home</span>
    </a>
    <a href="student.php" class="flex items-center gap-4 px-4 py-3 text-white bg-gray-800 transition">
        <i class="fa fa-user text-lg w-6"></i>
        <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition">Students</span>
    </a>
    <a href="requests.php" class="flex items-center gap-4 px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-800 transition">
        <i class="fa fa-calendar text-lg w-6"></i>
        <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition">Requests</span>
    </a>
    <div class="flex-grow"></div>
    <a href="logout.php" class="flex items-center gap-4 px-4 py-3 text-red-400 hover:text-white hover:bg-red-600 transition">
        <i class="fa fa-sign-out text-lg w-6"></i>
        <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition">Logout</span>
    </a>
</div>

<!-- MAIN CONTENT -->
<main class="ml-14 transition-all duration-300 pt-24 pb-20 min-h-[calc(100vh-80px)] w-full">

    <div class="max-w-4xl mx-auto px-6">
        <a href="student.php" class="text-blue-600 hover:text-blue-800 text-lg font-semibold mb-6 inline-block">
            <i class="fa fa-arrow-left mr-2"></i>Back to Student List
        </a>

        <div class="bg-white shadow-lg rounded-lg p-8 max-w-md mx-auto">
            <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Student Information</h2>

            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="font-semibold text-gray-600">ID:</span>
                    <span class="text-gray-800"><?= htmlspecialchars($student['id']) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="font-semibold text-gray-600">Email:</span>
                    <span class="text-gray-800 break-all"><?= htmlspecialchars($student['email']) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="font-semibold text-gray-600">Role:</span>
                    <span class="text-gray-800"><?= htmlspecialchars($student['role']) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="font-semibold text-gray-600">Created At:</span>
                    <span class="text-gray-800"><?= htmlspecialchars($student['created_at']) ?></span>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Handle sidebar hover to adjust main margin
const sidebar = document.querySelector('.group');
const main = document.querySelector('main');
sidebar.addEventListener('mouseenter', () => {
    main.classList.remove('ml-14');
    main.classList.add('ml-48');
});
sidebar.addEventListener('mouseleave', () => {
    main.classList.remove('ml-48');
    main.classList.add('ml-14');
});
</script>

</body>
</html>
