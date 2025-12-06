<?php
session_start();
require 'config.php';

// Only admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Index.php");
    exit();
}

// ✅ VALIDATE ID
if (!isset($_POST['id']) && !isset($_GET['id'])) {
    die("No request ID provided.");
}

$request_id = isset($_POST['id']) ? (int)$_POST['id'] : (int)$_GET['id'];

// ✅ UPDATE LOGIC
if (isset($_POST['update'])) {
    $status = $_POST['status'];
    $times_avail = (int) $_POST['times_avail'];

    $update_stmt = $conn->prepare(
        "UPDATE service_requests 
         SET status = ?, times_avail = ? 
         WHERE id = ?"
    );
    $update_stmt->bind_param("sii", $status, $times_avail, $request_id);

    if ($update_stmt->execute()) {
        header("Location: requests.php?message=updated");
        exit();
    } else {
        $error = "Error updating request.";
    }
}

// ✅ FETCH REQUEST DETAILS
$stmt = $conn->prepare(
    "SELECT sr.*, u.email AS student_email
     FROM service_requests sr
     JOIN users u ON sr.user_id = u.id
     WHERE sr.id = ?"
);
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Request not found.");
}

$request = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Request</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white p-6 rounded-xl shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold mb-4">Update Request</h2>

    <?php if (!empty($error)) : ?>
        <p class="text-red-500 mb-4"><?= $error ?></p>
    <?php endif; ?>

 <form method="POST" class="space-y-4">

    <!-- REQUIRED: KEEP THE ID -->
    <input type="hidden" name="id" value="<?= $request['id'] ?>">

    <div>
        <label class="block font-medium">Student Email</label>
        <input type="email" class="w-full border px-3 py-2 rounded"
               value="<?= htmlspecialchars($request['student_email']) ?>" readonly>
    </div>

    <div>
        <label class="block font-medium">Service</label>
        <input type="text" class="w-full border px-3 py-2 rounded"
               value="<?= htmlspecialchars($request['service']) ?>" readonly>
    </div>

    <div>
        <label class="block font-medium">Preferred Date</label>
        <input type="text" class="w-full border px-3 py-2 rounded"
               value="<?= htmlspecialchars($request['preferred_date']) ?>" readonly>
    </div>

    <div>
        <label class="block font-medium">Notes</label>
        <textarea class="w-full border px-3 py-2 rounded" readonly>
<?= htmlspecialchars($request['notes']) ?>
        </textarea>
    </div>

    <div>
        <label class="block font-medium">Status</label>
        <select name="status" class="w-full border px-3 py-2 rounded" required>
            <option value="Pending" <?= $request['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="Completed" <?= $request['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
        </select>
    </div>

    <div>
        <label class="block font-medium">Times Avail</label>
        <input type="number" name="times_avail"
               class="w-full border px-3 py-2 rounded"
               value="<?= (int)($request['times_avail'] ?? 0) ?>"
               min="0" required>
    </div>

    <button type="submit" name="update"
            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
        Update
    </button>

    <a href="requests.php" class="ml-2 text-gray-700 hover:underline">Cancel</a>

</form>
        
</div>

</body>
</html>
