<?php
require('fpdf186/fpdf.php'); // โหลดไลบรารี FPDF

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "iot";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับค่าปีและประเภทข้อมูลจากฟอร์ม
$year = isset($_POST['year']) ? $_POST['year'] : date('Y'); // ใช้ปีปัจจุบันถ้าไม่ได้รับค่า

$dataType = isset($_POST['data_type']) ? $_POST['data_type'] : 'all'; // ประเภทข้อมูล
$graphType = isset($_POST['graph_type']) ? $_POST['graph_type'] : 'line'; // ประเภทกราฟ

// สร้าง SQL ตามประเภทข้อมูลที่เลือก
if ($dataType == 'temperature') {
    $sql = "SELECT DATE_FORMAT(timestamp, '%Y-%m-%d %H:%i:%s') AS month, temperature
            FROM logs
            WHERE YEAR(timestamp) = '$year'
            ORDER BY DATE_FORMAT(timestamp, '%Y-%m-%d %H:%i:%s') DESC";
} elseif ($dataType == 'gas') {
    $sql = "SELECT DATE_FORMAT(timestamp, '%Y-%m-%d %H:%i:%s') AS month, gas_value
            FROM logs
            WHERE YEAR(timestamp) = '$year'
            ORDER BY DATE_FORMAT(timestamp, '%Y-%m-%d %H:%i:%s') DESC";
} else {
    $sql = "SELECT DATE_FORMAT(timestamp, '%Y-%m-%d %H:%i:%s') AS month, temperature, gas_value
            FROM logs
            WHERE YEAR(timestamp) = '$year'
            ORDER BY DATE_FORMAT(timestamp, '%Y-%m-%d %H:%i:%s') DESC";
}

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

// สร้างคลาส PDF
class PDF extends FPDF {
    public $dataType; // ตัวแปรเก็บข้อมูลประเภท
    public $isLastPage = false; // ตัวแปรเพื่อตรวจสอบหน้าสุดท้าย

    function Header() {
        // ถ้าเป็นหน้าสุดท้ายที่มีกราฟ ให้ข้ามการแสดงหัวตาราง
        if ($this->isLastPage) {
            return;
        }

        // กำหนดฟอนต์และหัวข้อ
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, "Yearly Report", 0, 1, 'C');
        $this->Ln(10);

        // กำหนดหัวตาราง
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(30, 144, 255); // กำหนดสีพื้นหลัง

        $this->Cell(60, 10, 'Date Time', 1, 0, 'C', true);

        if ($this->dataType == 'temperature') {
            $this->Cell(60, 10, 'Temperature (C)', 1, 0, 'C', true);
        } elseif ($this->dataType == 'gas') {
            $this->Cell(60, 10, 'Smoke Gas (ppm)', 1, 0, 'C', true);
        } else {
            $this->Cell(60, 10, 'Temperature (C)', 1, 0, 'C', true);
            $this->Cell(60, 10, 'Smoke Gas (ppm)', 1, 0, 'C', true);
        }
        $this->Ln();
    }

    function Footer() {
        // เพิ่มส่วน Footer หากต้องการ
    }
}

$pdf = new PDF();
$pdf->dataType = $dataType; // ส่งค่า $dataType ไปยังคลาส PDF
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// แสดงข้อมูลในตาราง
$rowCount = 0; // ใช้นับจำนวนแถว
$totalRows = $result->num_rows; // จำนวนทั้งหมดของแถวข้อมูล

while ($row = $result->fetch_assoc()) {
    // ถ้าแถวเกินหน้าหนึ่งก็เพิ่มหน้าใหม่
    if ($rowCount > 0 && $rowCount % 20 == 0) {
        $pdf->AddPage(); // เพิ่มหน้าใหม่ทุก ๆ 20 แถว
    }

    $pdf->Cell(60, 10, $row['month'], 1, 0, 'C');
    
    if ($dataType == 'temperature') {
        $pdf->Cell(60, 10, number_format($row['temperature'], 2) ?? '-', 1, 0, 'C');
    } elseif ($dataType == 'gas') {
        $pdf->Cell(60, 10, number_format($row['gas_value'], 2) ?? '-', 1, 0, 'C');
    } else {
        $pdf->Cell(60, 10, number_format($row['temperature'], 2) ?? '-', 1, 0, 'C');
        $pdf->Cell(60, 10, number_format($row['gas_value'], 2) ?? '-', 1, 0, 'C');
    }
    $pdf->Ln();
    $rowCount++;
}

// เช็คว่าเป็นหน้าสุดท้ายที่มีกราฟหรือไม่
$pdf->isLastPage = true; // ตั้งค่าเป็น true สำหรับหน้าสุดท้ายที่ต้องการแสดงกราฟ

// เพิ่มหน้าสำหรับกราฟ
$pdf->AddPage(); // เพิ่มหน้าใหม่สำหรับกราฟ
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, '', 0, 1, 'C');
$pdf->Ln(10);

// เพิ่มรูปภาพกราฟ
$graphImage = isset($_POST['graph_image']) ? $_POST['graph_image'] : '';
if (!empty($graphImage)) {
    $graphImage = str_replace('data:image/png;base64,', '', $graphImage);
    $graphImage = base64_decode($graphImage);
    $imageFile = 'graph.png';
    
    if (file_put_contents($imageFile, $graphImage)) {
        $pdf->Image($imageFile, 10, 30, 180, 100);
        unlink($imageFile); // ลบรูปหลังจากใช้
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();

// แสดง PDF
$pdf->Output('D', 'Annual_Report_' . $year . '.pdf');
exit;
?>
