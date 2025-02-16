<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Line Notification Message</title>
</head>
<body>
    <h1>Edit Line Notification Message</h1>

    <form action="update_message.php" method="POST">
        <label for="lineMessage">Enter Line Notification Message:</label><br>
        <textarea id="lineMessage" name="lineMessage" rows="4" cols="50"></textarea><br><br>
        <input type="submit" value="Update Message">
    </form>
</body>
</html>
