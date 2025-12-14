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
<body class="bg-gradient-to-br from-gray-100 to-gray-200 min-h-screen">

<!-- HEADER -->
<header class="fixed top-0 left-0 w-full z-10">
    <nav class="bg-gradient-to-r from-[#253b80] to-[#1e2f5f] border-b border-white h-20 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center text-white h-full">
            <div class="flex items-center space-x-3">
                <img src="../images/BUp.png" alt="BU Logo" class="h-16 w-auto drop-shadow-md" />
                <h1 class="text-xl font-bold tracking-wide">BICOL UNIVERSITY POLANGUI CLINIC - ADMIN - STUDENT DETAILS</h1>
            </div>
            <!-- No search needed for view page -->
        </div>
    </nav>
</header>

<!-- SIDEBAR -->
<div class="group fixed top-20 left-0 h-[calc(100vh-80px)] w-14 hover:w-48 bg-gradient-to-b from-[#111] to-[#222] transition-all duration-300 overflow-x-hidden z-20 flex flex-col shadow-xl">
    <a href="Admin.php" class="flex items-center gap-4 px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-800 transition-all duration-200 rounded-md mx-2 my-1">
        <i class="fa fa-home text-lg w-6"></i>
        <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Home</span>
    </a>
    <a href="student.php" class="flex items-center gap-4 px-4 py-3 text-white bg-gradient-to-r from-blue-600 to-blue-700 transition-all duration-200 rounded-md mx-2 my-1 shadow-md">
        <i class="fa fa-user text-lg w-6"></i>
        <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Students</span>
    </a>
    <a href="requests.php" class="flex items-center gap-4 px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-800 transition-all duration-200 rounded-md mx-2 my-1">
        <i class="fa fa-calendar text-lg w-6"></i>
        <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Requests</span>
    </a>
    <div class="flex-grow"></div>
    <a href="logout.php" class="flex items-center gap-4 px-4 py-3 text-red-400 hover:text-white hover:bg-red-600 transition-all duration-200 rounded-md mx-2 my-1">
        <i class="fa fa-sign-out text-lg w-6"></i>
        <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Logout</span>
    </a>
</div>

<!-- MAIN CONTENT -->
<main class="ml-14 transition-all duration-300 pt-24 pb-20 min-h-[calc(100vh-80px)] w-full">

    <div class="max-w-4xl mx-auto px-6">
        <a href="student.php" class="text-blue-600 hover:text-blue-800 text-lg font-semibold mb-6 inline-block transition-colors duration-200 hover:scale-105">
            <i class="fa fa-arrow-left mr-2"></i>Back to Student List
        </a>

        <div class="bg-white shadow-2xl rounded-xl p-8 max-w-lg mx-auto border border-gray-200 hover:shadow-3xl transition-shadow duration-300">
            <!-- Student Avatar/Icon -->
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                    <i class="fa fa-user"></i>
                </div>
            </div>

            <h2 class="text-3xl font-bold mb-8 text-center text-gray-800 tracking-wide">Student Information</h2>

            <div class="space-y-6">
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="font-semibold text-gray-600 flex items-center">
                        <i class="fa fa-id-badge mr-2 text-blue-500"></i>ID:
                    </span>
                    <span class="text-gray-800 font-medium bg-gray-50 px-3 py-1 rounded-md"><?= htmlspecialchars($student['id']) ?></span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="font-semibold text-gray-600 flex items-center">
                        <i class="fa fa-envelope mr-2 text-green-500"></i>Email:
                    </span>
                    <span class="text-gray-800 font-medium bg-gray-50 px-3 py-1 rounded-md break-all text-sm"><?= htmlspecialchars($student['email']) ?></span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="font-semibold text-gray-600 flex items-center">
                        <i class="fa fa-user-tag mr-2 text-purple-500"></i>Role:
                    </span>
                    <span class="text-gray-800 font-medium bg-gray-50 px-3 py-1 rounded-md capitalize"><?= htmlspecialchars($student['role']) ?></span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="font-semibold text-gray-600 flex items-center">
                        <i class="fa fa-calendar-alt mr-2 text-orange-500"></i>Created At:
                    </span>
                    <span class="text-gray-800 font-medium bg-gray-50 px-3 py-1 rounded-md text-sm"><?= htmlspecialchars($student['created_at']) ?></span>
                </div>
            </div>

            <!-- Optional: Action Buttons -->
            <div class="mt-8 flex justify-center space-x-4">
                <a href="update_student.php?id=<?= $student['id'] ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-all duration-200 hover:scale-105 shadow-md">
                    <i class="fa fa-edit mr-2"></i>Update
                </a>
                <button onclick="confirmDelete(<?= $student['id'] ?>)" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition-all duration-200 hover:scale-105 shadow-md">
                    <i class="fa fa-trash mr-2"></i>Delete
                </button>
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

// Confirm delete function
function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this student?')) {
        window.location.href = 'delete_student.php?id=' + id;
    }
}
</script>

</body>
</html>
