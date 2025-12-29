<?php
require_once '../config.php';

if (!isCustomer()) {
    redirect('../auth/login.php');
}

// Get statistics
$user_id = $_SESSION['user_id'];

$query = "SELECT COUNT(*) as count FROM bookings WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);
$total_bookings = mysqli_fetch_assoc($result)['count'];

$query = "SELECT COUNT(*) as count FROM bookings WHERE user_id = $user_id AND booking_status = 'confirmed'";
$result = mysqli_query($conn, $query);
$confirmed_bookings = mysqli_fetch_assoc($result)['count'];

$query = "SELECT COUNT(*) as count FROM bookings WHERE user_id = $user_id AND booking_status = 'pending'";
$result = mysqli_query($conn, $query);
$pending_bookings = mysqli_fetch_assoc($result)['count'];

$query = "SELECT SUM(total_amount) as total FROM bookings WHERE user_id = $user_id AND payment_status = 'paid'";
$result = mysqli_query($conn, $query);
$total_spent = mysqli_fetch_assoc($result)['total'] ?? 0;

// Get recent bookings
$bookings_query = "SELECT b.*, h.hotel_name, h.city, r.room_number, rt.type_name 
                   FROM bookings b 
                   LEFT JOIN hotels h ON b.hotel_id = h.id 
                   LEFT JOIN rooms r ON b.room_id = r.id 
                   LEFT JOIN room_types rt ON r.room_type_id = rt.id 
                   WHERE b.user_id = $user_id 
                   ORDER BY b.booking_date DESC 
                   LIMIT 5";
$recent_bookings = mysqli_query($conn, $bookings_query);
?>
<?php include 'includes/header.php'; ?>

<div class="dashboard-content">
    <h1>My Dashboard</h1>
    
    <div class="stats-grid">
        <div class="stat-card stat-blue">
            <div class="stat-icon">📅</div>
            <div class="stat-info">
                <h3><?php echo $total_bookings; ?></h3>
                <p>Total Bookings</p>
            </div>
        </div>
        
        <div class="stat-card stat-green">
            <div class="stat-icon">✅</div>
            <div class="stat-info">
                <h3><?php echo $confirmed_bookings; ?></h3>
                <p>Confirmed</p>
            </div>
        </div>
        
        <div class="stat-card stat-yellow">
            <div class="stat-icon">⏳</div>
            <div class="stat-info">
                <h3><?php echo $pending_bookings; ?></h3>
                <p>Pending</p>
            </div>
        </div>
        
        <div class="stat-card stat-pink">
            <div class="stat-icon">💰</div>
            <div class="stat-info">
                <h3><?php echo formatCurrency($total_spent); ?></h3>
                <p>Total Spent</p>
            </div>
        </div>
    </div>
    
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Recent Bookings</h2>
            <a href="my-bookings.php" class="btn btn-sm btn-primary">View All</a>
        </div>
        
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Hotel</th>
                        <th>Room</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($recent_bookings) > 0): ?>
                        <?php while ($booking = mysqli_fetch_assoc($recent_bookings)): ?>
                        <tr>
                            <td>#<?php echo $booking['id']; ?></td>
                            <td>
                                <?php echo $booking['hotel_name']; ?><br>
                                <small><?php echo $booking['city']; ?></small>
                            </td>
                            <td><?php echo $booking['room_number']; ?> (<?php echo $booking['type_name']; ?>)</td>
                            <td><?php echo date('M j, Y', strtotime($booking['check_in_date'])); ?></td>
                            <td><?php echo date('M j, Y', strtotime($booking['check_out_date'])); ?></td>
                            <td><?php echo formatCurrency($booking['total_amount']); ?></td>
                            <td><span class="badge <?php echo getBookingStatusBadge($booking['booking_status']); ?>"><?php echo ucfirst($booking['booking_status']); ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">No bookings yet. <a href="browse-hotels.php">Browse hotels</a></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
