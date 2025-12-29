<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../auth/login.php');
}

// Get filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Build query
$where = "1=1";
if ($filter === 'customer') {
    $where = "user_type = 'customer'";
} elseif ($filter === 'admin') {
    $where = "user_type = 'admin'";
}

$users_query = "SELECT u.*, 
                (SELECT COUNT(*) FROM bookings WHERE user_id = u.id) as total_bookings 
                FROM users u 
                WHERE $where 
                ORDER BY u.created_at DESC";
$users = mysqli_query($conn, $users_query);
?>
<?php include 'includes/header.php'; ?>

<div class="dashboard-content">
    <h1>User Management</h1>
    
    <div class="filter-bar">
        <a href="?filter=all" class="filter-btn <?php echo $filter === 'all' ? 'active' : ''; ?>">All Users</a>
        <a href="?filter=customer" class="filter-btn <?php echo $filter === 'customer' ? 'active' : ''; ?>">Customers</a>
        <a href="?filter=admin" class="filter-btn <?php echo $filter === 'admin' ? 'active' : ''; ?>">Admins</a>
    </div>
    
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>User Type</th>
                    <th>Total Bookings</th>
                    <th>Status</th>
                    <th>Registered</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($users)): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo $user['full_name']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['phone'] ?? 'N/A'; ?></td>
                    <td><span class="badge badge-info"><?php echo ucfirst($user['user_type']); ?></span></td>
                    <td><?php echo $user['total_bookings']; ?></td>
                    <td><span class="badge badge-<?php echo $user['status'] === 'active' ? 'success' : 'secondary'; ?>"><?php echo ucfirst($user['status']); ?></span></td>
                    <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
