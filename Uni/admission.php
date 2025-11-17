<?php
include "../connection/db_connect.php";

// Search functionality
$rawSearch = $_GET['search'] ?? "";
$search = $rawSearch;                     // For HTML output
$searchTerm = "%" . $rawSearch . "%";    // For SQL LIKE

$stmt = $conn->prepare("
    SELECT * FROM course
    WHERE course_name LIKE ? OR course_type LIKE ?
    ORDER BY course_name ASC
");
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$courses = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission | School Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .course-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .filter-btn.active {
            background-color: #1e40af;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-blue-800">School Portal</h1>
                <p class="text-gray-600">Admissions & Courses</p>
            </div>
            <div>
                <a href="../dashboard.php" class="text-blue-700 hover:text-blue-900 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <!-- Page Title -->
        <div class="mb-10 text-center">
            <h1 class="text-4xl font-bold text-gray-800 mb-3">Apply for Admission</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Browse our available courses and certificates. Find the perfect program to advance your education and career.
            </p>
        </div>

        <!-- Search and Filter Section -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <!-- Search Bar -->
                <form method="GET" class="w-full md:w-1/2">
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               placeholder="Search for a course or certificate..."
                               value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="w-full p-4 pl-12 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </form>

                <!-- Filter Buttons -->
                <div class="flex flex-wrap gap-2 w-full md:w-auto">
                    <button class="filter-btn active px-4 py-2 rounded-lg border border-gray-300 hover:bg-blue-50 transition-colors" data-filter="all">
                        All Programs
                    </button>
                    <button class="filter-btn px-4 py-2 rounded-lg border border-gray-300 hover:bg-blue-50 transition-colors" data-filter="degree">
                        Degree Courses
                    </button>
                    <button class="filter-btn px-4 py-2 rounded-lg border border-gray-300 hover:bg-blue-50 transition-colors" data-filter="certificate">
                        Certificates
                    </button>
                </div>
            </div>

            <!-- Results Count -->
            <div class="mt-4 text-gray-600">
                <?php 
                $count = $courses->num_rows;
                echo $count . " program" . ($count !== 1 ? 's' : '') . " found";
                if (!empty($search)) {
                    echo " for '<strong>" . htmlspecialchars($search, ENT_QUOTES, 'UTF-8') . "</strong>'";
                }
                ?>
            </div>
        </div>

        <!-- List of Courses -->
        <?php if ($courses->num_rows > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while($row = $courses->fetch_assoc()) { 
                    $isCertificate = strtolower($row['course_type']) === 'certificate';
                    $cardClass = $isCertificate ? 'certificate' : 'degree';
                ?>
                    <div class="course-card bg-white rounded-xl shadow-md overflow-hidden <?= $cardClass ?>">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold 
                                    <?= $isCertificate ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' ?>">
                                    <?= htmlspecialchars($row['course_type'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <span class="text-gray-500 text-sm">
                                    <i class="far fa-clock mr-1"></i><?= $row['duration_years'] ?> year<?= $row['duration_years'] > 1 ? 's' : '' ?>
                                </span>
                            </div>

                            <h2 class="text-xl font-bold text-gray-800 mb-3"><?= htmlspecialchars($row['course_name'], ENT_QUOTES, 'UTF-8') ?></h2>

                            <?php if (!empty($row['description'])): ?>
                                <p class="text-gray-600 mb-4 line-clamp-3"><?= htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endif; ?>

                            <a href="enroll.php?course_id=<?= $row['course_id'] ?>" 
                               class="block w-full bg-blue-700 hover:bg-blue-800 text-white text-center font-medium py-3 px-4 rounded-lg transition-colors mt-4">
                                <i class="fas fa-pencil-alt mr-2"></i>Apply Now
                            </a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <i class="fas fa-search text-5xl text-gray-300 mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-700 mb-2">No programs found</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    <?php if (!empty($search)): ?>
                        We couldn't find any programs matching "<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>". 
                    <?php else: ?>
                        There are currently no programs available for admission.
                    <?php endif; ?>
                </p>
                <?php if (!empty($search)): ?>
                    <a href="admission.php" class="inline-block bg-blue-700 hover:bg-blue-800 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                        View All Programs
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12 py-8">
        <div class="container mx-auto px-4 text-center">
            <p>© <?= date('Y') ?> School Portal. All rights reserved.</p>
            <p class="text-gray-400 mt-2">Need help? Contact our admissions office at admissions@schoolportal.edu</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    const filter = this.getAttribute('data-filter');
                    const courseCards = document.querySelectorAll('.course-card');
                    
                    courseCards.forEach(card => {
                        if (filter === 'all') card.style.display = 'block';
                        else if (filter === 'certificate') card.style.display = card.classList.contains('certificate') ? 'block' : 'none';
                        else if (filter === 'degree') card.style.display = card.classList.contains('degree') ? 'block' : 'none';
                    });
                });
            });
        });
    </script>
</body>
</html>
