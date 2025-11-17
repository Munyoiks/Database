<?php
include "../connection/db_connect.php";

// Search functionality
$search = $_GET['search'] ?? '';

$courses = $conn->query("
    SELECT * FROM course
    WHERE course_name LIKE '%$search%'
    ORDER BY course_name ASC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admission | School Portal</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-8">

<h1 class="text-3xl font-bold mb-6">Apply for Admission</h1>

<!-- Search Bar -->
<form method="GET" class="mb-6">
    <input type="text" 
           name="search" 
           placeholder="Search for a course or certificate..."
           value="<?= htmlspecialchars($search) ?>"
           class="p-3 w-1/2 rounded border border-gray-300">
    <button class="bg-blue-700 text-white px-4 py-2 rounded">Search</button>
</form>

<!-- List of Courses -->
<div class="grid grid-cols-3 gap-6">
<?php while($row = $courses->fetch_assoc()) { ?>
    <div class="bg-white p-5 rounded-xl shadow">
        <h2 class="text-xl font-bold text-blue-700"><?= $row['course_name'] ?></h2>
        <p class="text-gray-600 mb-3">Duration: <?= $row['duration_years'] ?> years</p>
        <p class="text-gray-500 mb-4">Type: <?= $row['course_type'] ?></p>

        <a href="enroll.php?course_id=<?= $row['course_id'] ?>" 
           class="bg-green-600 text-white px-4 py-2 rounded block text-center">
           Apply Now
        </a>
    </div>
<?php } ?>
</div>

</body>
</html>
