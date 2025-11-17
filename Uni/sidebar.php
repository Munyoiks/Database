<?php
// student/sidebar.php
session_start();
// Check if student is logged in, if not redirect to student login
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}
?>

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
                <a href="index.php" class="flex items-center p-3 rounded-lg sidebar-link">
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
                <a href="departments.php" class="flex items-center p-3 rounded-lg sidebar-link active">
                    <i class="fas fa-building mr-3"></i>
                    <span>Departments</span>
                </a>
            </li>
            <li class="mb-2">
                <a href="lecturers.php" class="flex items-center p-3 rounded-lg sidebar-link">
                    <i class="fas fa-chalkboard-teacher mr-3"></i>
                    <span>Lecturers</span>
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
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['student_name'] ?? 'Student'); ?></span>
        </div>
        <div class="text-xs text-blue-300 mt-1">
            <?php echo htmlspecialchars($_SESSION['admission_number'] ?? ''); ?>
        </div>
    </div>
</aside>

<style>
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
    .sidebar-link {
        transition: background-color 0.3s;
    }
    .sidebar-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
    .sidebar-link.active {
        background-color: rgba(255, 255, 255, 0.15);
    }
</style>