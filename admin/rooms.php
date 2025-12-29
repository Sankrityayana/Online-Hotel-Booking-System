<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../auth/login.php');
}

$hotel_id = isset($_GET['hotel_id']) ? (int)$_GET['hotel_id'] : 0;
$hotel = getHotelById($conn, $hotel_id);

if (!$hotel) {
    redirect('hotels.php');
}

$success = '';
$error = '';

// Handle room operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $room_type_id = (int)$_POST['room_type_id'];
            $room_number = sanitize($conn, $_POST['room_number']);
            $floor = (int)$_POST['floor'];
            $price_per_night = (float)$_POST['price_per_night'];
            $capacity = (int)$_POST['capacity'];
            $bed_type = sanitize($conn, $_POST['bed_type']);
            $size_sqft = (int)$_POST['size_sqft'];
            $description = sanitize($conn, $_POST['description']);
            
            $query = "INSERT INTO rooms (hotel_id, room_type_id, room_number, floor, price_per_night, capacity, bed_type, size_sqft, description) 
                      VALUES ($hotel_id, $room_type_id, '$room_number', $floor, $price_per_night, $capacity, '$bed_type', $size_sqft, '$description')";
            
            if (mysqli_query($conn, $query)) {
                $success = "Room added successfully";
                logActivity($conn, $_SESSION['user_id'], 'Add Room', "Added room $room_number to hotel ID: $hotel_id");
            } else {
                $error = "Failed to add room";
            }
        } elseif ($_POST['action'] === 'delete') {
            $room_id = (int)$_POST['room_id'];
            $query = "DELETE FROM rooms WHERE id = $room_id";
            
            if (mysqli_query($conn, $query)) {
                $success = "Room deleted successfully";
                logActivity($conn, $_SESSION['user_id'], 'Delete Room', "Deleted room ID: $room_id");
            } else {
                $error = "Failed to delete room";
            }
        }
    }
}

// Get room types
$room_types_query = "SELECT * FROM room_types ORDER BY type_name";
$room_types = mysqli_query($conn, $room_types_query);

// Get rooms for this hotel
$rooms_query = "SELECT r.*, rt.type_name 
                FROM rooms r 
                LEFT JOIN room_types rt ON r.room_type_id = rt.id 
                WHERE r.hotel_id = $hotel_id 
                ORDER BY r.floor, r.room_number";
$rooms = mysqli_query($conn, $rooms_query);
?>
<?php include 'includes/header.php'; ?>

<div class="dashboard-content">
    <div class="page-header">
        <div>
            <h1>Rooms - <?php echo $hotel['hotel_name']; ?></h1>
            <p><?php echo $hotel['city']; ?>, <?php echo $hotel['country']; ?></p>
        </div>
        <button class="btn btn-primary" onclick="document.getElementById('addModal').style.display='block'">Add New Room</button>
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
                    <th>Room Number</th>
                    <th>Floor</th>
                    <th>Type</th>
                    <th>Bed Type</th>
                    <th>Capacity</th>
                    <th>Size (sqft)</th>
                    <th>Price/Night</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($room = mysqli_fetch_assoc($rooms)): ?>
                <tr>
                    <td><?php echo $room['id']; ?></td>
                    <td><?php echo $room['room_number']; ?></td>
                    <td><?php echo $room['floor']; ?></td>
                    <td><?php echo $room['type_name']; ?></td>
                    <td><?php echo $room['bed_type']; ?></td>
                    <td><?php echo $room['capacity']; ?> guests</td>
                    <td><?php echo $room['size_sqft']; ?></td>
                    <td><?php echo formatCurrency($room['price_per_night']); ?></td>
                    <td><span class="badge badge-<?php echo $room['status'] === 'available' ? 'success' : 'warning'; ?>"><?php echo ucfirst($room['status']); ?></span></td>
                    <td>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Room Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('addModal').style.display='none'">&times;</span>
        <h2>Add New Room</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            
            <div class="form-row">
                <div class="form-group">
                    <label>Room Number *</label>
                    <input type="text" name="room_number" required>
                </div>
                
                <div class="form-group">
                    <label>Floor *</label>
                    <input type="number" name="floor" min="0" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Room Type *</label>
                    <select name="room_type_id" required>
                        <?php mysqli_data_seek($room_types, 0); ?>
                        <?php while ($type = mysqli_fetch_assoc($room_types)): ?>
                        <option value="<?php echo $type['id']; ?>"><?php echo $type['type_name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Bed Type *</label>
                    <input type="text" name="bed_type" placeholder="e.g., King, Queen" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Price per Night *</label>
                    <input type="number" name="price_per_night" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label>Capacity *</label>
                    <input type="number" name="capacity" min="1" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Size (sqft)</label>
                <input type="number" name="size_sqft" min="0">
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Add Room</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
