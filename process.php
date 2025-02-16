<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $period = $_POST['period'];

    // เปลี่ยนเส้นทางตามค่าที่เลือก
    if ($period == 'daily') {
        header("Location: daily.php");
    } elseif ($period == 'monthly') {
        header("Location: monthly.php");
    } elseif ($period == 'yearly') {
        header("Location: yearly.php");
    } else {
        echo "มีข้อผิดพลาดในการเลือกช่วงเวลา";
    }
    exit();
} else {
    echo "กรุณาเลือกช่วงเวลา!";
}
?>
