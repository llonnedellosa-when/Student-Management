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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Update Student</title>
    <style>
        /* Custom enhancements for better UX */
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }
        label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            display: block;
        }
        input[type="text"], input[type="email"] {
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        input[type="text"]:focus, input[type="email"]:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        button {
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        button:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
        }
        .readonly-note {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- HEADER (copied from student.php) -->
    <header class="fixed top-0 left-0 w-full z-10">
        <nav class="bg-gradient-to-t from-sky-300 to-orange-300 border-b border-white h-20">
            <div class="max-w-7xl mx-auto px-4 flex justify-between items-center text-white h-full">
                <div class="flex items-center space-x-3">
                    <img src="../images/BUp.png" alt="BU Logo" class="h-16 w-auto" />
                    <h1 class="text-xl font-bold">BICOL UNIVERSITY POLANGUI CLINIC - ADMIN - UPDATE STUDENT</h1>
                </div>
                <!-- No search bar needed here, so removed -->
            </div>
        </nav>
    </header>

    <!-- SIDEBAR (copied from student.php) -->
    <div class="group fixed top-20 left-0 h-[calc(100vh-80px)] w-14 hover:w-48 bg-sky-300 transition-all duration-300 overflow-x-hidden z-20 flex flex-col">
        <a href="Admin.php" class="flex items-center gap-4 px-4 py-3 text-black hover:text-white hover:bg-blue-500 transition">
            <i class="fa fa-home text-lg w-6"></i>
            <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition">Home</span>
        </a>
        <a href="student.php" class="flex items-center gap-4 px-4 py-3 text-black hover:text-white hover:bg-blue-500 transition">
            <i class="fa fa-user text-lg w-6"></i>
            <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition">Students</span>
        </a>
        <a href="requests.php" class="flex items-center gap-4 px-4 py-3 text-black hover:text-white hover:bg-blue-500 transition">
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
    <main class="ml-14 transition-all duration-300 pt-24 pb-20 min-h-[calc(100vh-80px)] w-full flex items-center justify-center">
        <div class="form-container">
            <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">Update Student Information</h1>

            <form action="update_student_process.php" method="POST" class="space-y-6">
                <input type="hidden" name="id" value="<?= htmlspecialchars($student['id']) ?>">

                <div>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($student['email']) ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>

                <div>
                    <label for="role">Role:</label>
                    <input type="text" id="role" name="role"
                           value="<?= htmlspecialchars($student['role']) ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                           readonly>
                    <p class="readonly-note">* Role cannot be changed.</p>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md">Save Changes</button>
            </form>
        </div>
    </main>

    <script>
        // Handle sidebar hover to adjust main margin (copied from student.php)
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
