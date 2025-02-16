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
            border-radius: 0px;
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

        .report-form {
            margin: 20px;
            text-align: center;
        }

        .report-form select,
        .report-form input[type="date"],
        .report-form input[type="submit"] {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            width: 250px;
            margin: 10px;
        }

        .report-form input[type="submit"] {
            background-color: #5cb85c;
            color: white;
            cursor: pointer;
        }

        .report-form input[type="submit"]:hover {
            background-color: #4cae4c;
        }

        .table-container {
            margin-top: 20px;
            text-align: center;
        }

        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color:rgb(3, 142, 255);
        }

        table {
            background-color: #ffffff;
        }

        canvas {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2></h2>
    </div>

    <div class="menu">
        <button onclick="window.location='index.php'">หน้าแรก</button>
        <button onclick="window.location='Current limits of gas and Temp.php'">ดูข้อมูลSmoke_GasและTemp</button>
        <button onclick="window.location='Set gas_Temp limits.php'">แก้ไขค่าการแจ้งเตือนSmoke_GasและTemp</button>
        <button onclick="window.location='Set Token_Linenotify.php'">แก้ไข Token การแจ้งเตือนผ่าน Line</button>
        <button onclick="window.location='report.php'">ออกรายงาน</button>
        <button onclick="window.location='index.php?logout=1'" class="logout">Logout</button>
    </div>
</head>
<body>
    <h1>เลือกช่วงเวลาที่ต้องการ</h1>
    <form action="process.php" method="post">
        <label>
            <input type="radio" name="period" value="daily" required> รายวัน
        </label><br>
        <label>
            <input type="radio" name="period" value="monthly"> รายเดือน
        </label><br>
        <label>
            <input type="radio" name="period" value="yearly"> รายปี
        </label><br><br>
        <button type="submit">ยืนยัน</button>
    </form>
</body>
</html>
