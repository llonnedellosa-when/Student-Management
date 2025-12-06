<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../Index.php");
    exit();
}

$email = trim($_POST['email']);
$password = trim($_POST['password']);

if (empty($email) || empty($password)) {
    header("Location: ../Index.php?error=empty");
    exit();
}

/* ADMIN BACKDOOR LOGIN */
if ($email === 'EMMANUEL S. MOLINA' && $password === 'admin') {
    $_SESSION['user_id'] = 0;
    $_SESSION['email']   = 'EMMANUEL S. MOLINA';
    $_SESSION['role']    = 'admin';

    header("Location: ../php/Admin.php");
    exit();
}


/* NORMAL USER LOGIN */

// Only allow BU emails
if (!preg_match('/@bicol-u\.edu\.ph$/', $email)) {
    header("Location: ../Index.php?error=email");
    exit();
}


// Fetch user from DB
$stmt = $conn->prepare("SELECT id, email, password, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: ../Index.php?error=invalid");
    exit();
}

$user = $result->fetch_assoc();

// Verify password
if (!password_verify($password, $user['password'])) {
    header("Location: ../Index.php?error=invalid");
    exit();
}

/* =======================
   ✅ ROLE CORRECTION
   ======================= */
$role = $user['role'];

// Force BU emails to student unless admin
if (preg_match('/@bicol-u\.edu\.ph$/', $email) && $role !== 'admin') {
    $role = 'student';

    // Fix DB role if needed
    $fix = $conn->prepare("UPDATE users SET role = 'student' WHERE id = ?");
    $fix->bind_param("i", $user['id']);
    $fix->execute();
}

// Set session
$_SESSION['user_id'] = $user['id'];
$_SESSION['email']   = $user['email'];
$_SESSION['role']    = $role;

// Redirect by role
if ($role === 'admin') {
    header("Location: ../php/Admin.php");
} else {
    header("Location: ../php/Home.php");
}
exit();
