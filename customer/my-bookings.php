<?php
require_once '../config.php';

if (!isCustomer()) {
    redirect('../auth/login.php');
}

$user_id = $_SESSION['user_id'];

// Get all bookings
$bookings_query = "SELECT b.*, h.hotel_name, h.city, r.room_number, rt.type_name 
                   FROM bookings b 
                   LEFT JOIN hotels h ON b.hotel_id = h.id 
                   LEFT JOIN rooms r ON b.room_id = r.id 
                   LEFT JOIN room_types rt ON r.room_type_id = rt.id 
                   WHERE b.user_id = $user_id 
                   ORDER BY b.booking_date DESC";
$bookings = mysqli_query($conn, $bookings_query);
?>
<?php include 'includes/header.php'; ?>

<div class="dashboard-content">
    <h1>My Bookings</h1>
    
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Hotel</th>
                    <th>Room</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Guests</th>
                    <th>Amount</th>
                    <th>Booking Status</th>
                    <th>Payment Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($bookings) > 0): ?>
                    <?php while ($booking = mysqli_fetch_assoc($bookings)): ?>
                    <tr>
                        <td>#<?php echo $booking['id']; ?></td>
                        <td>
                            <?php echo $booking['hotel_name']; ?><br>
                            <small><?php echo $booking['city']; ?></small>
                        </td>
                        <td><?php echo $booking['room_number']; ?> (<?php echo $booking['type_name']; ?>)</td>
                        <td><?php echo date('M j, Y', strtotime($booking['check_in_date'])); ?></td>
                        <td><?php echo date('M j, Y', strtotime($booking['check_out_date'])); ?></td>
                        <td><?php echo $booking['num_guests']; ?></td>
                        <td><?php echo formatCurrency($booking['total_amount']); ?></td>
                        <td><span class="badge <?php echo getBookingStatusBadge($booking['booking_status']); ?>"><?php echo ucfirst($booking['booking_status']); ?></span></td>
                        <td><span class="badge <?php echo getPaymentStatusBadge($booking['payment_status']); ?>"><?php echo ucfirst($booking['payment_status']); ?></span></td>
                        <td>
                            <a href="booking-confirmation.php?booking_id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-info">View Details</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" style="text-align: center;">
                            No bookings yet. <a href="browse-hotels.php">Browse hotels</a> to make your first booking!
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
