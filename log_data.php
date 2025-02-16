<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "iot"; 

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$temperature = $_POST['temperature'];
$gasValue = $_POST['gasValue'];

// ใช้ NOW() ใน SQL เพื่อบันทึกเวลาปัจจุบันโดยอัตโนมัติ
$sql = "INSERT INTO logs (temperature, gas_value, timestamp) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("dd", $temperature, $gasValue);

if ($stmt->execute()) {
    echo "Data logged successfully";
} else {
    echo "Error logging data: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
