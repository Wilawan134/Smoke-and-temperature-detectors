<?php
session_start();

if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You must log in first";
    header('location: login.php');
}

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
    header('location: login.php');
}

// รวมไฟล์เชื่อมต่อฐานข้อมูล
include 'iotserver.php';

$message = ""; // ตัวแปรสำหรับเก็บข้อความ

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $temp_limit = $_POST['temp_limit'];
    $gas_limit = $_POST['gas_limit'];

    // เตรียมคำสั่ง SQL ด้วย Prepared Statement เพื่อป้องกัน SQL Injection
    $sql = "UPDATE limits SET TempLimit = ?, GasLimit = ? WHERE id = 1";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("dd", $temp_limit, $gas_limit); // "dd" คือประเภทข้อมูลของตัวแปร (double)
        
        if ($stmt->execute()) {
            $message = "ตั้งค่าเรียบร้อยแล้ว"; // ข้อความยืนยัน
        } else {
            $message = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $stmt->error; // ข้อความข้อผิดพลาด
        }

        $stmt->close(); // ปิด Statement
    } else {
        $message = "ไม่สามารถเตรียมคำสั่ง SQL ได้: " . $conn->error; // ข้อความข้อผิดพลาด SQL
    }

    $conn->close(); // ปิดการเชื่อมต่อฐานข้อมูล
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ผลการตั้งค่า</title>
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
    <a class="button" href="Set gas_Temp limits.php">กลับไปที่หน้าตั้งค่า</a>
</body>
</html>
