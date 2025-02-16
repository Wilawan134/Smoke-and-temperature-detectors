<?php  
if (isset($_POST['download_pdf'])) {
    require('fpdf186/fpdf.php'); // รวมไลบรารี FPDF

    // เชื่อมต่อฐานข้อมูลและดึงข้อมูลที่ต้องการ
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "iot";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // รับข้อมูลจากฟอร์ม
    $month = $_POST['month'] ?? '';
    $year = $_POST['year'] ?? '';
    $dataType = $_POST['data_type'] ?? 'all';

    // ตรวจสอบว่าเลือกเดือนและปีหรือไม่
    if ($month && $year) {
        $startDate = "$year-$month-01";
        $endDate = date("Y-m-t", strtotime($startDate));  // ใช้ฟังก์ชั่น date เพื่อหาวันสุดท้ายของเดือน
    } else {
        $startDate = date("Y-m-01");
        $endDate = date("Y-m-t");
    }

    // กำหนดคอลัมน์ที่ต้องการดึงข้อมูล
    $columns = ['timestamp'];
    if ($dataType == 'temperature' || $dataType == 'all') {
        $columns[] = 'temperature';
    }
    if ($dataType == 'gas' || $dataType == 'all') {
        $columns[] = 'gas_value';
    }
    $columnsList = implode(', ', $columns);

    // คำสั่ง SQL ดึงข้อมูลจากฐานข้อมูล
    $sql = "SELECT $columnsList FROM logs 
            WHERE timestamp BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
            ORDER BY timestamp DESC";

    $result = $conn->query($sql);
    $tableData = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tableData[] = [
                date("Y-m-d H:i:s", strtotime($row['timestamp'])),
                $row['temperature'] ?? 'No data',
                $row['gas_value'] ?? 'No data',
            ];
        }
    } else {
        echo "No data found for the selected month.";
        exit;
    }

    // ปิดการเชื่อมต่อฐานข้อมูล
    $conn->close();

    // สร้าง PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);

    // กำหนดสีสำหรับหัวตาราง
    $pdf->SetFillColor(100, 100, 255); // สีฟ้าสำหรับหัวตาราง
    $pdf->SetTextColor(255, 255, 255); // สีตัวอักษรเป็นขาว
    $pdf->Cell(0, 10, 'Monthly Report (' . date('F Y', strtotime($startDate)) . ')', 0, 1, 'C', true);  // หัวข้อรายงานแสดงเดือนและปีที่เลือก

    $pdf->Ln(10); // เพิ่มช่องว่าง

    // กำหนดสีสำหรับหัวตาราง
    //$pdf->SetFillColor(200, 220, 255); // สีพื้นหลังของหัวตาราง
    $pdf->SetTextColor(0, 0, 0); // สีตัวอักษรเป็นดำ
    
    // กำหนดความกว้างของคอลัมน์
    $colWidth = 60;
    
    // เพิ่มหัวตาราง
    $pdf->Cell($colWidth, 10, 'Date Time', 1, 0, 'C', true); // ข้อความ 'Date Time'
    
    if ($dataType == 'temperature' || $dataType == 'all') {
        $pdf->Cell($colWidth, 10, 'Temperature (C)', 1, 0, 'C', true); // ข้อความ 'Temperature'
    }
    
    if ($dataType == 'gas' || $dataType == 'all') {
        $pdf->Cell($colWidth, 10, 'Smoke Gas (ppm)', 1, 0, 'C', true); // ข้อความ 'Smoke Gas'
    }

    $pdf->Ln();

    // กำหนดความสูงของแต่ละแถว
    $rowHeight = 10; // ขนาดแถว
    $maxRowsPerPage = floor(($pdf->GetPageHeight() - 100) / $rowHeight); // คำนวณจำนวนแถวที่พิมพ์ได้ในแต่ละหน้า

    $currentRow = 0;
    foreach ($tableData as $data) {
        if ($currentRow >= $maxRowsPerPage) {
            $pdf->AddPage();  // เพิ่มหน้าใหม่ถ้าจำนวนแถวเกินกำหนด
            $currentRow = 0;  // รีเซ็ตตัวนับแถว
            // เพิ่มหัวตารางใหม่ในหน้าถัดไป
            $pdf->Cell($colWidth, 10, 'Date Time', 1, 0, 'C', true);
            if ($dataType == 'temperature' || $dataType == 'all') {
                $pdf->Cell($colWidth, 10, 'Temperature (C)', 1, 0, 'C', true);
            }
            if ($dataType == 'gas' || $dataType == 'all') {
                $pdf->Cell($colWidth, 10, 'Smoke Gas (ppm)', 1, 0, 'C', true);
            }
            $pdf->Ln();
        }

        $pdf->SetTextColor(0, 0, 0); // กำหนดสีตัวอักษรเป็นดำ
        $pdf->Cell($colWidth, $rowHeight, $data[0], 1, 0, 'C'); // Date Time
        if ($dataType == 'temperature' || $dataType == 'all') {
            $pdf->Cell($colWidth, $rowHeight, $data[1], 1, 0, 'C'); // Temperature
        }
        if ($dataType == 'gas' || $dataType == 'all') {
            $pdf->Cell($colWidth, $rowHeight, $data[2], 1, 0, 'C'); // Smoke Gas
        }
        $pdf->Ln();
        $currentRow++;
    }

    // เพิ่มหน้าสำหรับกราฟ (เหมือนเดิม)
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, '', 0, 1, 'C');
$pdf->Ln(10);

// เพิ่มรูปภาพกราฟ
$graphImage = $_POST['graph_image'] ?? '';
if (!empty($graphImage)) {
    $graphImage = str_replace('data:image/png;base64,', '', $graphImage);
    $graphImage = base64_decode($graphImage);
    $imageFile = 'graph.png';
    
    if (file_put_contents($imageFile, $graphImage)) {
        $pdf->Image($imageFile, 10, 30, 180, 100);
        unlink($imageFile); // ลบรูปหลังจากใช้
    }
}


    // ส่งออกไฟล์ PDF
    $pdf->Output('D', 'data_report_' . date('F_Y', strtotime($startDate)) . '.pdf');
    exit;
}

?>
