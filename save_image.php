<?php
// รับข้อมูลที่ส่งจาก JavaScript
$data = json_decode(file_get_contents('php://input'), true);

// ตรวจสอบว่ามีข้อมูลภาพ
if (isset($data['image'])) {
    // แปลงข้อมูล base64 เป็นไฟล์ PNG
    $imageData = base64_decode(str_replace('data:image/png;base64,', '', $data['image']));
    $filePath = 'path/to/save/chart_image.png'; // ที่เก็บไฟล์ภาพ

    // บันทึกไฟล์
    file_put_contents($filePath, $imageData);
    echo "Image saved successfully.";
} else {
    echo "No image data received.";
}
?>
