<?php
include 'connection/db_connect.php';

// Fetch students
$sql = "SELECT s.*, c.course_name 
        FROM students s 
        LEFT JOIN course c ON s.course_id = c.course_id";
$result = $conn->query($sql);

// Fetch courses for dropdown
$courses = $conn->query("SELECT * FROM course");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Students Management</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

<h1 class="text-3xl font-bold mb-6">Students Management</h1>

<!-- Add Student Form -->
<div class="bg-white p-6 rounded-xl shadow w-1/2 mb-8">
  <h3 class="text-xl font-semibold mb-4">Add New Student</h3>

  <form action="add_student.php" method="POST" class="grid grid-cols-2 gap-4">

    <input type="text" name="first_name" placeholder="First Name" required class="p-2 border rounded">
    <input type="text" name="last_name" placeholder="Last Name" required class="p-2 border rounded">

    <input type="date" name="date_of_birth" required class="p-2 border rounded">

    <select name="gender" class="p-2 border rounded" required>
      <option value="">Select Gender</option>
      <option>Male</option>
      <option>Female</option>
    </select>

    <input type="text" name="phone" placeholder="Phone" class="p-2 border rounded">
    <input type="email" name="email" placeholder="Email" class="p-2 border rounded">

    <select name="course_id" class="p-2 border rounded" required>
      <option value="">Select Course</option>
      <?php while ($course = $courses->fetch_assoc()) { ?>
        <option value="<?= $course['course_id'] ?>"><?= $course['course_name'] ?></option>
      <?php } ?>
    </select>

    <input type="text" name="parent_name" placeholder="Parent Name" class="p-2 border rounded">
    <input type="text" name="parent_phone" placeholder="Parent Phone" class="p-2 border rounded">

    <select name="status_of_student" class="p-2 border rounded">
      <option value="Active">Active</option>
      <option value="Suspended">Suspended</option>
      <option value="Completed">Completed</option>
    </select>

    <button type="submit" class="col-span-2 bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
      Add Student
    </button>
  </form>
</div>

<!-- Student Table -->
<table class="w-full bg-white shadow rounded-xl">
  <thead class="bg-blue-900 text-white">
    <tr>
      <th class="p-3">ID</th>
      <th class="p-3">Name</th>
      <th class="p-3">DOB</th>
      <th class="p-3">Gender</th>
      <th class="p-3">Course</th>
      <th class="p-3">Status</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = $result->fetch_assoc()) { ?>
      <tr class="border-b">
        <td class="p-3"><?= $row['student_id'] ?></td>
        <td class="p-3"><?= $row['first_name'] . " " . $row['last_name'] ?></td>
        <td class="p-3"><?= $row['date_of_birth'] ?></td>
        <td class="p-3"><?= $row['gender'] ?></td>
        <td class="p-3"><?= $row['course_name'] ?></td>
        <td class="p-3"><?= $row['status_of_student'] ?></td>
      </tr>
    <?php } ?>
  </tbody>
</table>

</body>
</html>
