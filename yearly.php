<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "iot";

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับค่าจากฟอร์ม
$reportType = isset($_POST['report_type']) && in_array($_POST['report_type'], ['daily', 'monthly']) ? $_POST['report_type'] : 'monthly';
$month = isset($_POST['month']) ? mysqli_real_escape_string($conn, $_POST['month']) : date("n");  // เดือนที่เลือก
$year = isset($_POST['year']) ? mysqli_real_escape_string($conn, $_POST['year']) : '2024';  // ปีที่เลือก
$dataType = isset($_POST['data_type']) && in_array($_POST['data_type'], ['temperature', 'gas', 'all']) ? $_POST['data_type'] : 'all';
$graphType = isset($_POST['graph_type']) && in_array($_POST['graph_type'], ['line', 'bar', 'radar']) ? $_POST['graph_type'] : 'line';

// กำหนดคอลัมน์ที่จะดึงข้อมูล
$columns = ['timestamp'];
if ($dataType == 'temperature' || $dataType == 'all') {
    $columns[] = 'temperature';
}
if ($dataType == 'gas' || $dataType == 'all') {
    $columns[] = 'gas_value';
}
$columnsList = implode(', ', $columns);

// รับค่าปีที่ผู้ใช้เลือกจากฟอร์ม (ถ้าไม่ได้เลือกให้ใช้ปี 2024)
$year = isset($_POST['year']) ? $_POST['year'] : '2024';  // ใช้ปี 2024 ถ้าไม่ได้เลือก

// กำหนดวันที่เริ่มต้นและวันที่สิ้นสุดตามปีที่เลือก
$startDate = $year . '-01-01';  // วันที่เริ่มต้นเป็น 1 ม.ค. ของปีที่เลือก
$endDate = $year . '-12-31';    // วันที่สิ้นสุดเป็น 31 ธ.ค. ของปีที่เลือก

// กำหนดจำนวนแถวที่แสดงในแต่ละหน้า
$recordsPerPage = 10;  // จำนวนแถวที่แสดงในแต่ละหน้า

// รับค่าหน้าปัจจุบันจาก URL (ถ้าไม่มีให้เริ่มที่หน้า 1)
$page = isset($_POST['page']) ? $_POST['page'] : 1;  

// คำนวณค่า OFFSET
$offset = ($page - 1) * $recordsPerPage;

// กำหนดคำสั่ง SQL ที่ใช้ในการดึงข้อมูล
$sql = "SELECT $columnsList FROM logs 
        WHERE timestamp BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
        ORDER BY timestamp DESC
        LIMIT $recordsPerPage OFFSET $offset";

// ทำการ query
$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}

// จัดเตรียมข้อมูลสำหรับแสดงผล
$tableData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rowData = [date("Y-m-d H:i:s", strtotime($row['timestamp']))];
        $rowData[] = $row['temperature'] ?? null;
        $rowData[] = $row['gas_value'] ?? null;
        $tableData[] = $rowData;
    }
}

// คำนวณจำนวนแถวทั้งหมด
$totalRecordsQuery = "SELECT COUNT(*) AS total FROM logs WHERE timestamp BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
$resultTotal = $conn->query($totalRecordsQuery);
$totalRecords = $resultTotal->fetch_assoc()['total'];

// คำนวณจำนวนหน้าทั้งหมด
$totalPages = ceil($totalRecords / $recordsPerPage);



// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
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

        /* สไตล์ปุ่ม Logout */
        .menu button.logout {
            background-color: #007bff !important;  /* เปลี่ยนสีพื้นหลังเป็นสีแดงอ่อน */
            color: white !important; /* สีตัวอักษรเป็นสีขาว */
            border: 2px solid #007bff; /* เส้นขอบเป็นสีแดงเข้ม */
        }

        .menu button.logout:hover {
            background-color: #0056b3 !important; /* สีพื้นหลังเมื่อ hover จะเป็นสีแดงเข้ม */
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
            background-color: rgb(3, 142, 255);
        }

        table {
            background-color: #ffffff;
        }

        canvas {
            max-width: 100%;
            height: auto;
        }
        .pagination { text-align: center; margin: 20px; }
    .pagination a {
        padding: 10px 20px;
        margin: 5px;
        background-color: #007bff; /* สีพื้นหลังของปุ่ม */
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }

    .pagination a:hover {
        background-color: #0056b3; /* สีเมื่อ hover */
    }

    .pagination a.disabled {
        background-color: #ccc; /* สีสำหรับปุ่มที่ถูกปิดใช้งาน */
        pointer-events: none; /* ป้องกันการคลิกปุ่ม */
    }

    .pagination span {
        font-size: 16px;
        padding: 10px;
        margin: 0 10px;
    }

    .pagination a.active {
        background-color: #28a745; /* สีปุ่มหน้า active */
        color: white;
    }

    /* เพิ่มสีสำหรับปุ่ม "ก่อนหน้า" และ "หน้าถัดไป" */
    .pagination .prev, .pagination .next {
        background-color: #28a745; /* สีปุ่มสีเขียว */
        border-radius: 5px;
    }

    .pagination .prev:hover, .pagination .next:hover {
        background-color: #218838; /* สีเมื่อ hover */
    }
    </style>
    <script>
        function redirectToPage() {
            const period = document.getElementById('report-period').value;
            if (period === 'daily') {
                window.location.href = 'daily.php';
            } else if (period === 'monthly') {
                window.location.href = 'monthly.php';
            } else if (period === 'yearly') {
                window.location.href = 'yearly.php';
            }
        }
    </script>
    <script>
    // เก็บตำแหน่งการเลื่อนหน้าเมื่อหน้าเว็บโหลด
    window.onload = function() {
        if (localStorage.getItem("scrollPosition")) {
            window.scrollTo(0, localStorage.getItem("scrollPosition"));
        }
    };

    // ก่อนที่หน้าเว็บจะรีเฟรช เก็บตำแหน่งการเลื่อนหน้า
    window.onbeforeunload = function() {
        localStorage.setItem("scrollPosition", window.scrollY);
    };
</script>

</head>
<body>
    <div class="header">
        <h1></h1>
    </div>
    <div class="menu">
        <button onclick="window.location='index.php'">หน้าแรก</button>
        <button onclick="window.location='Current limits of gas and Temp.php'">ดูข้อมูล Smoke_Gas และ Temp</button>
        <button onclick="window.location='Set gas_Temp limits.php'">แก้ไขค่าการแจ้งเตือน Smoke_Gas และ Temp</button>
        <button onclick="window.location='Set Token_Linenotify.php'">แก้ไข Token การแจ้งเตือนผ่าน Line</button>
        <button onclick="window.location='daily.php'">ออกรายงาน</button>
        <button onclick="window.location='index.php?logout=1'" class="logout">Logout</button>
    </div>
    <div class="report-form">
        <h3>ออกรายงานแบบรายปี</h3>
        <!-- ส่วนเพิ่ม dropdown สำหรับเลือกช่วงเวลา -->
        <div class="report-form">
        <h3></h3>
        <select id="report-period" onchange="redirectToPage()">
            <option value="">-- กรุณาเลือกช่วงเวลา --</option>
            <option value="daily">รายวัน</option>
            <option value="monthly">รายเดือน</option>
            <option value="yearly">รายปี</option>
        </select>
        <title>ฟอร์ม Pop-up</title>
    <style>
        /* สไตล์สำหรับปุ่มเปิดฟอร์ม */
        .open-btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        /* สไตล์สำหรับพื้นหลังของ Pop-up */
        .popup {
            display: none; /* ซ่อน pop-up ตอนแรก */
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* พื้นหลังโปร่งใส */
            justify-content: center;
            align-items: center;
        }

        /* สไตล์สำหรับฟอร์ม Pop-up */
        .popup-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 300px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* ปุ่มปิด */
        .close-btn {
            background-color: #f44336;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 4px;
            width: 100%;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <!-- ปุ่มสำหรับเปิด Pop-up -->
    <button class="open-btn" onclick="openPopup()">ฟอร์มสำหรับดาวน์โหลด pdf</button>

    <!-- Pop-up ฟอร์ม -->
    <div class="popup" id="popupForm">
        <div class="popup-content">
            <form method="POST" action="generate_pdf.php">
            <h5>กรุณาเลือกให้ตรงกับหน้าแสดงรายงาน</h5>
                <select name="year">
                    <option value="2024" <?php if ($year == '2024') echo 'selected'; ?>>2024</option>
                    <option value="2025" <?php if ($year == '2025') echo 'selected'; ?>>2025</option>
                </select>

                <select name="data_type">
                    <option value="temperature" <?php if ($dataType == 'temperature') echo 'selected'; ?>>อุณหภูมิ</option>
                    <option value="gas" <?php if ($dataType == 'gas') echo 'selected'; ?>>แก๊สและควัน</option>
                    <option value="all" <?php if ($dataType == 'all') echo 'selected'; ?>>ทั้งหมด</option>
                </select>

                <select name="graph_type">
                    <option value="line" <?php if ($graphType == 'line') echo 'selected'; ?>>กราฟเส้น</option>
                    <option value="bar" <?php if ($graphType == 'bar') echo 'selected'; ?>>กราฟแท่ง</option>
                    <option value="radar" <?php if ($graphType == 'radar') echo 'selected'; ?>>กราฟเรดาร์</option>
                </select>
                
                <input type="submit" value="ดาวน์โหลดรายงาน PDF">
            </form>
            <button class="close-btn" onclick="closePopup()">ปิดฟอร์ม</button>
        </div>
    </div>

    <script>
        // ฟังก์ชันเปิด Pop-up
        function openPopup() {
            document.getElementById('popupForm').style.display = 'flex';
        }

        // ฟังก์ชันปิด Pop-up
        function closePopup() {
            document.getElementById('popupForm').style.display = 'none';
        }
    </script>

</body>
        <form method="POST" action="">
            <select name="year">
                <option value="2024" <?php if ($year == '2024') echo 'selected'; ?>>2024</option>
                <option value="2025" <?php if ($year == '2025') echo 'selected'; ?>>2025</option>
            </select>
    
            <select name="data_type">
                <option value="temperature" <?php if ($dataType == 'temperature') echo 'selected'; ?>>อุณหภูมิ</option>
                <option value="gas" <?php if ($dataType == 'gas') echo 'selected'; ?>>แก๊สและควัน</option>
                <option value="all" <?php if ($dataType == 'all') echo 'selected'; ?>>ทั้งหมด</option>
            </select>

            <select name="graph_type">
                <option value="line" <?php if ($graphType == 'line') echo 'selected'; ?>>กราฟเส้น</option>
                <option value="bar" <?php if ($graphType == 'bar') echo 'selected'; ?>>กราฟแท่ง</option>
                <option value="radar" <?php if ($graphType == 'radar') echo 'selected'; ?>>กราฟเรดาร์</option>
            </select>
            <input type="submit" value="แสดงรายงาน">
        </form>









<div id="reportResult"></div>

    <div class="download-pdf">
    <form method="POST" action="generate_pdf.php">
        <input type="hidden" name="report_data" value="<?php echo htmlspecialchars(json_encode($tableData)); ?>">
        <input type="hidden" name="data_type" value="<?php echo $dataType; ?>">
        
    </form>
</div>
        

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Date Time</th>
                    <?php if ($dataType == 'temperature' || $dataType == 'all') echo "<th>temperature</th>"; ?>
                    <?php if ($dataType == 'gas' || $dataType == 'all') echo "<th>Smoke_gas</th>"; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tableData as $data): ?>
                    <tr>
                        <td><?php echo $data[0]; ?></td>
                        <?php if ($dataType == 'temperature' || $dataType == 'all') echo "<td>" . ($data[1] ?? 'ไม่มีข้อมูล') . "</td>"; ?>
                        <?php if ($dataType == 'gas' || $dataType == 'all') echo "<td>" . ($data[2] ?? 'ไม่มีข้อมูล') . "</td>"; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- ปุ่มเปลี่ยนหน้า -->
    <div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>&month=<?php echo $month; ?>&year=<?php echo $year; ?>&data_type=<?php echo $dataType; ?>&graph_type=<?php echo $graphType; ?>">ก่อนหน้า</a>
    <?php else: ?>
        <a class="disabled">ก่อนหน้า</a>
    <?php endif; ?>

    <span>หน้า <?php echo $page; ?> จาก <?php echo $totalPages; ?></span>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?php echo $page + 1; ?>&month=<?php echo $month; ?>&year=<?php echo $year; ?>&data_type=<?php echo $dataType; ?>&graph_type=<?php echo $graphType; ?>">หน้าถัดไป</a>
    <?php else: ?>
        <a class="disabled">หน้าถัดไป</a>
    <?php endif; ?>
</div>
    <div>
        <canvas id="myChart"></canvas>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = <?php echo json_encode(array_column($tableData, 0)); ?>;
        const temperatureData = <?php echo json_encode(array_column($tableData, 1)); ?>;
        const gasData = <?php echo json_encode(array_column($tableData, 2)); ?>;
        const graphType = '<?php echo $graphType; ?>';

        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: graphType,
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Temperature (°C)',
                        data: temperatureData,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2,
                        fill: false,
                    },
                    {
                        label: 'Smoke_Gas Level',
                        data: gasData,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        fill: false,
                    },
                ],
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true },
                },
            },
        });
        
    </script>
    <script>
    function sendGraphToServer() {
        const canvas = document.getElementById('myChart');
        const imageData = canvas.toDataURL('image/png'); // แปลงกราฟเป็น base64

        // สร้าง input ซ่อนเพื่อส่งไปกับฟอร์ม
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'graph_image';
        input.value = imageData;

        const form = document.querySelector('form[action="generate_pdf.php"]');

        form.appendChild(input);
    }

    document.querySelector('form[action="generate_pdf.php"]').addEventListener('submit', sendGraphToServer);
</script>
</body>
</html>
