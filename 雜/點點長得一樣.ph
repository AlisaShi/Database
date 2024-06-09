<?php
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
                <li><a href="index.php">首頁</a></li>
                <li><a href="news.php">最新消息</a></li>
                <li><a href="weather.php">天氣預報</a></li>
                <li><a href="login.php">會員登入</a></li>
                <li>
                    <form method="GET" action="results.php">
                        <input type="text" id="search" name="search" placeholder="輸入景點名稱或描述">
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

            // 景点数据
            var locations = <?php echo json_encode($locations); ?>;
            console.log(locations);

            // 信息框
            var info = document.getElementById('info');

            // 添加标记点及事件监听
            locations.forEach(function(location) {
                var marker = L.marker([location.latitude, location.longitude]).addTo(map)
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
