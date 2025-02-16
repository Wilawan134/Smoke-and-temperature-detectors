<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "คุณต้องเข้าสู่ระบบก่อน";
    header('location: login.php');
}

$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "iot"; 

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("การเชื่อมต่อล้มเหลว: " . mysqli_connect_error());
}

$reportType = isset($_GET['report_type']) ? $_GET['report_type'] : 'daily';
$dataType = isset($_GET['data_type']) ? $_GET['data_type'] : 'all';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date("Y-m-d");
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date("Y-m-d");
$graphType = isset($_GET['graph_type']) ? $_GET['graph_type'] : 'line';

$selectFields = "DATE(timestamp) AS report_date";
if ($dataType == 'all' || $dataType == 'temperature') {
    $selectFields .= ", AVG(temperature) AS avg_temp";
}
if ($dataType == 'all' || $dataType == 'gas') {
    $selectFields .= ", AVG(gas_value) AS avg_gas";
}

if ($reportType === 'daily') {
    $sql = "SELECT $selectFields FROM logs WHERE DATE(timestamp) BETWEEN '$startDate' AND '$endDate' GROUP BY report_date ORDER BY report_date DESC";
} elseif ($reportType === 'monthly') {
    $sql = "SELECT $selectFields FROM logs WHERE MONTH(timestamp) = MONTH('$startDate') AND YEAR(timestamp) = YEAR('$startDate') GROUP BY report_date ORDER BY report_date DESC";
} else {
    $sql = "SELECT $selectFields FROM logs WHERE YEAR(timestamp) = YEAR('$startDate') GROUP BY report_date ORDER BY report_date DESC";
}

$result = $conn->query($sql);

$temperatureData = [];
$gasData = [];
$labels = [];

while ($row = $result->fetch_assoc()) {
    if (isset($row['avg_temp'])) {
        $temperatureData[] = round($row['avg_temp'], 2);
    }
    if (isset($row['avg_gas'])) {
        $gasData[] = round($row['avg_gas'], 2);
    }
    $labels[] = date("d-m-Y", strtotime($row['report_date']));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>กราฟรายงาน</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #333;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .print-button, .back-button {
            margin: 20px 0;
            padding: 10px 15px;
            background-color: #f44336;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .print-button:hover, .back-button:hover {
            background-color: #d32f2f;
        }
        label {
            margin-top: 10px;
            display: block;
        }
        select, input[type="date"] {
            margin: 10px 0 20px;
            padding: 8px;
            width: calc(100% - 20px);
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        canvas {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>กราฟรายงาน</h2>
        <form method="GET" action="">
            <label for="report_type">เลือกประเภทของรายงาน:</label>
            <select name="report_type" id="report_type">
                <option value="daily" <?php if ($reportType == 'daily') echo 'selected'; ?>>รายวัน</option>
                <option value="monthly" <?php if ($reportType == 'monthly') echo 'selected'; ?>>รายเดือน</option>
                <option value="yearly" <?php if ($reportType == 'yearly') echo 'selected'; ?>>รายปี</option>
            </select>

            <label for="data_type">เลือกประเภทข้อมูล:</label>
            <select name="data_type" id="data_type">
                <option value="all" <?php if ($dataType == 'all') echo 'selected'; ?>>ทั้งหมด</option>
                <option value="temperature" <?php if ($dataType == 'temperature') echo 'selected'; ?>>เฉพาะอุณหภูมิ</option>
                <option value="gas" <?php if ($dataType == 'gas') echo 'selected'; ?>>เฉพาะแก๊สควัน</option>
            </select>

            <label for="start_date">วันที่เริ่มต้น:</label>
            <input type="date" name="start_date" value="<?php echo $startDate; ?>">

            <label for="end_date">วันที่สิ้นสุด:</label>
            <input type="date" name="end_date" value="<?php echo $endDate; ?>">

            <label for="graph_type">เลือกประเภทของกราฟ:</label>
            <select name="graph_type" id="graph_type">
                <option value="line" <?php if ($graphType == 'line') echo 'selected'; ?>>เส้น</option>
                <option value="bar" <?php if ($graphType == 'bar') echo 'selected'; ?>>แท่ง</option>
            </select>

            <input type="submit" value="สร้างรายงาน">
        </form>

        <button class="print-button" onclick="window.print();">พิมพ์รายงาน</button>
        <canvas id="myChart" width="400" height="200"></canvas>

        <script>
            const temperatureData = <?php echo json_encode($temperatureData); ?>;
            const gasData = <?php echo json_encode($gasData); ?>;
            const labels = <?php echo json_encode($labels); ?>;
            const graphType = '<?php echo $graphType; ?>';
            const dataType = '<?php echo $dataType; ?>';

            const datasets = [];
            if (temperatureData.length > 0 && (dataType === 'all' || dataType === 'temperature')) {
                datasets.push({
                    label: 'อุณหภูมิ (\u00b0C)',
                    data: temperatureData,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1,
                    fill: false
                });
            }
            if (gasData.length > 0 && (dataType === 'all' || dataType === 'gas')) {
                datasets.push({
                    label: 'ระดับก๊าซ',
                    data: gasData,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    fill: false
                });
            }

            const ctx = document.getElementById('myChart').getContext('2d');
            const myChart = new Chart(ctx, {
                type: graphType,
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
    </div>

    <?php
        if (isset($conn) && $conn) {
            $conn->close();
        }
    ?>
</body>
</html>
