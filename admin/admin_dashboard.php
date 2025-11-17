<?php
session_start();
require_once '../connection/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Get statistics
$students_count = $conn->query("SELECT COUNT(*) as count FROM students WHERE status_of_student = 'active'")->fetch_assoc()['count'];
$lecturers_count = $conn->query("SELECT COUNT(*) as count FROM lecturers WHERE is_active = TRUE")->fetch_assoc()['count'];
$courses_count = $conn->query("SELECT COUNT(*) as count FROM course WHERE is_active = TRUE")->fetch_assoc()['count'];
$departments_count = $conn->query("SELECT COUNT(*) as count FROM department WHERE is_active = TRUE")->fetch_assoc()['count'];
$recent_registrations = $conn->query("SELECT COUNT(*) as count FROM student_auth WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['count'];

// Get recent financial transactions
$recent_payments = $conn->query("
    SELECT f.*, s.first_name, s.last_name, s.student_code 
    FROM finance f 
    JOIN students s ON f.student_id = s.student_id 
    ORDER BY f.created_at DESC 
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

// Get recent student registrations
$new_registrations = $conn->query("
    SELECT sa.*, s.first_name, s.last_name, s.student_code 
    FROM student_auth sa 
    JOIN students s ON sa.student_id = s.student_id 
    ORDER BY sa.created_at DESC 
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - School Management System</title>
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
        .stat-card {
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
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
                            <a class="nav-link active" href="admin_dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
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
                <!-- Welcome Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">Admin Dashboard</h1>
                    <div class="text-end">
                        <p class="mb-0">Welcome, <strong><?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin'; ?></strong></p>
                        <small class="text-muted"><?php echo ucfirst($_SESSION['admin_role']); ?></small>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card text-white bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $students_count; ?></h4>
                                        <p class="mb-0">Students</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card text-white bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $lecturers_count; ?></h4>
                                        <p class="mb-0">Lecturers</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-chalkboard-teacher fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card text-white bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $courses_count; ?></h4>
                                        <p class="mb-0">Courses</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-book fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card text-white bg-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $recent_registrations; ?></h4>
                                        <p class="mb-0">New Today</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-user-plus fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <a href="add_student.php" class="btn btn-outline-primary w-100">
                                            <i class="fas fa-user-plus me-2"></i>Add Student
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="manage_finance.php" class="btn btn-outline-success w-100">
                                            <i class="fas fa-money-bill me-2"></i>Manage Fees
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="manage_departments.php" class="btn btn-outline-warning w-100">
                                            <i class="fas fa-building me-2"></i>Add Department
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="manage_courses.php" class="btn btn-outline-info w-100">
                                            <i class="fas fa-book me-2"></i>Add Course
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Recent Student Registrations -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Recent Student Registrations</h5>
                                <a href="manage_students.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <?php if (empty($new_registrations)): ?>
                                    <p class="text-muted mb-0">No recent registrations.</p>
                                <?php else: ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($new_registrations as $registration): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($registration['first_name'] . ' ' . $registration['last_name']); ?></h6>
                                                <small class="text-muted"><?php echo htmlspecialchars($registration['student_code']); ?></small>
                                            </div>
                                            <small class="text-muted"><?php echo date('M j', strtotime($registration['created_at'])); ?></small>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Payments -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Recent Financial Transactions</h5>
                                <a href="manage_finance.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recent_payments)): ?>
                                    <p class="text-muted mb-0">No recent transactions.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Student</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recent_payments as $payment): ?>
                                                <tr>
                                                    <td>
                                                        <small><?php echo htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']); ?></small>
                                                    </td>
                                                    <td>
                                                        <small>Ksh <?php echo number_format($payment['amount_paid'], 2); ?></small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php 
                                                            switch($payment['payment_status']) {
                                                                case 'paid': echo 'success'; break;
                                                                case 'partial': echo 'warning'; break;
                                                                case 'pending': echo 'secondary'; break;
                                                                case 'overdue': echo 'danger'; break;
                                                                default: echo 'secondary';
                                                            }
                                                        ?>">
                                                            <?php echo ucfirst($payment['payment_status']); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>