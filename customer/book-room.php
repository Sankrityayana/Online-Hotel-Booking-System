<?php
require_once '../config.php';

if (!isCustomer()) {
    redirect('../auth/login.php');
}

$room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;
$room = getRoomById($conn, $room_id);

if (!$room) {
    redirect('browse-hotels.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $check_in = sanitize($conn, $_POST['check_in']);
    $check_out = sanitize($conn, $_POST['check_out']);
    $num_guests = (int)$_POST['num_guests'];
    $special_requests = sanitize($conn, $_POST['special_requests']);
    
    // Validate dates
    if (strtotime($check_in) < strtotime(date('Y-m-d'))) {
        $error = "Check-in date cannot be in the past";
    } elseif (strtotime($check_out) <= strtotime($check_in)) {
        $error = "Check-out date must be after check-in date";
    } elseif (!isRoomAvailable($conn, $room_id, $check_in, $check_out)) {
        $error = "Room is not available for selected dates";
    } else {
        // Calculate total
        $total_amount = calculateTotalAmount($room['price_per_night'], $check_in, $check_out);
        
        // Insert booking
        $query = "INSERT INTO bookings (user_id, room_id, hotel_id, check_in_date, check_out_date, num_guests, total_amount, special_requests, booking_status, payment_status) 
                  VALUES ($user_id, $room_id, {$room['hotel_id']}, '$check_in', '$check_out', $num_guests, $total_amount, '$special_requests', 'pending', 'pending')";
        
        if (mysqli_query($conn, $query)) {
            $booking_id = mysqli_insert_id($conn);
            logActivity($conn, $user_id, 'Book Room', "Booked room {$room['room_number']} at {$room['hotel_name']}");
            redirect("booking-confirmation.php?booking_id=$booking_id");
        } else {
            $error = "Booking failed. Please try again.";
        }
    }
}

// Calculate default dates
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$next_week = date('Y-m-d', strtotime('+7 days'));
?>
<?php include 'includes/header.php'; ?>

<div class="dashboard-content">
    <div class="booking-form-container">
        <h1>Book Room</h1>
        
        <div class="room-summary">
            <h2><?php echo $room['hotel_name']; ?></h2>
            <p><?php echo $room['city']; ?></p>
            <h3>Room <?php echo $room['room_number']; ?> - <?php echo $room['type_name']; ?></h3>
            <p>🛏️ <?php echo $room['bed_type']; ?> | 👥 <?php echo $room['capacity']; ?> guests</p>
            <p class="price-highlight"><?php echo formatCurrency($room['price_per_night']); ?> per night</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" class="booking-form">
            <div class="form-row">
                <div class="form-group">
                    <label>Check-in Date *</label>
                    <input type="date" name="check_in" min="<?php echo date('Y-m-d'); ?>" value="<?php echo $tomorrow; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Check-out Date *</label>
                    <input type="date" name="check_out" min="<?php echo $tomorrow; ?>" value="<?php echo $next_week; ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Number of Guests *</label>
                <input type="number" name="num_guests" min="1" max="<?php echo $room['capacity']; ?>" value="1" required>
                <small>Maximum capacity: <?php echo $room['capacity']; ?> guests</small>
            </div>
            
            <div class="form-group">
                <label>Special Requests</label>
                <textarea name="special_requests" rows="4" placeholder="Any special requests or requirements..."></textarea>
            </div>
            
            <div class="booking-summary">
                <p>Price per night: <?php echo formatCurrency($room['price_per_night']); ?></p>
                <p><small>Total amount will be calculated based on your selected dates</small></p>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Confirm Booking</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
