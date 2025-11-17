
<?php
session_start();
// Setup PDO connection for admin dashboard
$pdo = new PDO("mysql:host=localhost;dbname=school", "root", "munyoiks7");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
require_once '../connection/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Get statistics
$students_count = $pdo->query("SELECT COUNT(*) FROM students WHERE status_of_student = 'active'")->fetchColumn();
$lecturers_count = $pdo->query("SELECT COUNT(*) FROM lecturers WHERE is_active = TRUE")->fetchColumn();
$courses_count = $pdo->query("SELECT COUNT(*) FROM course WHERE is_active = TRUE")->fetchColumn();
$departments_count = $pdo->query("SELECT COUNT(*) FROM department WHERE is_active = TRUE")->fetchColumn();

// Get recent financial transactions
$recent_payments = $pdo->query("
    SELECT f.*, s.first_name, s.last_name, s.student_code 
    FROM finance f 
    JOIN students s ON f.student_id = s.student_id 
    ORDER BY f.created_at DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="list-group">
                    <a href="admin_dashboard.php" class="list-group-item list-group-item-action active">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="manage_departments.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-building"></i> Manage Departments
                    </a>
                    <a href="manage_courses.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-book"></i> Manage Courses
                    </a>
                    <a href="manage_lecturers.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-chalkboard-teacher"></i> Manage Lecturers
                    </a>
                    <a href="manage_finance.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-money-bill-wave"></i> Manage Finance
                    </a>
                    <a href="manage_students.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-users"></i> Manage Students
                    </a>
                    <?php if ($_SESSION['admin_role'] === 'super_admin'): ?>
                    <a href="admin_register.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-user-plus"></i> Register Admin
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $students_count; ?></h4>
                                        <p>Students</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $lecturers_count; ?></h4>
                                        <p>Lecturers</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-chalkboard-teacher fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $courses_count; ?></h4>
                                        <p>Courses</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-book fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $departments_count; ?></h4>
                                        <p>Departments</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-building fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Payments -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Recent Financial Transactions</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Student</th>
                                                <th>Amount Due</th>
                                                <th>Amount Paid</th>
                                                <th>Balance</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_payments as $payment): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']); ?></td>
                                                <td>Ksh <?php echo number_format($payment['amount_due'], 2); ?></td>
                                                <td>Ksh <?php echo number_format($payment['amount_paid'], 2); ?></td>
                                                <td>Ksh <?php echo number_format($payment['balance'], 2); ?></td>
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
                                                <td><?php echo date('M j, Y', strtotime($payment['payment_date'])); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
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