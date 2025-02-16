<?php
require('fpdf186/fpdf.php'); // รวม FPDF

// รับข้อมูลจาก POST
$year = isset($_POST['year']) ? $_POST['year'] : '2024';
$dataType = isset($_POST['data_type']) ? $_POST['data_type'] : 'all';
$graphType = isset($_POST['graph_type']) ? $_POST['graph_type'] : 'line';

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "iot";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงข้อมูลจากฐานข้อมูล
$sql = "SELECT 
            MONTH(timestamp) AS month, 
            AVG(temperature) AS avg_temperature, 
            MIN(temperature) AS min_temperature, 
            MAX(temperature) AS max_temperature,
            AVG(gas_value) AS avg_gas, 
            MIN(gas_value) AS min_gas, 
            MAX(gas_value) AS max_gas
        FROM logs 
        WHERE YEAR(timestamp) = '$year'
        GROUP BY MONTH(timestamp)
        ORDER BY MONTH(timestamp)";

$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}

// สร้าง PDF
$pdf = new FPDF();
$pdf->AddPage();

// ตั้งค่าฟอนต์
$pdf->SetFont('Arial', 'B', 16);

// ใส่หัวเรื่องรายงาน
$pdf->Cell(0, 10, "Report for the Year " . $year, 0, 1, 'C');
$pdf->Ln(10);

// ใส่หัวตาราง
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 10, 'Month', 1);
if ($dataType == 'temperature' || $dataType == 'all') {
    $pdf->Cell(40, 10, 'Average Temperature (°C)', 1);
    $pdf->Cell(40, 10, 'Minimum Temperature (°C)', 1);
    $pdf->Cell(40, 10, 'Maximum Temperature (°C)', 1);
}
if ($dataType == 'gas' || $dataType == 'all') {
    $pdf->Cell(40, 10, 'Average Gas Level', 1);
    $pdf->Cell(40, 10, 'Minimum Gas Level', 1);
    $pdf->Cell(40, 10, 'Maximum Gas Level', 1);
}
$pdf->Ln();

// ใส่ข้อมูลจากฐานข้อมูล
$pdf->SetFont('Arial', '', 12);
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(30, 10, $row['month'], 1);
    if ($dataType == 'temperature' || $dataType == 'all') {
        $pdf->Cell(40, 10, round($row['avg_temperature'], 2), 1);
        $pdf->Cell(40, 10, $row['min_temperature'], 1);
        $pdf->Cell(40, 10, $row['max_temperature'], 1);
    }
    if ($dataType == 'gas' || $dataType == 'all') {
        $pdf->Cell(40, 10, round($row['avg_gas'], 2), 1);
        $pdf->Cell(40, 10, $row['min_gas'], 1);
        $pdf->Cell(40, 10, $row['max_gas'], 1);
    }
    $pdf->Ln();
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();

// ส่งออก PDF (ให้ดาวน์โหลด)
$pdf->Output('report_' . $year . '.pdf', 'I');
?>
