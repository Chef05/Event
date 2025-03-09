<?php
session_start();
require 'db.php';

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ฟังก์ชันตรวจสอบและทำความสะอาด registration ID
function validateRegistrationId($registration_id) {
    $registration_id = filter_var($registration_id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $registration_id !== false ? (int)$registration_id : null;
}

// ฟังก์ชันอัปเดตสถานะการลงทะเบียน
function updateRegistrationStatus($pdo, $registration_id, $status) {
    $update_sql = "UPDATE registrations SET status = ? WHERE id = ?";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->execute([$status, $registration_id]);
}

// ฟังก์ชันอัปเดตสถานะเช็คอิน
function updateCheckInStatus($pdo, $registration_id, $checked_in) {
    $update_sql = "UPDATE registrations SET checked_in = ? WHERE id = ?";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->execute([$checked_in, $registration_id]);
}

// จัดการ POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['registration_id'], $_POST['action'])) {
        $registration_id = validateRegistrationId($_POST['registration_id']);
        $action = $_POST['action'];

        // ตรวจสอบ action
        if (!in_array($action, ['approve', 'reject', 'checkin', 'uncheckin'])) {
            echo "Invalid action."; // หรือ redirect ไปยังหน้า error
            exit;
        }

        if ($registration_id === null) {
            echo "Invalid registration ID."; // หรือ redirect ไปยังหน้า error
            exit;
        }

        try {
            if ($action == 'approve' || $action == 'reject') {
                $status = ($action == 'approve') ? 'approved' : 'rejected';
                updateRegistrationStatus($pdo, $registration_id, $status);
            } elseif ($action == 'checkin') {
                updateCheckInStatus($pdo, $registration_id, 1);
            } elseif ($action == 'uncheckin') {
                updateCheckInStatus($pdo, $registration_id, 0);
            }

            header("Location: my_events.php?success=1"); // เพิ่มพารามิเตอร์ success
            exit();

        } catch (PDOException $e) {
            // บันทึก error (ควรใช้ logging mechanism ที่เหมาะสม)
            error_log("Database error: " . $e->getMessage());
            // แสดงข้อความ error ที่เป็นมิตรต่อผู้ใช้
            echo "An error occurred while processing your request. Please try again later."; // หรือ redirect ไปยังหน้า error
            exit;
        }
    }
}

// ดึงข้อมูลกิจกรรมที่ผู้ใช้เป็นเจ้าของ
$sql = "SELECT * FROM events WHERE user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$events = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events</title>
    <link rel="stylesheet" href="style_my_event.css">
</head>
<body>
    <h1>My Events</h1>
    <a href="create_event.php">Create Event</a> |
    <a href="my_registrations.php">My Registrations</a> |
    <a href="event_list.php">Event List</a> |
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
        <a href="event_statistics.php">Event Statistics</a> |
    <?php endif; ?>
    <a href="logout.php">Logout</a>

    <?php if(isset($_GET['success']) && $_GET['success'] == 1): ?>
        <p class="success">Action completed successfully!</p>
    <?php endif; ?>

    <?php if (count($events) > 0): ?>
        <?php foreach ($events as $event): ?>
            <div>
                <h3><?= htmlspecialchars($event['title']); ?></h3>
                <p><?= htmlspecialchars($event['description']); ?></p>
                <p><strong>Start Date:</strong> <?= date('d/m/Y', strtotime($event['start_date'])); ?></p>
                <p><strong>End Date:</strong> <?= date('d/m/Y', strtotime($event['end_date'])); ?></p>

                <h4>Event Images</h4>
                <?php
                $image_sql = "SELECT * FROM event_images WHERE event_id = ?";
                $image_stmt = $pdo->prepare($image_sql);
                $image_stmt->execute([$event['id']]);
                $images = $image_stmt->fetchAll();
                ?>

                <?php if (count($images) > 0): ?>
                    <div class="event-images">
                        <?php foreach ($images as $image): ?>
                            <img src="<?= htmlspecialchars($image['image_url']); ?>" alt="Event Image">
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No images available for this event.</p>
                <?php endif; ?>

                <a href="edit_event.php?id=<?= $event['id']; ?>">Edit Event</a> |
                <form method="POST" action="delete_event.php" style="display:inline;">
                    <input type="hidden" name="event_id" value="<?= $event['id']; ?>">
                    <button type="submit" onclick="return confirm('Are you sure you want to delete this event?');">Delete Event</button>
                </form>

                <h4>Participants</h4>
                <?php
                $sql_participants = "SELECT r.id, r.user_id, u.first_name, u.last_name, r.status, r.checked_in
                                     FROM registrations r
                                     JOIN users u ON r.user_id = u.id
                                     WHERE r.event_id = ?";
                $stmt_participants = $pdo->prepare($sql_participants);
                $stmt_participants->execute([$event['id']]);
                $participants = $stmt_participants->fetchAll();
                ?>

                <?php if (count($participants) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Checked In</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($participants as $participant): ?>
                                <tr>
                                    <td><?= htmlspecialchars($participant['first_name'] . " " . $participant['last_name']); ?></td>
                                    <td><?= htmlspecialchars(ucfirst($participant['status'])); ?></td>
                                    <td>
                                        <?php if ($participant['checked_in']): ?>
                                            ✅ Yes
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="registration_id" value="<?= $participant['id']; ?>">
                                                <input type="hidden" name="action" value="uncheckin">
                                                <button type="submit" onclick="return confirm('Uncheck this participant?');">🔄 Uncheck</button>
                                            </form>
                                        <?php else: ?>
                                            ❌ No
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="registration_id" value="<?= $participant['id']; ?>">
                                                <input type="hidden" name="action" value="checkin">
                                                <button type="submit" onclick="return confirm('Check in this participant?');">📌 Check-In</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($participant['status'] == 'pending'): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="registration_id" value="<?= $participant['id']; ?>">
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" onclick="return confirm('Approve this participant?');">✅ Approve</button>
                                            </form>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="registration_id" value="<?= $participant['id']; ?>">
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" onclick="return confirm('Reject this participant?');">❌ Reject</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No registrations for this event.</p>
                <?php endif; ?>
                <hr>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>You have not created any events yet.</p>
    <?php endif; ?>

    <a href="index.php">Back to Homepage</a>

    <div id="lightbox" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.8); z-index: 1000;">
        <img id="lightbox-image" src="" style="display: block; max-width: 90%; max-height: 90%; margin: auto; position: absolute; top: 0; left: 0; bottom: 0; right: 0;">
        <span id="lightbox-close" style="position: absolute; top: 20px; right: 30px; font-size: 30px; color: white; cursor: pointer;">×</span>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const images = document.querySelectorAll('.event-images img');
        const lightbox = document.getElementById('lightbox');
        const lightboxImage = document.getElementById('lightbox-image');
        const lightboxClose = document.getElementById('lightbox-close');

        images.forEach(img => {
            img.addEventListener('click', function() {
                lightboxImage.src = this.src;
                lightbox.style.display = 'block';
            });
        });

        lightboxClose.addEventListener('click', function() {
            lightbox.style.display = 'none';
        });

        lightbox.addEventListener('click', function(event) {
            if (event.target === lightbox) {
                lightbox.style.display = 'none';
            }
        });
    });
    </script>
</body>
</html>