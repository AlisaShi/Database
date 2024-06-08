<?php
include 'db.php';

$sql = "SELECT * FROM location_info";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>景點查詢</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>景點查詢</h1>
        <nav>
            <ul>
                <li><a href="index.php">首頁</a></li>
                <li><a href="attractions.php">景點查詢</a></li>
                <li><a href="news.php">最新消息</a></li>
                <li><a href="weather.php">天氣預報</a></li>
                <li><a href="login.php">會員登入</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>景點列表</h2>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>頁面URL</th>
                <th>開放時間</th>
                <th>關閉時間</th>
                <th>地址</th>
                <th>最低海拔</th>
                <th>最高海拔</th>
                <th>描述</th>
                <th>管理部門</th>
                <th>座標</th>
                <th>小型車允許</th>
                <th>大型車允許</th>
                <th>區域ID</th>
                <th>類型ID</th>
                <th>活動強度</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['page_url']}</td>
                            <td>{$row['opening_time']}</td>
                            <td>{$row['closing_time']}</td>
                            <td>{$row['address']}</td>
                            <td>{$row['altitude_min']}</td>
                            <td>{$row['altitude_max']}</td>
                            <td>{$row['description']}</td>
                            <td>{$row['managing_department']}</td>
                            <td>{$row['coordinates']}</td>
                            <td>" . ($row['small_vehicle_allowed'] ? '是' : '否') . "</td>
                            <td>" . ($row['large_vehicle_allowed'] ? '是' : '否') . "</td>
                            <td>{$row['District_ID']}</td>
                            <td>{$row['Type_ID']}</td>
                            <td>{$row['activity_intensity']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='15'>沒有資料</td></tr>";
            }
            $conn->close();
            ?>
        </table>
    </main>
</body>
</html>
