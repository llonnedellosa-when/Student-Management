<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Index.php");
    exit();
}

// === SEARCH FUNCTION ===
$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

$query = "SELECT id, email, role, created_at 
          FROM users 
          WHERE role = 'student' 
          AND (email LIKE ? OR id LIKE ?)";

$stmt = $conn->prepare($query);
$like = "%$search%";
$stmt->bind_param("ss", $like, $like);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Student Page</title>
</head>

<body class="bg-gray-100 min-h-screen">

<!-- HEADER -->
<header>
    <nav class="bg-[#253b80] border-b border-white h-20">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center text-white h-full">
            <div class="flex items-center space-x-3">
                <img src="../images/BUp.png" alt="BU Logo" class="h-16 w-auto" />
                <h1 class="text-xl font-bold">BICOL UNIVERSITY POLANGUI CLINIC - REQUEST</h1>
            </div>
            <form class="flex border border-white rounded" action="action_page.php">
                <input
                    type="text"
                    placeholder="Search.."
                    name="search"
                    class="px-2 py-1 text-black rounded-l focus:outline-none"
                />
                <button type="submit" class="px-3 bg-[#009bde] hover:bg-[#0082c8] rounded-r">
                    <i class="fa fa-search text-white"></i>
                </button>
            </form>
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

    <!-- SEARCH BAR (now integrated into header, but keeping a note if needed; removed separate div) -->
    <!-- Table -->
    <section class="overflow-x-auto w-[85%] mx-auto bg-white rounded shadow-md p-4 mb-10">
        <table class="w-full border border-gray-300 border-collapse text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-300 px-4 py-2 text-left">ID</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Email</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Role</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Created At</th>
                    <th class="border border-gray-300 px-4 py-2 text-center">View</th>
                    <th class="border border-gray-300 px-4 py-2 text-center">Update</th>
                    <th class="border border-gray-300 px-4 py-2 text-center">Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="even:bg-gray-50">
                            <td class="px-4 py-2 border border-gray-300"><?= htmlspecialchars($row['id']) ?></td>
                            <td class="px-4 py-2 border border-gray-300"><?= htmlspecialchars($row['email']) ?></td>
                            <td class="px-4 py-2 border border-gray-300"><?= htmlspecialchars($row['role']) ?></td>
                            <td class="px-4 py-2 border border-gray-300"><?= htmlspecialchars($row['created_at']) ?></td>
                            <!-- VIEW -->
                            <td class="px-4 py-2 border border-gray-300 text-center">
                                <a href="student_view.php?id=<?= $row['id'] ?>"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs">
                                    View
                                </a>
                            </td>
                            <!-- UPDATE -->
                            <td class="px-4 py-2 border border-gray-300 text-center">
                                <form action="update_student.php" method="POST">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs">Update</button>
                                </form>
                            </td>
                            <!-- DELETE -->
                            <td class="px-4 py-2 border border-gray-300 text-center">
                                <form action="delete_student.php" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this student?');">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-4 py-2 border border-gray-300 text-center">No students found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
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
