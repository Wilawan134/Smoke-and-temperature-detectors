<?php
// ตรวจสอบหน้าปัจจุบัน
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เลือกช่วงเวลา</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            background-image: url('รูปภาพ/well.jpg');
            background-size: cover;
            background-position: center;
            height: 500px;
            width: 100%;
            text-align: center;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .menu {
            background-color: #333;
            text-align: center;
            padding: 10px 0;
        }

        .menu button {
            background-color: #007bff;
            color: white;
            padding: 20px 20px;
            border: none;
            border-radius: 5px;
            font-size: 17px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin: 0 5px;
        }

        .menu button:hover {
            background-color: #0056b3;
        }

        .menu button.logout {
            background-color: #d9534f;
            color: white;
        }

        .menu button.logout:hover {
            background-color: #c9302c;
        }

        .content {
            padding: 30px;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 80vh;
        }

        .report-form {
            background-color: #fff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            margin: 0 auto;
            text-align: center;
        }

        .report-form h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 30px;
        }

        .radio-group {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
        }

        /* ซ่อน radio ดั้งเดิม */
        .radio-group input[type="radio"] {
            display: none;
        }

        /* ปุ่มแบบกำหนดเอง */
        .radio-group label {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100px;
            height: 50px;
            background-color: #f0f0f0;
            border: 2px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .radio-group input[type="radio"]:checked + label {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .radio-group label:hover {
            background-color: #e7e7e7;
        }

        .report-form button {
            padding: 14px 50px;
            background-color: #5cb85c;
            color: white;
            font-size: 18px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            margin-top: 20px;
        }

        .report-form button:hover {
            background-color: #4cae4c;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .menu button.active {
    background-color: #000066 !important; /* สีพื้นหลังเมื่อปุ่ม active */
    color: white !important; /* สีตัวอักษรของปุ่ม */
}
    </style>
</head>
<body>
    <div class="header">
        <h2></h2>
    </div>

    <div class="menu">
    <button class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" onclick="window.location='index.php'">หน้าแรก</button>
    <button class="<?php echo ($current_page == 'Current limits of gas and Temp.php') ? 'active' : ''; ?>" onclick="window.location='Current limits of gas and Temp.php'">ดูข้อมูลSmoke_GasและTemp</button>
    <button class="<?php echo ($current_page == 'Set gas_Temp limits.php') ? 'active' : ''; ?>" onclick="window.location='Set gas_Temp limits.php'">แก้ไขค่าการแจ้งเตือนSmoke_GasและTemp</button>
    <button class="<?php echo ($current_page == 'Set Token_Linenotify.php') ? 'active' : ''; ?>" onclick="window.location='Set Token_Linenotify.php'">แก้ไข Token การแจ้งเตือนผู้ใช้งานผ่านLine</button>
    <button class="<?php echo ($current_page == 'report.php') ? 'active' : ''; ?>" onclick="window.location='report.php'">ออกรายงาน</button>
    <button class="<?php echo ($current_page == 'index.php?logout=1') ? 'active' : ''; ?>" onclick="window.location='index.php?logout=1'" class="logout">Logout</button>
    </div>

    <div class="content">
        <div class="report-form">
            <h2>กรุณาเลือกช่วงเวลา</h2>
            <form action="process.php" method="post">
                <div class="radio-group">
                    <input type="radio" id="daily" name="period" value="daily" required>
                    <label for="daily">รายวัน</label>

                    <input type="radio" id="monthly" name="period" value="monthly">
                    <label for="monthly">รายเดือน</label>

                    <input type="radio" id="yearly" name="period" value="yearly">
                    <label for="yearly">รายปี</label>
                </div>
                <button type="submit">ยืนยัน</button>
            </form>
        </div>
    </div>
</body>
</html>
