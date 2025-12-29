<?php
// Database configuration
$host = 'localhost:3307';
$username = 'root';
$password = '';
$database = 'hotel_booking_db';

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper functions
function sanitize($conn, $data) {
    return mysqli_real_escape_string($conn, trim($data));
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
}

function isCustomer() {
    return isLoggedIn() && $_SESSION['user_type'] === 'customer';
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['user_type'] === 'admin';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function getUserInfo($conn, $user_id) {
    $query = "SELECT * FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

function getHotelById($conn, $hotel_id) {
    $query = "SELECT * FROM hotels WHERE id = $hotel_id";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

function getRoomById($conn, $room_id) {
    $query = "SELECT r.*, rt.type_name, h.hotel_name, h.city 
              FROM rooms r 
              LEFT JOIN room_types rt ON r.room_type_id = rt.id 
              LEFT JOIN hotels h ON r.hotel_id = h.id 
              WHERE r.id = $room_id";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

function isRoomAvailable($conn, $room_id, $check_in, $check_out, $exclude_booking_id = null) {
    $query = "SELECT COUNT(*) as count FROM bookings 
              WHERE room_id = $room_id 
              AND booking_status NOT IN ('cancelled') 
              AND (
                  (check_in_date <= '$check_in' AND check_out_date > '$check_in') OR
                  (check_in_date < '$check_out' AND check_out_date >= '$check_out') OR
                  (check_in_date >= '$check_in' AND check_out_date <= '$check_out')
              )";
    
    if ($exclude_booking_id) {
        $query .= " AND id != $exclude_booking_id";
    }
    
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['count'] == 0;
}

function calculateTotalAmount($price_per_night, $check_in, $check_out) {
    $start = new DateTime($check_in);
    $end = new DateTime($check_out);
    $nights = $start->diff($end)->days;
    return $price_per_night * $nights;
}

function logActivity($conn, $user_id, $action, $description) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $description = sanitize($conn, $description);
    $query = "INSERT INTO activity_logs (user_id, action, description, ip_address) 
              VALUES ($user_id, '$action', '$description', '$ip')";
    mysqli_query($conn, $query);
}

function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $difference = time() - $timestamp;
    
    if ($difference < 60) {
        return "Just now";
    } elseif ($difference < 3600) {
        $minutes = floor($difference / 60);
        return $minutes . " minute" . ($minutes > 1 ? "s" : "") . " ago";
    } elseif ($difference < 86400) {
        $hours = floor($difference / 3600);
        return $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
    } elseif ($difference < 604800) {
        $days = floor($difference / 86400);
        return $days . " day" . ($days > 1 ? "s" : "") . " ago";
    } else {
        return date('M j, Y', $timestamp);
    }
}

function formatCurrency($amount) {
    return "$" . number_format($amount, 2);
}

function getBookingStatusBadge($status) {
    $badges = [
        'pending' => 'badge-warning',
        'confirmed' => 'badge-success',
        'cancelled' => 'badge-danger',
        'completed' => 'badge-info'
    ];
    return $badges[$status] ?? 'badge-secondary';
}

function getPaymentStatusBadge($status) {
    $badges = [
        'pending' => 'badge-warning',
        'paid' => 'badge-success',
        'refunded' => 'badge-info'
    ];
    return $badges[$status] ?? 'badge-secondary';
}

function uploadFile($file, $allowed_types, $max_size, $upload_dir) {
    if ($file['error'] !== 0) {
        return ['success' => false, 'message' => 'Error uploading file'];
    }
    
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'File too large'];
    }
    
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_name = uniqid() . '.' . $file_ext;
    $destination = $upload_dir . $file_name;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $file_name];
    } else {
        return ['success' => false, 'message' => 'Failed to move file'];
    }
}

function getStarRating($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $stars .= '⭐';
        } else {
            $stars .= '☆';
        }
    }
    return $stars;
}
?>
