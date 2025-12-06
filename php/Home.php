<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Home Page</title>
</head>
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Index.php");
    exit();
}

$services = [
    ['img' => '../images/1.webp', 'title' => 'Consultation'],
    ['img' => '../images/Vacc.jpg', 'title' => 'Vaccination'],
    ['img' => '../images/dental.jpg', 'title' => 'Dental Test'],
];
?>
<body class="min-h-screen bg-white flex flex-col">

<header class="bg-[#253b80] border-b border-white h-35">
    <nav class="py-4 flex justify-between items-center text-white" style="background-color:#009bde;"></nav>
    <nav class="max-w-2x1 mx-auto px-2 py-4 flex justify-between items-center text-white">
        <div class="flex items-center space-x-3">
            <img src="../images/BUp.png" alt="BU Logo" class="h-20 w-30 flex-shrink-0">
            <h1 class="text-xl font-bold py-4">BICOL UNIVERSITY POLANGUI CLINIC</h1>
        </div>
        <ul class="flex space-x-5 text-black">
            <li><a href="#" class="hover:underline">Home</a></li>
            <li><a href="#" class="hover:underline">About</a></li>
            <li><a href="#" class="hover:underline">Services</a></li>
            <li><a href="#" class="hover:underline">Contact</a></li>
        </ul>
    </nav>
</header>

<!-- Sidebar -->
<div class="group fixed top-[140px] left-0 h-[calc(100vh-140px)] 
            w-14 hover:w-48 
            bg-[#111] 
            transition-all duration-300 
            overflow-x-hidden 
            z-20 flex flex-col">

  <!-- Home -->
  <a href="Home.php"
     class="flex items-center gap-4 px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-800 transition">
    <i class="fa fa-home text-lg w-6"></i>
    <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition">
      Home
    </span>
  </a>

  <!-- Services -->
  <a href="#services"
     class="flex items-center gap-4 px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-800 transition">
    <i class="fa fa-wrench text-lg w-6"></i>
    <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition">
      Services
    </span>
  </a>

  <!-- Clients / Students -->
  <a href="#clients"
     class="flex items-center gap-4 px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-800 transition">
    <i class="fa fa-user text-lg w-6"></i>
    <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition">
      Students
    </span>
  </a>

  <!-- Requests -->
  <a href="#requests"
     class="flex items-center gap-4 px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-800 transition">
    <i class="fa fa-calendar text-lg w-6"></i>
    <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition">
      Requests
    </span>
  </a>

  <!-- Spacer -->
  <div class="flex-grow"></div>

  <!-- Logout -->
  <a href="logout.php"
     class="flex items-center gap-4 px-4 py-3 
            text-red-400 hover:text-white hover:bg-red-600 transition">
    <i class="fa fa-sign-out text-lg w-6"></i>
    <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition">
      Logout
    </span>
  </a>

</div>

<div class="mt-5 p-4">
    <div class="flex flex-wrap justify-center gap-6">
        <?php foreach ($services as $service): ?>
        <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 xl:w-1/5 mb-4 max-w-xs">
            <div class="shadow-xl rounded-lg overflow-hidden bg-gray-50 text-center h-full flex flex-col items-center p-4 cursor-pointer service-card"
                 data-service="<?= $service['title'] ?>">
                <img src="<?= $service['img'] ?>" class="w-24 h-24 object-cover mx-auto rounded-full mt-2" alt="<?= $service['title'] ?>">
                <div class="p-4 flex-grow">
                    <h5 class="text-lg font-semibold text-gray-800"><?= $service['title'] ?></h5>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Messages -->
    <?php if (isset($_GET['success'])): ?>
    <div class="max-w-md mx-auto bg-green-100 text-green-800 p-3 rounded-lg mb-4 text-center">
        Request submitted successfully!
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div class="max-w-md mx-auto bg-red-100 text-red-800 p-3 rounded-lg mb-4 text-center">
        Something went wrong. Please try again.
    </div>
    <?php endif; ?>

    <!-- Hidden Form -->
    <div id="serviceFormContainer" class="hidden mt-10 max-w-md mx-auto bg-white p-6 rounded-xl shadow-lg">
        <h2 class="text-xl font-bold mb-4 text-center text-gray-800">Service Request Form</h2>
        <form action="submit_service.php" method="POST" class="space-y-4">
            <input type="hidden" name="service" id="formService">

            <div>
                <label for="name" class="block text-gray-700 font-medium mb-1">Full Name</label>
                <input type="text" name="name" id="name" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label for="email" class="block text-gray-700 font-medium mb-1">BU Email</label>
                <input type="email" name="email" id="email" required
                    placeholder="Enter your BU email"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label for="date" class="block text-gray-700 font-medium mb-1">Preferred Date</label>
                <input type="date" name="date" id="date" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label for="notes" class="block text-gray-700 font-medium mb-1">Additional Notes</label>
                <textarea name="notes" id="notes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                          placeholder="Any additional information..."></textarea>
            </div>

            <button type="submit"
                    class="w-full py-2 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 transition-colors">
                Submit Request
            </button>
        </form>
    </div>
</div>

<script>
const cards = document.querySelectorAll('.service-card');
const formContainer = document.getElementById('serviceFormContainer');
const formServiceInput = document.getElementById('formService');

cards.forEach(card => {
    card.addEventListener('click', () => {
        const service = card.getAttribute('data-service');
        formServiceInput.value = service;
        formContainer.classList.remove('hidden');
        formContainer.scrollIntoView({ behavior: 'smooth' });
    });
});
</script>

</body>
<footer></footer>
</html>
