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

// ดึงข้อความล่าสุดจากตาราง line_notify_token
$sql = "SELECT LineMessage FROM line_notify_token WHERE id = 1";  // หรือ id ที่คุณต้องการ
$result = $conn->query($sql);

// ตรวจสอบว่าได้รับข้อมูลหรือไม่
if ($result->num_rows > 0) {
    // ดึงข้อมูลที่ได้
    $row = $result->fetch_assoc();
    echo $row["LineMessage"];  // ส่งข้อความที่ดึงมาให้ Arduino
} else {
    echo "ไม่พบข้อความ";
}

$conn->close();
?>
