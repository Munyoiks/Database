<?php
session_start();
require_once '../connection/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admission_number = trim($_POST['admission_number']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = trim($_POST['email']);

    $errors = [];

    // Validate inputs
    if (empty($admission_number) || empty($password) || empty($confirm_password) || empty($email)) {
        $errors[] = "All fields are required!";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match!";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long!";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format!";
    }

    if (empty($errors)) {
        try {
            // First, check if admission number already exists in student_auth table
            $authCheckStmt = $conn->prepare("SELECT auth_id FROM student_auth WHERE admission_number = ?");
            $authCheckStmt->bind_param("s", $admission_number);
            $authCheckStmt->execute();
            $authResult = $authCheckStmt->get_result();
            
            if ($authResult->fetch_assoc()) {
                $errors[] = "This admission number is already registered. Please login instead.";
            } else {
                // Check if student exists with this admission number
                $studentStmt = $conn->prepare("
                    SELECT student_id, first_name, last_name, student_code, email, status_of_student 
                    FROM students 
                    WHERE student_code = ? AND status_of_student = 'active'
                ");
                $studentStmt->bind_param("s", $admission_number);
                $studentStmt->execute();
                $studentResult = $studentStmt->get_result();
                $student = $studentResult->fetch_assoc();

                if (!$student) {
                    $errors[] = "No active student found with admission number: $admission_number. Please contact administration.";
                } else {
                    // Verify email matches (case-insensitive)
                    if (strtolower($student['email']) !== strtolower($email)) {
                        $errors[] = "Email does not match our records for admission number: $admission_number. Please use the email registered with the school.";
                    } else {
                        // Check if student already has an account (by student_id)
                        $studentAuthCheck = $conn->prepare("SELECT auth_id FROM student_auth WHERE student_id = ?");
                        $studentAuthCheck->bind_param("i", $student['student_id']);
                        $studentAuthCheck->execute();
                        $studentAuthResult = $studentAuthCheck->get_result();
                        
                        if ($studentAuthResult->fetch_assoc()) {
                            $errors[] = "An account already exists for this student. Please login instead.";
                        } else {
                            // Create student auth record
                            $password_hash = password_hash($password, PASSWORD_DEFAULT);
                            $authStmt = $conn->prepare("INSERT INTO student_auth (student_id, admission_number, password_hash) VALUES (?, ?, ?)");
                            $authStmt->bind_param("iss", $student['student_id'], $admission_number, $password_hash);
                            
                            if ($authStmt->execute()) {
                                // Create notification for admin
                                $notificationStmt = $conn->prepare("
                                    INSERT INTO notifications (title, message, type, related_id) 
                                    VALUES (?, ?, 'student_registration', ?)
                                ");
                                $notificationMessage = "New student registration: " . $student['first_name'] . " " . $student['last_name'] . " (" . $student['student_code'] . ")";
                                $notificationTitle = "New Student Registration";
                                $notificationStmt->bind_param("ssi", $notificationTitle, $notificationMessage, $student['student_id']);
                                $notificationStmt->execute();

                                // Redirect to login with success message
                                $_SESSION['registration_success'] = "Registration successful! You can now login with your admission number and password.";
                                header("Location: login.php");
                                exit();
                            } else {
                                $errors[] = "Failed to create account. Please try again.";
                            }
                        }
                    }
                }
            }

        } catch (Exception $e) {
            // Check if it's a duplicate entry error
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $errors[] = "This admission number is already registered. Please login instead.";
            } else {
                $errors[] = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - School Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .register-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .info-box {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto">
            <div class="card register-card bg-white">
                <div class="card-header bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-t-2xl p-8 text-center">
                    <h3 class="text-2xl font-bold"><i class="fas fa-user-graduate me-2"></i>Student Registration</h3>
                    <p class="mb-0 mt-2">Create your student account</p>
                </div>
                <div class="card-body p-8">
                    <div class="info-box">
                        <h5 class="font-bold"><i class="fas fa-info-circle me-2"></i>Important Information</h5>
                        <p class="mb-0 text-sm">You must be an enrolled student with a valid admission number and registered email to create an account.</p>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong class="font-bold">Registration Failed</strong>
                            </div>
                            <div class="mt-2">
                                <?php foreach ($errors as $error): ?>
                                    <div class="mb-1 text-sm">• <?php echo htmlspecialchars($error); ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" id="registrationForm">
                        <div class="mb-4">
                            <label for="admission_number" class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-id-card me-2"></i>Admission Number
                            </label>
                            <input type="text" class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" 
                                   id="admission_number" name="admission_number" 
                                   value="<?php echo isset($_POST['admission_number']) ? htmlspecialchars($_POST['admission_number']) : ''; ?>" 
                                   required placeholder="Enter your admission number">
                            <small class="text-gray-500 text-xs mt-1">Your official admission number provided by the school</small>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-envelope me-2"></i>Registered Email
                            </label>
                            <input type="email" class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" 
                                   id="email" name="email" 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                   required placeholder="Enter your registered email address">
                            <small class="text-gray-500 text-xs mt-1">The email address registered with the school</small>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                <input type="password" class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" 
                                       id="password" name="password" required>
                                <small class="text-gray-500 text-xs mt-1">Minimum 6 characters</small>
                            </div>
                            <div>
                                <label for="confirm_password" class="block text-gray-700 text-sm font-bold mb-2">
                                    <i class="fas fa-lock me-2"></i>Confirm Password
                                </label>
                                <input type="password" class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" 
                                       id="confirm_password" name="confirm_password" required>
                                <div id="passwordMatch" class="text-xs mt-1"></div>
                            </div>
                        </div>

                        <div class="space-y-3 mt-6">
                            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transform transition hover:scale-105 duration-200 ease-in-out">
                                <i class="fas fa-user-plus me-2"></i>Register Account
                            </button>
                            <a href="login.php" class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-4 rounded-lg text-center transition duration-200 ease-in-out no-underline">
                                <i class="fas fa-sign-in-alt me-2"></i>Already have an account? Login
                            </a>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center text-gray-500 py-4 border-t">
                    <small>School Management System &copy; <?php echo date('Y'); ?></small>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            const passwordMatch = document.getElementById('passwordMatch');

            confirmPassword.addEventListener('input', function() {
                if (this.value === password.value) {
                    this.classList.remove('border-red-500');
                    this.classList.add('border-green-500');
                    passwordMatch.innerHTML = '<i class="fas fa-check text-green-500"></i> Passwords match';
                    passwordMatch.className = 'text-green-500 text-xs mt-1';
                } else {
                    this.classList.remove('border-green-500');
                    this.classList.add('border-red-500');
                    passwordMatch.innerHTML = '<i class="fas fa-times text-red-500"></i> Passwords do not match';
                    passwordMatch.className = 'text-red-500 text-xs mt-1';
                }
            });
        });
    </script>
</body>
</html>