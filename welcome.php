<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Website</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-image: url('รูปภาพ/fire.jpg'); /* รูปห้องครัวไฟไหม้ */
            background-size: cover;
            background-position: center;
            height: 100vh;
            margin: 0;
            color: #fff; /* เปลี่ยนสีตัวอักษรให้ดูชัดเจนบนพื้นหลัง */
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            background-attachment: fixed;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.81); /* เพิ่มความโปร่งแสง */
            padding: 30px; /* ลดขนาด padding */
            border-radius: 20px;
            max-width: 500px; /* ลดความกว้างของกล่อง */
            width: 60%; /* ปรับขนาดให้เล็กลง */
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2); /* ลดความเข้มของเงา */
            margin-left: 0%; /* เลื่อนกล่องไปทางขวา */
        }

        h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        p {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .btn-login {
            background-color: #0066FF;
            color: white;
            padding: 12px 25px;
            font-size: 1.1rem;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background-color: #0047b3;
            transform: translateY(-3px); /* ลดการเคลื่อนไหวให้ไม่มากเกินไป */
        }

        .btn-login:active {
            transform: translateY(1px); /* คลิกแล้วให้ปุ่มยุบลงน้อยลง */
        }

        .footer {
            position: absolute;
            bottom: 10px;
            width: 100%;
            text-align: center;
            font-size: 0.9rem;
            color: #fff;
            padding: 5px;
        }

        .footer a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Our Website</h1>
        <p>We're glad to have you here! Please log in to continue.</p>
        <a href="login.php"><button class="btn-login">Login</button></a>
    </div>

    <div class="footer">
        <p>&copy; 2025 Your Website. All Rights Reserved.</p>
    </div>
</body>
</html>
