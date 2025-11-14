<?php include '../connection/db_connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>My Information - Student Dashboard</title>
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

    <!-- Personal Information Card -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
      <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
          <i class="fas fa-user-circle mr-2 text-blue-600"></i>
          Personal Information
        </h2>
      </div>
      <div class="p-6">
        <form id="personalInfoForm">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
              <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                     value="John Doe" readonly>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Student ID</label>
              <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                     value="STU2024001" readonly>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
              <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                     value="john.doe@student.school.edu">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
              <input type="tel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                     value="+254 712 345 678">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
              <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                     value="2000-05-15">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
              <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option>Male</option>
                <option>Female</option>
                <option>Other</option>
              </select>
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Home Address</label>
              <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3">123 Main Street, Nairobi, Kenya</textarea>
            </div>
          </div>
          <div class="mt-6 flex justify-end">
            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
              Update Information
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
        <form id="parentInfoForm">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Father's Information -->
            <div class="space-y-4">
              <h3 class="text-lg font-medium text-gray-800 border-b pb-2">Father's Information</h3>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="Robert Doe">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <input type="tel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="+254 723 456 789">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="robert.doe@email.com">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Occupation</label>
                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="Business Owner">
              </div>
            </div>

            <!-- Mother's Information -->
            <div class="space-y-4">
              <h3 class="text-lg font-medium text-gray-800 border-b pb-2">Mother's Information</h3>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="Mary Doe">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <input type="tel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="+254 734 567 890">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="mary.doe@email.com">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Occupation</label>
                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="Teacher">
              </div>
            </div>
          </div>

          <!-- Emergency Contact -->
          <div class="mt-8 pt-6 border-t">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Emergency Contact (Other than Parents)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="James Smith">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Relationship</label>
                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="Uncle">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <input type="tel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="+254 745 678 901">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="james.smith@email.com">
              </div>
            </div>
          </div>

          <div class="mt-6 flex justify-end">
            <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium">
              Update Parent Information
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
            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" 
                   value="Computer Science" readonly>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Program</label>
            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" 
                   value="Bachelor of Science in Computer Science" readonly>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Admission Year</label>
            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" 
                   value="2024" readonly>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Expected Graduation</label>
            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" 
                   value="2028" readonly>
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Academic Advisor</label>
            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" 
                   value="Dr. Sarah Johnson" readonly>
          </div>
        </div>
        
        <!-- Current Courses -->
        <div class="mt-6">
          <h3 class="text-lg font-medium text-gray-800 mb-4">Current Courses</h3>
          <div class="space-y-3">
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
              <div>
                <span class="font-medium">CS101 - Introduction to Programming</span>
                <span class="text-sm text-gray-600 ml-2">Dr. Mike Chen</span>
              </div>
              <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">In Progress</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
              <div>
                <span class="font-medium">MATH201 - Calculus II</span>
                <span class="text-sm text-gray-600 ml-2">Prof. Emily Brown</span>
              </div>
              <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">In Progress</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
              <div>
                <span class="font-medium">PHY101 - General Physics</span>
                <span class="text-sm text-gray-600 ml-2">Dr. James Wilson</span>
              </div>
              <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">In Progress</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script>
    // Simple form submission handlers
    document.addEventListener('DOMContentLoaded', function() {
      // Personal Information Form
      document.querySelector('#personalInfoForm button').addEventListener('click', function() {
        alert('Personal information updated successfully!');
      });

      // Parent Information Form
      document.querySelector('#parentInfoForm button').addEventListener('click', function() {
        alert('Parent/Guardian information updated successfully!');
      });
    });
  </script>
</body>
</html>