<?php
session_start();
require_once '../connection/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle lecturer creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_lecturer'])) {
    $lecturer_code = trim($_POST['lecturer_code']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $department_id = $_POST['department_id'];
    $qualification = trim($_POST['qualification']);
    $specialization = trim($_POST['specialization']);
    $employment_type = $_POST['employment_type'];
    $hire_date = $_POST['hire_date'];
    $salary = $_POST['salary'];

    try {
        $stmt = $conn->prepare("INSERT INTO lecturers (lecturer_code, full_name, email, phone, department_id, qualification, specialization, employment_type, hire_date, salary) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssissssd", $lecturer_code, $full_name, $email, $phone, $department_id, $qualification, $specialization, $employment_type, $hire_date, $salary);
        
        if ($stmt->execute()) {
            $success = "Lecturer added successfully!";
        } else {
            $error = "Failed to add lecturer: " . $conn->error;
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Handle lecturer deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $stmt = $conn->prepare("UPDATE lecturers SET is_active = FALSE WHERE lecturer_id = ?");
        $stmt->bind_param("i", $delete_id);
        
        if ($stmt->execute()) {
            $success = "Lecturer deleted successfully!";
        } else {
            $error = "Failed to delete lecturer: " . $conn->error;
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get all lecturers with department information
$lecturers_result = $conn->query("
    SELECT l.*, d.department_name, d.department_code 
    FROM lecturers l 
    LEFT JOIN department d ON l.department_id = d.department_id 
    WHERE l.is_active = TRUE 
    ORDER BY l.full_name
");
$lecturers = [];
while ($row = $lecturers_result->fetch_assoc()) {
    $lecturers[] = $row;
}

// Get departments for dropdown
$departments_result = $conn->query("SELECT department_id, department_name FROM department WHERE is_active = TRUE ORDER BY department_name");
$departments = [];
while ($row = $departments_result->fetch_assoc()) {
    $departments[] = $row;
}

// Get statistics
$total_lecturers = $conn->query("SELECT COUNT(*) as count FROM lecturers WHERE is_active = TRUE")->fetch_assoc()['count'];
$full_time = $conn->query("SELECT COUNT(*) as count FROM lecturers WHERE employment_type = 'full_time' AND is_active = TRUE")->fetch_assoc()['count'];
$part_time = $conn->query("SELECT COUNT(*) as count FROM lecturers WHERE employment_type = 'part_time' AND is_active = TRUE")->fetch_assoc()['count'];
$visiting = $conn->query("SELECT COUNT(*) as count FROM lecturers WHERE employment_type = 'visiting' AND is_active = TRUE")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Lecturers - School Management System</title>
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
        .lecturer-card {
            transition: transform 0.2s;
            border-left: 4px solid #007bff;
        }
        .lecturer-card:hover {
            transform: translateY(-5px);
        }
        .employment-badge {
            font-size: 0.75em;
        }
        .salary-amount {
            font-size: 1.1em;
            font-weight: bold;
        }
        .stats-card {
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-3px);
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
                            <a class="nav-link" href="manage_courses.php">
                                <i class="fas fa-book me-2"></i>Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="manage_lecturers.php">
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
                    <h1 class="h3">Manage Lecturers</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLecturerModal">
                        <i class="fas fa-plus me-2"></i>Add Lecturer
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

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card text-white bg-primary">
                            <div class="card-body text-center">
                                <h4 class="mb-1"><?php echo $total_lecturers; ?></h4>
                                <p class="mb-0">Total Lecturers</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card text-white bg-success">
                            <div class="card-body text-center">
                                <h4 class="mb-1"><?php echo $full_time; ?></h4>
                                <p class="mb-0">Full Time</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card text-white bg-warning">
                            <div class="card-body text-center">
                                <h4 class="mb-1"><?php echo $part_time; ?></h4>
                                <p class="mb-0">Part Time</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card text-white bg-info">
                            <div class="card-body text-center">
                                <h4 class="mb-1"><?php echo $visiting; ?></h4>
                                <p class="mb-0">Visiting</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lecturers Grid -->
                <?php if (empty($lecturers)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Lecturers Found</h5>
                            <p class="text-muted">Get started by adding your first lecturer.</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLecturerModal">
                                <i class="fas fa-plus me-2"></i>Add First Lecturer
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($lecturers as $lecturer): 
                            // Determine badge color based on employment type
                            $employment_badge_class = '';
                            switch($lecturer['employment_type']) {
                                case 'full_time':
                                    $employment_badge_class = 'bg-success';
                                    break;
                                case 'part_time':
                                    $employment_badge_class = 'bg-warning';
                                    break;
                                case 'visiting':
                                    $employment_badge_class = 'bg-info';
                                    break;
                                default:
                                    $employment_badge_class = 'bg-secondary';
                            }
                        ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card lecturer-card h-100">
                                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="card-title mb-1"><?php echo htmlspecialchars($lecturer['full_name']); ?></h5>
                                        <small class="opacity-75"><?php echo htmlspecialchars($lecturer['lecturer_code']); ?></small>
                                    </div>
                                    <span class="badge <?php echo $employment_badge_class; ?> employment-badge">
                                        <?php echo ucfirst(str_replace('_', ' ', $lecturer['employment_type'])); ?>
                                    </span>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong><i class="fas fa-envelope me-2 text-muted"></i>Email:</strong>
                                        <p class="mb-1">
                                            <a href="mailto:<?php echo htmlspecialchars($lecturer['email']); ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($lecturer['email']); ?>
                                            </a>
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <strong><i class="fas fa-phone me-2 text-muted"></i>Phone:</strong>
                                        <p class="mb-1"><?php echo htmlspecialchars($lecturer['phone'] ?? 'Not provided'); ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <strong><i class="fas fa-building me-2 text-muted"></i>Department:</strong>
                                        <p class="mb-1"><?php echo htmlspecialchars($lecturer['department_name'] ?? 'Not assigned'); ?></p>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <strong><i class="fas fa-graduation-cap me-2 text-muted"></i>Qualification:</strong>
                                            <p class="mb-1 small"><?php echo htmlspecialchars($lecturer['qualification'] ?? 'N/A'); ?></p>
                                        </div>
                                        <div class="col-6">
                                            <strong><i class="fas fa-star me-2 text-muted"></i>Specialization:</strong>
                                            <p class="mb-1 small"><?php echo htmlspecialchars($lecturer['specialization'] ?? 'N/A'); ?></p>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <strong><i class="fas fa-calendar me-2 text-muted"></i>Hire Date:</strong>
                                            <p class="mb-1 small"><?php echo date('M Y', strtotime($lecturer['hire_date'])); ?></p>
                                        </div>
                                        <div class="col-6">
                                            <strong><i class="fas fa-money-bill-wave me-2 text-muted"></i>Salary:</strong>
                                            <p class="mb-1 salary-amount text-success">Ksh <?php echo number_format($lecturer['salary'] ?? 0, 2); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="btn-group w-100">
                                        <a href="edit_lecturer.php?id=<?php echo $lecturer['lecturer_id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </a>
                                        <a href="manage_lecturers.php?delete_id=<?php echo $lecturer['lecturer_id']; ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Are you sure you want to delete this lecturer?')">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </a>
                                        <a href="lecturer_courses.php?id=<?php echo $lecturer['lecturer_id']; ?>" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-book me-1"></i>Courses
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
    
    <!-- Add Lecturer Modal -->
    <div class="modal fade" id="addLecturerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>Add New Lecturer
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="add_lecturer" value="1">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="lecturer_code" class="form-label">Lecturer Code *</label>
                                <input type="text" class="form-control" id="lecturer_code" name="lecturer_code" required 
                                       placeholder="e.g., LEC001" maxlength="20">
                                <small class="form-text text-muted">Unique identifier for the lecturer</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" required 
                                       placeholder="e.g., Dr. John Smith">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required 
                                       placeholder="e.g., john.smith@school.com">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       placeholder="e.g., +254 712 345 678">
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
                                <label for="employment_type" class="form-label">Employment Type *</label>
                                <select class="form-control" id="employment_type" name="employment_type" required>
                                    <option value="full_time">Full Time</option>
                                    <option value="part_time">Part Time</option>
                                    <option value="visiting">Visiting</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="qualification" class="form-label">Qualification *</label>
                                <input type="text" class="form-control" id="qualification" name="qualification" required 
                                       placeholder="e.g., PhD in Computer Science">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="specialization" class="form-label">Specialization</label>
                                <input type="text" class="form-control" id="specialization" name="specialization" 
                                       placeholder="e.g., Artificial Intelligence">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="hire_date" class="form-label">Hire Date *</label>
                                <input type="date" class="form-control" id="hire_date" name="hire_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="salary" class="form-label">Monthly Salary (Ksh) *</label>
                                <input type="number" class="form-control" id="salary" name="salary" 
                                       required placeholder="e.g., 80000" min="0" step="0.01">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Add Lecturer
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
            const hireDate = document.getElementById('hire_date');
            if (hireDate && !hireDate.value) {
                const today = new Date().toISOString().split('T')[0];
                hireDate.value = today;
            }

            const salary = document.getElementById('salary');
            if (salary && !salary.value) {
                salary.value = '80000';
            }

            // Form validation
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const lecturerCode = document.getElementById('lecturer_code').value;
                if (!/^[A-Z0-9-]+$/.test(lecturerCode)) {
                    e.preventDefault();
                    alert('Lecturer code should contain only uppercase letters, numbers, and hyphens.');
                    return false;
                }

                const email = document.getElementById('email').value;
                if (!email.includes('@')) {
                    e.preventDefault();
                    alert('Please enter a valid email address.');
                    return false;
                }
            });

            // Phone number formatting
            const phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.startsWith('0')) {
                    value = '+254' + value.substring(1);
                }
                if (value.length > 3) {
                    value = value.substring(0, 4) + ' ' + value.substring(4);
                }
                if (value.length > 8) {
                    value = value.substring(0, 8) + ' ' + value.substring(8);
                }
                if (value.length > 12) {
                    value = value.substring(0, 12) + ' ' + value.substring(12);
                }
                e.target.value = value;
            });
        });
    </script>
</body>
</html>