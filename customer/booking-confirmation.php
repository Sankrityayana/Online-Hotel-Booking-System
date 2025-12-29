<?php
require_once '../config.php';

if (!isCustomer()) {
    redirect('../auth/login.php');
}

$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
$user_id = $_SESSION['user_id'];

$query = "SELECT b.*, h.hotel_name, h.address, h.city, h.country, h.phone as hotel_phone, 
          r.room_number, rt.type_name, r.bed_type 
          FROM bookings b 
          LEFT JOIN hotels h ON b.hotel_id = h.id 
          LEFT JOIN rooms r ON b.room_id = r.id 
          LEFT JOIN room_types rt ON r.room_type_id = rt.id 
          WHERE b.id = $booking_id AND b.user_id = $user_id";
$result = mysqli_query($conn, $query);
$booking = mysqli_fetch_assoc($result);

if (!$booking) {
    redirect('my-bookings.php');
}

// Calculate nights
$check_in = new DateTime($booking['check_in_date']);
$check_out = new DateTime($booking['check_out_date']);
$nights = $check_in->diff($check_out)->days;
?>
<?php include 'includes/header.php'; ?>

<div class="dashboard-content">
    <div class="confirmation-container">
        <div class="confirmation-header">
            <div class="success-icon">✅</div>
            <h1>Booking Confirmed!</h1>
            <p>Your booking has been successfully placed.</p>
            <p class="booking-id">Booking ID: #<?php echo $booking['id']; ?></p>
        </div>
        
        <div class="confirmation-details">
            <div class="detail-section">
                <h2>Hotel Information</h2>
                <p><strong><?php echo $booking['hotel_name']; ?></strong></p>
                <p>📍 <?php echo $booking['address']; ?></p>
                <p><?php echo $booking['city']; ?>, <?php echo $booking['country']; ?></p>
                <p>📞 <?php echo $booking['hotel_phone']; ?></p>
            </div>
            
            <div class="detail-section">
                <h2>Room Details</h2>
                <p><strong>Room <?php echo $booking['room_number']; ?></strong></p>
                <p>Type: <?php echo $booking['type_name']; ?></p>
                <p>Bed: <?php echo $booking['bed_type']; ?></p>
            </div>
            
            <div class="detail-section">
                <h2>Stay Information</h2>
                <p><strong>Check-in:</strong> <?php echo date('l, F j, Y', strtotime($booking['check_in_date'])); ?></p>
                <p><strong>Check-out:</strong> <?php echo date('l, F j, Y', strtotime($booking['check_out_date'])); ?></p>
                <p><strong>Number of Nights:</strong> <?php echo $nights; ?></p>
                <p><strong>Guests:</strong> <?php echo $booking['num_guests']; ?></p>
            </div>
            
            <div class="detail-section">
                <h2>Payment Information</h2>
                <p><strong>Total Amount:</strong> <?php echo formatCurrency($booking['total_amount']); ?></p>
                <p><strong>Booking Status:</strong> <span class="badge <?php echo getBookingStatusBadge($booking['booking_status']); ?>"><?php echo ucfirst($booking['booking_status']); ?></span></p>
                <p><strong>Payment Status:</strong> <span class="badge <?php echo getPaymentStatusBadge($booking['payment_status']); ?>"><?php echo ucfirst($booking['payment_status']); ?></span></p>
            </div>
            
            <?php if ($booking['special_requests']): ?>
            <div class="detail-section">
                <h2>Special Requests</h2>
                <p><?php echo nl2br($booking['special_requests']); ?></p>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="confirmation-actions">
            <a href="my-bookings.php" class="btn btn-primary">View My Bookings</a>
            <a href="browse-hotels.php" class="btn btn-secondary">Browse More Hotels</a>
        </div>
        
        <div class="confirmation-note">
            <p>📧 A confirmation email has been sent to your registered email address.</p>
            <p>⚠️ Please note that this is a pending booking. The hotel will confirm your reservation shortly.</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
