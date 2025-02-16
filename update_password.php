<?php
require 'sever.php'; // ไฟล์เชื่อมต่อฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT); // แฮชรหัสผ่านใหม่

    // อัปเดตรหัสผ่านใหม่ในฐานข้อมูล
    $stmt = $pdo->prepare('UPDATE user SET password = ? WHERE email = ?');
    $stmt->execute([$new_password, $email]);

    echo "Your password has been successfully updated. You can now log in with your new password.";
}
?>
