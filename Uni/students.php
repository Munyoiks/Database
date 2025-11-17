<?php 
// Check if session is already started before starting it
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Setup PDO connection for student dashboard
$pdo = new PDO("mysql:host=localhost;dbname=school", "root", "munyoiks7");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
include '../connection/db_connect.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
  header("Location: login.php");
  exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student data
$stmt = $pdo->prepare("
    SELECT s.*, c.course_name, d.department_name 
    FROM students s 
    LEFT JOIN course c ON s.course_id = c.course_id 
    LEFT JOIN department d ON c.department_id = d.department_id 
    WHERE s.student_id = ?
");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_personal'])) {
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $date_of_birth = $_POST['date_of_birth'];
        $gender = $_POST['gender'];
        $address = trim($_POST['address']);
        
        $update_stmt = $pdo->prepare("
            UPDATE students 
            SET email = ?, phone = ?, date_of_birth = ?, gender = ?, address = ? 
            WHERE student_id = ?
        ");
        if ($update_stmt->execute([$email, $phone, $date_of_birth, $gender, $address, $student_id])) {
            $_SESSION['success'] = "Personal information updated successfully!";
            // Refresh student data
            $stmt->execute([$student_id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $_SESSION['error'] = "Failed to update personal information!";
        }
    }
    
    if (isset($_POST['update_parent'])) {
        $parent_name = trim($_POST['parent_name']);
        $parent_phone = trim($_POST['parent_phone']);
        $parent_email = trim($_POST['parent_email']);
        $parent_occupation = trim($_POST['parent_occupation']);
        $emergency_contact = trim($_POST['emergency_contact']);
        $emergency_relationship = trim($_POST['emergency_relationship']);
        $emergency_phone = trim($_POST['emergency_phone']);
        
        $update_stmt = $pdo->prepare("
            UPDATE students 
            SET parent_name = ?, parent_phone = ?, parent_email = ?, parent_occupation = ?,
                emergency_contact = ?, emergency_relationship = ?, emergency_phone = ?
            WHERE student_id = ?
        ");
        if ($update_stmt->execute([
            $parent_name, $parent_phone, $parent_email, $parent_occupation,
            $emergency_contact, $emergency_relationship, $emergency_phone, $student_id
        ])) {
            $_SESSION['success'] = "Parent information updated successfully!";
            // Refresh student data
            $stmt->execute([$student_id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $_SESSION['error'] = "Failed to update parent information!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>My Information - Student Dashboard</title>
  <style>
    .editable-field {
      transition: all 0.3s ease;
    }
    .editable-field:focus {
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
      border-color: #3b82f6;
    }
    .save-btn {
      transition: all 0.3s ease;
    }
    .save-btn:hover {
      transform: translateY(-1px);
    }
    .success-message {
      animation: slideDown 0.3s ease;
    }
    @keyframes slideDown {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body class="bg-gray-50 flex">
  <?php include 'sidebar.php'; ?>

  <!-- Main Content -->
  <main class="flex-1 p-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-800">My Information</h1>
      <div class="text-sm text-gray-600">
        Last updated: <?php echo date('M j, Y'); ?>
      </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success'])): ?>
      <div class="success-message bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
        <div class="flex items-center">
          <i class="fas fa-check-circle mr-2"></i>
          <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <div class="flex items-center">
          <i class="fas fa-exclamation-circle mr-2"></i>
          <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Personal Information Card -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
      <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
          <i class="fas fa-user-circle mr-2 text-blue-600"></i>
          Personal Information
        </h2>
      </div>
      <div class="p-6">
        <form method="POST" id="personalInfoForm">
          <input type="hidden" name="update_personal" value="1">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
              <input type="text" 
                     class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" 
                     value="<?php echo isset($student['first_name'], $student['last_name']) ? htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) : ''; ?>" 
                     readonly>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Student ID</label>
              <input type="text" 
                     class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" 
                     value="<?php echo isset($student['student_code']) ? htmlspecialchars($student['student_code']) : ''; ?>" 
                     readonly>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
              <input type="email" 
                     name="email"
                     class="w-full px-4 py-2 border border-gray-300 rounded-lg editable-field focus:outline-none focus:ring-2 focus:ring-blue-500" 
                     value="<?php echo htmlspecialchars($student['email'] ?? ''); ?>"
                     required>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
              <input type="tel" 
                     name="phone"
                     class="w-full px-4 py-2 border border-gray-300 rounded-lg editable-field focus:outline-none focus:ring-2 focus:ring-blue-500" 
                     value="<?php echo htmlspecialchars($student['phone'] ?? ''); ?>">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
              <input type="date" 
                     name="date_of_birth"
                     class="w-full px-4 py-2 border border-gray-300 rounded-lg editable-field focus:outline-none focus:ring-2 focus:ring-blue-500" 
                     value="<?php echo htmlspecialchars($student['date_of_birth'] ?? ''); ?>">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
              <select name="gender"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg editable-field focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="Male" <?php echo ($student['gender'] ?? '') == 'Male' ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo ($student['gender'] ?? '') == 'Female' ? 'selected' : ''; ?>>Female</option>
                <option value="Other" <?php echo ($student['gender'] ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
              </select>
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Home Address</label>
              <textarea name="address"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg editable-field focus:outline-none focus:ring-2 focus:ring-blue-500" 
                        rows="3"><?php echo htmlspecialchars($student['address'] ?? ''); ?></textarea>
            </div>
          </div>
          <div class="mt-6 flex justify-end">
            <button type="submit" 
                    class="save-btn bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-all duration-300">
              <i class="fas fa-save mr-2"></i>Update Information
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Parent/Guardian Information Card -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
      <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
          <i class="fas fa-users mr-2 text-green-600"></i>
          Parent/Guardian Information
        </h2>
      </div>
      <div class="p-6">
        <form method="POST" id="parentInfoForm">
          <input type="hidden" name="update_parent" value="1">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Parent/Guardian Information -->
            <div class="space-y-4">
              <h3 class="text-lg font-medium text-gray-800 border-b pb-2">Parent/Guardian Information</h3>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input type="text" 
                       name="parent_name"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg editable-field focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="<?php echo htmlspecialchars($student['parent_name'] ?? ''); ?>">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <input type="tel" 
                       name="parent_phone"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg editable-field focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="<?php echo htmlspecialchars($student['parent_phone'] ?? ''); ?>">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" 
                       name="parent_email"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg editable-field focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="<?php echo htmlspecialchars($student['parent_email'] ?? ''); ?>">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Occupation</label>
                <input type="text" 
                       name="parent_occupation"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg editable-field focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="<?php echo htmlspecialchars($student['parent_occupation'] ?? ''); ?>">
              </div>
            </div>

            <!-- Emergency Contact -->
            <div class="space-y-4">
              <h3 class="text-lg font-medium text-gray-800 border-b pb-2">Emergency Contact</h3>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input type="text" 
                       name="emergency_contact"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg editable-field focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="<?php echo htmlspecialchars($student['emergency_contact'] ?? ''); ?>">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Relationship</label>
                <input type="text" 
                       name="emergency_relationship"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg editable-field focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="<?php echo htmlspecialchars($student['emergency_relationship'] ?? ''); ?>">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <input type="tel" 
                       name="emergency_phone"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg editable-field focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="<?php echo htmlspecialchars($student['emergency_phone'] ?? ''); ?>">
              </div>
            </div>
          </div>

          <div class="mt-6 flex justify-end">
            <button type="submit" 
                    class="save-btn bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-all duration-300">
              <i class="fas fa-save mr-2"></i>Update Parent Information
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Academic Information Card -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 bg-purple-50">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
          <i class="fas fa-graduation-cap mr-2 text-purple-600"></i>
          Academic Information
        </h2>
      </div>
      <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
            <input type="text" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" 
                   value="<?php echo htmlspecialchars($student['department_name'] ?? 'Not assigned'); ?>" 
                   readonly>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Program</label>
            <input type="text" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" 
                   value="<?php echo htmlspecialchars($student['course_name'] ?? 'Not assigned'); ?>" 
                   readonly>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Admission Year</label>
            <input type="text" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" 
                   value="<?php echo date('Y', strtotime($student['enrollment_date'] ?? 'now')); ?>" 
                   readonly>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Expected Graduation</label>
            <input type="text" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" 
                   value="<?php 
                     $enrollment_year = date('Y', strtotime($student['enrollment_date'] ?? 'now'));
                     $graduation_year = $enrollment_year + 4;
                     echo $graduation_year;
                   ?>" 
                   readonly>
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Student Status</label>
            <input type="text" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 capitalize" 
                   value="<?php echo htmlspecialchars($student['status_of_student'] ?? 'active'); ?>" 
                   readonly>
          </div>
        </div>
        
        <!-- Current Courses -->
        <div class="mt-6">
          <h3 class="text-lg font-medium text-gray-800 mb-4">Current Courses</h3>
          <?php
          // Fetch current courses for the student
          $courses_stmt = $pdo->prepare("
            SELECT c.course_code, c.course_name, l.full_name as lecturer_name
            FROM student_course sc 
            JOIN course c ON sc.course_id = c.course_id 
            LEFT JOIN course_lecturers cl ON c.course_id = cl.course_id 
            LEFT JOIN lecturers l ON cl.lecturer_id = l.lecturer_id 
            WHERE sc.student_id = ? AND sc.status = 'enrolled'
          ");
          $courses_stmt->execute([$student_id]);
          $current_courses = $courses_stmt->fetchAll(PDO::FETCH_ASSOC);
          ?>
          
          <?php if (empty($current_courses)): ?>
            <div class="text-center py-4 text-gray-500">
              <i class="fas fa-book-open fa-2x mb-2"></i>
              <p>No courses enrolled for current semester</p>
            </div>
          <?php else: ?>
            <div class="space-y-3">
              <?php foreach ($current_courses as $course): ?>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                  <div>
                    <span class="font-medium"><?php echo htmlspecialchars($course['course_code']); ?> - <?php echo htmlspecialchars($course['course_name']); ?></span>
                    <?php if ($course['lecturer_name']): ?>
                      <span class="text-sm text-gray-600 ml-2"><?php echo htmlspecialchars($course['lecturer_name']); ?></span>
                    <?php endif; ?>
                  </div>
                  <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">In Progress</span>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Add real-time validation and visual feedback
      const editableFields = document.querySelectorAll('.editable-field');
      
      editableFields.forEach(field => {
        field.addEventListener('focus', function() {
          this.style.backgroundColor = '#fefefe';
          this.style.borderColor = '#3b82f6';
        });
        
        field.addEventListener('blur', function() {
          this.style.backgroundColor = '';
        });
      });

      // Form submission feedback
      const forms = document.querySelectorAll('form');
      forms.forEach(form => {
        form.addEventListener('submit', function(e) {
          const submitBtn = this.querySelector('button[type="submit"]');
          const originalText = submitBtn.innerHTML;
          
          // Show loading state
          submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
          submitBtn.disabled = true;
          
          // Re-enable after 2 seconds if still on page (form didn't redirect)
          setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
          }, 2000);
        });
      });

      // Auto-save functionality (optional - for better UX)
      let saveTimeout;
      editableFields.forEach(field => {
        field.addEventListener('input', function() {
          clearTimeout(saveTimeout);
          // Could implement auto-save here if needed
        });
      });
    });
  </script>
</body>
</html>