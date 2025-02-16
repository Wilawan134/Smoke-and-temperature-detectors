<?php 
    session_start();
    include('server.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* ใช้สีพื้นหลังที่สวยงาม */
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 10;
        }

        /* เมนู */
        .menu {
            font-size: 32px;
            font-weight: bold;
            color: #6e8efb;
            text-align: center;
            margin-bottom: 10px; /* ลดระยะห่างระหว่างคำว่า Register กับฟอร์ม */
            left: 50%;
            transform: translateX(+130%); /* ทำให้ข้อความอยู่กลางหน้า */
            background-color: #FFFFFF; /* เปลี่ยนพื้นหลังของกล่องข้อความ Register */
            padding: 15px; /* เพิ่ม padding เพื่อให้ข้อความไม่ชิดขอบ */
            border-radius: 10px; /* ขอบกล่องเป็นทรงกลม */
        }

        /* ฟอร์ม */
        .form-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
            box-sizing: border-box;
            margin-top: 10px; /* ลดระยะห่างระหว่างฟอร์มกับส่วนบน */
        }

        .form-container h2 {
            text-align: center;
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
        }

        /* กล่องข้อความ */
        .input-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            color: #333;
            display: block;
            margin-bottom: 8px;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
            border-color: #6e8efb;
            outline: none;
        }

        /* ปุ่มสมัคร */
        .btn {
            background-color: #6e8efb;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #a777e3;
        }

        /* กล่องข้อผิดพลาด */
        .error {
            background-color: #f44336;
            color: white;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        /* ข้อความเพิ่มเติม */
        p {
            text-align: center;
            color: #333;
            font-size: 14px;
        }

        a {
            color: #6e8efb;
            font-weight: bold;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="menu">
        <h2>Register</h2>
    </div>
    
    <div class="form-container">
        <form action="register_db.php" method="post">
            <?php include('errors.php'); ?>
            <?php if (isset($_SESSION['error'])) : ?>
                <div class="error">
                    <h3>
                        <?php
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                        ?>
                    </h3>
                </div>
            <?php endif ?>
            
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" name="username" required>
            </div>

            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="input-group">
                <label for="password_1">Password</label>
                <input type="password" name="password_1" required>
            </div>

            <div class="input-group">
                <label for="password_2">Confirm Password</label>
                <input type="password" name="password_2" required>
            </div>

            <div class="input-group">
                <button type="submit" name="reg_user" class="btn">Register</button>
            </div>
            
            <p>Already a member? <a href="login.php">Sign in</a></p>
        </form>
    </div>

</body>
</html>
