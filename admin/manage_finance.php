<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=school", "root", "munyoiks7");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
require_once '../connection/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle fee update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_fee'])) {
    $student_id = $_POST['student_id'];
    $amount_due = $_POST['amount_due'];
    $academic_year = $_POST['academic_year'];
    $semester = $_POST['semester'];
    $fee_type = $_POST['fee_type'];
    $due_date = $_POST['due_date'];
    $description = $_POST['description'];
    
    try {
        // Check if fee record already exists
        $checkStmt = $pdo->prepare("SELECT finance_id FROM finance WHERE student_id = ? AND academic_year = ? AND semester = ? AND fee_type = ?");
        $checkStmt->execute([$student_id, $academic_year, $semester, $fee_type]);
        $existing_fee = $checkStmt->fetch();
        
        if ($existing_fee) {
            // Update existing fee
            $stmt = $pdo->prepare("UPDATE finance SET amount_due = ?, due_date = ?, description = ?, updated_at = NOW() WHERE finance_id = ?");
            $stmt->execute([$amount_due, $due_date, $description, $existing_fee['finance_id']]);
        } else {
            // Insert new fee record
            $stmt = $pdo->prepare("INSERT INTO finance (student_id, academic_year, semester, fee_type, amount_due, due_date, description, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$student_id, $academic_year, $semester, $fee_type, $amount_due, $due_date, $description, $_SESSION['admin_id']]);
        }
        
        $success = "Fee updated successfully!";
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Handle payment recording
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['record_payment'])) {
    $finance_id = $_POST['finance_id'];
    $amount_paid = $_POST['amount_paid'];
    $payment_method = $_POST['payment_method'];
    $transaction_reference = $_POST['transaction_reference'];
    $payment_date = $_POST['payment_date'];
    $notes = $_POST['notes'];
    
    try {
        $pdo->beginTransaction();
        
        // Get current finance record
        $financeStmt = $pdo->prepare("SELECT * FROM finance WHERE finance_id = ?");
        $financeStmt->execute([$finance_id]);
        $finance = $financeStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($finance) {
            $new_amount_paid = $finance['amount_paid'] + $amount_paid;
            $balance = $finance['amount_due'] - $new_amount_paid;
            $payment_status = ($balance <= 0) ? 'paid' : (($new_amount_paid > 0) ? 'partial' : 'pending');
            
            // Update finance record
            $updateStmt = $pdo->prepare("UPDATE finance SET amount_paid = ?, payment_status = ?, payment_date = ? WHERE finance_id = ?");
            $updateStmt->execute([$new_amount_paid, $payment_status, $payment_date, $finance_id]);
            
            // Record transaction
            $transactionStmt = $pdo->prepare("INSERT INTO payment_transactions (finance_id, student_id, amount_paid, payment_method, transaction_reference, payment_date, received_by, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $transactionStmt->execute([$finance_id, $finance['student_id'], $amount_paid, $payment_method, $transaction_reference, $payment_date, $_SESSION['admin_id'], $notes]);
            
            $pdo->commit();
            $success = "Payment recorded successfully!";
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Database error: " . $e->getMessage();
    }
}

// Get students for dropdown
$students = $pdo->query("SELECT student_id, student_code, first_name, last_name FROM students WHERE status_of_student = 'active' ORDER BY first_name, last_name")->fetchAll(PDO::FETCH_ASSOC);

// Get financial records
$finance_query = "
    SELECT f.*, s.student_code, s.first_name, s.last_name, c.course_name 
    FROM finance f 
    JOIN students s ON f.student_id = s.student_id 
    LEFT JOIN course c ON s.course_id = c.course_id 
    ORDER BY f.created_at DESC
";
$finance_records = $pdo->query($finance_query)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Finance - School Management System</title>
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
                    <h3>Finance Management</h3>
                </div>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="row">
                    <!-- Update Fee Form -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Update Student Fees</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="update_fee" value="1">
                                    <div class="mb-3">
                                        <label for="student_id" class="form-label">Student</label>
                                        <select class="form-control" id="student_id" name="student_id" required>
                                            <option value="">Select Student</option>
                                            <?php foreach ($students as $student): ?>
                                                <option value="<?php echo $student['student_id']; ?>">
                                                    <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name'] . ' (' . $student['student_code'] . ')'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="academic_year" class="form-label">Academic Year</label>
                                        <select class="form-control" id="academic_year" name="academic_year" required>
                                            <option value="2024">2024</option>
                                            <option value="2025">2025</option>
                                            <option value="2026">2026</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="semester" class="form-label">Semester</label>
                                        <select class="form-control" id="semester" name="semester" required>
                                            <option value="Semester 1">Semester 1</option>
                                            <option value="Semester 2">Semester 2</option>
                                            <option value="Semester 3">Semester 3</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="fee_type" class="form-label">Fee Type</label>
                                        <select class="form-control" id="fee_type" name="fee_type" required>
                                            <option value="tuition">Tuition Fee</option>
                                            <option value="hostel">Hostel Fee</option>
                                            <option value="library">Library Fee</option>
                                            <option value="laboratory">Laboratory Fee</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="amount_due" class="form-label">Amount Due (Ksh)</label>
                                        <input type="number" step="0.01" class="form-control" id="amount_due" name="amount_due" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="due_date" class="form-label">Due Date</label>
                                        <input type="date" class="form-control" id="due_date" name="due_date" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update Fee</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Record Payment Form -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Record Payment</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="record_payment" value="1">
                                    <div class="mb-3">
                                        <label for="finance_id" class="form-label">Select Fee Record</label>
                                        <select class="form-control" id="finance_id" name="finance_id" required>
                                            <option value="">Select Fee Record</option>
                                            <?php foreach ($finance_records as $record): ?>
                                                <option value="<?php echo $record['finance_id']; ?>">
                                                    <?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name'] . ' - ' . $record['fee_type'] . ' - Ksh ' . number_format($record['amount_due'], 2)); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="amount_paid" class="form-label">Amount Paid (Ksh)</label>
                                        <input type="number" step="0.01" class="form-control" id="amount_paid" name="amount_paid" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="payment_method" class="form-label">Payment Method</label>
                                        <select class="form-control" id="payment_method" name="payment_method" required>
                                            <option value="cash">Cash</option>
                                            <option value="bank_transfer">Bank Transfer</option>
                                            <option value="cheque">Cheque</option>
                                            <option value="online">Online</option>
                                            <option value="card">Card</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="transaction_reference" class="form-label">Transaction Reference</label>
                                        <input type="text" class="form-control" id="transaction_reference" name="transaction_reference">
                                    </div>
                                    <div class="mb-3">
                                        <label for="payment_date" class="form-label">Payment Date</label>
                                        <input type="date" class="form-control" id="payment_date" name="payment_date" required value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Notes</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success">Record Payment</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Financial Records Table -->
                <div class="card">
                    <div class="card-header">
                        <h5>Financial Records</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Academic Year</th>
                                        <th>Semester</th>
                                        <th>Fee Type</th>
                                        <th>Amount Due</th>
                                        <th>Amount Paid</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($finance_records as $record): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($record['course_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($record['academic_year']); ?></td>
                                        <td><?php echo htmlspecialchars($record['semester']); ?></td>
                                        <td><?php echo ucfirst($record['fee_type']); ?></td>
                                        <td>Ksh <?php echo number_format($record['amount_due'], 2); ?></td>
                                        <td>Ksh <?php echo number_format($record['amount_paid'], 2); ?></td>
                                        <td>Ksh <?php echo number_format($record['balance'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                switch($record['payment_status']) {
                                                    case 'paid': echo 'success'; break;
                                                    case 'partial': echo 'warning'; break;
                                                    case 'pending': echo 'secondary'; break;
                                                    case 'overdue': echo 'danger'; break;
                                                    default: echo 'secondary';
                                                }
                                            ?>">
                                                <?php echo ucfirst($record['payment_status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($record['due_date'])); ?></td>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>