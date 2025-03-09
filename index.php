<?php
session_start();

// ฟังก์ชันสำหรับแสดงเมนู
function displayMenu() {
    if (isset($_SESSION['user_id'])) {
        $first_name = $_SESSION['first_name'] ?? 'User';
        $last_name = $_SESSION['last_name'] ?? '';
        ?>
        <p>Welcome, <?= htmlspecialchars($first_name) . ' ' . htmlspecialchars($last_name); ?>!</p>
        <a href="profile.php">Profile</a> |
        <a href="my_events.php">My events</a> |
        <a href="my_registrations.php">My Registrations</a> | 
        <a href="create_event.php">Create Event</a> | 
        <a href="event_list.php">Event List</a> | 
        <a href="logout.php">Logout</a>
        <?php
    } else {
        ?>
        <a href="login.php">Login</a> | 
        <a href="register.php">Register</a>
        <?php
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registration</title>
    <link rel="stylesheet" href="style_index.css">
</head>
<body>
    <h1>Welcome to Event Registration System</h1>
    
    <?php displayMenu(); ?>
</body>
</html>