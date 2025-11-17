
<?php
session_start();
// Setup PDO connection for admin registration
$pdo = new PDO("mysql:host=localhost;dbname=school", "root", "munyoiks7");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
require_once '../connection/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = trim($_POST['full_name']);
    $role = $_POST['role'];

    $errors = [];

    // Validate required fields
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($full_name)) {
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

    // Check if username or email already exists
    if (empty($errors)) {
        $checkStmt = $pdo->prepare("SELECT admin_id FROM admin_users WHERE username = ? OR email = ?");
        $checkStmt->execute([$username, $email]);
        if ($checkStmt->fetch()) {
            $errors[] = "Username or email already exists!";
        }
    }

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO admin_users (username, email, password_hash, full_name, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $password_hash, $full_name, $role]);

            // Redirect to login page with success message
            header("Location: admin_login.php?registered=1");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin - School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            backdrop-filter: blur(10px);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 2rem;
            text-align: center;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .password-strength {
            height: 5px;
            border-radius: 5px;
            margin-top: 5px;
            transition: all 0.3s;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card register-card">
                    <div class="card-header">
                        <h3><i class="fas fa-user-shield me-2"></i>Admin Registration</h3>
                        <p class="mb-0">Create a new administrator account</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h5><i class="fas fa-exclamation-triangle me-2"></i>Registration Failed</h5>
                                <?php foreach ($errors as $error): ?>
                                    <div class="mb-1">• <?php echo htmlspecialchars($error); ?></div>
                                <?php endforeach; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" id="registrationForm">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="full_name" class="form-label">
                                        <i class="fas fa-user me-2"></i>Full Name
                                    </label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" 
                                           value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" 
                                           required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">
                                        <i class="fas fa-at me-2"></i>Username
                                    </label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                           required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-2"></i>Email
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                           required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">
                                    <i class="fas fa-user-tag me-2"></i>Role
                                </label>
                                <select class="form-control" id="role" name="role" required>
                                    <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                    <option value="finance_manager" <?php echo (isset($_POST['role']) && $_POST['role'] == 'finance_manager') ? 'selected' : ''; ?>>Finance Manager</option>
                                    <option value="super_admin" <?php echo (isset($_POST['role']) && $_POST['role'] == 'super_admin') ? 'selected' : ''; ?>>Super Admin</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock me-2"></i>Password
                                    </label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <div class="password-strength" id="passwordStrength"></div>
                                    <small class="form-text text-muted">Minimum 6 characters</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">
                                        <i class="fas fa-lock me-2"></i>Confirm Password
                                    </label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    <div id="passwordMatch" class="form-text"></div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>Register Admin
                                </button>
                                <a href="admin_login.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Login
                                </a>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center text-muted">
                        <small>School Management System &copy; <?php echo date('Y'); ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            const passwordStrength = document.getElementById('passwordStrength');
            const passwordMatch = document.getElementById('passwordMatch');

            password.addEventListener('input', function() {
                const strength = calculatePasswordStrength(this.value);
                updatePasswordStrength(strength);
            });

            confirmPassword.addEventListener('input', function() {
                if (this.value === password.value) {
                    this.style.borderColor = '#28a745';
                    passwordMatch.innerHTML = '<i class="fas fa-check text-success"></i> Passwords match';
                    passwordMatch.className = 'form-text text-success';
                } else {
                    this.style.borderColor = '#dc3545';
                    passwordMatch.innerHTML = '<i class="fas fa-times text-danger"></i> Passwords do not match';
                    passwordMatch.className = 'form-text text-danger';
                }
            });

            function calculatePasswordStrength(pass) {
                let strength = 0;
                if (pass.length >= 6) strength += 1;
                if (pass.length >= 8) strength += 1;
                if (/[A-Z]/.test(pass)) strength += 1;
                if (/[0-9]/.test(pass)) strength += 1;
                if (/[^A-Za-z0-9]/.test(pass)) strength += 1;
                return strength;
            }

            function updatePasswordStrength(strength) {
                const colors = ['#dc3545', '#ffc107', '#ffc107', '#17a2b8', '#28a745'];
                const widths = ['20%', '40%', '60%', '80%', '100%'];
                const messages = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
                
                passwordStrength.style.width = widths[strength];
                passwordStrength.style.backgroundColor = colors[strength];
            }
        });
    </script>
</body>
</html>