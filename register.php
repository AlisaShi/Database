<?php
include('header.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>會員註冊</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h1>會員註冊</h1>

    <main>
        <form action="register_process.php" method="POST">
            <label for="first_name">名字：</label>
            <input type="text" id="first_name" name="first_name" required><br>
            <label for="last_name">姓氏：</label>
            <input type="text" id="last_name" name="last_name" required><br>
            <label for="gender">性別：</label>
            <input type="text" id="gender" name="gender" required><br>
            <label for="email">電子郵件：</label>
            <input type="email" id="email" name="email" required><br>
            <label for="birthday">生日：</label>
            <input type="date" id="birthday" name="birthday" required><br>
            <label for="password">密碼：</label>
            <input type="password" id="password" name="password" required><br>

            <input type="submit" value="註冊">
        </form>
    </main>
</body>

</html>