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

// ตรวจสอบหน้าปัจจุบัน
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
    .success-message {
        color: green;
        font-weight: bold;
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
    .menu button.active {
    background-color: #000066 !important; /* สีพื้นหลังเมื่อปุ่ม active */
    color: white !important; /* สีตัวอักษรของปุ่ม */
}

</style>

</head>
<body>
    <!-- ส่วนของรูปปกด้านบน -->
    <div class="header">
        <!-- คุณสามารถใส่เนื้อหาภายในได้ -->
    </div>

    <!-- ปุ่มเมนู 5 ปุ่ม -->
    <div class="menu">
    <button class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" onclick="window.location='index.php'">หน้าแรก</button>
    <button class="<?php echo ($current_page == 'Current limits of gas and Temp.php') ? 'active' : ''; ?>" onclick="window.location='Current limits of gas and Temp.php'">ดูข้อมูลSmoke_GasและTemp</button>
    <button class="<?php echo ($current_page == 'Set gas_Temp limits.php') ? 'active' : ''; ?>" onclick="window.location='Set gas_Temp limits.php'">แก้ไขค่าการแจ้งเตือนSmoke_GasและTemp</button>
    <button class="<?php echo ($current_page == 'Set Token_Linenotify.php') ? 'active' : ''; ?>" onclick="window.location='Set Token_Linenotify.php'">แก้ไข Token การแจ้งเตือนผู้ใช้งานผ่านLine</button>
    <button class="<?php echo ($current_page == 'report.php') ? 'active' : ''; ?>" onclick="window.location='daily.php'">ออกรายงาน</button>
    <button class="<?php echo ($current_page == 'index.php?logout=1') ? 'active' : ''; ?>" onclick="window.location='index.php?logout=1'" class="logout">Logout</button>
</div>

    <div class="homecontent">
        <!-- notification message -->
        <?php if (isset($_SESSION['success'])) : ?>
            <div class="success">
                <h3>
                    <?php
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                    ?>
                </h3>
            </div>
        <?php endif ?>

        <!-- logged in user information -->
        <?php if (isset($_SESSION['username'])) : ?>
            <p>Welcome <strong><?php echo $_SESSION['username']; ?></strong></p>
            <p><a href="index.php?logout='1'" style="color: red;">Logout</a></p>
        <?php endif ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ตัวอย่างการแสดงข้อมูลแบบไดนามิก
            const buttons = document.querySelectorAll('.menu button');
            const homeContent = document.querySelector('.homecontent');

            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    let contentSection = this.innerText;

                    // เปลี่ยนเนื้อหาที่แสดงใน homecontent
                    if (contentSection === 'หน้าแรก') {
                        homeContent.innerHTML = "<h2>Welcome to the Homepage</h2><p>This is the home section. You can modify the content dynamically here.</p>";
                    } else if (contentSection === 'ดูข้อมูล Smoke_Gas และ Temp') {
                        homeContent.innerHTML = "<h2>ข้อมูลระดับแก๊สและอุณหภูมิ</h2><p>Displaying real-time gas volume and temperature data.</p>";
                    } else if (contentSection === 'แก้ไขค่าการแจ้งเตือน') {
                        homeContent.innerHTML = "<h2>Edit Notifications Settings</h2><p>Edit your thresholds for gas and temperature alarms here.</p>";
                    } else if (contentSection === 'ออกรายงาน') {
                        homeContent.innerHTML = "<h2>Generate Report</h2><p>Generate your reports here.</p>";
                    }
                });
            });
        });
    </script>
</body>
</html>
