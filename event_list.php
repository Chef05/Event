<?php
session_start();
require 'db.php';

// ฟังก์ชันสำหรับดึงข้อมูลกิจกรรมตามเงื่อนไข
function getEvents($pdo, $search_term = "", $start_date = "", $end_date = "") {
    $sql = "SELECT e.*, u.first_name, u.last_name 
            FROM events e 
            LEFT JOIN users u ON e.user_id = u.id 
            WHERE 1=1 "; // WHERE 1=1 ทำให้เพิ่มเงื่อนไข AND ได้ง่าย

    $params = [];

    if (!empty($search_term)) {
        $sql .= " AND (e.title LIKE :search_term OR e.description LIKE :search_term) ";
        $params[':search_term'] = '%' . $search_term . '%';
    }

    if (!empty($start_date) && !empty($end_date)) {
        $sql .= " AND e.start_date >= :start_date AND e.end_date <= :end_date ";
        $params[':start_date'] = $start_date;
        $params[':end_date'] = $end_date;
    }

    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false; // ส่งกลับ false เมื่อมีข้อผิดพลาด
    }
}

$events = [];
$search_term = $start_date = $end_date = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search_term = htmlspecialchars($_POST['search_term'] ?? '');
    $start_date = htmlspecialchars($_POST['start_date'] ?? '');
    $end_date = htmlspecialchars($_POST['end_date'] ?? '');

    // ตรวจสอบความถูกต้องของรูปแบบวันที่
    if (!empty($start_date) && !preg_match("/^\d{4}-\d{2}-\d{2}$/", $start_date)) {
      $start_date_error = "Invalid start date format. Use YYYY-MM-DD.";
      $start_date = ""; // Clear the variable to avoid incorrect queries
    }

    if (!empty($end_date) && !preg_match("/^\d{4}-\d{2}-\d{2}$/", $end_date)) {
        $end_date_error = "Invalid end date format. Use YYYY-MM-DD.";
        $end_date = ""; // Clear the variable to avoid incorrect queries
    }

    $events = getEvents($pdo, $search_term, $start_date, $end_date);

    if ($events === false) {
        echo "<p class='error'>An error occurred while fetching events. Please try again later.</p>";
    }
} else {
    $events = getEvents($pdo); // ดึงกิจกรรมทั้งหมด
    if ($events === false) {
        echo "<p class='error'>An error occurred while fetching events. Please try again later.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event List</title>
    <link rel="stylesheet" href="style_event_list.css">
</head>
<body>
    <h1>Event List</h1>

    <!-- ฟอร์มค้นหากิจกรรม -->
    <form method="POST" action="event_list.php">
        <input type="text" name="search_term" placeholder="ค้นหากิจกรรม" value="<?= htmlspecialchars($search_term) ?>">

        <!-- ช่วงวันที่ -->
        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
        <?php if (isset($start_date_error)): ?>
          <p class="error"><?= htmlspecialchars($start_date_error) ?></p>
        <?php endif; ?>

        <label for="end_date">End Date:</label>
        <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
        <?php if (isset($end_date_error)): ?>
          <p class="error"><?= htmlspecialchars($end_date_error) ?></p>
        <?php endif; ?>

        <button type="submit">ค้นหา</button>
    </form>

    <?php if (isset($_SESSION['user_id'])): ?>
        <p>Welcome, <?= isset($_SESSION['first_name']) && isset($_SESSION['last_name']) ? htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']) : 'User'; ?>!</p>
        <a href="my_events.php">My Events</a> |
        <a href="my_registrations.php">My Registrations</a> |
        <a href="create_event.php">Create Event</a> |
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a> |
        <a href="register.php">Register</a>
    <?php endif; ?>

    <h2>All Events</h2>
    <div class="event-container">
        <?php if ($events && count($events) > 0): ?>
            <?php foreach ($events as $event): ?>
                <div class="event-item">
                    <h3><?= htmlspecialchars($event['title']); ?></h3>
                    <p><?= htmlspecialchars($event['description']); ?></p>
                    <p><strong>Organized by:</strong> <?= !empty($event['first_name']) && !empty($event['last_name']) ? htmlspecialchars($event['first_name'] . ' ' . $event['last_name']) : 'Unknown'; ?></p>

                    <!-- แสดงวันที่เริ่มต้นและวันที่สิ้นสุด -->
                    <p><strong>Start Date:</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($event['start_date']))); ?></p>
                    <p><strong>End Date:</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($event['end_date']))); ?></p>

                    <!-- ดึงและแสดงภาพทั้งหมดที่เกี่ยวข้องกับกิจกรรม -->
                    <?php
                    $imageStmt = $pdo->prepare("SELECT image_url FROM event_images WHERE event_id = ?");
                    $imageStmt->execute([$event['id']]);
                    $images = $imageStmt->fetchAll();
                    ?>

                    <?php if (count($images) > 0): ?>
                        <p><strong>Event Images:</strong></p>
                        <div class="event-images">
                            <?php foreach ($images as $image): ?>
                                <img src="<?= htmlspecialchars($image['image_url']); ?>" alt="<?= htmlspecialchars($event['title']); ?>">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <br>
                    <a href="register_event.php?event_id=<?= $event['id']; ?>">Register</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No events available.</p>
        <?php endif; ?>
    </div>

    <a href="index.php">กลับไปที่หน้าแรก</a>

    <!-- Lightbox -->
    <div id="lightbox">
        <img id="lightbox-image" src="">
        <span id="lightbox-close">×</span>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('.event-images img');
            const lightbox = document.getElementById('lightbox');
            const lightboxImage = document.getElementById('lightbox-image');
            const lightboxClose = document.getElementById('lightbox-close');

            images.forEach(img => {
                img.addEventListener('click', function(event) {
                    event.stopPropagation(); // ป้องกันไม่ให้ lightbox ปิดทันทีที่คลิกรูปภาพ
                    lightboxImage.src = this.src;
                    lightbox.style.display = 'block';
                });
            });

            lightboxClose.addEventListener('click', function() {
                lightbox.style.display = 'none';
            });

            lightbox.addEventListener('click', function(event) {
                lightbox.style.display = 'none';
            });
        });
    </script>
</body>
</html>