<?php
session_start();
require_once 'db.php';

// ฟังก์ชันสร้าง CSRF token
function generateCSRFToken() {
    return bin2hex(random_bytes(32));
}

// ฟังก์ชันตรวจสอบ CSRF token
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ฟังก์ชันตรวจสอบและทำความสะอาดข้อมูลที่ผู้ใช้ป้อนเข้ามา
function validateRegistrationData($data, &$errors) {
    $errors = [];

    // ตรวจสอบอีเมล
    $email = trim($data['email']);
    if (empty($email)) {
        $errors['email'] = "จำเป็นต้องระบุอีเมล";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "รูปแบบอีเมลไม่ถูกต้อง";
    }

    // ตรวจสอบชื่อจริง
    $first_name = trim($data['first_name']);
    if (empty($first_name)) {
        $errors['first_name'] = "จำเป็นต้องระบุชื่อจริง";
    } elseif (strlen($first_name) > 100) {
        $errors['first_name'] = "ชื่อจริงต้องไม่เกิน 100 ตัวอักษร";
    }

    // ตรวจสอบนามสกุล
    $last_name = trim($data['last_name']);
    if (empty($last_name)) {
        $errors['last_name'] = "จำเป็นต้องระบุนามสกุล";
    } elseif (strlen($last_name) > 100) {
        $errors['last_name'] = "นามสกุลต้องไม่เกิน 100 ตัวอักษร";
    }

    // ตรวจสอบเพศ
    $gender = $data['gender'];
    if (!in_array($gender, ['male', 'female', 'other'])) {
        $errors['gender'] = "รูปแบบเพศไม่ถูกต้อง";
    }

    // ตรวจสอบรหัสผ่าน
    $password = $data['password'];
    if (empty($password)) {
        $errors['password'] = "จำเป็นต้องระบุรหัสผ่าน";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร";
    }
    // ตรวจสอบบทบาท
    $role = $data['role'];
    if (!in_array($role, ['volunteer', 'admin'])) {
        $errors['role'] = "รูปแบบบทบาทไม่ถูกต้อง";
    }

    return empty($errors);
}

// สร้าง CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = generateCSRFToken();
}

// ตรวจสอบว่ามีการส่งข้อมูลมาจากฟอร์มหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบ CSRF token
    if (!validateCSRFToken($_POST['csrf_token'])) {
        $error_message = "CSRF token ไม่ถูกต้อง";
    } else {
        // ตรวจสอบและทำความสะอาดข้อมูลที่ผู้ใช้ป้อนเข้ามา
        if (validateRegistrationData($_POST, $errors)) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $gender = $_POST['gender'];
            $role = $_POST['role'];

            // ตรวจสอบว่ามีอีเมลนี้ในฐานข้อมูลหรือไม่
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $existing_user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing_user) {
                $error_message = "อีเมลนี้มีผู้ใช้งานแล้ว";
            } else {
                // แฮชรหัสผ่าน
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // เพิ่มผู้ใช้ใหม่ลงในฐานข้อมูล
                $stmt = $pdo->prepare("INSERT INTO users (email, password, first_name, last_name, gender, role)
                                       VALUES (:email, :password, :first_name, :last_name, :gender, :role)");
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':first_name', $first_name);
                $stmt->bindParam(':last_name', $last_name);
                $stmt->bindParam(':gender', $gender);
                $stmt->bindParam(':role', $role);
                $stmt->execute();

                // นำผู้ใช้ไปยังหน้า login พร้อมข้อความแจ้งเตือน
                $_SESSION['success_message'] = "ลงทะเบียนสำเร็จ โปรดเข้าสู่ระบบ";
                header("Location: login.php");
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style_register.css">
</head>
<body>
    <h1>Register</h1>

    <?php if (isset($error_message)): ?>
        <p class="error"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <label for="email">Email:</label>
        <input type="email" name="email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
        <?php if (isset($errors['email'])): ?>
            <p class="error"><?= htmlspecialchars($errors['email']) ?></p>
        <?php endif; ?>
        <br>

        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" required value="<?= isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : '' ?>">
        <?php if (isset($errors['first_name'])): ?>
            <p class="error"><?= htmlspecialchars($errors['first_name']) ?></p>
        <?php endif; ?>
        <br>

        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" required value="<?= isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : '' ?>">
        <?php if (isset($errors['last_name'])): ?>
            <p class="error"><?= htmlspecialchars($errors['last_name']) ?></p>
        <?php endif; ?>
        <br>

        <label for="gender">Gender:</label>
        <select name="gender">
            <option value="male" <?= (isset($_POST['gender']) && $_POST['gender'] == 'male') ? 'selected' : '' ?>>Male</option>
            <option value="female" <?= (isset($_POST['gender']) && $_POST['gender'] == 'female') ? 'selected' : '' ?>>Female</option>
            <option value="other" <?= (isset($_POST['gender']) && $_POST['gender'] == 'other') ? 'selected' : '' ?>>Other</option>
        </select>
        <?php if (isset($errors['gender'])): ?>
            <p class="error"><?= htmlspecialchars($errors['gender']) ?></p>
        <?php endif; ?>
        <br>

        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <?php if (isset($errors['password'])): ?>
            <p class="error"><?= htmlspecialchars($errors['password']) ?></p>
        <?php endif; ?>
        <br>

        <label for="role">Role:</label>
        <select name="role">
            <option value="volunteer" <?= (isset($_POST['role']) && $_POST['role'] == 'volunteer') ? 'selected' : '' ?>>Volunteer</option>
            <option value="admin" <?= (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
        </select>
        <?php if (isset($errors['role'])): ?>
            <p class="error"><?= htmlspecialchars($errors['role']) ?></p>
        <?php endif; ?>
        <br>

        <button type="submit">Register</button>
    </form>

    <a href="login.php">Already have an account? Login here</a>
</body>
</html>