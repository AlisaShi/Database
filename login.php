<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>會員登入</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>會員登入</h1>
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
        <h2>登入</h2>
        <form action="authenticate.php" method="POST">
            <label for="email">電子郵件：</label>
            <input type="email" id="email" name="email" required><br>
            <label for="password">密碼：</label>
            <input type="password" id="password" name="password" required><br>
            <input type="submit" value="登入">
        </form>
        <p>還沒有帳戶？<a href="register.php">註冊</a></p>
    </main>
</body>
</html>
