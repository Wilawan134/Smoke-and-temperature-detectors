<?php  
session_start();

if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You must log in first";
    header('location: login.php');
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
    header('location: login.php');
    exit();
}

// รวมไฟล์เชื่อมต่อฐานข้อมูล
include 'iotserver.php';

// ดึงข้อมูลขีดจำกัดล่าสุดจากตาราง limits
$sql = "SELECT TempLimit, GasLimit FROM limits ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

// ตั้งค่าค่าตั้งต้น
$TempLimit = "N/A";  
$GasLimit = "N/A";  

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $TempLimit = $row['TempLimit'];  
    $GasLimit = $row['GasLimit'];  
}
// ตรวจสอบหน้าปัจจุบัน
$current_page = basename($_SERVER['PHP_SELF']);
$conn->close();  // ปิดการเชื่อมต่อฐานข้อมูล
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iot ตั้งค่าขีดจำกัดที่ต้องการแจ้งเตือน</title>
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
        font-size: 30px;
        border-radius: 0px;
    }

    .menu button {
        background-color: #0066FF !important;
        color: white !important;
        padding: 20px 20px;
        border: none;
        border-radius: 5px;
        font-size: 17px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .menu button:hover {
        background-color: #000066 !important;
    }

    .menu button.logout {
        background-color: #990000 !important;
        color: white !important;
    }

    .current-limits {
        position: absolute;
        top: 700px;
        left: 20px;
        background-color: rgba(255, 255, 255, 0.8);
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        text-align: center;
        z-index: 1000; /* เพื่อให้อยู่เหนือองค์ประกอบอื่น */
    }

    .current-limits h3 {
        margin: 0 0 10px;
        font-size: 16px;
        font-weight: bold;
    }

    .current-limits p {
        margin: 0;
        font-size: 14px;
    }

    .homecontent {
        margin: 20px;
    }

    .refresh-button {
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #0066FF;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 5px;
    }

    input[type="number"]::placeholder {
        color: #888; /* ทำให้ข้อความใน placeholder จาง */
        font-style: italic;
    }
    .menu button.active {
    background-color: #000066 !important; /* สีพื้นหลังเมื่อปุ่ม active */
    color: white !important; /* สีตัวอักษรของปุ่ม */
}
    </style>
</head>
<body>
    <div class="header">
       <!--  <h2>IoT ระบบแจ้งเตือน</h2>-->
    </div>

    <div class="menu">
    <button class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" onclick="window.location='index.php'">หน้าแรก</button>
    <button class="<?php echo ($current_page == 'Current limits of gas and Temp.php') ? 'active' : ''; ?>" onclick="window.location='Current limits of gas and Temp.php'">ดูข้อมูลSmoke_GasและTemp</button>
    <button class="<?php echo ($current_page == 'Set gas_Temp limits.php') ? 'active' : ''; ?>" onclick="window.location='Set gas_Temp limits.php'">แก้ไขค่าการแจ้งเตือนSmoke_GasและTemp</button>
    <button class="<?php echo ($current_page == 'Set Token_Linenotify.php') ? 'active' : ''; ?>" onclick="window.location='Set Token_Linenotify.php'">แก้ไข Token การแจ้งเตือนผู้ใช้งานผ่านLine</button>
    <button class="<?php echo ($current_page == 'report.php') ? 'active' : ''; ?>" onclick="window.location='daily.php'">ออกรายงาน</button>
    <button class="<?php echo ($current_page == 'index.php?logout=1') ? 'active' : ''; ?>" onclick="window.location='index.php?logout=1'" class="logout">Logout</button>
    </div>

    <!-- ส่วนข้อมูลขีดจำกัดปัจจุบันในมุมขวาบน -->
    <div class="current-limits">
        <h3>ขีดจำกัดปัจจุบัน</h3>
        <div>
            <h4>Temp</h4>
            <p><?php echo $TempLimit . ' °C'; ?></p>
        </div>
        <div>
            <h4>Smoke_Gas</h4>
            <p><?php echo $GasLimit; ?></p>
        </div>
    </div>

    <div class="homecontent">
        <h3>ตั้งค่าขีดจำกัดที่ต้องการแจ้งเตือน</h3>
        <form id="limitForm" action="set_limits.php" method="POST" onsubmit="return confirmSetLimits();">
        <label for="temp_limit">ขีดจำกัดTemp (°C): </label>
        <input type="number" id="temp_limit" name="temp_limit" min="30" max="60" placeholder="ควรตั้งไม่ต่ำกว่า30°C และไม่มากกว่า60°C" required> 
        <br>
        <label for="gas_limit">ขีดจำกัดSmoke_Gas: </label>
        <input type="number" id="gas_limit" name="gas_limit" min="300" max="2000" placeholder="ควรตั้งไม่ต่ำกว่า300 และไม่มากกว่า2000" required>
        <br>
        <button type="submit">ตั้งค่า</button>
    </form>

    </div>

    <script>
        function confirmSetLimits() {
            return confirm('คุณแน่ใจหรือว่าต้องการบันทึกค่าขีดจำกัดนี้?');
        }
    </script>
</body>
</html>
