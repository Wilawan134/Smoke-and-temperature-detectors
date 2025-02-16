<?php
require('fpdf186/fpdf.php'); // Load FPDF library

// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "iot";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$startDate = $_POST['start_date'] ?? date("Y-m-d");
$dataType = $_POST['data_type'] ?? 'all';
$graphImage = $_POST['graph_image'] ?? ''; // Get image from form

// Validate data type
$allowedDataTypes = ['temperature', 'gas', 'all'];
if (!in_array($dataType, $allowedDataTypes)) {
    $dataType = 'all';
}

// Set the columns to fetch
$columns = ['timestamp'];
if ($dataType == 'temperature' || $dataType == 'all') {
    $columns[] = 'temperature';
}
if ($dataType == 'gas' || $dataType == 'all') {
    $columns[] = 'gas_value';
}
$columnsList = implode(', ', $columns);

// SQL query to fetch data
$sql = "SELECT $columnsList FROM logs WHERE timestamp LIKE '$startDate%' ORDER BY timestamp DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

// Custom PDF class
class PDF extends FPDF {
    public $dataType;
    public $columnWidth;
    public $columnCount;
    public $isLastPage = false; // เพิ่มตัวแปรตรวจสอบหน้าสุดท้าย

    function Header() {
        if ($this->isLastPage) {
            return; // ถ้าเป็นหน้าสุดท้าย ไม่ต้องแสดงหัวตาราง
        }

        // Center the main header
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, "Daily Report", 0, 1, 'C');
        $this->Ln(10);

        // Table headers
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(30, 144, 255); // Dark blue
        $this->SetX((210 - 180) / 2);
        $this->Cell($this->columnWidth, 10, 'Date Time', 1, 0, 'C', true);
        if ($this->dataType == 'temperature' || $this->dataType == 'all') {
            $this->Cell($this->columnWidth, 10, 'Temperature (C)', 1, 0, 'C', true);
        }
        if ($this->dataType == 'gas' || $this->dataType == 'all') {
            $this->Cell($this->columnWidth, 10, 'Smoke Gas (ppm)', 1, 0, 'C', true);
        }
        $this->Ln();
    }
}

$pdf = new PDF();
$pdf->dataType = $dataType;

// Calculate column width dynamically
$pdf->columnCount = 1; // At least timestamp
if ($dataType == 'temperature' || $dataType == 'all') {
    $pdf->columnCount++;
}
if ($dataType == 'gas' || $dataType == 'all') {
    $pdf->columnCount++;
}
$pdf->columnWidth = 180 / $pdf->columnCount; // Adjust columns to fit

$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Display table data
$pdf->SetFillColor(255, 255, 255);
while ($row = $result->fetch_assoc()) {
    // Check if we need a new page
    if ($pdf->GetY() > 270) {
        $pdf->AddPage(); // New page
    }

    $pdf->SetX((210 - 180) / 2);
    $pdf->Cell($pdf->columnWidth, 10, date("Y-m-d H:i:s", strtotime($row['timestamp'])), 1, 0, 'C', true);
    if ($dataType == 'temperature' || $dataType == 'all') {
        $pdf->Cell($pdf->columnWidth, 10, $row['temperature'] ?? '-', 1, 0, 'C', true);
    }
    if ($dataType == 'gas' || $dataType == 'all') {
        $pdf->Cell($pdf->columnWidth, 10, $row['gas_value'] ?? '-', 1, 0, 'C', true);
    }
    $pdf->Ln();
}

// **หน้าสุดท้าย (หน้ากราฟ)**
$pdf->isLastPage = true; // กำหนดให้เป็นหน้าสุดท้าย
$pdf->AddPage(); // เพิ่มหน้าสำหรับกราฟ
$pdf->SetFont('Arial', 'B', 14);
$pdf->Ln(20);

// เพิ่มรูปภาพกราฟ (ถ้ามี)
if (!empty($graphImage)) {
    $graphImage = str_replace('data:image/png;base64,', '', $graphImage);
    $graphImage = base64_decode($graphImage);
    $imageFile = 'graph.png';
    
    if (file_put_contents($imageFile, $graphImage)) {
        $pdf->Image($imageFile, 10, 30, 180, 100);
        unlink($imageFile); // Delete the image file after use
    }
}

// Close database connection
$conn->close();

// Output the PDF for download
$pdf->Output('D', 'report_' . $startDate . '.pdf');
exDt;
?>
