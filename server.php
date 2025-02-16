<?php

    $servername = "localhost"; 
    $username = "root"; 
    $password = ""; 
    $dbname = "register_db"; 

    // สร้างการเชื่อมต่อกับฐานข้อมูล
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // ตรวจสอบการเชื่อมต่อ
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error()); // ต้องเป็น mysqli_connect_error()
    } 

?>
