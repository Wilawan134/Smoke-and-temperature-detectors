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

// กำหนดค่าเริ่มต้น
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date("Y-m-d");
$dataType = isset($_GET['data_type']) ? $_GET['data_type'] : 'all';
$graphType = isset($_GET['graph_type']) ? $_GET['graph_type'] : 'line';
$page = isset($_GET['page']) ? $_GET['page'] : 1;


// กำหนดจำนวนแถวต่อหน้า
$rowsPerPage = 10;

// ตรวจสอบหน้าปัจจุบัน
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $rowsPerPage;

// ตรวจสอบประเภทข้อมูล
$columns = ['timestamp'];
if ($dataType == 'temperature' || $dataType == 'all') {
    $columns[] = 'temperature';
}
if ($dataType == 'gas' || $dataType == 'all') {
    $columns[] = 'gas_value';
}
$columnsList = implode(', ', $columns);

// สร้างคำสั่ง SQL
$sql = "SELECT $columnsList FROM logs 
        WHERE timestamp BETWEEN '$startDate 00:00:00' AND '$startDate 23:59:59'
        ORDER BY timestamp DESC
        LIMIT $rowsPerPage OFFSET $offset";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rowData = [
            date("Y-m-d H:i:s", strtotime($row['timestamp'])),
            $row['temperature'] ?? null,
            $row['gas_value'] ?? null,
        ];
        $tableData[] = $rowData;
    }
} else {
    $tableData = [];
}

// คำนวณจำนวนหน้าทั้งหมด
$sqlCount = "SELECT COUNT(*) as total FROM logs WHERE timestamp BETWEEN '$startDate 00:00:00' AND '$startDate 23:59:59'";
$countResult = $conn->query($sqlCount);
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $rowsPerPage);




// ปิดการเชื่อมต่อ
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IoT รายงานข้อมูล</title>
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
        /* สไตล์ปุ่ม Logout */
        .menu button.logout {
            background-color: #ff4d4d !important;  /* เปลี่ยนสีพื้นหลังเป็นสีแดงอ่อน */
            color: white !important; /* สีตัวอักษรเป็นสีขาว */
            border: 2px solid #ff0000; /* เส้นขอบเป็นสีแดงเข้ม */
        }

        .menu button.logout:hover {
            background-color: #cc0000 !important; /* สีพื้นหลังเมื่อ hover จะเป็นสีแดงเข้ม */
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
        <h3>ออกรายงานแบบรายวัน</h3>
        
    <!-- ส่วนเพิ่ม dropdown สำหรับเลือกช่วงเวลา -->
    <div class="report-form"> 
        <h3></h3>
        <select id="report-period" onchange="redirectToPage()">
            <option value="">-- กรุณาเลือกช่วงเวลา --</option>
            <option value="daily">รายวัน</option>
            <option value="monthly">รายเดือน</option>
            <option value="yearly">รายปี</option>
        </select>
    </div>
    <form method="GET" action="">
    <!-- เลือกวันที่ -->
    <input type="date" name="start_date" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">

    <!-- เลือกประเภทข้อมูล -->
    <select name="data_type">
        <option value="temperature" <?php if (isset($_GET['data_type']) && $_GET['data_type'] == 'temperature') echo 'selected'; ?>>อุณหภูมิ</option>
        <option value="gas" <?php if (isset($_GET['data_type']) && $_GET['data_type'] == 'gas') echo 'selected'; ?>>แก๊สและควัน</option>
        <option value="all" <?php if (isset($_GET['data_type']) && $_GET['data_type'] == 'all') echo 'selected'; ?>>ทั้งหมด</option>
    </select>

    <!-- เลือกประเภทกราฟ -->
    <select name="graph_type">
        <option value="line" <?php if (isset($_GET['graph_type']) && $_GET['graph_type'] == 'line') echo 'selected'; ?>>กราฟเส้น</option>
        <option value="bar" <?php if (isset($_GET['graph_type']) && $_GET['graph_type'] == 'bar') echo 'selected'; ?>>กราฟแท่ง</option>
        <option value="radar" <?php if (isset($_GET['graph_type']) && $_GET['graph_type'] == 'radar') echo 'selected'; ?>>กราฟเรดาร์</option>
    </select>

    <input type="submit" value="แสดงรายงาน">
</form>




        <form method="POST" action="download_report.php">
            <input type="hidden" name="start_date" value="<?php echo $startDate; ?>">
            <input type="hidden" name="data_type" value="<?php echo $dataType; ?>">
            <input type="hidden" name="graph_type" value="<?php echo $graphType; ?>">
            <input type="submit" value="ดาวน์โหลดรายงาน PDF">
        </form>
    </div>
    
    

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>date time</th>
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
    <<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>&start_date=<?php echo $startDate; ?>&data_type=<?php echo $dataType; ?>">หน้าก่อนหน้า</a>
    <?php endif; ?>

    <span>หน้าที่ <?php echo $page; ?> จาก <?php echo $totalPages; ?> หน้า</span>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?php echo $page + 1; ?>&start_date=<?php echo $startDate; ?>&data_type=<?php echo $dataType; ?>">หน้าถัดไป</a>
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

        const form = document.querySelector('form[action="download_report.php"]');
        form.appendChild(input);
    }

    document.querySelector('form[action="download_report.php"]').addEventListener('submit', sendGraphToServer);
</script>

</body>
</html>
