<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=school", "root", "munyoiks7");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
        $stmt = $pdo->prepare("INSERT INTO department (department_name, department_code, head_of_department, description, established_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$department_name, $department_code, $head_of_department, $description, $established_date]);
        
        $success = "Department created successfully!";
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Get all departments
$departments = $pdo->query("SELECT * FROM department WHERE is_active = TRUE ORDER BY department_name")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Departments - School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <?php include 'admin_sidebar.php'; ?>
            
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>Manage Departments</h3>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
                        <i class="fas fa-plus"></i> Add Department
                    </button>
                </div>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="row">
                    <?php foreach ($departments as $department): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($department['department_name']); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($department['department_code']); ?></h6>
                                <p class="card-text">
                                    <strong>Head of Department:</strong> <?php echo htmlspecialchars($department['head_of_department']); ?><br>
                                    <strong>Established:</strong> <?php echo date('F Y', strtotime($department['established_date'])); ?>
                                </p>
                                <?php if ($department['description']): ?>
                                    <p class="card-text"><?php echo htmlspecialchars($department['description']); ?></p>
                                <?php endif; ?>
                                <div class="btn-group">
                                    <a href="edit_department.php?id=<?php echo $department['department_id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="department_courses.php?id=<?php echo $department['department_id']; ?>" class="btn btn-sm btn-outline-info">View Courses</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Department Modal -->
    <div class="modal fade" id="addDepartmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="add_department" value="1">
                        <div class="mb-3">
                            <label for="department_name" class="form-label">Department Name</label>
                            <input type="text" class="form-control" id="department_name" name="department_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="department_code" class="form-label">Department Code</label>
                            <input type="text" class="form-control" id="department_code" name="department_code" required>
                        </div>
                        <div class="mb-3">
                            <label for="head_of_department" class="form-label">Head of Department</label>
                            <input type="text" class="form-control" id="head_of_department" name="head_of_department" required>
                        </div>
                        <div class="mb-3">
                            <label for="established_date" class="form-label">Established Date</label>
                            <input type="date" class="form-control" id="established_date" name="established_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Department</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>