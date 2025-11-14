<?php include '../connection/db_connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Lecturers Directory - Student Dashboard</title>
</head>
<body class="bg-gray-50 flex">
  <?php include 'sidebar.php'; ?>

  <!-- Main Content -->
  <main class="flex-1 p-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-800">Lecturers Directory</h1>
      <div class="flex space-x-4">
        <div class="relative">
          <input type="text" placeholder="Search lecturers..." class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
          <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
        <select class="px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option>All Departments</option>
          <option>Computer Science</option>
          <option>Mathematics</option>
          <option>Physics</option>
          <option>Engineering</option>
        </select>
      </div>
    </div>

    <!-- Statistics Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
      <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
        <div class="flex items-center">
          <div class="bg-blue-100 p-3 rounded-lg mr-4">
            <i class="fas fa-chalkboard-teacher text-blue-600 text-xl"></i>
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
      
      <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
        <div class="flex items-center">
          <div class="bg-green-100 p-3 rounded-lg mr-4">
            <i class="fas fa-building text-green-600 text-xl"></i>
          </div>
          <div>
            <p class="text-sm text-gray-600">Departments</p>
            <?php 
              $r = $conn->query("SELECT COUNT(DISTINCT department_id) AS total FROM lecturers");
              $count = $r->fetch_assoc()['total'];
            ?>
            <p class="text-2xl font-bold text-gray-800"><?= $count ?></p>
          </div>
        </div>
      </div>
      
      <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
        <div class="flex items-center">
          <div class="bg-purple-100 p-3 rounded-lg mr-4">
            <i class="fas fa-book text-purple-600 text-xl"></i>
          </div>
          <div>
            <p class="text-sm text-gray-600">Total Courses</p>
           <?php 
  $r = $conn->query("SELECT COUNT(*) AS total FROM lecturers");
  $count = $r->fetch_assoc()['total'] ?? 0;
?>

            <p class="text-2xl font-bold text-gray-800"><?= $count ?></p>
          </div>
        </div>
      </div>
      
      <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
        <div class="flex items-center">
          <div class="bg-orange-100 p-3 rounded-lg mr-4">
            <i class="fas fa-star text-orange-600 text-xl"></i>
          </div>
          <div>
            <p class="text-sm text-gray-600">Avg. Rating</p>
            <p class="text-2xl font-bold text-gray-800">4.7/5</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Lecturer Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php
      $result = $conn->query("SELECT * FROM lecturers");
      if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          echo '
          <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 hover:shadow-lg transition-shadow duration-300">
            <div class="p-6">
              <div class="flex items-center mb-4">
                <div class="w-16 h-16 rounded-full bg-gradient-to-r from-purple-500 to-blue-600 flex items-center justify-center text-white font-bold text-xl mr-4">
                  '.substr($row['name'], 0, 1).'
                </div>
                <div>
                  <h3 class="text-xl font-bold text-gray-800">'.$row['name'].'</h3>
                  <p class="text-gray-600">'.$row['department'].' Department</p>
                  <div class="flex items-center mt-1">
                    <div class="flex text-yellow-400">
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star-half-alt"></i>
                    </div>
                    <span class="text-sm text-gray-500 ml-2">4.7</span>
                  </div>
                </div>
              </div>
              <div class="space-y-3 text-sm text-gray-600">
                <div class="flex items-center">
                  <i class="fas fa-envelope mr-3 text-gray-400 w-5"></i>
                  <span class="truncate">'.$row['email'].'</span>
                </div>
                <div class="flex items-center">
                  <i class="fas fa-phone mr-3 text-gray-400 w-5"></i>
                  <span>'.$row['phone'].'</span>
                </div>
                <div class="flex items-center">
                  <i class="fas fa-book mr-3 text-gray-400 w-5"></i>
                  <span>'.$row['courses'].' Courses</span>
                </div>
                <div class="flex items-center">
                  <i class="fas fa-clock mr-3 text-gray-400 w-5"></i>
                  <span>Office Hours: Mon-Wed, 2-4 PM</span>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Office: Room '.rand(100, 400).'</span>
                <div class="flex space-x-2">
                  <button class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center">
                    <i class="fas fa-eye mr-1"></i> View
                  </button>
                  <button class="text-green-600 hover:text-green-800 font-medium text-sm flex items-center">
                    <i class="fas fa-calendar mr-1"></i> Schedule
                  </button>
                </div>
              </div>
            </div>
          </div>
          ';
        }
      } else {
        echo '
        <div class="col-span-3 text-center py-12">
          <div class="bg-white rounded-xl shadow-sm p-8 max-w-md mx-auto">
            <i class="fas fa-chalkboard-teacher text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Lecturers Found</h3>
            <p class="text-gray-500 mb-4">There are currently no lecturers in the system.</p>
          </div>
        </div>';
      }
      ?>
    </div>

    <!-- Contact Information Section -->
    <div class="mt-8 bg-white rounded-xl shadow-md overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
          <i class="fas fa-info-circle mr-2 text-blue-600"></i>
          Lecturer Contact Guidelines
        </h2>
      </div>
      <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <h3 class="text-lg font-medium text-gray-800 mb-3">Office Hours</h3>
            <ul class="space-y-2 text-gray-600">
              <li class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                Check individual lecturer profiles for specific office hours
              </li>
              <li class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                Schedule appointments through the university portal
              </li>
              <li class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                Respect scheduled time slots
              </li>
            </ul>
          </div>
          <div>
            <h3 class="text-lg font-medium text-gray-800 mb-3">Communication Policy</h3>
            <ul class="space-y-2 text-gray-600">
              <li class="flex items-center">
                <i class="fas fa-envelope text-blue-500 mr-2"></i>
                Use university email for all communications
              </li>
              <li class="flex items-center">
                <i class="fas fa-clock text-orange-500 mr-2"></i>
                Allow 24-48 hours for email responses
              </li>
              <li class="flex items-center">
                <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                Emergency contacts available through department office
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
      const lecturerCards = document.querySelectorAll('.bg-white.rounded-xl');
      
      searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        
        lecturerCards.forEach(card => {
          const lecturerName = card.querySelector('h3').textContent.toLowerCase();
          const department = card.querySelector('p').textContent.toLowerCase();
          
          if (lecturerName.includes(searchTerm) || department.includes(searchTerm)) {
            card.style.display = 'block';
          } else {
            card.style.display = 'none';
          }
        });
      });

      // View profile button handler
      document.querySelectorAll('button:contains("View")').forEach(button => {
        button.addEventListener('click', function() {
          const lecturerName = this.closest('.bg-white').querySelector('h3').textContent;
          alert('Viewing profile of ' + lecturerName);
        });
      });

      // Schedule button handler
      document.querySelectorAll('button:contains("Schedule")').forEach(button => {
        button.addEventListener('click', function() {
          const lecturerName = this.closest('.bg-white').querySelector('h3').textContent;
          alert('Scheduling appointment with ' + lecturerName);
        });
      });
    });
  </script>
</body>
</html>