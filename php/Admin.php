<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Page</title>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />

    <!-- Tailwind (you had a weird @tailwindcss/browser script, this is official CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<?php
session_start();
require 'config.php'; // connect to DB

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Index.php");
    exit();
}

// Fetch service requests from DB (for the table)
$query = "SELECT sr.id, u.email AS student_email, sr.service, sr.preferred_date, sr.notes, sr.created_at 
          FROM service_requests sr
          JOIN users u ON sr.user_id = u.id
          ORDER BY sr.created_at DESC";
$result = $conn->query($query);

// === Stats for cards and charts ===
// Total students
$students_count = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='student'")->fetch_assoc()['total'];
// Total service requests
$requests_count = $conn->query("SELECT COUNT(*) AS total FROM service_requests")->fetch_assoc()['total'];

// Requests by service type
$service_data = [];
$service_query = $conn->query("SELECT service, COUNT(*) as count FROM service_requests GROUP BY service");
while ($row = $service_query->fetch_assoc()) {
    $service_data[$row['service']] = (int)$row['count'];
}

// Students registered by month (last 6 months)
$student_month_data = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i month"));
    $res = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='student' AND DATE_FORMAT(created_at, '%Y-%m') = '$month'");
    $count = $res->fetch_assoc()['count'];
    // Format month nicely like "Mar 2025"
    $month_label = date('M Y', strtotime($month . '-01'));
    $student_month_data[$month_label] = (int)$count;
}
?>

<body class="bg-sky-100 min-h-screen">
    <header class="fixed top-0 left-0 w-full z-10">
        <nav class="bg-gradient-to-t from-sky-300 to-orange-300 border-b border-white h-20">
            <div class="max-w-7xl mx-auto px-4 flex justify-between items-center text-white h-full">
                <div class="flex items-center space-x-3">
                    <img src="../images/BUp.png" alt="BU Logo" class="h-16 w-auto" />
                    <h1 class="text-xl font-bold">BICOL UNIVERSITY POLANGUI CLINIC - ADMIN</h1>
                </div>
                <form class="flex border border-white rounded" action="action_page.php">
                    <input
                        type="text"
                        placeholder="Search.."
                        name="search"
                        class="px-2 py-1 text-black rounded-l focus:outline-none" />
                    <button type="submit" class="px-3 bg-[#009bde] hover:bg-[#0082c8] rounded-r">
                        <i class="fa fa-search text-white"></i>
                    </button>
                </form>
            </div>
        </nav>
    </header>

    <!-- Sidebar -->
    <div
        class="group fixed top-20 left-0 h-[calc(100vh-80px)] w-14 hover:w-48 bg-sky-300 transition-all duration-300 overflow-x-hidden z-20 flex flex-col">
        <a
            href="Admin.php"
            class="flex items-center gap-4 px-4 py-3 text-black hover:text-white hover:bg-blue-500 transition">
            <i class="fa fa-home text-lg w-6"></i>
            <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition">Home</span>
        </a>

        <a
            href="student.php"
            class="flex items-center gap-4 px-4 py-3 text-black hover:text-white hover:bg-blue-500 transition">
            <i class="fa fa-user text-lg w-6"></i>
            <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition">Students</span>
        </a>

        <a
            href="requests.php"
            class="flex items-center gap-4 px-4 py-3 text-black hover:text-white hover:bg-blue-500 transition">
            <i class="fa fa-calendar text-lg w-6"></i>
            <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition">Requests</span>
        </a>

        <div class="flex-grow"></div>

        <a
            href="logout.php"
            class="flex items-center gap-4 px-4 py-3 text-red-400 hover:text-white hover:bg-red-600 transition">
            <i class="fa fa-sign-out text-lg w-6"></i>
            <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition">Logout</span>
        </a>
    </div>

    <!-- Main content -->
    <main class="ml-14 transition-all duration-300 pt-24 pb-20 min-h-[calc(100vh-80px)] w-full">

        <!-- Stats + Charts -->
        <section class="max-w-7xl mx-auto px-6 mb-10
               grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Students Registered Card + count animation -->
            <div
                id="studentsCard"
                class="bg-white shadow-md rounded-lg p-6 flex flex-col items-center border-l-4 border-blue-500">
                <i class="fa fa-users text-4xl text-blue-500 mb-2"></i>
                <p class="text-gray-500 text-sm">Total Students</p>
                <h2 id="studentsCount" class="text-3xl font-bold">0</h2>
            </div>

            <!-- Service Requests Card + count animation -->
            <div
                id="requestsCard"
                class="bg-white shadow-md rounded-lg p-6 flex flex-col items-center border-l-4 border-green-500">
                <i class="fa fa-calendar text-4xl text-green-500 mb-2"></i>
                <p class="text-gray-500 text-sm">Total Requests</p>
                <h2 id="requestsCount" class="text-3xl font-bold">0</h2>
            </div>

            <!-- Chart 1: Service Requests by Type -->
            <div class="bg-white shadow-md rounded-lg p-4 border-l-4 border-purple-500">
                <h3 class="font-semibold mb-2 text-center">Requests by Service Type</h3>
                <canvas id="serviceChart" height="180"></canvas>
            </div>

            <!-- Chart 2: Students Registered Last 6 Months -->
            <div class="bg-white shadow-md rounded-lg p-4 border-l-4 border-orange-500">
                <h3 class="font-semibold mb-2 text-center">Students Registered (Last 6 Months)</h3>
                <canvas id="studentsChart" height="180"></canvas>
            </div>
        </section>


    <script>
        // Animate count up for numbers
        function animateCount(id, target) {
            let el = document.getElementById(id);
            let count = 0;
            let step = Math.ceil(target / 100);
            let interval = setInterval(() => {
                count += step;
                if (count >= target) {
                    count = target;
                    clearInterval(interval);
                }
                el.textContent = count.toLocaleString();
            }, 20);
        }

        // Data from PHP for charts
        const serviceLabels = <?= json_encode(array_keys($service_data)) ?>;
        const serviceCounts = <?= json_encode(array_values($service_data)) ?>;

        const studentLabels = <?= json_encode(array_keys($student_month_data)) ?>;
        const studentCounts = <?= json_encode(array_values($student_month_data)) ?>;

        document.addEventListener('DOMContentLoaded', () => {
            animateCount('studentsCount', <?= $students_count ?>);
            animateCount('requestsCount', <?= $requests_count ?>);

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

            // Chart 1: Pie chart for service requests by type
            new Chart(document.getElementById('serviceChart'), {
                type: 'pie',
                data: {
                    labels: serviceLabels,
                    datasets: [{
                        data: serviceCounts,
                        backgroundColor: [
                            '#3b82f6',
                            '#10b981',
                            '#f59e0b',
                            '#8b5cf6',
                            '#ef4444',
                            '#14b8a6',
                            '#eab308',
                        ],
                    }, ],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            enabled: true
                        },
                    },
                },
            });

            // Chart 2: Bar chart for students registered by month
            new Chart(document.getElementById('studentsChart'), {
                type: 'bar',
                data: {
                    labels: studentLabels,
                    datasets: [{
                        label: 'Students',
                        data: studentCounts,
                        backgroundColor: '#3b82f6',
                    }, ],
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            precision: 0
                        },
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: true
                        },
                    },
                },
            });
        });
    </script>
</body>

</html>