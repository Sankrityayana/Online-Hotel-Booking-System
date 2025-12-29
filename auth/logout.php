<?php
require_once '../config.php';

if (isLoggedIn()) {
    logActivity($conn, $_SESSION['user_id'], 'Logout', 'User logged out');
}

session_destroy();
redirect('../index.php');
?>
