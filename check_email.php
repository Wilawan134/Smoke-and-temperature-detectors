<?php
require 'server.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // ถ้าเจออีเมล ให้เปลี่ยนไปยังหน้า reset_password.php พร้อมส่งค่าอีเมลไปด้วย
        header("Location: reset_password2.php?email=$email");
        exit();
    } else {
        echo "Email not found.";
    }

    $stmt->close();
}

$conn->close();
?>
