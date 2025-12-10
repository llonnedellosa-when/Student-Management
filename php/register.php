<?php
include "config.php";

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // ✅ BASIC VALIDATIONS
    if (empty($email) || empty($password) || empty($confirm_password)) {
        $message = "All fields are required.";
    } elseif (!preg_match('/@bicol-u\.edu\.ph$/', $email)) {
        $message = "Please use your Bicol University email (@bicol-u.edu.ph).";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // ✅ CHECK IF EMAIL EXISTS
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Email already exists.";
        } else {
            // ✅ FORCE ROLE = STUDENT
            $role = 'student';

            $stmt = $conn->prepare(
                "INSERT INTO users (email, password, role) VALUES (?, ?, ?)"
            );
            $stmt->bind_param("sss", $email, $hashed_password, $role);

            if ($stmt->execute()) {
                header("Location: ../Index.php?success=registered");
                exit();
            } else {
                $message = "Error creating account.";
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Register</title>
</head>

<body class="bg-gradient-to-tr from-blue-300 to-purple-400 flex items-center justify-center min-h-screen">

    <div class="bg-white w-full max-w-md p-6 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold text-center mb-6">Create Account</h2>

        <?php if (!empty($message)): ?>
            <div class="mb-4 text-center text-red-600">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">

            <input
                type="email"
                name="email"
                placeholder="BU Email (@bicol-u.edu.ph)"
                class="w-full px-4 py-2 bg-white text-black border border-[#A7C7E7]  rounded-lg focus:outline-none focus:ring focus:border-blue-400"
                required>

            <input
                type="password"
                name="password"
                placeholder="Password (min. 8 characters)"
                class="w-full px-4 py-2 bg-white text-black border border-[#A7C7E7] rounded-lg focus:outline-none focus:ring focus:border-blue-400"
                required>

            <input
                type="password"
                name="confirm_password"
                placeholder="Confirm Password"
                class="w-full px-4 py-2 bg-white text-blue border border-[#A7C7E7] rounded-lg focus:outline-none focus:ring focus:border-blue-400 "
                required>

            <button
                type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 hover:-translate-y-0.5 transition-transform duration-200">
                Register
            </button>

        </form>
    </div>

</body>

</html>