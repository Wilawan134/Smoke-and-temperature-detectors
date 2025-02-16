<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$conn = new mysqli("localhost", "root", "", "register_db");

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Database connected successfully.<br>";  // ข้อความตรวจสอบการเชื่อมต่อ
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    echo "Email to update: " . $email . "<br>"; // แสดงอีเมลที่กำลังจะอัปเดต

    // ตรวจสอบอีเมลในฐานข้อมูล
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // สร้าง token ใหม่
        $token = bin2hex(random_bytes(50));
        echo "Generated Token: " . $token . "<br>"; // แสดงค่า token ที่สร้าง

        // เริ่มต้นธุรกรรม
        $conn->begin_transaction();

        // อัปเดตฐานข้อมูล
        $stmt = $conn->prepare("UPDATE user SET reset_token = ?, token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
        if ($stmt === false) {
            die('MySQL prepare error: ' . $conn->error);
        }

        $stmt->bind_param("ss", $token, $email);
        if ($stmt->error) {
            die('MySQL bind_param error: ' . $stmt->error);
        }

        if ($stmt->execute()) {
            $conn->commit();  // ยืนยันการอัปเดต
            echo "Token successfully updated in the database.<br>";
        } else {
            $conn->rollback();  // ถ้ามีข้อผิดพลาดให้ย้อนกลับการอัปเดต
            echo "Error executing query: " . $stmt->error;  // ข้อความข้อผิดพลาด
        }

        // ตรวจสอบผลการอัปเดต
        $checkStmt = $conn->prepare("SELECT reset_token, token_expiry FROM user WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $userData = $checkResult->fetch_assoc();
            echo "reset_token: " . $userData['reset_token'] . "<br>";
            echo "token_expiry: " . $userData['token_expiry'] . "<br>";
        } else {
            echo "Error: Token not found in the database after update.<br>";
        }

        // ส่งอีเมล
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'Wilawan080945@gmail.com';
            $mail->Password = 'bjdw wnmt lcew xswm'; // ใช้ App Password ของ Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('Wilawan080945@gmail.com', 'Your Website');
            $mail->addAddress($email);

            // สร้างลิงก์รีเซ็ตรหัสผ่าน
            $reset_link = "http://localhost/Smoke%20and%20temperature%20detectors/reset_password.php?token=$token";
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Click <a href='$reset_link'>here</a> to reset your password. This link will expire in 1 hour.";

            // ส่งอีเมล
            if ($mail->send()) {
                echo '<script>
                        alert("ลิงก์รีเซ็ตรหัสผ่านได้ถูกส่งไปยังอีเมลของคุณแล้ว");
                        window.location.href = "login.php";
                      </script>';
            } else {
                echo "Error sending email: " . $mail->ErrorInfo;
            }
        } catch (Exception $e) {
            echo "Error sending email: {$mail->ErrorInfo}";
        }
    } else {
        // ถ้าไม่พบอีเมลในฐานข้อมูล
        echo '<script>
                alert("ไม่พบบัญชีผู้ใช้ที่ใช้อีเมลนี้");
                window.location.href = "forget_password.php";
                </script>';
    }
}
?>
