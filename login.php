<?php
session_start();
require_once 'db.php';

// ฟังก์ชันสร้าง token CSRF
function generateCSRFToken() {
    return bin2hex(random_bytes(32));
}

// ตรวจสอบ token CSRF
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ฟังก์ชันสำหรับตรวจสอบข้อมูลผู้ใช้และสร้าง session
function loginUser($pdo, $email, $password) {
    // Sanitize email
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // ตรวจสอบข้อมูลผู้ใช้จากฐานข้อมูล
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // เก็บข้อมูลผู้ใช้ใน session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['first_name'] = $user['first_name'];  // เก็บชื่อ
        $_SESSION['last_name'] = $user['last_name'];    // เก็บนามสกุล
        $_SESSION['role'] = $user['role'];              // เก็บบทบาทผู้ใช้

        return true;
    } else {
        return false;
    }
}

// สร้าง CSRF token เมื่อเปิดหน้า
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = generateCSRFToken();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $csrf_token = $_POST['csrf_token'];

    // ตรวจสอบ CSRF token
    if (!validateCSRFToken($csrf_token)) {
        $error_message = "Invalid CSRF token.";
    } else {
        // ตรวจสอบข้อมูลผู้ใช้
        if (loginUser($pdo, $email, $password)) {
            // เปลี่ยนเส้นทางไปยังหน้า index.php
            header("Location: index.php");
            exit();
        } else {
            $error_message = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style_login.css">
</head>
<body>
    <h1>Login</h1>

    <?php if (isset($error_message)): ?>
        <p class="error"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <!-- เพิ่ม CSRF token -->
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
</body>
</html>