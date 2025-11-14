<?php include '../connection/db_connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Student Dashboard</title>
  <style>
    .dashboard-card:hover {
      transform: translateY(-5px);
      transition: transform 0.3s ease;
    }
    .sidebar-link:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }
    .active {
      background-color: rgba(255, 255, 255, 0.15);
    }
  </style>
</head>
<body class="bg-gray-50 flex">

  <!-- Sidebar -->
  <aside class="w-64 bg-blue-900 text-white min-h-screen p-5 shadow-lg">
    <div class="flex items-center mb-8">
      <div class="bg-blue-700 p-2 rounded-lg mr-3">
        <i class="fas fa-graduation-cap text-xl"></i>
      </div>
      <h2 class="text-2xl font-bold">Student Dashboard</h2>
    </div>
    <nav>
      <ul>
        <li class="mb-2">
          <a href="index.php" class="flex items-center p-3 rounded-lg sidebar-link active">
            <i class="fas fa-tachometer-alt mr-3"></i>
            <span>Dashboard</span>
          </a>
        </li>
        <li class="mb-2">
          <a href="students.php" class="flex items-center p-3 rounded-lg sidebar-link">
            <i class="fas fa-user-graduate mr-3"></i>
            <span>Students</span>
          </a>
        </li>
        <li class="mb-2">
          <a href="courses.php" class="flex items-center p-3 rounded-lg sidebar-link">
            <i class="fas fa-book mr-3"></i>
            <span>Courses</span>
          </a>
        </li>
        <li class="mb-2">
          <a href="finance.php" class="flex items-center p-3 rounded-lg sidebar-link">
            <i class="fas fa-money-bill-wave mr-3"></i>
            <span>Finance</span>
          </a>
        </li>
        <li class="mb-2">
          <a href="lecturers.php" class="flex items-center p-3 rounded-lg sidebar-link">
            <i class="fas fa-chalkboard-teacher mr-3"></i>
            <span>Lecturers</span>
          </a>
        </li>
        <li class="mb-2">
          <a href="departments.php" class="flex items-center p-3 rounded-lg sidebar-link">
            <i class="fas fa-building mr-3"></i>
            <span>Departments</span>
          </a>
        </li>
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-800">Student Dashboard</h1>
      <div class="flex items-center space-x-4">
        <div class="relative">
          <input type="text" placeholder="Search..." class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
        <div class="flex items-center">
          <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">AD</div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Total Students -->
      <div class="bg-white p-6 rounded-xl shadow-md dashboard-card border-l-4 border-blue-500">
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-gray-500 text-sm font-medium">Total Students</h3>
            <?php 
              $r = $conn->query("SELECT COUNT(*) AS total FROM students");
              $count = $r->fetch_assoc()['total'];
            ?>
            <p class="text-3xl font-bold text-gray-800 mt-2"><?= $count ?></p>
          </div>
          <div class="bg-blue-100 p-3 rounded-lg">
            <i class="fas fa-user-graduate text-blue-600 text-xl"></i>
          </div>
        </div>
        <div class="mt-4 text-sm text-gray-500">
          <span class="text-green-500"><i class="fas fa-arrow-up"></i> 5.2%</span> from last month
        </div>
      </div>

      <!-- Courses -->
      <div class="bg-white p-6 rounded-xl shadow-md dashboard-card border-l-4 border-green-500">
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-gray-500 text-sm font-medium">Courses</h3>
            <?php 
              $r = $conn->query("SELECT COUNT(*) AS total FROM course");
              $count = $r->fetch_assoc()['total'];
            ?>
            <p class="text-3xl font-bold text-gray-800 mt-2"><?= $count ?></p>
          </div>
          <div class="bg-green-100 p-3 rounded-lg">
            <i class="fas fa-book text-green-600 text-xl"></i>
          </div>
        </div>
        <div class="mt-4 text-sm text-gray-500">
          <span class="text-green-500"><i class="fas fa-arrow-up"></i> 2.1%</span> from last month
        </div>
      </div>

      <!-- Lecturers -->
      <div class="bg-white p-6 rounded-xl shadow-md dashboard-card border-l-4 border-purple-500">
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-gray-500 text-sm font-medium">Lecturers</h3>
            <?php 
              $r = $conn->query("SELECT COUNT(*) AS total FROM lecturers");
              $count = $r->fetch_assoc()['total'];
            ?>
            <p class="text-3xl font-bold text-gray-800 mt-2"><?= $count ?></p>
          </div>
          <div class="bg-purple-100 p-3 rounded-lg">
            <i class="fas fa-chalkboard-teacher text-purple-600 text-xl"></i>
          </div>
        </div>
        <div class="mt-4 text-sm text-gray-500">
          <span class="text-green-500"><i class="fas fa-arrow-up"></i> 3.7%</span> from last month
        </div>
      </div>

      <!-- Pending Payments -->
      <div class="bg-white p-6 rounded-xl shadow-md dashboard-card border-l-4 border-red-500">
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-gray-500 text-sm font-medium">Pending Payments</h3>
            <?php 
              $r = $conn->query("SELECT SUM(balance) AS pending FROM finance");
              $pending = $r->fetch_assoc()['pending'] ?? 0;
            ?>
            <p class="text-3xl font-bold text-gray-800 mt-2">KSh <?= number_format($pending) ?></p>
          </div>
          <div class="bg-red-100 p-3 rounded-lg">
            <i class="fas fa-money-bill-wave text-red-600 text-xl"></i>
          </div>
        </div>
        <div class="mt-4 text-sm text-gray-500">
          <span class="text-red-500"><i class="fas fa-arrow-up"></i> 8.3%</span> from last month
        </div>
      </div>
    </div>

    <!-- Recent Activity and Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Recent Students -->
      <div class="bg-white p-6 rounded-xl shadow-md">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold text-gray-800">Recent Students</h2>
          <a href="students.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
        </div>
        <div class="space-y-4">
          <?php
          $result = $conn->query("SELECT * FROM students ORDER BY id DESC LIMIT 5");
          if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
              echo '
              <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
                <div class="flex items-center">
                  <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                    '.substr($row['name'], 0, 1).'
                  </div>
                  <div class="ml-4">
                    <h4 class="font-medium text-gray-800">'.$row['name'].'</h4>
                    <p class="text-sm text-gray-500">Student ID: '.$row['id'].'</p>
                  </div>
                </div>
                <span class="text-sm px-2 py-1 bg-green-100 text-green-800 rounded-full">Active</span>
              </div>
              ';
            }
          } else {
            echo '<p class="text-gray-500">No students found.</p>';
          }
          ?>
        </div>
      </div>

      <!-- Department Overview -->
      <div class="bg-white p-6 rounded-xl shadow-md">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold text-gray-800">Department Overview</h2>
          <a href="departments.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
        </div>
        <div class="space-y-4">
          <?php
          $result = $conn->query("SELECT * FROM departments LIMIT 4");
          if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
              // Get student count for this department
              $studentCountQuery = $conn->query("SELECT COUNT(*) as count FROM students WHERE department_id = " . $row['id']);
              $studentCount = $studentCountQuery ? $studentCountQuery->fetch_assoc()['count'] : 0;
              
              echo '
              <div class="flex justify-between items-center p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
                <div>
                  <h4 class="font-medium text-gray-800">'.$row['name'].'</h4>
                  <p class="text-sm text-gray-500">'.$studentCount.' students</p>
                </div>
                <div class="w-16 bg-gray-200 rounded-full h-2.5">
                  <div class="bg-blue-600 h-2.5 rounded-full" style="width: '.min($studentCount*5, 100).'%"></div>
                </div>
              </div>
              ';
            }
          } else {
            echo '<p class="text-gray-500">No departments found.</p>';
          }
          ?>
        </div>
      </div>
    </div>
  </main>
</body>
</html>