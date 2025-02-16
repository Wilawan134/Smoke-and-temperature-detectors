<?php
require('pChart/pChart-master/library/pData.php');
require('pChart/pChart-master/library/pDraw.php');
require('pChart/pChart-master/library/pImage.php');

// ตรวจสอบว่าไฟล์มีอยู่จริง
if (!file_exists('pChart/pChart-master/library/pData.php')) {
    die('pData.php not found!');
}

// ทดสอบสร้างกราฟจากข้อมูลง่าย ๆ
$MyData = new pData();
$MyData->addPoints([1, 2, 3, 4], "Sample Data");
$MyData->addPoints(['A', 'B', 'C', 'D'], "Labels");
$MyData->setAbscissa("Labels");

// สร้าง pImage object หลังจากสร้าง pData
$myPicture = new pImage(700, 230, $MyData);
$myPicture->setFontProperties(array("FontName" => "pChart/pChart-master/library/fonts/DejaVuSans.ttf", "FontSize" => 10)); // ตอนนี้สามารถตั้งค่าฟอนต์ได้แล้ว
$myPicture->setGraphArea(60, 40, 650, 190);
$myPicture->drawScale();
$myPicture->drawLineChart();
$myPicture->render('test_graph.png');

// ทดสอบการสร้าง pData
$MyData = new pData();
if ($MyData) {
    echo 'pData class loaded successfully.';
} else {
    echo 'Failed to load pData class.';
}
?>
