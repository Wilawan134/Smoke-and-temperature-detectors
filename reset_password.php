<?php
$conn = new mysqli("localhost", "root", "", "register_db");

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $result = $conn->query("SELECT * FROM user WHERE reset_token='$token' AND token_expiry > NOW()");

    if ($result->num_rows > 0) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // ใช้ md5 ในการแปลงรหัสผ่านใหม่
            $new_password = md5($_POST['password']);
            $conn->query("UPDATE user SET password='$new_password', reset_token=NULL, token_expiry=NULL WHERE reset_token='$token'");
            $message = "Password has been reset successfully.";
        }
    } else {
        $message = "Invalid or expired token.";
    }
} else {
    $message = "No token provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #444;
        }
        .container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            padding: 30px;
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
            text-align: left;
        }
        input[type="password"] {
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input[type="password"]:focus {
            border-color: #007BFF;
            outline: none;
        }
        button {
            padding: 12px;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        .back-link {
            margin-top: 15px;
            display: inline-block;
            font-size: 14px;
            color: #007BFF;
            text-decoration: none;
            transition: color 0.3s;
        }
        .back-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        .message {
            margin-top: 20px;
            font-size: 16px;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            font-weight: bold;
        }
        .success {
            background-color: #28a745;
            color: white;
        }
        .error {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <?php if (isset($message)): ?>
            <div class="message <?= strpos($message, 'success') !== false ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="password">Enter new password:</label>
            <input type="password" id="password" name="password" placeholder="New Password" required>
            <button type="submit">Reset Password</button>
        </form>
        <a href="login.php" class="back-link">Back to Login</a>
    </div>
</body>
</html>
