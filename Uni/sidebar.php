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
        <a href="index.php" class="flex items-center p-3 rounded-lg sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
          <i class="fas fa-tachometer-alt mr-3"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li class="mb-2">
        <a href="students.php" class="flex items-center p-3 rounded-lg sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'students.php' ? 'active' : '' ?>">
          <i class="fas fa-user-graduate mr-3"></i>
          <span>Students</span>
        </a>
      </li>
      <li class="mb-2">
        <a href="courses.php" class="flex items-center p-3 rounded-lg sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'courses.php' ? 'active' : '' ?>">
          <i class="fas fa-book mr-3"></i>
          <span>Courses</span>
        </a>
      </li>
      <li class="mb-2">
        <a href="finance.php" class="flex items-center p-3 rounded-lg sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'finance.php' ? 'active' : '' ?>">
          <i class="fas fa-money-bill-wave mr-3"></i>
          <span>Finance</span>
        </a>
      </li>
      <li class="mb-2">
        <a href="lecturers.php" class="flex items-center p-3 rounded-lg sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'lecturers.php' ? 'active' : '' ?>">
          <i class="fas fa-chalkboard-teacher mr-3"></i>
          <span>Lecturers</span>
        </a>
      </li>
      <li class="mb-2">
        <a href="departments.php" class="flex items-center p-3 rounded-lg sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'departments.php' ? 'active' : '' ?>">
          <i class="fas fa-building mr-3"></i>
          <span>Departments</span>
        </a>
      </li>
    </ul>
  </nav>
</aside>