<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "iot"; 

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} 

// ดึงค่า TempLimit และ GasLimit จากตาราง limits
$sql_limits = "SELECT TempLimit, GasLimit FROM limits ORDER BY id DESC LIMIT 1";
$result_limits = $conn->query($sql_limits);

if (!$result_limits) {
    die("SQL Error: " . $conn->error); 
}

$response = array(); 

if ($result_limits && $result_limits->num_rows > 0) {
    $row_limits = $result_limits->fetch_assoc();
    $response['TempLimit'] = (int)$row_limits['TempLimit'];
    $response['GasLimit'] = (int)$row_limits['GasLimit'];
} else {
    // ค่าตั้งต้น ถ้าไม่พบข้อมูลในฐานข้อมูล
    $response['TempLimit'] = 40; // ค่าตั้งต้น
    $response['GasLimit'] = 1500; // ค่าตั้งต้น
}

// ดึงค่า Line Token จากตาราง line_notify_token
$sql_token = "SELECT token FROM line_notify_token ORDER BY id DESC LIMIT 1";
$result_token = $conn->query($sql_token);

if ($result_token && $result_token->num_rows > 0) {
    $row_token = $result_token->fetch_assoc();
    $response['LineToken'] = $row_token['token'];
} else {
    // แสดงข้อความถ้าไม่สามารถดึง Token ได้
    $response['LineToken'] = "";
    $response['error'] = "LineToken not found in the database.";
}

header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>
