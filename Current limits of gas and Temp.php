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
$dataType = 'all';  // ค่าเริ่มต้นคือ 'all', หมายถึงแสดงทั้งอุณหภูมิและแก๊ส


// รวมไฟล์เชื่อมต่อฐานข้อมูล
include 'iotserver.php';


// ดึงข้อมูลอุณหภูมิจากตาราง
// ดึงข้อมูลขีดจำกัดจากตาราง limits
$sql = "SELECT TempLimit, GasLimit FROM limits ORDER BY id DESC LIMIT 1"; // ดึงลิมิตล่าสุด
$result = $conn->query($sql);

$TempLimit = "N/A"; // ค่าตั้งต้น
$GasLimit = "N/A"; // ค่าตั้งต้น

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $TempLimit = $row['TempLimit']; // ดึงค่า TempLimit
    $GasLimit = $row['GasLimit'];   // ดึงค่า GasLimit
}
// ดึงข้อมูลจากฐานข้อมูลสำหรับแสดงในตาราง
// ตัวอย่าง SQL query สำหรับดึงข้อมูลจากตาราง logs
// ดึงข้อมูลจากฐานข้อมูลสำหรับแสดงในตาราง (ดึงแค่แถวล่าสุด)
$sql_data = "SELECT timestamp, temperature, gas_value FROM logs ORDER BY timestamp DESC LIMIT 1";
$result_data = $conn->query($sql_data);
$tableData = []; // ตัวแปรเก็บข้อมูลตาราง

if ($result_data && $result_data->num_rows > 0) {
    // ดึงแค่แถวล่าสุด
    while ($row_data = $result_data->fetch_assoc()) {
        $tableData[] = [$row_data['timestamp'], $row_data['temperature'], $row_data['gas_value']];
    }
}

$latestLog = "No logs available"; // ค่าตั้งต้น

// ดึงข้อมูลล่าสุดจากคอลัมน์ log_content
$sql_log = "SELECT log_content FROM logs ORDER BY timestamp DESC LIMIT 1"; // ใช้ชื่อคอลัมน์ที่ถูกต้อง
$result_log = $conn->query($sql_log);

if ($result_log && $result_log->num_rows > 0) {
    $row_log = $result_log->fetch_assoc();
    $latestLog = $row_log['log_content']; // ดึงข้อมูลล่าสุดจากคอลัมน์ log_content
} else {
    $latestLog = "No recent logs found."; // หากไม่พบข้อมูลจากฐานข้อมูล
}



// ดึงข้อมูลล่าสุดจากคอลัมน์ log_content
$sql_log = "SELECT log_content FROM logs ORDER BY timestamp DESC LIMIT 1"; // ใช้ชื่อคอลัมน์ที่ถูกต้อง
$result_log = $conn->query($sql_log);

$latestLog = "No logs available"; // ค่าตั้งต้น

if ($result_log && $result_log->num_rows > 0) {
    $row_log = $result_log->fetch_assoc();
    $latestLog = $row_log['log_content']; // ดึงข้อมูลล่าสุดจากคอลัมน์ log_content
} else {
    $latestLog = "No recent logs found."; // หากไม่พบข้อมูลจากฐานข้อมูล
}

echo $latestLog;
// ตรวจสอบหน้าปัจจุบัน
$current_page = basename($_SERVER['PHP_SELF']);

$conn->close(); // ปิดการเชื่อมต่อ
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ดูข้อมูล Smoke_Gas และ Temp</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
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
        border-radius: 0px; /* มุมโค้งเล็กน้อย */
    }

    .menu button {
        background-color: #0066FF !important; /* สีพื้นหลังของปุ่ม */
        color: white !important; /* สีตัวอักษรของปุ่ม */
        padding: 20px 20px; /* ระยะขอบภายใน */
        border: none; /* ไม่มีเส้นขอบ */
        border-radius: 5px; /* ขอบโค้งมน */
        font-size: 17px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    /* เปลี่ยนสีเมื่อชี้เมาส์ */
    .menu button:hover {
        background-color: #000066 !important; /* สีเมื่อชี้เมาส์ */
    }

    /* สไตล์ปุ่ม Logout */
    .menu button.logout {
        background-color: #ff4d4d !important;  /* เปลี่ยนสีพื้นหลังเป็นสีแดงอ่อน */
        color: white !important; /* สีตัวอักษรเป็นสีขาว */
        border: 2px solid #ff0000; /* เส้นขอบเป็นสีแดงเข้ม */
    }

    .menu button.logout:hover {
        background-color: #cc0000 !important; /* สีพื้นหลังเมื่อ hover จะเป็นสีแดงเข้ม */
    }

    .log-container {
        margin-top: 20px;
        padding: 10px;
        background-color: #f4f4f4;
        border-radius: 5px;
    }
    .menu button.active {
    background-color: #000066 !important; /* สีพื้นหลังเมื่อปุ่ม active */
    color: white !important; /* สีตัวอักษรของปุ่ม */
}
    </style>
</head>
<body>
    <!-- ส่วนของรูปปกด้านบน -->
    <div class="header">
        <h2></h2>
    </div>
    
    <!-- เมนู -->
    <div class="menu">
    <button class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" onclick="window.location='index.php'">หน้าแรก</button>
    <button class="<?php echo ($current_page == 'Current limits of gas and Temp.php') ? 'active' : ''; ?>" onclick="window.location='Current limits of gas and Temp.php'">ดูข้อมูลSmoke_GasและTemp</button>
    <button class="<?php echo ($current_page == 'Set gas_Temp limits.php') ? 'active' : ''; ?>" onclick="window.location='Set gas_Temp limits.php'">แก้ไขค่าการแจ้งเตือนSmoke_GasและTemp</button>
    <button class="<?php echo ($current_page == 'Set Token_Linenotify.php') ? 'active' : ''; ?>" onclick="window.location='Set Token_Linenotify.php'">แก้ไข Token การแจ้งเตือนผู้ใช้งานผ่านLine</button>
    <button class="<?php echo ($current_page == 'report.php') ? 'active' : ''; ?>" onclick="window.location='daily.php'">ออกรายงาน</button>
    <button class="<?php echo ($current_page == 'index.php?logout=1') ? 'active' : ''; ?>" onclick="window.location='index.php?logout=1'" class="logout">Logout</button>
    </div>

    <div class="homecontent">
        <h3>ขีดจำกัดของ Gas และ Temp ปัจจุบัน</h3>
        <div class="data-container">
            <div class="data-item">
                <h4>ขีดจำกัดของTemp</h4>
                <p><?php echo $TempLimit . ' °C'; ?> </p>
            </div>
            <div class="data-item">
                <h4>ขีดจำกัดของSmoke_Gas</h4>
                <p><?php echo $GasLimit; ?>  </p>
            </div>
        </div>

        <div class="table-container">
    <table>
        <thead>
            <tr>
                <p><?php echo "รายงานล่าสุดจากระบบ "  ?></p>
                <th>วันที่เวลา</th>
                <?php if ($dataType == 'temperature' || $dataType == 'all') { echo "<th>อุณหภูมิ</th>"; } ?>
                <?php if ($dataType == 'gas' || $dataType == 'all') { echo "<th>แก๊สและควัน</th>"; } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tableData as $data): ?>
                <tr>
                    <td><?php echo $data[0]; ?></td>
                    <?php if ($dataType == 'temperature' || $dataType == 'all') { echo "<td>{$data[1]}</td>"; } ?>
                    <?php if ($dataType == 'gas' || $dataType == 'all') { echo "<td>{$data[2]}</td>"; } ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


        

        <button class="refresh-button" onclick="window.location='Current limits of gas and Temp.php'">Refresh Data</button>
    </div>

    <script>
        function fetchData() {
            // คุณสามารถเพิ่มฟังก์ชันนี้เพื่อดึงค่าก๊าซถ้าต้องการ
            console.log("Data refresh functionality can be implemented if needed.");
        }
    </script>
</body>
</html>
