<?php
include 'db.php'; // 连接数据库的文件

session_start();

//check to see if the user is logged in.
if(isset($_SESSION['email'])) {
    // User is logged in
    $email = $_SESSION['email'];
    $user_id = $_SESSION['user_id'];
}

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
            body {
                font-family: Arial, sans-serif;
                margin: 10;
            }
            #map {height: 600px;}
            #info {
                margin-top: 20px;
                padding: 10px;
                border: 1px solid #ccc;
            }
        </style>
    </head>
    <body>
        <header>
            <h1>首頁</h1>
            <nav>
                <ul>
                    <li><a href="index.php">首頁</a></li>
                    <li><a href="trails.php">步道地圖</a></li>
                    <li><a href="leaflet.php">林道地圖</a></li>
                    <li><a href="news.php">最新消息</a></li>
                    <li><a href="weather.php">天氣預報</a></li>
                    <li><a href="login.php">會員登入</a></li>
                    <li>
                        <form method="GET" action="results.php">
                            <input type="text" id="search" name="search" placeholder="輸入景點名稱或描述">
                            <input type="submit" value="查詢">
                        </form>
                    </li>
                    <?php if(isset($_SESSION['email'])):?>
                        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username'])?></p>
                        <li><a href="logout.php">登出</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </header>
        <main>
            <div id="map"></div>
            <div id="info">將游標移到地圖上的標記點以查看詳細資訊</div>

            <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
            <script>
                // 初始化地图
                // 初始化地图
                var map = L.map('map').setView([23.6978, 120.9605], 7);

                // 添加OpenStreetMap图层
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                // 定义不同颜色的样式
                var styles = {
                    1: {
                        color: 'red',
                        fillColor: 'red',
                        fillOpacity: 0.5,
                        radius: 8
                    },
                    2: {
                        color: 'blue',
                        fillColor: 'blue',
                        fillOpacity: 0.5,
                        radius: 8
                    },
                    3: {
                        color: 'green',
                        fillColor: 'green',
                        fillOpacity: 0.5,
                        radius: 8
                    },
                    4: {
                        color: 'yellow',
                        fillColor: 'yellow',
                        fillOpacity: 0.5,
                        radius: 8
                    },
                    5: {
                        color: 'purple',
                        fillColor: 'purple',
                        fillOpacity: 0.5,
                        radius: 8
                    },
                };

                // 景点数据
                var locations = <?php echo json_encode($locations); ?>;
                console.log(locations);

                // 信息框
                var info = document.getElementById('info');

                // 添加标记点及事件监听
                locations.forEach(function(location) {
                    var style = styles[location.Type_ID] || styles[1]; // 默認使用Type_ID為1的樣式
                    var popupContent = `<a href="details.php?id=${location.id}" target="_blank">${location.location_name}</a>`;
                    var marker = L.circleMarker([location.latitude, location.longitude], style).addTo(map)
                        .bindPopup(popupContent); // 使用超連結作為彈出框的內容

                    marker.on('mouseover', function() {
                        info.innerHTML =
                            `<b>${location.location_name}</b>
                <br>${location.address}`;
                        this.openPopup(); // 當滑鼠懸停在標記點上時打開彈出窗口
                    });

                    marker.on('mouseout', function() {
                        // 清空信息框内容或其他操作
                    });

                    map.on('click', function() {
                        if (isHovered) {
                            isHovered.closePopup(); // 點擊地圖的其他地方時關閉彈出窗口
                        }
                        isHovered = false;
                    });
                });
            </script>
        </main>
    </body>
</html>