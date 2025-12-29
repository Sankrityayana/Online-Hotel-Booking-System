<?php
require_once '../config.php';

if (!isCustomer()) {
    redirect('../auth/login.php');
}

$hotel_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$hotel = getHotelById($conn, $hotel_id);

if (!$hotel) {
    redirect('browse-hotels.php');
}

// Get available rooms
$rooms_query = "SELECT r.*, rt.type_name 
                FROM rooms r 
                LEFT JOIN room_types rt ON r.room_type_id = rt.id 
                WHERE r.hotel_id = $hotel_id AND r.status = 'available' 
                ORDER BY r.price_per_night";
$rooms = mysqli_query($conn, $rooms_query);
?>
<?php include 'includes/header.php'; ?>

<div class="dashboard-content">
    <div class="hotel-details">
        <div class="hotel-header">
            <div>
                <h1><?php echo $hotel['hotel_name']; ?></h1>
                <p class="hotel-location">📍 <?php echo $hotel['address']; ?>, <?php echo $hotel['city']; ?>, <?php echo $hotel['country']; ?></p>
                <p class="hotel-rating"><?php echo getStarRating($hotel['star_rating']); ?></p>
            </div>
        </div>
        
        <div class="hotel-description">
            <h2>About This Hotel</h2>
            <p><?php echo $hotel['description']; ?></p>
            
            <div class="hotel-contact">
                <p>📞 <?php echo $hotel['phone']; ?></p>
                <p>✉️ <?php echo $hotel['email']; ?></p>
            </div>
        </div>
        
        <h2>Available Rooms</h2>
        <div class="rooms-grid">
            <?php if (mysqli_num_rows($rooms) > 0): ?>
                <?php while ($room = mysqli_fetch_assoc($rooms)): ?>
                <div class="room-card">
                    <div class="room-image">
                        🛏️
                    </div>
                    <div class="room-info">
                        <h3>Room <?php echo $room['room_number']; ?> - <?php echo $room['type_name']; ?></h3>
                        <p class="room-details">
                            🛏️ <?php echo $room['bed_type']; ?> | 
                            👥 <?php echo $room['capacity']; ?> guests | 
                            📐 <?php echo $room['size_sqft']; ?> sqft | 
                            Floor <?php echo $room['floor']; ?>
                        </p>
                        <p class="room-description"><?php echo $room['description']; ?></p>
                        <div class="room-footer">
                            <div class="room-price">
                                <?php echo formatCurrency($room['price_per_night']); ?>/night
                            </div>
                            <a href="book-room.php?room_id=<?php echo $room['id']; ?>" class="btn btn-primary">Book Now</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No rooms available at this hotel.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
