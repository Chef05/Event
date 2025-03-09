<?php
session_start();
require 'db.php';

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบว่าผู้ใช้มีบทบาทเป็น admin หรือไม่
if ($_SESSION['role'] != 'admin') {
    echo "You are not authorized to access this page.";
    exit();
}

// ฟังก์ชันสำหรับดึงข้อมูลสถิติของกิจกรรม
function getEventStatistics($pdo) {
    $sql = "SELECT e.id, e.title,
                   COUNT(r.id) AS total_registrations,
                   SUM(CASE WHEN r.checked_in = 1 THEN 1 ELSE 0 END) AS total_checked_in,
                   SUM(CASE WHEN u.gender = 'male' THEN 1 ELSE 0 END) AS total_male,
                   SUM(CASE WHEN u.gender = 'female' THEN 1 ELSE 0 END) AS total_female,
                   SUM(CASE WHEN r.status = 'approved' THEN 1 ELSE 0 END) AS total_approved,
                   SUM(CASE WHEN r.status = 'rejected' THEN 1 ELSE 0 END) AS total_rejected
            FROM events e
            LEFT JOIN registrations r ON e.id = r.event_id
            LEFT JOIN users u ON r.user_id = u.id
            GROUP BY e.id, e.title
            ORDER BY e.title";

    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

$eventStatistics = getEventStatistics($pdo);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Statistics</title>
    <link rel="stylesheet" href="style_event_statistics.css">
</head>
<body>
    <h1>Event Statistics</h1>

    <a href="index.php">Back to Homepage</a> |
    <a href="my_events.php">My Events</a> |
    <a href="event_list.php">Event List</a> |
    <a href="logout.php">Logout</a>

    <?php if ($eventStatistics && count($eventStatistics) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Event Title</th>
                    <th>Total Registrations</th>
                    <th>Total Checked In</th>
                    <th>Total Male</th>
                    <th>Total Female</th>
                    <th>Total Approved</th>
                    <th>Total Rejected</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($eventStatistics as $statistic): ?>
                    <tr>
                        <td><?= htmlspecialchars($statistic['title']); ?></td>
                        <td><?= htmlspecialchars($statistic['total_registrations']); ?></td>
                        <td><?= htmlspecialchars($statistic['total_checked_in']); ?></td>
                        <td><?= htmlspecialchars($statistic['total_male']); ?></td>
                        <td><?= htmlspecialchars($statistic['total_female']); ?></td>
                        <td><?= htmlspecialchars($statistic['total_approved']); ?></td>
                        <td><?= htmlspecialchars($statistic['total_rejected']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No event statistics available.</p>
    <?php endif; ?>

</body>
</html>