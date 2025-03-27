<?php
session_start();
include "db.php"; // Include database connection

// Check if the user is logged in as a faculty member
if (!isset($_SESSION['faculty_id'])) {
    header("Location: faculty_login.php");
    exit();
}

// Fetch faculty details
$faculty_id = $_SESSION['faculty_id'];
$query = "SELECT name, profile_picture FROM faculty WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
$faculty = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Faculty Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    .gradient-bg {
      background: linear-gradient(135deg, #4F46E5, #10B981);
    }
    .card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card {
      background: rgba(255, 255, 255, 0.9);
      border-radius: 12px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      padding: 20px;
      text-align: center;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    .dark-mode {
      background-color: #1E293B;
      color: #ffffff;
    }
    .dark-mode .bg-white {
      background-color: #334155;
      color: #ffffff;
    }
  </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900">

<!-- Navbar -->
<nav class="gradient-bg text-white py-4 px-6 shadow-lg">
  <div class="container mx-auto flex justify-between items-center">
    
    <!-- Logo / Title -->
    <h1 class="text-2xl font-bold flex items-center">
      📌 Faculty Dashboard
    </h1>

    <!-- Mobile Menu Button -->
    <button id="menuToggle" class="md:hidden p-2 rounded-lg bg-white bg-opacity-10 hover:bg-opacity-20 transition">
      ☰
    </button>

    <!-- Desktop Menu -->
    <div id="navMenu" class="hidden md:flex items-center space-x-6">
      <!-- Dark Mode Toggle -->
      <button onclick="toggleDarkMode()" class="p-2 bg-white bg-opacity-10 rounded-lg hover:bg-opacity-20 transition">
        🌙
      </button>

      <!-- Profile Section -->
      <div class="flex items-center space-x-3">
        <img src="<?php echo $faculty['profile_picture']; ?>" alt="Profile" class="w-12 h-12 rounded-full border-2 border-white shadow-lg">
        <span class="font-semibold text-lg"><?php echo $faculty['name']; ?></span>
      </div>

      <!-- Logout Button -->
      <a href="faculty_logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
        Logout
      </a>
    </div>
  </div>

  <!-- Mobile Menu (Dropdown) -->
  <div id="mobileMenu" class="hidden md:hidden bg-gray-800 text-white px-6 py-4 mt-2 rounded-lg">
    <ul class="space-y-4 text-center">
      <li>
        <button onclick="toggleDarkMode()" class="w-full p-2 bg-white bg-opacity-10 rounded-lg hover:bg-opacity-20 transition">
          🌙 Dark Mode
        </button>
      </li>
      <li class="flex flex-col items-center">
        <img src="<?php echo $faculty['profile_picture']; ?>" alt="Profile" class="w-16 h-16 rounded-full border-2 border-white shadow-lg">
        <span class="font-semibold text-lg mt-2"><?php echo $faculty['name']; ?></span>
      </li>
      <li>
        <a href="faculty_logout.php" class="block bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
          Logout
        </a>
      </li>
    </ul>
  </div>
</nav>

<!-- Main Content -->
<div class="container mx-auto p-6">

 <!-- Welcome Card -->
 <div class="container mx-auto p-6 flex justify-center">
      <div class="card animate-fade-in">
        <img src="<?php echo $faculty['profile_picture']; ?>" alt="Profile Picture" class="w-24 h-24 rounded-full border-4 border-white shadow-lg mx-auto">
        <h2 class="text-2xl font-bold mt-4 text-gray-800 dark:text-white">Welcome, <?php echo $faculty['name']; ?>!</h2>
        <p class="text-gray-600 dark:text-gray-300 mt-2">You are now in your faculty dashboard. Manage your tasks efficiently!</p>
      </div>
    </div>
  <!-- Cards Section -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    <!-- Mark Attendance -->
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md card">
      <div class="flex justify-center">
        <span class="text-5xl text-green-500"><i class="fas fa-check-circle"></i></span>
      </div>
      <h3 class="text-xl font-semibold mt-4 text-center text-gray-800 dark:text-white">Mark Attendance</h3>
      <p class="text-center text-gray-500 dark:text-gray-300 mt-2">Easily mark attendance for your students.</p>
      <div class="flex justify-center mt-4">
        <a href="mark_attendance.php" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition">
          Go
        </a>
      </div>
    </div>

    <!-- View Attendance -->
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md card">
      <div class="flex justify-center">
        <span class="text-5xl text-blue-500"><i class="fas fa-user-check"></i></span>
      </div>
      <h3 class="text-xl font-semibold mt-4 text-center text-gray-800 dark:text-white">View Time Table</h3>
      <p class="text-center text-gray-500 dark:text-gray-300 mt-2">Check Time Table anytime.</p>
      <div class="flex justify-center mt-4">
        <a href="time_table.php" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition">
          View
        </a>
      </div>
    </div>

    <!-- Logout -->
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md card">
      <div class="flex justify-center">
        <span class="text-5xl text-red-500"><i class="fas fa-sign-out-alt"></i></span>
      </div>
      <h3 class="text-xl font-semibold mt-4 text-center text-gray-800 dark:text-white">Logout</h3>
      <p class="text-center text-gray-500 dark:text-gray-300 mt-2">Securely log out of your account.</p>
      <div class="flex justify-center mt-4">
        <a href="faculty_logout.php" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg transition">
          Logout
        </a>
      </div>
    </div>

     

  </div>


<!-- External Tools -->
<div class="mt-8 p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
  <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-white flex items-center">
    <i class="fas fa-tools text-blue-500 mr-2"></i> Useful Tools
  </h2>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- Google Classroom -->
    <a href="https://classroom.google.com" target="_blank" class="p-6 bg-gray-100 dark:bg-gray-700 rounded-lg shadow-md hover:shadow-xl transform transition-all duration-300 hover:-translate-y-2">
      <div class="flex flex-col items-center text-center">
        <span class="text-5xl text-green-500"><i class="fas fa-chalkboard-teacher"></i></span>
        <h3 class="mt-4 text-lg font-semibold text-gray-800 dark:text-white">Google Classroom</h3>
        <p class="mt-2 text-gray-500 dark:text-gray-300">Manage classes and assignments easily.</p>
      </div>
    </a>

    <!-- Microsoft Teams -->
    <a href="https://teams.microsoft.com" target="_blank" class="p-6 bg-gray-100 dark:bg-gray-700 rounded-lg shadow-md hover:shadow-xl transform transition-all duration-300 hover:-translate-y-2">
      <div class="flex flex-col items-center text-center">
        <span class="text-5xl text-blue-500"><i class="fab fa-microsoft"></i></span>
        <h3 class="mt-4 text-lg font-semibold text-gray-800 dark:text-white">Microsoft Teams</h3>
        <p class="mt-2 text-gray-500 dark:text-gray-300">Collaborate and communicate effectively.</p>
      </div>
    </a>

    <!-- Zoom -->
    <a href="https://zoom.us" target="_blank" class="p-6 bg-gray-100 dark:bg-gray-700 rounded-lg shadow-md hover:shadow-xl transform transition-all duration-300 hover:-translate-y-2">
      <div class="flex flex-col items-center text-center">
        <span class="text-5xl text-blue-400"><i class="fas fa-video"></i></span>
        <h3 class="mt-4 text-lg font-semibold text-gray-800 dark:text-white">Zoom</h3>
        <p class="mt-2 text-gray-500 dark:text-gray-300">Host virtual meetings seamlessly.</p>
      </div>
    </a>

  </div>
</div>

</div>


<script>
  function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
  }

  if (localStorage.getItem('darkMode') === 'true') {
    document.body.classList.add('dark-mode');
  }


  
</script>

<!-- JavaScript for Mobile Menu -->
<script>
  document.getElementById('menuToggle').addEventListener('click', function () {
    document.getElementById('mobileMenu').classList.toggle('hidden');
  });
</script>


</body>
</html>