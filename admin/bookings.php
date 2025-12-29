<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../auth/login.php');
}

// Get filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Build query
$where = "1=1";
if ($filter === 'confirmed') {
    $where = "b.booking_status = 'confirmed'";
} elseif ($filter === 'pending') {
    $where = "b.booking_status = 'pending'";
} elseif ($filter === 'cancelled') {
    $where = "b.booking_status = 'cancelled'";
}

$bookings_query = "SELECT b.*, u.full_name, u.email, h.hotel_name, r.room_number, rt.type_name 
                   FROM bookings b 
                   LEFT JOIN users u ON b.user_id = u.id 
                   LEFT JOIN hotels h ON b.hotel_id = h.id 
                   LEFT JOIN rooms r ON b.room_id = r.id 
                   LEFT JOIN room_types rt ON r.room_type_id = rt.id 
                   WHERE $where 
                   ORDER BY b.booking_date DESC";
$bookings = mysqli_query($conn, $bookings_query);
?>
<?php include 'includes/header.php'; ?>

<div class="dashboard-content">
    <h1>All Bookings</h1>
    
    <div class="filter-bar">
        <a href="?filter=all" class="filter-btn <?php echo $filter === 'all' ? 'active' : ''; ?>">All</a>
        <a href="?filter=pending" class="filter-btn <?php echo $filter === 'pending' ? 'active' : ''; ?>">Pending</a>
        <a href="?filter=confirmed" class="filter-btn <?php echo $filter === 'confirmed' ? 'active' : ''; ?>">Confirmed</a>
        <a href="?filter=cancelled" class="filter-btn <?php echo $filter === 'cancelled' ? 'active' : ''; ?>">Cancelled</a>
    </div>
    
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Customer</th>
                    <th>Hotel</th>
                    <th>Room</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Guests</th>
                    <th>Amount</th>
                    <th>Booking Status</th>
                    <th>Payment Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($booking = mysqli_fetch_assoc($bookings)): ?>
                <tr>
                    <td>#<?php echo $booking['id']; ?></td>
                    <td>
                        <?php echo $booking['full_name']; ?><br>
                        <small><?php echo $booking['email']; ?></small>
                    </td>
                    <td><?php echo $booking['hotel_name']; ?></td>
                    <td><?php echo $booking['room_number']; ?> (<?php echo $booking['type_name']; ?>)</td>
                    <td><?php echo date('M j, Y', strtotime($booking['check_in_date'])); ?></td>
                    <td><?php echo date('M j, Y', strtotime($booking['check_out_date'])); ?></td>
                    <td><?php echo $booking['num_guests']; ?></td>
                    <td><?php echo formatCurrency($booking['total_amount']); ?></td>
                    <td><span class="badge <?php echo getBookingStatusBadge($booking['booking_status']); ?>"><?php echo ucfirst($booking['booking_status']); ?></span></td>
                    <td><span class="badge <?php echo getPaymentStatusBadge($booking['payment_status']); ?>"><?php echo ucfirst($booking['payment_status']); ?></span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
