<?php
    session_start();
    include('server.php'); // สมมุติว่ามีการเชื่อมต่อกับฐานข้อมูลในไฟล์นี้


    $errors = array(); // สร้างอาร์เรย์สำหรับเก็บข้อผิดพลาด

    if (isset($_POST['login_user'])) {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

    // ตรวจสอบว่าชื่อผู้ใช้ว่างหรือไม่
    if (empty($username)) {
        array_push($errors, "Username is required");
    }

    // ตรวจสอบว่ารหัสผ่านว่างหรือไม่
    if (empty($password)) {
        array_push($errors, "Password is required");
    }

    // ถ้าไม่มีข้อผิดพลาด
    if (count($errors) == 0) {
        $password = md5($password); // เข้ารหัสรหัสผ่านด้วย MD5
        $query = "SELECT * FROM user WHERE username='$username' AND password='$password'";
        $result = mysqli_query($conn, $query);

        // ถ้าพบชื่อผู้ใช้และรหัสผ่านในฐานข้อมูล
        if (mysqli_num_rows($result) == 1) {
            $_SESSION['username'] = $username;
            $_SESSION['success'] = "You are now logged in";
            header("location: index.php"); 
            exit();
        } else {
            // ถ้าชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง
            array_push($errors, "Wrong username/password combination");
            $_SESSION['error'] = "Login ไม่สำเร็จกรุณาลองอีกครั้ง";
            header("location: login.php"); // ย้ายไปหน้า login.php
        }
    }
}
?>