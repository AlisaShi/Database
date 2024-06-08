<?php
include 'db.php';

// 獲取性別選項
$sex_query = "SELECT Sex_ID, Sex FROM sex";
$sex_result = $conn->query($sex_query);
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
    <header>
        <h1>會員註冊</h1>
        <nav>
            <ul>
                <li><a href="index.php">首頁</a></li>
                <li><a href="news.php">最新消息</a></li>
                <li><a href="weather.php">天氣預報</a></li>
                <li><a href="login.php">會員登入</a></li>
                <li><a href="register.php">會員註冊</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>註冊</h2>
        <form action="register_process.php" method="POST">
            <label for="first_name">名字：</label>
            <input type="text" id="first_name" name="first_name" required><br>
            <label for="last_name">姓氏：</label>
            <input type="text" id="last_name" name="last_name" required><br>
            <label for="gender">性別：</label>
            <select id="gender" name="gender" required>
                <?php
                if ($sex_result->num_rows > 0) {
                    while($row = $sex_result->fetch_assoc()) {
                        echo "<option value='{$row['Sex_ID']}'>{$row['Sex']}</option>";
                    }
                } else {
                    echo "<option value=''>沒有資料</option>";
                }
                ?>
            </select><br>
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
<?php
$conn->close();
?>
