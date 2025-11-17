<?php 
session_start();
require_once 'connection/db_connect.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

// Get student details
$student_id = $_SESSION['student_id'];
$student_stmt = $conn->prepare("
    SELECT s.*, c.course_name, c.course_code, d.department_name 
    FROM students s 
    LEFT JOIN course c ON s.course_id = c.course_id 
    LEFT JOIN department d ON s.department_id = d.department_id 
    WHERE s.student_id = ?
");
$student_stmt->bind_param("i", $student_id);
$student_stmt->execute();
$student_result = $student_stmt->get_result();
$student = $student_result->fetch_assoc();

// Get finance information
$finance_stmt = $conn->prepare("
    SELECT * FROM finance 
    WHERE student_id = ? 
    ORDER BY academic_year DESC, semester DESC 
    LIMIT 5
");
$finance_stmt->bind_param("i", $student_id);
$finance_stmt->execute();
$finance_result = $finance_stmt->get_result();
$finance_records = [];
while ($row = $finance_result->fetch_assoc()) {
    $finance_records[] = $row;
}

// Get course count
$course_stmt = $conn->prepare("
    SELECT COUNT(*) as course_count 
    FROM student_course 
    WHERE student_id = ? AND status = 'enrolled'
");
$course_stmt->bind_param("i", $student_id);
$course_stmt->execute();
$course_result = $course_stmt->get_result();
$course_count = $course_result->fetch_assoc()['course_count'] ?? 0;

// Get pending fees
$pending_stmt = $conn->prepare("
    SELECT SUM(balance) as pending_fees 
    FROM finance 
    WHERE student_id = ? AND payment_status IN ('pending', 'partial')
");
$pending_stmt->bind_param("i", $student_id);
$pending_stmt->execute();
$pending_result = $pending_stmt->get_result();
$pending_fees = $pending_result->fetch_assoc()['pending_fees'] ?? 0;
?>

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
    /* Custom scrollbar for sidebar */
    .sidebar-scroll {
      height: calc(100vh - 8rem);
      overflow-y: auto;
    }
    .sidebar-scroll::-webkit-scrollbar {
      width: 6px;
    }
    .sidebar-scroll::-webkit-scrollbar-track {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 3px;
    }
    .sidebar-scroll::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.3);
      border-radius: 3px;
    }
    .sidebar-scroll::-webkit-scrollbar-thumb:hover {
      background: rgba(255, 255, 255, 0.5);
    }
    /* Custom scrollbar for main content */
    .main-scroll {
      height: calc(100vh - 2rem);
      overflow-y: auto;
    }
    .main-scroll::-webkit-scrollbar {
      width: 8px;
    }
    .main-scroll::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 4px;
    }
    .main-scroll::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 4px;
    }
    .main-scroll::-webkit-scrollbar-thumb:hover {
      background: #a8a8a8;
    }
  </style>
</head>
<body class="bg-gray-50 flex overflow-hidden">

  <!-- Sidebar -->
  <aside class="w-64 bg-blue-900 text-white min-h-screen p-5 shadow-lg flex flex-col">
    <div class="flex items-center mb-8">
      <div class="bg-blue-700 p-2 rounded-lg mr-3">
        <i class="fas fa-graduation-cap text-xl"></i>
      </div>
      <h2 class="text-2xl font-bold">Student Dashboard</h2>
    </div>
    <nav class="sidebar-scroll flex-1">
      <ul>
        <li class="mb-2">
          <a href="index.php" class="flex items-center p-3 rounded-lg sidebar-link active">
            <i class="fas fa-tachometer-alt mr-3"></i>
            <span>Dashboard</span>
          </a>
        </li>
        <li class="mb-2">
          <a href="admission.php" class="flex items-center p-3 rounded-lg sidebar-link">
            <i class="fas fa-file-alt mr-3"></i>
            <span>Admission</span>
          </a>
        </li>
        <li class="mb-2">
          <a href="students.php" class="flex items-center p-3 rounded-lg sidebar-link">
            <i class="fas fa-user-graduate mr-3"></i>
            <span>My Information</span>
          </a>
        </li>
        <li class="mb-2">
          <a href="courses.php" class="flex items-center p-3 rounded-lg sidebar-link">
            <i class="fas fa-book mr-3"></i>
            <span>My Courses</span>
          </a>
        </li>
        <li class="mb-2">
          <a href="attendance.php" class="flex items-center p-3 rounded-lg sidebar-link">
            <i class="fas fa-calendar-check mr-3"></i>
            <span>Attendance</span>
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
        <li class="mb-2">
          <a href="logout.php" class="flex items-center p-3 rounded-lg sidebar-link text-red-200 hover:bg-red-800">
            <i class="fas fa-sign-out-alt mr-3"></i>
            <span>Logout</span>
          </a>
        </li>
      </ul>
    </nav>
    <div class="pt-4 border-t border-blue-700 mt-4">
      <div class="flex items-center text-sm text-blue-200">
        <i class="fas fa-user-circle mr-2"></i>
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['student_name']); ?></span>
      </div>
      <div class="text-xs text-blue-300 mt-1">
        <?php echo htmlspecialchars($_SESSION['admission_number']); ?>
      </div>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-8 main-scroll">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-800">Student Dashboard</h1>
      <div class="flex items-center space-x-4">
        <div class="relative">
          <input type="text" placeholder="Search..." class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
        <div class="flex items-center space-x-3">
          <div class="text-right">
            <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($_SESSION['student_name']); ?></p>
            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($student['course_name'] ?? 'Not assigned'); ?></p>
          </div>
          <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
            <?php 
              $names = explode(' ', $_SESSION['student_name']);
              $initials = '';
              foreach ($names as $name) {
                $initials .= strtoupper(substr($name, 0, 1));
              }
              echo substr($initials, 0, 2);
            ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- My Courses -->
      <div class="bg-white p-6 rounded-xl shadow-md dashboard-card border-l-4 border-blue-500">
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-gray-500 text-sm font-medium">My Courses</h3>
            <p class="text-3xl font-bold text-gray-800 mt-2"><?= $course_count ?></p>
          </div>
          <div class="bg-blue-100 p-3 rounded-lg">
            <i class="fas fa-book text-blue-600 text-xl"></i>
          </div>
        </div>
        <div class="mt-4 text-sm text-gray-500">
          <span class="text-green-500"><i class="fas fa-arrow-up"></i> Enrolled courses</span> this semester
        </div>
      </div>

      <!-- Attendance -->
      <div class="bg-white p-6 rounded-xl shadow-md dashboard-card border-l-4 border-green-500">
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-gray-500 text-sm font-medium">Attendance</h3>
            <p class="text-3xl font-bold text-gray-800 mt-2">87%</p>
          </div>
          <div class="bg-green-100 p-3 rounded-lg">
            <i class="fas fa-calendar-check text-green-600 text-xl"></i>
          </div>
        </div>
        <div class="mt-4 text-sm text-gray-500">
          <span class="text-green-500"><i class="fas fa-check-circle"></i> Good</span> standing
        </div>
      </div>

      <!-- GPA -->
      <div class="bg-white p-6 rounded-xl shadow-md dashboard-card border-l-4 border-purple-500">
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-gray-500 text-sm font-medium">Current GPA</h3>
            <p class="text-3xl font-bold text-gray-800 mt-2">3.75</p>
          </div>
          <div class="bg-purple-100 p-3 rounded-lg">
            <i class="fas fa-chart-line text-purple-600 text-xl"></i>
          </div>
        </div>
        <div class="mt-4 text-sm text-gray-500">
          <span class="text-green-500"><i class="fas fa-arrow-up"></i> 0.15</span> from last term
        </div>
      </div>

      <!-- Pending Fees -->
      <div class="bg-white p-6 rounded-xl shadow-md dashboard-card border-l-4 border-red-500">
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-gray-500 text-sm font-medium">Pending Fees</h3>
            <p class="text-3xl font-bold text-gray-800 mt-2">KSh <?= number_format($pending_fees) ?></p>
          </div>
          <div class="bg-red-100 p-3 rounded-lg">
            <i class="fas fa-money-bill-wave text-red-600 text-xl"></i>
          </div>
        </div>
        <div class="mt-4 text-sm text-gray-500">
          <span class="text-red-500">Due in 15 days</span>
        </div>
      </div>
    </div>

    <!-- Recent Activity and Upcoming Events -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Upcoming Assignments -->
      <div class="bg-white p-6 rounded-xl shadow-md">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold text-gray-800">Upcoming Assignments</h2>
          <a href="courses.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
        </div>
        <div class="space-y-4">
          <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
            <div class="flex items-center">
              <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                <i class="fas fa-file-alt"></i>
              </div>
              <div class="ml-4">
                <h4 class="font-medium text-gray-800">Programming Project</h4>
                <p class="text-sm text-gray-500">CS101 - Due Mar 25</p>
              </div>
            </div>
            <span class="text-sm px-2 py-1 bg-red-100 text-red-800 rounded-full">Urgent</span>
          </div>
          <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
            <div class="flex items-center">
              <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                <i class="fas fa-book"></i>
              </div>
              <div class="ml-4">
                <h4 class="font-medium text-gray-800">Calculus Homework</h4>
                <p class="text-sm text-gray-500">MATH201 - Due Mar 28</p>
              </div>
            </div>
            <span class="text-sm px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full">Pending</span>
          </div>
          <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
            <div class="flex items-center">
              <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                <i class="fas fa-flask"></i>
              </div>
              <div class="ml-4">
                <h4 class="font-medium text-gray-800">Physics Lab Report</h4>
                <p class="text-sm text-gray-500">PHY101 - Due Apr 2</p>
              </div>
            </div>
            <span class="text-sm px-2 py-1 bg-blue-100 text-blue-800 rounded-full">Upcoming</span>
          </div>
        </div>
      </div>

      <!-- Recent Grades -->
      <div class="bg-white p-6 rounded-xl shadow-md">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold text-gray-800">Recent Grades</h2>
          <a href="courses.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
        </div>
        <div class="space-y-4">
          <div class="flex justify-between items-center p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
            <div>
              <h4 class="font-medium text-gray-800">CS101 - Midterm Exam</h4>
              <p class="text-sm text-gray-500">Programming Fundamentals</p>
            </div>
            <div class="text-right">
              <span class="text-lg font-bold text-green-600">85%</span>
              <p class="text-sm text-gray-500">A-</p>
            </div>
          </div>
          <div class="flex justify-between items-center p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
            <div>
              <h4 class="font-medium text-gray-800">MATH201 - Quiz 2</h4>
              <p class="text-sm text-gray-500">Integration Techniques</p>
            </div>
            <div class="text-right">
              <span class="text-lg font-bold text-blue-600">78%</span>
              <p class="text-sm text-gray-500">B+</p>
            </div>
          </div>
          <div class="flex justify-between items-center p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
            <div>
              <h4 class="font-medium text-gray-800">ENG101 - Essay</h4>
              <p class="text-sm text-gray-500">Academic Writing</p>
            </div>
            <div class="text-right">
              <span class="text-lg font-bold text-purple-600">92%</span>
              <p class="text-sm text-gray-500">A</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white rounded-xl shadow-md overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h2 class="text-xl font-semibold text-gray-800">Quick Actions</h2>
      </div>
      <div class="p-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <a href="courses.php" class="bg-blue-50 hover:bg-blue-100 p-4 rounded-lg text-center transition-colors duration-200">
            <i class="fas fa-book text-blue-600 text-2xl mb-2"></i>
            <p class="text-sm font-medium text-gray-800">Course Materials</p>
          </a>
          <a href="attendance.php" class="bg-green-50 hover:bg-green-100 p-4 rounded-lg text-center transition-colors duration-200">
            <i class="fas fa-calendar-check text-green-600 text-2xl mb-2"></i>
            <p class="text-sm font-medium text-gray-800">View Attendance</p>
          </a>
          <a href="finance.php" class="bg-purple-50 hover:bg-purple-100 p-4 rounded-lg text-center transition-colors duration-200">
            <i class="fas fa-credit-card text-purple-600 text-2xl mb-2"></i>
            <p class="text-sm font-medium text-gray-800">Pay Fees</p>
          </a>
          <a href="admission.php" class="bg-orange-50 hover:bg-orange-100 p-4 rounded-lg text-center transition-colors duration-200">
            <i class="fas fa-file-alt text-orange-600 text-2xl mb-2"></i>
            <p class="text-sm font-medium text-gray-800">Admission Status</p>
          </a>
        </div>
      </div>
    </div>
  </main>
</body>
</html>