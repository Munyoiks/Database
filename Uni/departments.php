<?php include '../connection/db_connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Departments Directory - Student Dashboard</title>
</head>
<body class="bg-gray-50 flex">
  <?php include 'sidebar.php'; ?>

  <!-- Main Content -->
  <main class="flex-1 p-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-800">Departments Directory</h1>
      <div class="flex space-x-4">
        <div class="relative">
          <input type="text" placeholder="Search departments..." class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
          <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
      </div>
    </div>

    <!-- Statistics Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
      <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
        <div class="flex items-center">
          <div class="bg-indigo-100 p-3 rounded-lg mr-4">
            <i class="fas fa-building text-indigo-600 text-xl"></i>
          </div>
          <div>
            <p class="text-sm text-gray-600">Total Departments</p>
            <?php 
              $r = $conn->query("SELECT COUNT(*) AS total FROM department");
              $count = $r->fetch_assoc()['total'];
            ?>
            <p class="text-2xl font-bold text-gray-800"><?= $count ?></p>
          </div>
        </div>
      </div>
      
      <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
        <div class="flex items-center">
          <div class="bg-blue-100 p-3 rounded-lg mr-4">
            <i class="fas fa-user-graduate text-blue-600 text-xl"></i>
          </div>
          <div>
            <p class="text-sm text-gray-600">Total Students</p>
            <?php 
              $r = $conn->query("SELECT COUNT(*) AS total FROM students");
              $count = $r->fetch_assoc()['total'];
            ?>
            <p class="text-2xl font-bold text-gray-800"><?= $count ?></p>
          </div>
        </div>
      </div>
      
      <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
        <div class="flex items-center">
          <div class="bg-green-100 p-3 rounded-lg mr-4">
            <i class="fas fa-book text-green-600 text-xl"></i>
          </div>
          <div>
            <p class="text-sm text-gray-600">Total Courses</p>
            <?php 
              $r = $conn->query("SELECT COUNT(*) AS total FROM course");
              $count = $r->fetch_assoc()['total'];
            ?>
            <p class="text-2xl font-bold text-gray-800"><?= $count ?></p>
          </div>
        </div>
      </div>
      
      <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
        <div class="flex items-center">
          <div class="bg-purple-100 p-3 rounded-lg mr-4">
            <i class="fas fa-chalkboard-teacher text-purple-600 text-xl"></i>
          </div>
          <div>
            <p class="text-sm text-gray-600">Total Lecturers</p>
            <?php 
              $r = $conn->query("SELECT COUNT(*) AS total FROM lecturers");
              $count = $r->fetch_assoc()['total'];
            ?>
            <p class="text-2xl font-bold text-gray-800"><?= $count ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Department Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
      <?php
      $result = $conn->query("SELECT * FROM department");
      if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          // Get counts for each department
          $studentCountQuery = $conn->query("SELECT COUNT(*) as count FROM students WHERE department_id = " . $row['id']);
          $studentCount = $studentCountQuery ? $studentCountQuery->fetch_assoc()['count'] : 0;
          
          $courseCountQuery = $conn->query("SELECT COUNT(*) as count FROM course WHERE department_id = " . $row['id']);
          $courseCount = $courseCountQuery ? $courseCountQuery->fetch_assoc()['count'] : 0;
          
          $lecturerCountQuery = $conn->query("SELECT COUNT(*) as count FROM lecturers WHERE department = '" . $row['name'] . "'");
          $lecturerCount = $lecturerCountQuery ? $lecturerCountQuery->fetch_assoc()['count'] : 0;
          
          // Color variations for different departments
          $colors = [
            'bg-gradient-to-r from-blue-500 to-blue-600',
            'bg-gradient-to-r from-green-500 to-green-600',
            'bg-gradient-to-r from-purple-500 to-purple-600',
            'bg-gradient-to-r from-orange-500 to-orange-600',
            'bg-gradient-to-r from-red-500 to-red-600',
            'bg-gradient-to-r from-indigo-500 to-indigo-600'
          ];
          $colorClass = $colors[array_rand($colors)];
          
          echo '
          <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 hover:shadow-lg transition-shadow duration-300">
            <div class="'.$colorClass.' p-6 text-white">
              <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-full bg-white bg-opacity-20 flex items-center justify-center text-white font-bold text-lg">
                  '.substr($row['name'], 0, 1).'
                </div>
                <span class="bg-white bg-opacity-20 text-white text-xs px-3 py-1 rounded-full">'.$studentCount.' Students</span>
              </div>
              <h3 class="text-xl font-bold mb-2">'.$row['name'].'</h3>
              <p class="text-white text-opacity-90 text-sm">'.$row['description'].'</p>
            </div>
            <div class="p-6">
              <div class="space-y-4">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <i class="fas fa-user-tie text-gray-400 mr-3"></i>
                    <span class="text-sm text-gray-600">Department Head</span>
                  </div>
                  <span class="text-sm font-medium text-gray-800">'.$row['head'].'</span>
                </div>
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <i class="fas fa-book text-gray-400 mr-3"></i>
                    <span class="text-sm text-gray-600">Courses Offered</span>
                  </div>
                  <span class="text-sm font-medium text-gray-800">'.$courseCount.'</span>
                </div>
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <i class="fas fa-chalkboard-teacher text-gray-400 mr-3"></i>
                    <span class="text-sm text-gray-600">Lecturers</span>
                  </div>
                  <span class="text-sm font-medium text-gray-800">'.$lecturerCount.'</span>
                </div>
              </div>
              
              <!-- Progress bar for student capacity -->
              <div class="mt-6">
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                  <span>Student Capacity</span>
                  <span>'.min($studentCount, 100).'%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div class="'.$colorClass.' h-2 rounded-full" style="width: '.min($studentCount, 100).'%"></div>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
              <div class="flex justify-between">
                <button class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center">
                  <i class="fas fa-eye mr-2"></i> View Details
                </button>
                <button class="text-green-600 hover:text-green-800 font-medium text-sm flex items-center">
                  <i class="fas fa-list mr-2"></i> Courses
                </button>
                <button class="text-purple-600 hover:text-purple-800 font-medium text-sm flex items-center">
                  <i class="fas fa-users mr-2"></i> Lecturers
                </button>
              </div>
            </div>
          </div>
          ';
        }
      } else {
        echo '
        <div class="col-span-3 text-center py-12">
          <div class="bg-white rounded-xl shadow-sm p-8 max-w-md mx-auto">
            <i class="fas fa-building text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Departments Found</h3>
            <p class="text-gray-500 mb-4">There are currently no departments in the system.</p>
          </div>
        </div>';
      }
      ?>
    </div>

    <!-- Department Information Section -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 bg-indigo-50">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
          <i class="fas fa-info-circle mr-2 text-indigo-600"></i>
          About University Departments
        </h2>
      </div>
      <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div>
            <h3 class="text-lg font-medium text-gray-800 mb-4">Department Services</h3>
            <ul class="space-y-3 text-gray-600">
              <li class="flex items-start">
                <i class="fas fa-graduation-cap text-green-500 mt-1 mr-3"></i>
                <span>Academic advising and course selection guidance</span>
              </li>
              <li class="flex items-start">
                <i class="fas fa-book-open text-blue-500 mt-1 mr-3"></i>
                <span>Research opportunities and project supervision</span>
              </li>
              <li class="flex items-start">
                <i class="fas fa-briefcase text-purple-500 mt-1 mr-3"></i>
                <span>Career counseling and internship placements</span>
              </li>
              <li class="flex items-start">
                <i class="fas fa-handshake text-orange-500 mt-1 mr-3"></i>
                <span>Industry partnerships and networking events</span>
              </li>
            </ul>
          </div>
          <div>
            <h3 class="text-lg font-medium text-gray-800 mb-4">Contact Information</h3>
            <ul class="space-y-3 text-gray-600">
              <li class="flex items-center">
                <i class="fas fa-clock text-indigo-500 mr-3"></i>
                <span>Office Hours: Monday-Friday, 8:00 AM - 5:00 PM</span>
              </li>
              <li class="flex items-center">
                <i class="fas fa-envelope text-indigo-500 mr-3"></i>
                <span>Email: departments@university.edu</span>
              </li>
              <li class="flex items-center">
                <i class="fas fa-phone text-indigo-500 mr-3"></i>
                <span>Phone: +254 20 123 4567</span>
              </li>
              <li class="flex items-center">
                <i class="fas fa-map-marker-alt text-indigo-500 mr-3"></i>
                <span>Location: Administration Building, 2nd Floor</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Simple search functionality
      const searchInput = document.querySelector('input[type="text"]');
      const departmentCards = document.querySelectorAll('.bg-white.rounded-xl');
      
      searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        
        departmentCards.forEach(card => {
          const deptName = card.querySelector('h3').textContent.toLowerCase();
          const deptDescription = card.querySelector('p').textContent.toLowerCase();
          
          if (deptName.includes(searchTerm) || deptDescription.includes(searchTerm)) {
            card.style.display = 'block';
          } else {
            card.style.display = 'none';
          }
        });
      });

      // Button handlers for department actions
      document.querySelectorAll('button:contains("View Details")').forEach(button => {
        button.addEventListener('click', function() {
          const deptName = this.closest('.bg-white').querySelector('h3').textContent;
          alert('Viewing details for ' + deptName + ' department');
        });
      });

      document.querySelectorAll('button:contains("Courses")').forEach(button => {
        button.addEventListener('click', function() {
          const deptName = this.closest('.bg-white').querySelector('h3').textContent;
          alert('Viewing courses offered by ' + deptName + ' department');
        });
      });

      document.querySelectorAll('button:contains("Lecturers")').forEach(button => {
        button.addEventListener('click', function() {
          const deptName = this.closest('.bg-white').querySelector('h3').textContent;
          alert('Viewing lecturers in ' + deptName + ' department');
        });
      });
    });
  </script>
</body>
</html>