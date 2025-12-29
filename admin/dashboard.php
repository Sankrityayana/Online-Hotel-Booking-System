<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../auth/login.php');
}

// Get statistics
$stats = [];

$query = "SELECT COUNT(*) as count FROM users WHERE user_type = 'customer'";
$result = mysqli_query($conn, $query);
$stats['total_customers'] = mysqli_fetch_assoc($result)['count'];

$query = "SELECT COUNT(*) as count FROM hotels WHERE status = 'active'";
$result = mysqli_query($conn, $query);
$stats['total_hotels'] = mysqli_fetch_assoc($result)['count'];

$query = "SELECT COUNT(*) as count FROM rooms WHERE status = 'available'";
$result = mysqli_query($conn, $query);
$stats['available_rooms'] = mysqli_fetch_assoc($result)['count'];

$query = "SELECT COUNT(*) as count FROM bookings";
$result = mysqli_query($conn, $query);
$stats['total_bookings'] = mysqli_fetch_assoc($result)['count'];

$query = "SELECT COUNT(*) as count FROM bookings WHERE booking_status = 'confirmed'";
$result = mysqli_query($conn, $query);
$stats['confirmed_bookings'] = mysqli_fetch_assoc($result)['count'];

$query = "SELECT SUM(total_amount) as revenue FROM bookings WHERE payment_status = 'paid'";
$result = mysqli_query($conn, $query);
$stats['total_revenue'] = mysqli_fetch_assoc($result)['revenue'] ?? 0;

// Get recent bookings
$recent_bookings_query = "SELECT b.*, u.full_name, h.hotel_name, r.room_number 
                          FROM bookings b 
                          LEFT JOIN users u ON b.user_id = u.id 
                          LEFT JOIN hotels h ON b.hotel_id = h.id 
                          LEFT JOIN rooms r ON b.room_id = r.id 
                          ORDER BY b.booking_date DESC 
                          LIMIT 10";
$recent_bookings = mysqli_query($conn, $recent_bookings_query);

// Get recent activity
$activity_query = "SELECT al.*, u.full_name 
                   FROM activity_logs al 
                   LEFT JOIN users u ON al.user_id = u.id 
                   ORDER BY al.created_at DESC 
                   LIMIT 10";
$activities = mysqli_query($conn, $activity_query);
?>
<?php include 'includes/header.php'; ?>

<div class="dashboard-content">
    <h1>Admin Dashboard</h1>
    
    <div class="stats-grid">
        <div class="stat-card stat-blue">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <h3><?php echo $stats['total_customers']; ?></h3>
                <p>Total Customers</p>
            </div>
        </div>
        
        <div class="stat-card stat-pink">
            <div class="stat-icon">🏨</div>
            <div class="stat-info">
                <h3><?php echo $stats['total_hotels']; ?></h3>
                <p>Active Hotels</p>
            </div>
        </div>
        
        <div class="stat-card stat-green">
            <div class="stat-icon">🛏️</div>
            <div class="stat-info">
                <h3><?php echo $stats['available_rooms']; ?></h3>
                <p>Available Rooms</p>
            </div>
        </div>
        
        <div class="stat-card stat-purple">
            <div class="stat-icon">📅</div>
            <div class="stat-info">
                <h3><?php echo $stats['total_bookings']; ?></h3>
                <p>Total Bookings</p>
            </div>
        </div>
        
        <div class="stat-card stat-yellow">
            <div class="stat-icon">✅</div>
            <div class="stat-info">
                <h3><?php echo $stats['confirmed_bookings']; ?></h3>
                <p>Confirmed Bookings</p>
            </div>
        </div>
        
        <div class="stat-card stat-blue">
            <div class="stat-icon">💰</div>
            <div class="stat-info">
                <h3><?php echo formatCurrency($stats['total_revenue']); ?></h3>
                <p>Total Revenue</p>
            </div>
        </div>
    </div>
    
    <div class="dashboard-grid">
        <div class="dashboard-section">
            <h2>Recent Bookings</h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Hotel</th>
                            <th>Room</th>
                            <th>Check-in</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($booking = mysqli_fetch_assoc($recent_bookings)): ?>
                        <tr>
                            <td>#<?php echo $booking['id']; ?></td>
                            <td><?php echo $booking['full_name']; ?></td>
                            <td><?php echo $booking['hotel_name']; ?></td>
                            <td><?php echo $booking['room_number']; ?></td>
                            <td><?php echo date('M j, Y', strtotime($booking['check_in_date'])); ?></td>
                            <td><?php echo formatCurrency($booking['total_amount']); ?></td>
                            <td><span class="badge <?php echo getBookingStatusBadge($booking['booking_status']); ?>"><?php echo ucfirst($booking['booking_status']); ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="dashboard-section">
            <h2>Recent Activity</h2>
            <div class="activity-list">
                <?php while ($activity = mysqli_fetch_assoc($activities)): ?>
                <div class="activity-item">
                    <div class="activity-content">
                        <strong><?php echo $activity['full_name']; ?></strong>
                        <p><?php echo $activity['description']; ?></p>
                        <small><?php echo timeAgo($activity['created_at']); ?></small>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
