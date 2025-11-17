<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="admin_dashboard.php">
            <i class="fas fa-school"></i> School Management System
        </a>
        <div class="navbar-nav ms-auto">
            <span class="navbar-text me-3">
                Welcome, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>
            </span>
            <a class="nav-link" href="admin_logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</nav>