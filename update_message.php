<?php
// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "iot";  // ใช้ฐานข้อมูล iot

$conn = new mysqli($servername, $username, $password, $dbname);

// เช็คการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบการรับค่าจากฟอร์ม (ข้อความใหม่)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ตรวจสอบว่า 'message' ถูกส่งมาหรือไม่
    if (isset($_POST['message']) && !empty($_POST['message'])) {
        // รับข้อความใหม่จากฟอร์ม
        $newMessage = $_POST['message'];

        // อัปเดตข้อความในตาราง line_notify_token
        $sql = "UPDATE line_notify_token SET LineMessage = ? WHERE id = 1";  // ใช้ตาราง line_notify_token และ id ที่ต้องการ
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $newMessage);  // s คือชนิดข้อมูล String

        // ตรวจสอบว่าอัปเดตสำเร็จหรือไม่
        if ($stmt->execute()) {
            echo "ข้อความอัปเดตสำเร็จ!";
        } else {
            echo "เกิดข้อผิดพลาดในการอัปเดตข้อความ: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "กรุณากรอกข้อความที่จะอัปเดต";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อความแจ้งเตือน</title>
</head>
<body>
    <h2>กรอกข้อความใหม่ที่จะส่งแจ้งเตือนผ่าน Line</h2>
    <form method="POST" action="update_message.php">
        <label for="message">กรอกข้อความ:</label><br>
        <textarea name="message" id="message" rows="4" cols="50"></textarea><br><br>
        <input type="submit" value="อัปเดตข้อความ">
    </form>
</body>
</html>
