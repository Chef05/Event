<?php
session_start();
require 'db.php';

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ฟังก์ชันสำหรับดึงข้อมูลกิจกรรมที่ผู้ใช้สมัครเข้าร่วมพร้อมรูปภาพ
function getRegistrationsWithImages($pdo, $user_id) {
    $sql = "SELECT e.*, r.status, r.checked_in
            FROM registrations r
            JOIN events e ON r.event_id = e.id
            WHERE r.user_id = ? 
            ORDER BY e.start_date DESC";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$user_id]);
        $registrations = $stmt->fetchAll();

        // ดึงข้อมูลรูปภาพสำหรับแต่ละกิจกรรม
        foreach ($registrations as &$registration) {
            $image_sql = "SELECT * FROM event_images WHERE event_id = ?";
            $image_stmt = $pdo->prepare($image_sql);
            $image_stmt->execute([$registration['id']]);
            $registration['images'] = $image_stmt->fetchAll();
        }

        return $registrations;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false; // ส่งกลับ false เมื่อมีข้อผิดพลาด
    }
}

$registrations = getRegistrationsWithImages($pdo, $user_id);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Registrations</title>
    <link rel="stylesheet" href="style_my_registration.css">
</head>
<body>
    <h1>My Registrations</h1>
    <a href="event_list.php">Browse Events</a> |
    <a href="my_events.php">My Events</a> |
    <a href="logout.php">Logout</a>

    <div class="event-container">
        <?php if ($registrations && count($registrations) > 0): ?>
            <?php foreach ($registrations as $event): ?>
                <div class="event-item">
                    <h3><?= htmlspecialchars($event['title']); ?></h3>
                    <p><?= nl2br(htmlspecialchars($event['description'])); ?></p>
                    <p><strong>Start Date:</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($event['start_date']))); ?></p>
                    <p><strong>End Date:</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($event['end_date']))); ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars(ucfirst($event['status'])); ?></p>
                    <p><strong>Checked In:</strong> <?= $event['checked_in'] ? '✅ Yes' : '❌ No'; ?></p>

                    <!-- ลิงค์ยกเลิกการลงทะเบียน -->
                    <a href="cancel_registration.php?event_id=<?= $event['id']; ?>">Cancel Registration</a>

                    <h4>Event Images</h4>
                    <?php if (isset($event['images']) && count($event['images']) > 0): ?>
                        <div class="event-images">
                            <?php foreach ($event['images'] as $image): ?>
                                <img src="<?= htmlspecialchars($image['image_url']); ?>" 
                                     alt="Event Image">
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No images available for this event.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>You have not registered for any events yet.</p>
        <?php endif; ?>
    </div>

    <a href="index.php">Back to Homepage</a>

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