<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../auth/login.php');
}

$success = '';
$error = '';

// Handle hotel operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $hotel_name = sanitize($conn, $_POST['hotel_name']);
            $description = sanitize($conn, $_POST['description']);
            $address = sanitize($conn, $_POST['address']);
            $city = sanitize($conn, $_POST['city']);
            $state = sanitize($conn, $_POST['state']);
            $country = sanitize($conn, $_POST['country']);
            $postal_code = sanitize($conn, $_POST['postal_code']);
            $phone = sanitize($conn, $_POST['phone']);
            $email = sanitize($conn, $_POST['email']);
            $star_rating = (int)$_POST['star_rating'];
            
            $query = "INSERT INTO hotels (hotel_name, description, address, city, state, country, postal_code, phone, email, star_rating) 
                      VALUES ('$hotel_name', '$description', '$address', '$city', '$state', '$country', '$postal_code', '$phone', '$email', $star_rating)";
            
            if (mysqli_query($conn, $query)) {
                $success = "Hotel added successfully";
                logActivity($conn, $_SESSION['user_id'], 'Add Hotel', "Added hotel: $hotel_name");
            } else {
                $error = "Failed to add hotel";
            }
        } elseif ($_POST['action'] === 'delete') {
            $hotel_id = (int)$_POST['hotel_id'];
            $query = "DELETE FROM hotels WHERE id = $hotel_id";
            
            if (mysqli_query($conn, $query)) {
                $success = "Hotel deleted successfully";
                logActivity($conn, $_SESSION['user_id'], 'Delete Hotel', "Deleted hotel ID: $hotel_id");
            } else {
                $error = "Failed to delete hotel";
            }
        }
    }
}

// Get all hotels
$hotels_query = "SELECT h.*, 
                 (SELECT COUNT(*) FROM rooms WHERE hotel_id = h.id) as total_rooms,
                 (SELECT COUNT(*) FROM bookings WHERE hotel_id = h.id) as total_bookings
                 FROM hotels h 
                 ORDER BY h.created_at DESC";
$hotels = mysqli_query($conn, $hotels_query);
?>
<?php include 'includes/header.php'; ?>

<div class="dashboard-content">
    <div class="page-header">
        <h1>Hotel Management</h1>
        <button class="btn btn-primary" onclick="document.getElementById('addModal').style.display='block'">Add New Hotel</button>
    </div>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Hotel Name</th>
                    <th>City</th>
                    <th>Country</th>
                    <th>Rating</th>
                    <th>Rooms</th>
                    <th>Bookings</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($hotel = mysqli_fetch_assoc($hotels)): ?>
                <tr>
                    <td><?php echo $hotel['id']; ?></td>
                    <td><?php echo $hotel['hotel_name']; ?></td>
                    <td><?php echo $hotel['city']; ?></td>
                    <td><?php echo $hotel['country']; ?></td>
                    <td><?php echo getStarRating($hotel['star_rating']); ?></td>
                    <td><?php echo $hotel['total_rooms']; ?></td>
                    <td><?php echo $hotel['total_bookings']; ?></td>
                    <td><span class="badge badge-<?php echo $hotel['status'] === 'active' ? 'success' : 'secondary'; ?>"><?php echo ucfirst($hotel['status']); ?></span></td>
                    <td>
                        <a href="rooms.php?hotel_id=<?php echo $hotel['id']; ?>" class="btn btn-sm btn-info">View Rooms</a>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Hotel Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('addModal').style.display='none'">&times;</span>
        <h2>Add New Hotel</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            
            <div class="form-row">
                <div class="form-group">
                    <label>Hotel Name *</label>
                    <input type="text" name="hotel_name" required>
                </div>
                
                <div class="form-group">
                    <label>Star Rating *</label>
                    <select name="star_rating" required>
                        <option value="3">3 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="5">5 Stars</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label>Address *</label>
                <input type="text" name="address" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>City *</label>
                    <input type="text" name="city" required>
                </div>
                
                <div class="form-group">
                    <label>State</label>
                    <input type="text" name="state">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Country *</label>
                    <input type="text" name="country" required>
                </div>
                
                <div class="form-group">
                    <label>Postal Code</label>
                    <input type="text" name="postal_code">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone">
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Add Hotel</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
