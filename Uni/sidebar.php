<?php
// Check if session is already started before starting it in sidebar.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!-- Sidebar -->
<div class="w-64 bg-white shadow-lg min-h-screen">
    <div class="p-6 border-b">
        <h2 class="text-xl font-bold text-gray-800">Student Dashboard</h2>
    </div>
    <nav class="mt-6">
        <a href="dashboard.php" class="block py-3 px-6 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200">
            <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
        </a>
        <a href="admission.php" class="block py-3 px-6 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200">
            <i class="fas fa-user-graduate mr-3"></i>Admission
        </a>
        <a href="students.php" class="block py-3 px-6 bg-blue-50 text-blue-600 border-r-2 border-blue-600">
            <i class="fas fa-user-circle mr-3"></i>My Information
        </a>
        <a href="courses.php" class="block py-3 px-6 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200">
            <i class="fas fa-book mr-3"></i>My Courses
        </a>
        <a href="attendance.php" class="block py-3 px-6 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200">
            <i class="fas fa-calendar-check mr-3"></i>Attendance
        </a>
        <a href="finance.php" class="block py-3 px-6 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200">
            <i class="fas fa-money-bill-wave mr-3"></i>Finance
        </a>
        <a href="departments.php" class="block py-3 px-6 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200">
            <i class="fas fa-building mr-3"></i>Departments
        </a>
        <a href="lecturers.php" class="block py-3 px-6 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200">
            <i class="fas fa-chalkboard-teacher mr-3"></i>Lecturers
        </a>
        <a href="logout.php" class="block py-3 px-6 text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors duration-200 mt-10">
            <i class="fas fa-sign-out-alt mr-3"></i>Logout
        </a>
    </nav>
</div>