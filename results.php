<?php
include 'db.php';

session_start();

//check to see if the user is logged in.
if(isset($_SESSION['email'])) {
    // User is logged in
    $email = $_SESSION['email'];
    $user_id = $_SESSION['user_id'];
}

// 獲取用戶輸入的關鍵字
$search_term = $_GET['search'];

// 查詢 location_info 表
$sql_location = "SELECT Type_ID, id, location_name, ST_X(coordinates) as longitude, ST_Y(coordinates) as latitude, address 
                 FROM location_info 
                 WHERE address LIKE '%$search_term%' OR description LIKE '%$search_term%'";

$result_location = $conn->query($sql_location);

// 檢查 location_info 查詢是否有錯誤
if ($conn->error) {
    die("Location query failed: " . $conn->error);
}

// 將結果轉為陣列
$locations = [];
while ($row = $result_location->fetch_assoc()) {
    $locations[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查詢結果</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        #map { height: 600px; margin-top: 20px; }
        #info { margin-top: 20px; padding: 10px; border: 1px solid #ccc; }
    </style>
</head>

<body>
    <header>
    <header>
        <h1>查詢結果</h1>
        <nav>
            <ul>
                <li><a href="index.php">首頁</a></li>
                <li><a href="leaflet.php">步道地圖</a></li>
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
        <div id="info">將游標移到地圖上的標記點以查看詳細資訊</div>
        <div id="map"></div>

        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
        <script>
            // 初始化地圖
            var map = L.map('map').setView([23.6978, 120.9605], 7);

            // 添加OpenStreetMap圖層
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // 定義不同顏色的樣式
            var styles = {
                1: { color: 'red', fillColor: 'red', fillOpacity: 0.5, radius: 8 },
                2: { color: 'blue', fillColor: 'blue', fillOpacity: 0.5, radius: 8 },
                3: { color: 'green', fillColor: 'green', fillOpacity: 0.5, radius: 8 },
                4: { color: 'yellow', fillColor: 'yellow', fillOpacity: 0.5, radius: 8 },
                5: { color: 'purple', fillColor: 'purple', fillOpacity: 0.5, radius: 8 },
            };

            // 景點數據
            var locations = <?php echo json_encode($locations); ?>;
            console.log(locations);

            // 信息框
            var info = document.getElementById('info');

            // 添加標記點及事件監聽
            locations.forEach(function(location) {
                var style = styles[location.Type_ID] || styles[1]; // 默認使用Type_ID為1的樣式
                var marker = L.circleMarker([location.latitude, location.longitude], style).addTo(map)
                    .bindPopup(location.location_name);
                
                var clicked = false; // 追蹤彈出窗口是否被點擊
                
                marker.on('click', function() {
                    clicked = true; // 當標記點被點擊時設置clicked為true
                });
                
                marker.on('mouseover', function() {
                    info.innerHTML = 
                        `<b>${location.location_name}</b>
                        <br>${location.address}`;
                    this.openPopup(); // 當滑鼠懸停在標記點上時打開彈出窗口
                    if(isHovered && isHovered !== this) {
                        isHovered.closePopup();
                    }
                    isHovered = this;
                });

                marker.on('mouseout', function() {
                });
                
                map.on('click', function() {
                    if (isHovered) {
                        isHovered.closePopup(); // 關閉最後懸停標記點的彈出窗口
                    }
                });

                marker.on('dblclick', function() {
                    window.location.href = 'details.php?id=' + location.id;
                });
            });
        </script>

        <?php if (count($locations) > 0): ?>
            <ul>
                <?php foreach ($locations as $location): ?>
                    <li><a href="details.php?id=<?= $location['id'] ?>"><?= $location['location_name'] ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>沒有找到相關景點。</p>
        <?php endif; ?>
    </main>
</body>
</html>