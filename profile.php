<?php
session_start();
require 'db.php';

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือยัง
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$stmt = $pdo->prepare("SELECT email, first_name, last_name, gender, role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found!";
    exit();
}

// อัปเดตโปรไฟล์เมื่อมีการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $gender = $_POST['gender'];

    // อัปเดตข้อมูลในฐานข้อมูล
    $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, gender = ? WHERE id = ?");
    $stmt->execute([$first_name, $last_name, $gender, $user_id]);

    // อัปเดต session ด้วยค่าใหม่
    $_SESSION['first_name'] = $first_name;
    $_SESSION['last_name'] = $last_name;

    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
</head>
<body>
    <h1>My Profile</h1>

    <form method="POST">
        <label for="email">Email:</label><br>
        <input type="email" value="<?= htmlspecialchars($user['email']); ?>" disabled><br><br>

        <label for="role">Role:</label><br>
        <input type="text" value="<?= htmlspecialchars($user['role']); ?>" disabled><br><br>

        <label for="first_name">First Name:</label><br>
        <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']); ?>" required><br><br>

        <label for="last_name">Last Name:</label><br>
        <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']); ?>" required><br><br>

        <label for="gender">Gender:</label><br>
        <select name="gender" required>
            <option value="male" <?= ($user['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
            <option value="female" <?= ($user['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
            <option value="other" <?= ($user['gender'] == 'other') ? 'selected' : ''; ?>>Other</option>
        </select><br><br>

        <button type="submit">Update Profile</button>
    </form>

    <a href="index.php">Back to Home</a>
</body>
</html>
