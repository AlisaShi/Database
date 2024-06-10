<?php
session_start();

// 检查用户是否已登录
$is_logged_in = isset($_SESSION['email']);

// 处理用户点击登出的逻辑
if (isset($_GET['logout'])) {
    // 清除所有会话变量
    session_unset();
    // 销毁会话
    session_destroy();
    // 重定向到未登录状态的首页
    header("Location: index.php");
    exit();
}

include 'db.php'; // 连接数据库的文件

// 获取景点数据
$query = "SELECT Type_ID, id, location_name, ST_X(coordinates) as longitude, ST_Y(coordinates) as latitude, address FROM `location_info`;";
$result = $conn->query($query);

$locations = [];
while ($row = $result->fetch_assoc()) {
    $locations[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>首頁</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        #map { height: 600px; }
        #info { margin-top: 20px; padding: 10px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <header>
        <h1>首頁</h1>
        <nav>
            <ul>
                
                <?php if (!$is_logged_in) { ?>
                    <!-- 如果用户未登录，显示登录链接 -->
                    <li><a href="indexLoginver.php">首頁</a></li>
                    <li><a href="news.php">最新消息</a></li>
                    <li><a href="weather.php">天氣預報</a></li>
                    <li><a href="login.php">會員登入</a></li>
                <?php } else { ?>
                    <!-- 如果用户已登录，显示笔记功能链接和登出链接 -->
                    <li><a href="indexLoginver.php">首頁</a></li>
                    <li><a href="news.php">最新消息</a></li>
                    <li><a href="weather.php">天氣預報</a></li>
                    <li><a href="note.php">筆記</a></li>
                    <li><a href="index.php?logout=true">登出</a></li>
                <?php } ?>
                <li>
                    <form method="GET" action="resultsLoginver.php">
                        <input type="text" id="searchLoginver" name="searchLoginver" placeholder="輸入景點名稱或描述">
                        <input type="submit" value="查詢">
                    </form>
                </li>
            </ul>
        </nav>
    </header>
    <main>
        <div id="map"></div>
        <div id="info">將游標移到地圖上的標記點以查看詳細資訊</div>

        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
        <script>
            // 初始化地图
            var map = L.map('map').setView([23.6978, 120.9605], 7);

            // 添加OpenStreetMap图层
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // 定义不同颜色的样式
            var styles = {
                1: { color: 'red', fillColor: 'red', fillOpacity: 0.5, radius: 8 },
                2: { color: 'blue', fillColor: 'blue', fillOpacity: 0.5, radius: 8 },
                3: { color: 'green', fillColor: 'green', fillOpacity: 0.5, radius: 8 },
                4: { color: 'yellow', fillColor: 'yellow', fillOpacity: 0.5, radius: 8 },
                5: { color: 'purple', fillColor: 'purple', fillOpacity: 0.5, radius: 8 },
            };

            // 景点数据
            var locations = <?php echo json_encode($locations); ?>;
            console.log(locations);

            // 信息框
            var info = document.getElementById('info');

            // 添加标记点及事件监听
            locations.forEach(function(location) {
                var style = styles[location.Type_ID] || styles[2]; // 默认使用Type_ID为1的样式
                var marker = L.circleMarker([location.latitude, location.longitude], style).addTo(map)
                    .bindPopup(location.location_name);

                marker.on('mouseover', function() {
                    info.innerHTML = `<b>${location.location_name}</b><br>${location.address}`;
                });

                marker.on('mouseout', function() {
                    info.innerHTML = '將游標移到地圖上的標記點以查看詳細資訊';
                });
            });
        </script>
    </main>
</body>
</html>
