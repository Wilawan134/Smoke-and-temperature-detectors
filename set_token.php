<?php
// รวมไฟล์เชื่อมต่อฐานข้อมูล
include 'iotserver.php';

$message = ""; // ตัวแปรสำหรับเก็บข้อความ

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token_line'];

    // ตรวจสอบและบันทึก Token ในฐานข้อมูล
    $sql = "UPDATE line_notify_token SET token='$token' WHERE id=1"; // สมมุติว่ามีตารางสำหรับบันทึก token

    if ($conn->query($sql) === TRUE) {
        $message = "Token บันทึกเรียบร้อยแล้ว"; // ข้อความยืนยัน
    } else {
        $message = "เกิดข้อผิดพลาด: " . $conn->error; // ข้อความข้อผิดพลาด
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ผลการบันทึก Token</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 50px;
        }
        .message {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none; /* เอาเส้นใต้ของลิงก์ออก */
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="message">
        <h2><?php echo $message; ?></h2>
    </div>
    <a class="button" href="Set Token_Linenotify.php">กลับไปที่หน้าก่อนหน้า</a>
</body>
</html>
