<?php
session_start();
require_once '../connection/db_connect.php';

// Check if user is already logged in
if (isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

// Display registration success message
if (isset($_SESSION['registration_success'])) {
    $success = $_SESSION['registration_success'];
    unset($_SESSION['registration_success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admission_number = trim($_POST['admission_number']);
    $password = $_POST['password'];
    
    try {
        $stmt = $conn->prepare("
            SELECT sa.*, s.first_name, s.last_name, s.student_code, s.email, s.course_id, s.status_of_student 
            FROM student_auth sa 
            JOIN students s ON sa.student_id = s.student_id 
            WHERE sa.admission_number = ? AND sa.is_active = TRUE AND s.status_of_student = 'active'
        ");
        $stmt->bind_param("s", $admission_number);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        
        if ($student && password_verify($password, $student['password_hash'])) {
            // Set session variables
            $_SESSION['student_id'] = $student['student_id'];
            $_SESSION['admission_number'] = $student['admission_number'];
            $_SESSION['student_name'] = $student['first_name'] . ' ' . $student['last_name'];
            $_SESSION['student_code'] = $student['student_code'];
            $_SESSION['student_email'] = $student['email'];
            $_SESSION['course_id'] = $student['course_id'];
            
            // Update last login
            $updateStmt = $conn->prepare("UPDATE student_auth SET last_login = NOW() WHERE auth_id = ?");
            $updateStmt->bind_param("i", $student['auth_id']);
            $updateStmt->execute();
            
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid admission number or password!";
        }
    } catch (Exception $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - School Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body>
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto">
            <div class="card login-card bg-white">
                <div class="card-header bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-t-2xl p-8 text-center">
                    <h3 class="text-2xl font-bold"><i class="fas fa-user-graduate me-2"></i>Student Portal</h3>
                    <p class="mb-0 mt-2">Sign in to your account</p>
                </div>
                <div class="card-body p-8">
                    <?php if (isset($success)): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($error)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-4">
                            <label for="admission_number" class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-id-card me-2"></i>Admission Number
                            </label>
                            <input type="text" class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" 
                                   id="admission_number" name="admission_number" required>
                        </div>
                        <div class="mb-6">
                            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-lock me-2"></i>Password
                            </label>
                            <input type="password" class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" 
                                   id="password" name="password" required>
                        </div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <input id="remember_me" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                                    Remember me
                                </label>
                            </div>
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-800">
                                Forgot Password?
                            </a>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transform transition hover:scale-105 duration-200 ease-in-out w-full">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <a href="register.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium no-underline">
                            <i class="fas fa-user-plus me-1"></i>Don't have an account? Register here
                        </a>
                    </div>
                </div>
                <div class="card-footer text-center text-gray-500 py-4 border-t">
                    <small>School Management System &copy; <?php echo date('Y'); ?></small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>