<?php
include('db_config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>山上優雅</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        header {
            background-color: #333;
            color: white;
            padding: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .title {
            font-size: 30px;
            margin-left: 20px;
        }

        .title a {
            color: white;
            text-decoration: none;
        }

        nav {
            flex: 1;
            text-align: center;
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            display: inline-block;
            margin: 0;
        }

        nav ul li {
            display: inline;
            margin: 0 10px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
        }

        .user-actions {
            margin-right: 20px;
        }

        .user-actions a {
            color: white;
            text-decoration: none;
            margin-left: 10px;
        }

        #info {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        form {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        input[type="text"] {
            padding: 5px;
            font-size: 16px;
        }

        input[type="submit"] {
            padding: 5px 10px;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <header>
        <div class="title">
            <a href="index.php">山上優雅</a>
        </div>
        <nav>
            <ul>
                <?php if (isset($_SESSION['User_first_name'])) : ?>
                    <li><span>Welcome, <?php echo htmlspecialchars($_SESSION['User_first_name']); ?>!</span></li>
                    <?php if (isset($_SESSION['User_permission']) && $_SESSION['User_permission'] == 1) : ?>
                        <li><a href="manage_locations.php">管理景點</a></li>
                        <li><a href="manage_trails.php">管理步道</a></li>
                        <li><a href="manage_users.php">管理使用者</a></li>
                        <li><a href="manage_departments.php">管理部門</a></li>
                    <?php else : ?>
                        <li><a href="index.php">景點地圖</a></li>
                        <li><a href="trails.php">步道地圖</a></li>
                        <li><a href="leaflet.php">林道地圖</a></li>
                        <li><a href="news.php">最新消息</a></li>
                        <li><a href="weather.php">天氣預報</a></li>
                        <li><a href="showfavorite.php">我的收藏景點</a></li>
                        <li>
                            <form method="GET" action="results.php">
                                <input type="text" id="search" name="search" placeholder="輸入景點名稱或描述">
                                <input type="submit" value="查詢">
                            </form>
                        </li>
                    <?php endif; ?>
                <?php else : ?>
                    <li><a href="index.php">景點地圖</a></li>
                    <li><a href="trails.php">步道地圖</a></li>
                    <li><a href="leaflet.php">林道地圖</a></li>
                    <li><a href="news.php">最新消息</a></li>
                    <li><a href="weather.php">天氣預報</a></li>
                    <li>
                        <form method="GET" action="results.php">
                            <input type="text" id="search" name="search" placeholder="輸入景點名稱或描述">
                            <input type="submit" value="查詢">
                        </form>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="user-actions">
            <?php if (isset($_SESSION['User_first_name'])) : ?>
                <a href="logout.php">Logout</a>
            <?php else : ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
            <a href="index.php">首頁</a>
        </div>
    </header>
</body>

</html>
