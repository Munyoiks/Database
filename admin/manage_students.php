<?php
session_start();
require_once '../connection/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_code = trim($_POST['student_code']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $course_id = $_POST['course_id'];
    $department_id = $_POST['department_id'];
    $phone = trim($_POST['phone']);
    $parent_name = trim($_POST['parent_name']);
    $parent_phone = trim($_POST['parent_phone']);

    try {
        $stmt = $conn->prepare("
            INSERT INTO students (student_code, first_name, last_name, email, phone, course_id, department_id, parent_name, parent_phone, status_of_student, enrollment_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', CURDATE())
        ");
        $stmt->bind_param("sssssiiss", $student_code, $first_name, $last_name, $email, $phone, $course_id, $department_id, $parent_name, $parent_phone);
        
        if ($stmt->execute()) {
            $success = "Student added successfully!";
            // Clear form
            $_POST = array();
        } else {
            $error = "Failed to add student: " . $conn->error;
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get courses and departments for dropdown
$courses = $conn->query("SELECT course_id, course_name FROM course WHERE is_active = TRUE");
$departments = $conn->query("SELECT department_id, department_name FROM department WHERE is_active = TRUE");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                            <a class="nav-link active" href="add_student.php">
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
                            <a class="nav-link" href="manage_finance.php">
                                <i class="fas fa-money-bill-wave me-2"></i>Finance
                            </a>
                        </li>
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
                    <h1 class="h3">Add New Student</h1>
                    <a href="manage_students.php" class="btn btn-secondary">Back to Students</a>
                </div>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Admission Number *</label>
                                    <input type="text" name="student_code" class="form-control" value="<?php echo $_POST['student_code'] ?? ''; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" name="email" class="form-control" value="<?php echo $_POST['email'] ?? ''; ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name *</label>
                                    <input type="text" name="first_name" class="form-control" value="<?php echo $_POST['first_name'] ?? ''; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name *</label>
                                    <input type="text" name="last_name" class="form-control" value="<?php echo $_POST['last_name'] ?? ''; ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control" value="<?php echo $_POST['phone'] ?? ''; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Course *</label>
                                    <select name="course_id" class="form-control" required>
                                        <option value="">Select Course</option>
                                        <?php while ($course = $courses->fetch_assoc()): ?>
                                            <option value="<?php echo $course['course_id']; ?>" <?php echo (isset($_POST['course_id']) && $_POST['course_id'] == $course['course_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($course['course_name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Department *</label>
                                    <select name="department_id" class="form-control" required>
                                        <option value="">Select Department</option>
                                        <?php while ($dept = $departments->fetch_assoc()): ?>
                                            <option value="<?php echo $dept['department_id']; ?>" <?php echo (isset($_POST['department_id']) && $_POST['department_id'] == $dept['department_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($dept['department_name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Parent/Guardian Name</label>
                                    <input type="text" name="parent_name" class="form-control" value="<?php echo $_POST['parent_name'] ?? ''; ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Parent/Guardian Phone</label>
                                    <input type="text" name="parent_phone" class="form-control" value="<?php echo $_POST['parent_phone'] ?? ''; ?>">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Add Student</button>
                            <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>