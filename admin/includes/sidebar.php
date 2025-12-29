<aside class="sidebar">
    <div class="sidebar-header">
        <h3>🏨 Admin Panel</h3>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
            📊 Dashboard
        </a>
        <a href="hotels.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'hotels.php' ? 'active' : ''; ?>">
            🏨 Hotels
        </a>
        <a href="bookings.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'bookings.php' ? 'active' : ''; ?>">
            📅 Bookings
        </a>
        <a href="users.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : ''; ?>">
            👥 Users
        </a>
        <a href="../index.php" class="nav-link">
            🏠 Back to Home
        </a>
    </nav>
</aside>
