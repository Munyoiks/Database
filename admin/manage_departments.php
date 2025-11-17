<?php
session_start();
require_once '../connection/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle department creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_department'])) {
    $department_name = trim($_POST['department_name']);
    $department_code = trim($_POST['department_code']);
    $head_of_department = trim($_POST['head_of_department']);
    $description = trim($_POST['description']);
    $established_date = $_POST['established_date'];
    
    try {
        $stmt = $conn->prepare("INSERT INTO department (department_name, department_code, head_of_department, description, established_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $department_name, $department_code, $head_of_department, $description, $established_date);
        $stmt->execute();
        
        $success = "Department created successfully!";
    } catch (Exception $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Handle department deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $stmt = $conn->prepare("UPDATE department SET is_active = FALSE WHERE department_id = ?");
        $stmt->bind_param("i", $delete_id);
        
        if ($stmt->execute()) {
            $success = "Department deleted successfully!";
        } else {
            $error = "Failed to delete department: " . $conn->error;
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get all departments
$departments_result = $conn->query("SELECT * FROM department WHERE is_active = TRUE ORDER BY department_name");
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
    <title>Manage Departments - School Management System</title>
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
        .department-card {
            transition: transform 0.2s;
            border-left: 4px solid #007bff;
        }
        .department-card:hover {
            transform: translateY(-5px);
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
                            <a class="nav-link active" href="manage_departments.php">
                                <i class="fas fa-building me-2"></i>Departments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_courses.php">
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
                    <h1 class="h3">Manage Departments</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
                        <i class="fas fa-plus me-2"></i>Add Department
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
                    <?php
                    // Get statistics
                    $total_departments = $conn->query("SELECT COUNT(*) as count FROM department WHERE is_active = TRUE")->fetch_assoc()['count'];
                    $total_students = $conn->query("SELECT COUNT(*) as count FROM students WHERE status_of_student = 'active'")->fetch_assoc()['count'];
                    $total_courses = $conn->query("SELECT COUNT(*) as count FROM course WHERE is_active = TRUE")->fetch_assoc()['count'];
                    $total_lecturers = $conn->query("SELECT COUNT(*) as count FROM lecturers WHERE is_active = TRUE")->fetch_assoc()['count'];
                    ?>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card text-white bg-primary">
                            <div class="card-body text-center">
                                <h4 class="mb-1"><?php echo $total_departments; ?></h4>
                                <p class="mb-0">Total Departments</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card text-white bg-success">
                            <div class="card-body text-center">
                                <h4 class="mb-1"><?php echo $total_students; ?></h4>
                                <p class="mb-0">Total Students</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card text-white bg-warning">
                            <div class="card-body text-center">
                                <h4 class="mb-1"><?php echo $total_courses; ?></h4>
                                <p class="mb-0">Total Courses</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card text-white bg-info">
                            <div class="card-body text-center">
                                <h4 class="mb-1"><?php echo $total_lecturers; ?></h4>
                                <p class="mb-0">Total Lecturers</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Departments Grid -->
                <?php if (empty($departments)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Departments Found</h5>
                            <p class="text-muted">Get started by adding your first department.</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
                                <i class="fas fa-plus me-2"></i>Add First Department
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($departments as $department): 
                            // Get counts for each department
                            $deptId = $department['department_id'];
                            $studentCountQuery = $conn->query("SELECT COUNT(*) as count FROM students WHERE department_id = $deptId AND status_of_student = 'active'");
                            $studentCount = $studentCountQuery ? $studentCountQuery->fetch_assoc()['count'] : 0;
                            
                            $courseCountQuery = $conn->query("SELECT COUNT(*) as count FROM course WHERE department_id = $deptId AND is_active = TRUE");
                            $courseCount = $courseCountQuery ? $courseCountQuery->fetch_assoc()['count'] : 0;
                            
                            $lecturerCountQuery = $conn->query("SELECT COUNT(*) as count FROM lecturers WHERE department_id = $deptId AND is_active = TRUE");
                            $lecturerCount = $lecturerCountQuery ? $lecturerCountQuery->fetch_assoc()['count'] : 0;
                        ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card department-card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-1"><?php echo htmlspecialchars($department['department_name']); ?></h5>
                                    <small class="opacity-75"><?php echo htmlspecialchars($department['department_code']); ?></small>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong><i class="fas fa-user-tie me-2 text-muted"></i>Head of Department:</strong>
                                        <p class="mb-1"><?php echo htmlspecialchars($department['head_of_department']); ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <strong><i class="fas fa-calendar me-2 text-muted"></i>Established:</strong>
                                        <p class="mb-1"><?php echo date('F Y', strtotime($department['established_date'])); ?></p>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <strong><i class="fas fa-user-graduate me-2 text-muted"></i>Students:</strong>
                                            <p class="mb-1"><?php echo $studentCount; ?></p>
                                        </div>
                                        <div class="col-6">
                                            <strong><i class="fas fa-book me-2 text-muted"></i>Courses:</strong>
                                            <p class="mb-1"><?php echo $courseCount; ?></p>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <strong><i class="fas fa-chalkboard-teacher me-2 text-muted"></i>Lecturers:</strong>
                                            <p class="mb-1"><?php echo $lecturerCount; ?></p>
                                        </div>
                                        <div class="col-6">
                                            <strong><i class="fas fa-chart-line me-2 text-muted"></i>Capacity:</strong>
                                            <p class="mb-1"><?php echo min($studentCount, 100); ?>%</p>
                                        </div>
                                    </div>
                                    <?php if ($department['description']): ?>
                                        <div class="mb-3">
                                            <strong><i class="fas fa-info-circle me-2 text-muted"></i>Description:</strong>
                                            <p class="mb-0 text-muted small"><?php echo htmlspecialchars($department['description']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="btn-group w-100">
                                        <a href="edit_department.php?id=<?php echo $department['department_id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </a>
                                        <a href="manage_departments.php?delete_id=<?php echo $department['department_id']; ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Are you sure you want to delete this department?')">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </a>
                                        <a href="department_courses.php?id=<?php echo $department['department_id']; ?>" class="btn btn-sm btn-outline-info">
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
    
    <!-- Add Department Modal -->
    <div class="modal fade" id="addDepartmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>Add New Department
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="add_department" value="1">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="department_name" class="form-label">Department Name *</label>
                                <input type="text" class="form-control" id="department_name" name="department_name" required 
                                       placeholder="e.g., Computer Science">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="department_code" class="form-label">Department Code *</label>
                                <input type="text" class="form-control" id="department_code" name="department_code" required 
                                       placeholder="e.g., CS" maxlength="10">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="head_of_department" class="form-label">Head of Department *</label>
                                <input type="text" class="form-control" id="head_of_department" name="head_of_department" required 
                                       placeholder="e.g., Dr. John Smith">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="established_date" class="form-label">Established Date *</label>
                                <input type="date" class="form-control" id="established_date" name="established_date" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" 
                                      placeholder="Brief description of the department..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Add Department
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set established date to today by default
        document.addEventListener('DOMContentLoaded', function() {
            const establishedDate = document.getElementById('established_date');
            if (establishedDate && !establishedDate.value) {
                const today = new Date().toISOString().split('T')[0];
                establishedDate.value = today;
            }

            // Form validation
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const deptCode = document.getElementById('department_code').value;
                if (!/^[A-Z0-9-]+$/.test(deptCode)) {
                    e.preventDefault();
                    alert('Department code should contain only uppercase letters, numbers, and hyphens.');
                    return false;
                }
            });
        });
    </script>
</body>
</html>