<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Hotel Booking System</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <header class="dashboard-header">
                <h2>Hotel Booking System</h2>
                <div class="user-menu">
                    <span>👤 <?php echo $_SESSION['full_name']; ?></span>
                    <a href="../auth/logout.php" class="btn btn-sm btn-danger">Logout</a>
                </div>
            </header>
