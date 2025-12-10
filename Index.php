<?php
// Start session to show any messages if needed
session_start();
?>

<!doctype html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <title>Student Management System</title>
</head>

<body class="min-h-screen bg-gradient-to-r from-blue-200 to-purple-300 flex flex-colshadow-md ">

  <main class="flex flex-1 items-center justify-center">
    <div class="bg-gradient-to-tr from-blue-300 to-purple-400 p-25 rounded-xl shadow-lg border border-[#A7C7E7] w-100 text-center">

      <h1 class="text-3xl font-bold mb-2 text-black">Welcome!</h1>
      <p class="text-black mb-6">Log in to your account</p>

      <?php
      // Display success message after registration
      if (isset($_GET['success']) && $_GET['success'] === 'registered'): ?>
        <div class="bg-green-100 text-green-800 p-3 rounded-lg mb-4 text-center">
          Account registered successfully! You can now log in.
        </div>
      <?php endif; ?>

      <?php
      // Display error messages
      if (isset($_GET['error'])): ?>
        <p class="text-red-400 text-sm mb-3">
          <?php
          if ($_GET['error'] === 'email') echo "BU email only is allowed for students.";
          if ($_GET['error'] === 'invalid') echo "Invalid credentials.";
          ?>
        </p>
      <?php endif; ?>

      <form action="php/login.php" method="POST" class="flex flex-col space-y-4 mb-6">

        <!-- Email / Username input -->
        <input type="text" name="email" placeholder="BU Email or admin"
          value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>"
          class="px-3 py-2 bg-white text-black border border-[#A7C7E7] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#A7C7E7] shadow-md hover:-translate-y-0.5 transition-transform duration-200">

        <!-- Password input -->
        <div class="relative w-full">
          <input
            type="password"
            name="password"
            placeholder="Password"
            class="w-full px-3 py-2 pr-10 bg-white text-black border border-[#A7C7E7] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#A7C7E7] shadow-md hover:-translate-y-0.5 transition-transform duration-200"
            required>
          <span
            onclick="togglePassword(this)"
            class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-white select-none">
            👁️
          </span>

        </div>

        <!-- Login button -->
        <button
          type="submit"
          class="w-full px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 hover:-translate-y-0.5 transition-transform duration-200">
          Log in
        </button>

        <p class="text-black text-sm mb-2">Don't have an account?</p>

        <!-- Register button -->
        <a
          href="php/register.php"
          class="block w-full px-6 py-2 bg-blue-500 text-white rounded-lg text-center hover:bg-blue-600 hover:-translate-y-0.5 transition-transform duration-200"> Register
        </a>

      </form>
    </div>
  </main>
  <script>
    function togglePassword(icon) {
      const input = icon.previousElementSibling;

      if (input.type === "password") {
        input.type = "text";
        icon.textContent = "🙈";
      } else {
        input.type = "password";
        icon.textContent = "👁️";
      }
    }
  </script>

</body>

</html>