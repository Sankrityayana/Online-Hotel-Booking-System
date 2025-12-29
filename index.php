<?php
require_once 'config.php';

// Get statistics
$query = "SELECT COUNT(*) as count FROM hotels WHERE status = 'active'";
$result = mysqli_query($conn, $query);
$total_hotels = mysqli_fetch_assoc($result)['count'];

$query = "SELECT COUNT(*) as count FROM rooms WHERE status = 'available'";
$result = mysqli_query($conn, $query);
$total_rooms = mysqli_fetch_assoc($result)['count'];

$query = "SELECT COUNT(*) as count FROM bookings WHERE booking_status = 'confirmed'";
$result = mysqli_query($conn, $query);
$total_bookings = mysqli_fetch_assoc($result)['count'];

// Get featured hotels
$featured_query = "SELECT h.*, 
                   (SELECT MIN(price_per_night) FROM rooms WHERE hotel_id = h.id AND status = 'available') as min_price,
                   (SELECT COUNT(*) FROM rooms WHERE hotel_id = h.id AND status = 'available') as available_rooms
                   FROM hotels h 
                   WHERE h.status = 'active' 
                   ORDER BY h.star_rating DESC 
                   LIMIT 6";
$featured_hotels = mysqli_query($conn, $featured_query);

// Get popular cities
$cities_query = "SELECT city, COUNT(*) as hotel_count 
                 FROM hotels 
                 WHERE status = 'active' 
                 GROUP BY city 
                 ORDER BY hotel_count DESC 
                 LIMIT 4";
$cities = mysqli_query($conn, $cities_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking System - Find Your Perfect Stay</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <h2>🏨 Hotel Booking System</h2>
            </div>
            <div class="nav-menu">
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <a href="admin/dashboard.php" class="btn btn-primary">Admin Dashboard</a>
                    <?php else: ?>
                        <a href="customer/dashboard.php" class="btn btn-primary">My Dashboard</a>
                    <?php endif; ?>
                    <a href="auth/logout.php" class="btn btn-secondary">Logout</a>
                <?php else: ?>
                    <a href="auth/login.php" class="btn btn-primary">Login</a>
                    <a href="auth/register.php" class="btn btn-secondary">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Find Your Perfect Stay</h1>
            <p>Book hotels worldwide with the best prices and deals</p>
            
            <div class="search-box">
                <form action="customer/browse-hotels.php" method="GET">
                    <input type="text" name="search" placeholder="Where do you want to go?" required>
                    <input type="date" name="check_in" min="<?php echo date('Y-m-d'); ?>" placeholder="Check-in">
                    <input type="date" name="check_out" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" placeholder="Check-out">
                    <button type="submit" class="btn btn-primary">Search Hotels</button>
                </form>
            </div>
            
            <div class="hero-stats">
                <div class="stat-item">
                    <h3><?php echo $total_hotels; ?>+</h3>
                    <p>Hotels</p>
                </div>
                <div class="stat-item">
                    <h3><?php echo $total_rooms; ?>+</h3>
                    <p>Rooms</p>
                </div>
                <div class="stat-item">
                    <h3><?php echo $total_bookings; ?>+</h3>
                    <p>Happy Customers</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Destinations -->
    <section class="destinations">
        <div class="container">
            <h2>Popular Destinations</h2>
            <div class="destinations-grid">
                <?php while ($city = mysqli_fetch_assoc($cities)): ?>
                <a href="customer/browse-hotels.php?city=<?php echo urlencode($city['city']); ?>" class="destination-card">
                    <div class="destination-icon">🏙️</div>
                    <h3><?php echo $city['city']; ?></h3>
                    <p><?php echo $city['hotel_count']; ?> hotels</p>
                </a>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Featured Hotels -->
    <section class="featured-hotels">
        <div class="container">
            <h2>Featured Hotels</h2>
            <div class="hotels-grid">
                <?php while ($hotel = mysqli_fetch_assoc($featured_hotels)): ?>
                <div class="hotel-card">
                    <div class="hotel-image">
                        🏨
                    </div>
                    <div class="hotel-info">
                        <h3><?php echo $hotel['hotel_name']; ?></h3>
                        <p class="hotel-location">📍 <?php echo $hotel['city']; ?>, <?php echo $hotel['country']; ?></p>
                        <p class="hotel-rating"><?php echo getStarRating($hotel['star_rating']); ?></p>
                        <div class="hotel-footer">
                            <div class="hotel-price">
                                <?php if ($hotel['min_price']): ?>
                                    From <?php echo formatCurrency($hotel['min_price']); ?>/night
                                <?php else: ?>
                                    Contact for rates
                                <?php endif; ?>
                            </div>
                            <?php if (isLoggedIn() && isCustomer()): ?>
                                <a href="customer/hotel-details.php?id=<?php echo $hotel['id']; ?>" class="btn btn-primary">View Details</a>
                            <?php else: ?>
                                <a href="auth/login.php" class="btn btn-primary">Login to Book</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="features">
        <div class="container">
            <h2>Why Choose Us</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">💰</div>
                    <h3>Best Prices</h3>
                    <p>We guarantee the best prices for your hotel bookings</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🌟</div>
                    <h3>Quality Selection</h3>
                    <p>Carefully curated hotels with verified reviews</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3>Secure Booking</h3>
                    <p>Your data and payments are 100% secure</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>Instant Confirmation</h3>
                    <p>Get instant booking confirmation via email</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Hotel Booking System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
