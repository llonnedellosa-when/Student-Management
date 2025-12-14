<?php
session_start();
require 'config.php';

// Admin-only access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Index.php");
    exit();
}

// Fetch service requests with user email via LEFT JOIN
$query = "
    SELECT sr.*, u.email
    FROM service_requests sr
    LEFT JOIN users u ON sr.user_id = u.id
    ORDER BY sr.created_at DESC
";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<title>Request Page</title>
</head>
<body>
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


<!-- Sidebar -->
<div
    class="group fixed top-20 left-0 h-[calc(100vh-80px)] w-14 hover:w-48 bg-[#111] transition-all duration-300 overflow-x-hidden z-20 flex flex-col"
>
    <a
        href="Admin.php"
        class="flex items-center gap-4 px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-800 transition"
    >
        <i class="fa fa-home text-lg w-6"></i>
        <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition">Home</span>
    </a>

    <a
        href="student.php"
        class="flex items-center gap-4 px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-800 transition"
    >
        <i class="fa fa-user text-lg w-6"></i>
        <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition">Students</span>
    </a>

    <a
        href="requests.php"
        class="flex items-center gap-4 px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-800 transition"
    >
        <i class="fa fa-calendar text-lg w-6"></i>
        <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition">Requests</span>
    </a>

    <div class="flex-grow"></div>

    <a
        href="logout.php"
        class="flex items-center gap-4 px-4 py-3 text-red-400 hover:text-white hover:bg-red-600 transition"
    >
        <i class="fa fa-sign-out text-lg w-6"></i>
        <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition">Logout</span>
    </a>
</div>

<div class="overflow-x-auto my-5">
    <table class="w-[85%] mx-auto border border-gray-300 border-collapse text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border border-gray-300 px-4 py-2 text-left">ID</th>
                <th class="border border-gray-300 px-4 py-2 text-left">User ID</th>
                <th class="border border-gray-300 px-4 py-2 text-left">Email</th>
                <th class="border border-gray-300 px-4 py-2 text-left">Service</th>
                <th class="border border-gray-300 px-4 py-2 text-left">Preferred Date</th>
                <th class="border border-gray-300 px-4 py-2 text-left">Notes</th>
                <th class="border border-gray-300 px-4 py-2 text-left">Requested At</th>
                <th class="border border-gray-300 px-4 py-2 text-left">Update</th>
                <th class="border border-gray-300 px-4 py-2 text-left">Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr class="even:bg-gray-50">
                        <td class="px-4 py-2 border border-gray-300"><?= htmlspecialchars($row['id']) ?></td>
                        <td class="px-4 py-2 border border-gray-300"><?= htmlspecialchars($row['user_id']) ?></td>
                        <td class="px-4 py-2 border border-gray-300"><?= htmlspecialchars($row['email']) ?></td>
                        <td class="px-4 py-2 border border-gray-300"><?= htmlspecialchars($row['service']) ?></td>
                        <td class="px-4 py-2 border border-gray-300"><?= htmlspecialchars($row['preferred_date']) ?></td>
                        <td class="px-4 py-2 border border-gray-300"><?= htmlspecialchars($row['notes']) ?></td>
                        <td class="px-4 py-2 border border-gray-300"><?= htmlspecialchars($row['created_at']) ?></td>
                        <td class="px-4 py-2 border border-gray-300 text-center">
                            <form action="update_request.php" method="POST">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded">Update</button>
                            </form>
                        </td>
                        <td class="px-4 py-2 border border-gray-300 text-center">
                            <form action="delete_request.php" method="POST" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td class="px-4 py-2 border border-gray-300 text-center" colspan="9">No service requests found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
