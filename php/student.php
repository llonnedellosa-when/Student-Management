<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Student Page</title>
</head>

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

<body>

<!-- HEADER -->
<header>
    <nav class="border-b border-white">
        <div class="py-4" style="background-color:#009bde;"></div>
        <div class="px-6 py-4 flex justify-between items-center text-white bg-[#253b80]">
            <div class="flex items-center space-x-3">
                <img src="../images/BUp.png" class="h-20">
                <h1 class="text-xl font-bold">
                    BICOL UNIVERSITY POLANGUI CLINIC – STUDENT LIST
                </h1>
            </div>
        </div>
    </nav>
</header>

<!-- SIDEBAR -->
<div class="group fixed top-[140px] left-0 h-[calc(100vh-140px)]
            w-14 hover:w-48 bg-[#111] transition-all duration-300 z-20 flex flex-col">

    <a href="Admin.php" class="flex items-center px-4 py-3 text-gray-400 hover:text-white">
        <i class="fa fa-home w-6"></i>
        <span class="opacity-0 group-hover:opacity-100">Home</span>
    </a>

    <a href="student.php" class="flex items-center px-4 py-3 text-white bg-gray-800">
        <i class="fa fa-user w-6"></i>
        <span class="opacity-0 group-hover:opacity-100">Students</span>
    </a>

    <a href="requests.php" class="flex items-center px-4 py-3 text-gray-400 hover:text-white">
        <i class="fa fa-calendar w-6"></i>
        <span class="opacity-0 group-hover:opacity-100">Requests</span>
    </a>

    <div class="flex-grow"></div>

    <a href="logout.php" class="flex items-center px-4 py-3 text-red-400 hover:text-white">
        <i class="fa fa-sign-out w-6"></i>
        <span class="opacity-0 group-hover:opacity-100">Logout</span>
    </a>
</div>

<!-- SEARCH BAR -->
<div class="ml-20 my-6">
    <form method="GET" class="w-[85%] mx-auto flex">
        <input type="text" name="search" placeholder="Search student by email or ID..."
            value="<?= htmlspecialchars($search) ?>"
            class="w-full px-4 py-2 border border-gray-300 rounded-l" />

        <button class="bg-blue-600 text-white px-4 py-2 rounded-r">Search</button>
    </form>
</div>

<!-- TABLE -->
<div class="overflow-x-auto my-10 ml-20">
    <table class="w-[85%] mx-auto border border-gray-300 border-collapse text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-4 py-2">ID</th>
                <th class="border px-4 py-2">Email</th>
                <th class="border px-4 py-2">Role</th>
                <th class="border px-4 py-2">Created At</th>
                <th class="border px-4 py-2">View</th>
                <th class="border px-4 py-2">Update</th>
                <th class="border px-4 py-2">Delete</th>
            </tr>
        </thead>

<tbody>
<?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr class="even:bg-gray-50">
            <td class="border px-4 py-2"><?= htmlspecialchars($row['id']) ?></td>
            <td class="border px-4 py-2"><?= htmlspecialchars($row['email']) ?></td>
            <td class="border px-4 py-2"><?= htmlspecialchars($row['role']) ?></td>
            <td class="border px-4 py-2"><?= htmlspecialchars($row['created_at']) ?></td>

            <!-- VIEW -->
            <td class="border px-4 py-2 text-center">
                <a href="student_view.php?id=<?= $row['id'] ?>"
                    class="bg-green-600 text-white px-3 py-1 rounded text-xs">
                    View
                </a>
            </td>

            <!-- UPDATE -->
            <td class="border px-4 py-2 text-center">
                <form action="update_student.php" method="POST">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button class="bg-blue-500 text-white px-3 py-1 rounded text-xs"> Update </button>
                </form> 
            </td>

            <!-- DELETE -->
            <td class="border px-4 py-2 text-center">
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
        <td colspan="7" class="text-center py-4">No students found.</td>
    </tr>
<?php endif; ?>
</tbody>
    </table>
</div>

</body>
</html>
