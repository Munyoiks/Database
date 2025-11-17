<?php include '../connection/db_connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>My Courses - Student Dashboard</title>
</head>
<body class="bg-gray-50 flex">
  <?php include 'sidebar.php'; ?>

  <!-- Main Content -->
  <main class="flex-1 p-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-800">My Courses</h1>
      <div class="flex space-x-4">
        <div class="relative">
          <input type="text" placeholder="Search courses..." class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
          <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
        <select class="px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option>All Semesters</option>
          <option>Spring 2024</option>
          <option>Fall 2023</option>
          <option>Summer 2023</option>
        </select>
      </div>
    </div>

    <!-- Course Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
      <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
        <div class="flex items-center">
          <div class="bg-blue-100 p-3 rounded-lg mr-4">
            <i class="fas fa-book text-blue-600 text-xl"></i>
          </div>
          <div>
            <p class="text-sm text-gray-600">Registered Courses</p>
            <?php 
              $r = $conn->query("SELECT COUNT(*) AS total FROM course");
$count = $r->fetch_assoc()['total'];
            ?>
            <p class="text-2xl font-bold text-gray-800"><?= $count ?: '4' ?></p>
          </div>
        </div>
      </div>
      
      <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
        <div class="flex items-center">
          <div class="bg-green-100 p-3 rounded-lg mr-4">
            <i class="fas fa-check-circle text-green-600 text-xl"></i>
          </div>
          <div>
            <p class="text-sm text-gray-600">Completed</p>
            <p class="text-2xl font-bold text-gray-800">12</p>
          </div>
        </div>
      </div>
      
      <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
        <div class="flex items-center">
          <div class="bg-purple-100 p-3 rounded-lg mr-4">
            <i class="fas fa-graduation-cap text-purple-600 text-xl"></i>
          </div>
          <div>
            <p class="text-sm text-gray-600">Current GPA</p>
            <p class="text-2xl font-bold text-gray-800">3.75</p>
          </div>
        </div>
      </div>
      
      <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
        <div class="flex items-center">
          <div class="bg-orange-100 p-3 rounded-lg mr-4">
            <i class="fas fa-credit-card text-orange-600 text-xl"></i>
          </div>
          <div>
            <p class="text-sm text-gray-600">Total Credits</p>
            <p class="text-2xl font-bold text-gray-800">15</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Current Semester Courses -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
      <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
          <i class="fas fa-play-circle mr-2 text-blue-600"></i>
          Current Semester Courses (Spring 2024)
        </h2>
      </div>
      <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php
          // Get current semester courses for the student
          $currentCourses = $conn->query("
    SELECT c.course_name, c.duration_years, sc.enrollment_date
    FROM course c
    JOIN student_course sc ON c.course_id = sc.course_id
    WHERE sc.student_id = 1
");


          if ($currentCourses && $currentCourses->num_rows > 0) {
            while($row = $currentCourses->fetch_assoc()) {
              echo '
              <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl shadow-sm border border-blue-100 hover:shadow-md transition-shadow duration-300">
                <div class="p-6">
                  <div class="flex justify-between items-start mb-4">
                    <div>
                      <h3 class="text-lg font-bold text-gray-800">'.$row['name'].'</h3>
                      <span class="text-sm text-blue-600 font-medium">'.$row['code'].'</span>
                    </div>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Registered</span>
                  </div>
                  <p class="text-gray-600 text-sm mb-4 line-clamp-2">'.$row['description'].'</p>
                  
                  <div class="space-y-2 text-sm text-gray-600 mb-4">
                    <div class="flex items-center">
                      <i class="fas fa-user-tie text-gray-400 mr-2 w-4"></i>
                      <span>'.$row['lecturer_name'].'</span>
                    </div>
                    <div class="flex items-center">
                      <i class="fas fa-clock text-gray-400 mr-2 w-4"></i>
                      <span>'.$row['credits'].' Credits</span>
                    </div>
                    <div class="flex items-center">
                      <i class="fas fa-calendar text-gray-400 mr-2 w-4"></i>
                      <span>Mon, Wed 10:00-11:30</span>
                    </div>
                  </div>
                  
                  <div class="flex justify-between items-center">
                    <button class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center">
                      <i class="fas fa-eye mr-1"></i> View Details
                    </button>
                    <button class="text-green-600 hover:text-green-800 font-medium text-sm flex items-center">
                      <i class="fas fa-download mr-1"></i> Materials
                    </button>
                  </div>
                </div>
              </div>
              ';
            }
          } else {
            // Fallback to sample data
            $sampleCourses = [
              ['name' => 'Introduction to Programming', 'code' => 'CS101', 'credits' => 3, 'lecturer' => 'Dr. Mike Chen', 'description' => 'Fundamental programming concepts using Python'],
              ['name' => 'Calculus II', 'code' => 'MATH201', 'credits' => 4, 'lecturer' => 'Prof. Emily Brown', 'description' => 'Advanced calculus topics including integration and series'],
              ['name' => 'General Physics', 'code' => 'PHY101', 'credits' => 4, 'lecturer' => 'Dr. James Wilson', 'description' => 'Classical mechanics, thermodynamics, and waves'],
            ];

            foreach ($sampleCourses as $course) {
              echo '
              <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl shadow-sm border border-blue-100 hover:shadow-md transition-shadow duration-300">
                <div class="p-6">
                  <div class="flex justify-between items-start mb-4">
                    <div>
                      <h3 class="text-lg font-bold text-gray-800">'.$course['name'].'</h3>
                      <span class="text-sm text-blue-600 font-medium">'.$course['code'].'</span>
                    </div>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Registered</span>
                  </div>
                  <p class="text-gray-600 text-sm mb-4 line-clamp-2">'.$course['description'].'</p>
                  
                  <div class="space-y-2 text-sm text-gray-600 mb-4">
                    <div class="flex items-center">
                      <i class="fas fa-user-tie text-gray-400 mr-2 w-4"></i>
                      <span>'.$course['lecturer'].'</span>
                    </div>
                    <div class="flex items-center">
                      <i class="fas fa-clock text-gray-400 mr-2 w-4"></i>
                      <span>'.$course['credits'].' Credits</span>
                    </div>
                    <div class="flex items-center">
                      <i class="fas fa-calendar text-gray-400 mr-2 w-4"></i>
                      <span>Mon, Wed 10:00-11:30</span>
                    </div>
                  </div>
                  
                  <div class="flex justify-between items-center">
                    <button class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center">
                      <i class="fas fa-eye mr-1"></i> View Details
                    </button>
                    <button class="text-green-600 hover:text-green-800 font-medium text-sm flex items-center">
                      <i class="fas fa-download mr-1"></i> Materials
                    </button>
                  </div>
                </div>
              </div>
              ';
            }
          }
          ?>
        </div>
      </div>
    </div>

    <!-- Available Courses for Registration -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
          <i class="fas fa-plus-circle mr-2 text-green-600"></i>
          Available Courses for Registration
        </h2>
      </div>
      <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php
       $availableCourses = $conn->query("
    SELECT 
        c.*, 
        d.department_name AS department_name,
        l.full_name AS lecturer_name
    FROM course c
    LEFT JOIN department d 
        ON c.department_id = d.department_id
    LEFT JOIN course_lecturers cl
        ON c.course_id = cl.course_id
    LEFT JOIN lecturers l
        ON cl.lecturer_id = l.lecturer_id
    WHERE c.course_id NOT IN (
        SELECT course_id FROM student_course WHERE student_id = 1
    )
    LIMIT 6
");



          if ($availableCourses && $availableCourses->num_rows > 0) {
            while($row = $availableCourses->fetch_assoc()) {
              echo '
              <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
                <div class="p-6">
                  <div class="flex justify-between items-start mb-4">
                    <div>
                      <h3 class="text-lg font-bold text-gray-800">'.$row['name'].'</h3>
                      <span class="text-sm text-gray-500">'.$row['code'].'</span>
                    </div>
                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Available</span>
                  </div>
                  <p class="text-gray-600 text-sm mb-4 line-clamp-2">'.$row['description'].'</p>
                  
                  <div class="space-y-2 text-sm text-gray-600 mb-4">
                    <div class="flex items-center">
                      <i class="fas fa-user-tie text-gray-400 mr-2 w-4"></i>
                      <span>'.$row['lecturer_name'].'</span>
                    </div>
                    <div class="flex items-center">
                      <i class="fas fa-building text-gray-400 mr-2 w-4"></i>
                      <span>'.$row['department_name'].'</span>
                    </div>
                    <div class="flex items-center">
                      <i class="fas fa-clock text-gray-400 mr-2 w-4"></i>
                      <span>'.$row['credits'].' Credits</span>
                    </div>
                  </div>
                  
                  <div class="flex justify-between items-center">
                    <button class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center">
                      <i class="fas fa-info-circle mr-1"></i> Details
                    </button>
                    <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center register-course" data-course-id="'.$row['id'].'" data-course-name="'.$row['name'].'">
                      <i class="fas fa-plus mr-1"></i> Register
                    </button>
                  </div>
                </div>
              </div>
              ';
            }
          } else {
            echo '<p class="text-gray-500 col-span-3 text-center py-8">No available courses found for registration.</p>';
          }
          ?>
        </div>
      </div>
    </div>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Course registration functionality
      document.querySelectorAll('.register-course').forEach(button => {
        button.addEventListener('click', function() {
          const courseId = this.getAttribute('data-course-id');
          const courseName = this.getAttribute('data-course-name');
          
          if (confirm(`Are you sure you want to register for "${courseName}"?`)) {
            // Simulate registration process
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Registering...';
            this.disabled = true;
            
            setTimeout(() => {
              alert(`Successfully registered for ${courseName}!`);
              this.closest('.bg-white').remove();
            }, 1500);
          }
        });
      });

      // Search functionality
      const searchInput = document.querySelector('input[type="text"]');
      searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const courseCards = document.querySelectorAll('.bg-white .p-6');
        
        courseCards.forEach(card => {
          const courseName = card.querySelector('h3').textContent.toLowerCase();
          const courseCode = card.querySelector('span').textContent.toLowerCase();
          
          if (courseName.includes(searchTerm) || courseCode.includes(searchTerm)) {
            card.closest('.bg-white').style.display = 'block';
          } else {
            card.closest('.bg-white').style.display = 'none';
          }
        });
      });

      // View details buttons
      document.querySelectorAll('button:contains("View Details")').forEach(button => {
        button.addEventListener('click', function() {
          const courseName = this.closest('.p-6').querySelector('h3').textContent;
          alert(`Viewing details for: ${courseName}\n\nThis would open a detailed course view with syllabus, schedule, and materials.`);
        });
      });

      // Download materials buttons
      document.querySelectorAll('button:contains("Materials")').forEach(button => {
        button.addEventListener('click', function() {
          const courseName = this.closest('.p-6').querySelector('h3').textContent;
          alert(`Downloading course materials for: ${courseName}\n\nThis would download all available course materials.`);
        });
      });
    });
  </script>
</body>
</html>