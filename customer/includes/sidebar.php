<aside class="sidebar">
    <div class="sidebar-header">
        <h3>🏨 Customer Panel</h3>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
            📊 Dashboard
        </a>
        <a href="browse-hotels.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'browse-hotels.php' ? 'active' : ''; ?>">
            🔍 Browse Hotels
        </a>
        <a href="my-bookings.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'my-bookings.php' ? 'active' : ''; ?>">
            📅 My Bookings
        </a>
        <a href="../index.php" class="nav-link">
            🏠 Back to Home
        </a>
    </nav>
</aside>
