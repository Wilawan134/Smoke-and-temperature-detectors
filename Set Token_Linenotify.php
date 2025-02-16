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

// ดึงข้อมูลจากทั้งสองตาราง logs และ line_notify_token
$sql = "SELECT logs.gas_value, logs.temperature, logs.timestamp, line_notify_token.token
        FROM logs
        JOIN line_notify_token ON logs.id = line_notify_token.id
        ORDER BY logs.timestamp DESC LIMIT 1";

$result = $conn->query($sql);

// ตั้งค่าค่าตั้งต้นของ Token
$existingToken = "N/A";  
$gas_value = $temperature = "";

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $existingToken = $row['token'];  // ดึงค่า token จากตาราง line_notify_token
    $gas_value = $row['gas_value'];  // ดึงค่า gas_value จากตาราง logs
    $temperature = $row['temperature'];  // ดึงค่า temperature จากตาราง logs
}

// ฟังก์ชันสำหรับการส่งข้อความผ่าน Line Notify
function sendLineNotify($token, $message) {
    $url = "https://notify-api.line.me/api/notify";
    $data = array("message" => $message);
    $headers = array(
        "Authorization: Bearer " . $token
    );

    // ส่งข้อมูลผ่าน cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

// ตรวจสอบการรับค่าจากฟอร์ม (ข้อความใหม่)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['message']) && !empty($_POST['message'])) {
        $newMessage = $_POST['message'];

        // อัปเดตข้อความในตาราง line_notify_token
        $sql = "UPDATE line_notify_token SET LineMessage = ? WHERE id = 1";  
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $newMessage);

        if ($stmt->execute()) {
            // ส่งข้อความแจ้งเตือนผ่าน Line Notify
            $message = "อัพเดต Line Notify";
            sendLineNotify($existingToken, $message); 
            $_SESSION['success'] = "ข้อความอัปเดตสำเร็จและส่งการแจ้งเตือนแล้ว!";
        } else {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการอัปเดตข้อความ: " . $stmt->error;
        }

        $stmt->close();

        // เปลี่ยนเส้นทางหลังประมวลผล
        header('Location: Set Token_Linenotify.php');
        exit();
    } else {
        $_SESSION['error'] = "กรุณากรอกข้อความที่จะอัปเดต";
    }
}

// ตรวจสอบหน้าปัจจุบัน
$current_page = basename($_SERVER['PHP_SELF']);

// ดึงข้อมูลข้อความจากฐานข้อมูล
$sql = "SELECT LineMessage FROM line_notify_token WHERE id = 1";  
$result = $conn->query($sql);
$existingMessage = "ข้อความไม่พบ";  // ค่าตั้งต้น

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $existingMessage = $row['LineMessage'];  // ดึงค่าข้อความที่บันทึกไว้
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตั้งค่า Token Line Notify</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* สไตล์ของหน้าเว็บ */
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
        }

        .menu button {
            background-color: #0066FF;
            color: white;
            padding: 20px 20px;
            border: none;
            border-radius: 5px;
            font-size: 17px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .menu button:hover {
            background-color: #000066;
        }

        .menu button.logout {
            background-color: #ff4d4d;
            color: white;
            border: none;
        }

        .menu button.logout:hover {
            background-color: #cc0000;
        }

        #message {
            width: 100%;
            max-width: 600px;
            padding: 10px;
            font-size: 16px;
        }

        /* ป๊อปอัพ */
        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .popup-content {
            background-color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px;
        }

        .popup button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }

        .popup .cancel {
            background-color: #ff4d4d;
            color: white;
        }

        .popup .confirm {
            background-color: #4CAF50;
            color: white;
        }

        input[type="button"] {
            background-color: #4CAF50;
            color: white;
            padding: 15px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 170px;
        }

        input[type="button"]:hover {
            background-color: #45a049;
        }

        .homecontent input[type="text"] {
            width: 150%;
            max-width: 700px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .homecontent button {
            background-color: #4CAF50;
            color: white;
            padding: 15px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .homecontent button:hover {
            background-color: #45a049;
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
        <h3>ตั้งค่า Token Line Notify</h3>
        <form id="tokenForm" action="Set Token_Linenotify.php" method="POST" onsubmit="return confirmToken();">
            <label for="token_line">Token Line Notify: </label>
            <input type="text" id="token_line" name="token_line" placeholder="Token ปัจจุบัน: <?php echo $existingToken; ?>" required>
            <br><br>
            <button type="submit">บันทึก Token</button>
        </form>

        <h3></h3>
        <form id="messageForm" method="POST" action="" onsubmit="return false;">
            <label for="message">กรอกข้อความใหม่ที่ต้องการใช้ในการแจ้งเตือน:</label><br>
            <textarea name="message" id="message" rows="4" cols="50" placeholder="ข้อความปัจจุบัน: <?php echo $existingMessage; ?>" style="color: rgba(0, 0, 0, 0.5);"></textarea><br><br>
            <input type="button" value="อัปเดตข้อความ" onclick="showConfirmation();">
        </form>
    </div>

    <!-- ป๊อปอัพยืนยัน -->
    <div class="popup" id="popup">
        <div class="popup-content">
            <h3>คุณแน่ใจหรือว่าต้องการบันทึกข้อความนี้?</h3>
            <button class="confirm" onclick="submitForm()">ตกลง</button>
            <button class="cancel" onclick="closePopup()">ยกเลิก</button>
        </div>
    </div>

    <script>
        // ฟังก์ชันแสดงป๊อปอัพยืนยัน
        function showConfirmation() {
            var message = document.getElementById('message').value;
            if (message.trim() === "") {
                alert("กรุณากรอกข้อความที่จะอัปเดต");
                return;
            }
            document.getElementById('popup').style.display = 'flex';
        }

        // ฟังก์ชันสำหรับส่งฟอร์มหลังยืนยัน
        function submitForm() {
            document.getElementById('messageForm').submit(); // ส่งฟอร์มจริง
            closePopup(); // ปิดป๊อปอัพหลังจากส่งฟอร์ม
            alert("ข้อความอัปเดตสำเร็จ!"); // แสดงข้อความแจ้งเตือนสำเร็จ
        }

        // ฟังก์ชันปิดป๊อปอัพ
        function closePopup() {
            document.getElementById('popup').style.display = 'none'; // ปิดป๊อปอัพ
        }
    </script>
</body>
</html>
