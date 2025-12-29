<?php
require_once '../config.php';

if (!isCustomer()) {
    redirect('../auth/login.php');
}

// Search parameters
$search = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';
$city = isset($_GET['city']) ? sanitize($conn, $_GET['city']) : '';
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 10000;

// Build query
$where = "h.status = 'active'";
if (!empty($search)) {
    $where .= " AND (h.hotel_name LIKE '%$search%' OR h.description LIKE '%$search%')";
}
if (!empty($city)) {
    $where .= " AND h.city LIKE '%$city%'";
}

$hotels_query = "SELECT h.*, 
                 (SELECT MIN(price_per_night) FROM rooms WHERE hotel_id = h.id AND status = 'available') as min_price,
                 (SELECT COUNT(*) FROM rooms WHERE hotel_id = h.id AND status = 'available') as available_rooms
                 FROM hotels h 
                 WHERE $where 
                 HAVING (min_price IS NULL OR (min_price >= $min_price AND min_price <= $max_price))
                 ORDER BY h.star_rating DESC, h.hotel_name";
$hotels = mysqli_query($conn, $hotels_query);

// Get cities for filter
$cities_query = "SELECT DISTINCT city FROM hotels WHERE status = 'active' ORDER BY city";
$cities = mysqli_query($conn, $cities_query);
?>
<?php include 'includes/header.php'; ?>

<div class="dashboard-content">
    <h1>Browse Hotels</h1>
    
    <div class="search-section">
        <form method="GET" class="search-form">
            <div class="search-row">
                <input type="text" name="search" placeholder="Search hotels..." value="<?php echo $search; ?>">
                
                <select name="city">
                    <option value="">All Cities</option>
                    <?php while ($city_row = mysqli_fetch_assoc($cities)): ?>
                    <option value="<?php echo $city_row['city']; ?>" <?php echo $city === $city_row['city'] ? 'selected' : ''; ?>>
                        <?php echo $city_row['city']; ?>
                    </option>
                    <?php endwhile; ?>
                </select>
                
                <input type="number" name="min_price" placeholder="Min Price" value="<?php echo $min_price > 0 ? $min_price : ''; ?>" step="10">
                <input type="number" name="max_price" placeholder="Max Price" value="<?php echo $max_price < 10000 ? $max_price : ''; ?>" step="10">
                
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>
    </div>
    
    <div class="hotels-grid">
        <?php if (mysqli_num_rows($hotels) > 0): ?>
            <?php while ($hotel = mysqli_fetch_assoc($hotels)): ?>
            <div class="hotel-card">
                <div class="hotel-image">
                    🏨
                </div>
                <div class="hotel-info">
                    <h3><?php echo $hotel['hotel_name']; ?></h3>
                    <p class="hotel-location">📍 <?php echo $hotel['city']; ?>, <?php echo $hotel['country']; ?></p>
                    <p class="hotel-rating"><?php echo getStarRating($hotel['star_rating']); ?></p>
                    <p class="hotel-description"><?php echo substr($hotel['description'], 0, 120); ?>...</p>
                    <div class="hotel-footer">
                        <div class="hotel-price">
                            <?php if ($hotel['min_price']): ?>
                                From <?php echo formatCurrency($hotel['min_price']); ?>/night
                            <?php else: ?>
                                No rooms available
                            <?php endif; ?>
                        </div>
                        <?php if ($hotel['available_rooms'] > 0): ?>
                            <a href="hotel-details.php?id=<?php echo $hotel['id']; ?>" class="btn btn-primary">View Rooms</a>
                        <?php else: ?>
                            <span class="badge badge-secondary">No Availability</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No hotels found matching your criteria.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
