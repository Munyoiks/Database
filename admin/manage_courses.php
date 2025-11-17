<?php
session_start();
require_once '../connection/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle course creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {
    $course_name = trim($_POST['course_name']);
    $course_code = trim($_POST['course_code']);
    $duration_years = $_POST['duration_years'];
    $department_id = $_POST['department_id'];
    $course_type = $_POST['course_type'];
    $credits = $_POST['credits'];
    $tuition_fee = $_POST['tuition_fee'];
    $description = trim($_POST['description']);

    try {
        $stmt = $conn->prepare("INSERT INTO course (course_name, course_code, duration_years, department_id, course_type, credits, tuition_fee, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiisids", $course_name, $course_code, $duration_years, $department_id, $course_type, $credits, $tuition_fee, $description);
        
        if ($stmt->execute()) {
            $success = "Course created successfully!";
        } else {
            $error = "Failed to add course: " . $conn->error;
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Handle course deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $stmt = $conn->prepare("UPDATE course SET is_active = FALSE WHERE course_id = ?");
        $stmt->bind_param("i", $delete_id);
        
        if ($stmt->execute()) {
            $success = "Course deleted successfully!";
        } else {
            $error = "Failed to delete course: " . $conn->error;
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get all courses with department information
$courses_result = $conn->query("
    SELECT c.*, d.department_name, d.department_code 
    FROM course c 
    LEFT JOIN department d ON c.department_id = d.department_id 
    WHERE c.is_active = TRUE 
    ORDER BY c.course_name
");
$courses = [];
while ($row = $courses_result->fetch_assoc()) {
    $courses[] = $row;
}

// Get departments for dropdown
$departments_result = $conn->query("SELECT department_id, department_name FROM department WHERE is_active = TRUE ORDER BY department_name");
$departments = [];
while ($row = $departments_result->fetch_assoc()) {
    $departments[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses - School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: #343a40 !important;
            min-height: 100vh;
        }
        .sidebar .nav-link {
            color: #fff;
            border-radius: 5px;
            margin-bottom: 5px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: #495057;
        }
        .course-card {
            transition: transform 0.2s;
            border-left: 4px solid #007bff;
        }
        .course-card:hover {
            transform: translateY(-5px);
        }
        .course-type-badge {
            font-size: 0.75em;
        }
        .fee-amount {
            font-size: 1.1em;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-3">
                    <h4 class="text-white mb-4">Admin Panel</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="admin_dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="add_student.php">
                                <i class="fas fa-user-plus me-2"></i>Add Student
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_students.php">
                                <i class="fas fa-users me-2"></i>Manage Students
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_departments.php">
                                <i class="fas fa-building me-2"></i>Departments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="manage_courses.php">
                                <i class="fas fa-book me-2"></i>Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_lecturers.php">
                                <i class="fas fa-chalkboard-teacher me-2"></i>Lecturers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_finance.php">
                                <i class="fas fa-money-bill-wave me-2"></i>Finance
                            </a>
                        </li>
                        <?php if ($_SESSION['admin_role'] === 'super_admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_register.php">
                                <i class="fas fa-user-plus me-2"></i>Register Admin
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-warning" href="admin_logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">Manage Courses</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                        <i class="fas fa-plus me-2"></i>Add Course
                    </button>
                </div>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Courses Grid -->
                <?php if (empty($courses)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Courses Found</h5>
                            <p class="text-muted">Get started by adding your first course.</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                                <i class="fas fa-plus me-2"></i>Add First Course
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($courses as $course): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card course-card h-100">
                                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="card-title mb-1"><?php echo htmlspecialchars($course['course_name']); ?></h5>
                                        <small class="opacity-75"><?php echo htmlspecialchars($course['course_code']); ?></small>
                                    </div>
                                    <span class="badge bg-light text-dark course-type-badge">
                                        <?php echo htmlspecialchars($course['course_type']); ?>
                                    </span>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong><i class="fas fa-building me-2 text-muted"></i>Department:</strong>
                                        <p class="mb-1"><?php echo htmlspecialchars($course['department_name'] ?? 'Not assigned'); ?></p>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <strong><i class="fas fa-clock me-2 text-muted"></i>Duration:</strong>
                                            <p class="mb-1"><?php echo $course['duration_years']; ?> years</p>
                                        </div>
                                        <div class="col-6">
                                            <strong><i class="fas fa-graduation-cap me-2 text-muted"></i>Credits:</strong>
                                            <p class="mb-1"><?php echo $course['credits'] ?? 'N/A'; ?></p>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <strong><i class="fas fa-money-bill-wave me-2 text-muted"></i>Tuition Fee:</strong>
                                        <p class="mb-1 fee-amount text-success">Ksh <?php echo number_format($course['tuition_fee'] ?? 0, 2); ?></p>
                                    </div>
                                    <?php if ($course['description']): ?>
                                        <div class="mb-3">
                                            <strong><i class="fas fa-info-circle me-2 text-muted"></i>Description:</strong>
                                            <p class="mb-0 text-muted small"><?php echo htmlspecialchars($course['description']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="btn-group w-100">
                                        <a href="edit_course.php?id=<?php echo $course['course_id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </a>
                                        <a href="manage_courses.php?delete_id=<?php echo $course['course_id']; ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Are you sure you want to delete this course?')">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Add Course Modal -->
    <div class="modal fade" id="addCourseModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>Add New Course
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="add_course" value="1">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="course_name" class="form-label">Course Name *</label>
                                <input type="text" class="form-control" id="course_name" name="course_name" required 
                                       placeholder="e.g., Bachelor of Computer Science">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="course_code" class="form-label">Course Code *</label>
                                <input type="text" class="form-control" id="course_code" name="course_code" required 
                                       placeholder="e.g., BSC-CS" maxlength="20">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="department_id" class="form-label">Department *</label>
                                <select class="form-control" id="department_id" name="department_id" required>
                                    <option value="">Select Department</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?php echo $dept['department_id']; ?>">
                                            <?php echo htmlspecialchars($dept['department_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="course_type" class="form-label">Course Type *</label>
                                <select class="form-control" id="course_type" name="course_type" required>
                                    <option value="Degree">Degree</option>
                                    <option value="Diploma">Diploma</option>
                                    <option value="Certificate">Certificate</option>
                                    <option value="Masters">Masters</option>
                                    <option value="PhD">PhD</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="duration_years" class="form-label">Duration (Years) *</label>
                                <select class="form-control" id="duration_years" name="duration_years" required>
                                    <option value="1">1 Year</option>
                                    <option value="2">2 Years</option>
                                    <option value="3">3 Years</option>
                                    <option value="4" selected>4 Years</option>
                                    <option value="5">5 Years</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="credits" class="form-label">Total Credits</label>
                                <input type="number" class="form-control" id="credits" name="credits" 
                                       placeholder="e.g., 120" min="0" step="1">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="tuition_fee" class="form-label">Tuition Fee (Ksh) *</label>
                                <input type="number" class="form-control" id="tuition_fee" name="tuition_fee" 
                                       required placeholder="e.g., 50000" min="0" step="0.01">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Course Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" 
                                      placeholder="Brief description of the course, objectives, and learning outcomes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Add Course
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set default values
            const tuitionFee = document.getElementById('tuition_fee');
            if (tuitionFee && !tuitionFee.value) {
                tuitionFee.value = '50000';
            }

            const credits = document.getElementById('credits');
            if (credits && !credits.value) {
                credits.value = '120';
            }

            // Form validation
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const courseCode = document.getElementById('course_code').value;
                if (!/^[A-Z0-9-]+$/.test(courseCode)) {
                    e.preventDefault();
                    alert('Course code should contain only uppercase letters, numbers, and hyphens.');
                    return false;
                }
            });
        });
    </script>
</body>
</html>