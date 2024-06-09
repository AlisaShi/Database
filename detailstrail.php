<?php
include 'db.php';

// Sanitize and validate the ID parameter
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "無效的步道 ID。";
    exit();
}

// 查詢 trail 表中的指定數據
$sql = "SELECT * FROM trail WHERE TRAILID = $id";
$result = $conn->query($sql);

// 檢查是否有查到結果
if ($result->num_rows > 0) {
    $trail = $result->fetch_assoc();
} else {
    echo "找不到該步道的資料。";
    exit();
}

// Fetch the district information
$district_id = $trail['District_ID'];
$sql_district = "SELECT * FROM district WHERE District_ID = '$district_id'";
$result_district = $conn->query($sql_district);

if ($result_district->num_rows > 0) {
    $district = $result_district->fetch_assoc();
} else {
    echo "找不到該區的詳細訊息。";
    exit();
}

// Fetch weather forecast data for the district
$sql_weather = "SELECT wf.Start_Time, wf.End_Time, wt.Weather_Type, wf.MaxTemperature, wf.MinTemperature, wf.Remarks 
                FROM weather_forecast wf
                JOIN weather_types wt ON wf.Weather_Type_ID = wt.Weather_Type_ID
                WHERE wf.District_ID = '$district_id'
                AND wf.Start_Time >= CURDATE() AND wf.End_Time < DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                ORDER BY wf.Start_Time";
$result_weather = $conn->query($sql_weather);

$forecast_data = [];
if ($result_weather->num_rows > 0) {
    while ($row = $result_weather->fetch_assoc()) {
        $forecast_data[] = $row;
    }
}

// Fetch the managing department information
$tr_id = $trail['TR_ID'] ?? null; // Use null coalescing operator to avoid undefined index error
$managing_department = "未知";

if ($tr_id) {
    $sql_tr = "SELECT TR_Name, TR_Phone FROM tr_admin WHERE TR_ID = '$tr_id'";
    $result_tr = $conn->query($sql_tr);

    if ($result_tr->num_rows > 0) {
        $tr_info = $result_tr->fetch_assoc();
        $managing_department = $tr_info['TR_Name'] . ', 連絡電話: ' . $tr_info['TR_Phone'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>步道資訊</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-omnivore@0.3.4/leaflet-omnivore.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
</head>
<body>
<header>
<h1><?php echo htmlspecialchars($trail['TR_CNAME']); ?></h1>
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
                    <button onclick="window.location.href='results.php'">返回列表</button>
                    <button onclick="window.location.href='index.php'">返回首頁</button>
                </li>
            </ul>
        </nav>
    </header>
    
    <main>
        <p><strong>Trail ID:</strong> <?php echo htmlspecialchars($trail['TRAILID']); ?></p>
        <p><strong>City ID:</strong> <?php echo htmlspecialchars($trail['City_ID']); ?></p>
        <p><strong>District ID:</strong> <?php echo htmlspecialchars($trail['District_ID']); ?></p>
        <p><strong>Length:</strong> <?php echo htmlspecialchars($trail['TR_LENGTH']); ?></p>
        <p><strong>Altitude:</strong> <?php echo htmlspecialchars($trail['TR_ALT']); ?></p>
        <p><strong>Lowest Altitude:</strong> <?php echo htmlspecialchars($trail['TR_ALT_LOW']); ?></p>
        <p><strong>Permit Stop:</strong> <?php echo $trail['TR_permit_stop'] ? 'Yes' : 'No'; ?></p>
        <p><strong>Paving:</strong> <?php echo htmlspecialchars($trail['TR_PAVE']); ?></p>
        <p><strong>Difficulty Class:</strong> <?php echo htmlspecialchars($trail['TR_DIF_CLASS']); ?></p>
        <p><strong>Tour:</strong> <?php echo htmlspecialchars($trail['TR_TOUR']); ?></p>
        <p><strong>Best Season:</strong> <?php echo htmlspecialchars($trail['TR_BEST_SEASON']); ?></p>
        
        <h2>該地區一周天氣預報:  <?php echo htmlspecialchars($district['District']); ?></h2>
        <div> 早上: 06:00:00 ~ 18:00:00</div>
        <div> 晚上: 18:00:00 ~ 06:00:00(跨天)</div>
        <table>
            <thead>
                <tr>
                    <th>日期</th>
                    <th>時間</th>
                    <th>星期</th>
                    <th>天氣類別</th>
                    <th>最高氣溫</th>
                    <th>最低氣溫</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($forecast_data)): ?>
                    <?php foreach ($forecast_data as $data): ?>
                        <tr>
                            <td><?php echo date("Y-m-d", strtotime($data['Start_Time'])); ?></td>
                            <td colspan="1">
                                <?php 
                                    $end_time = date("H:i:s", strtotime($data['End_Time']));
                                    echo ($end_time == "06:00:00") ? '晚上' : (($end_time == "18:00:00") ? '早上' : '');
                                ?>
                            </td>
                            <td><?php echo date("l", strtotime($data['Start_Time'])); ?></td>
                            <td><?php echo htmlspecialchars($data['Weather_Type']); ?></td>
                            <td><?php echo htmlspecialchars($data['MaxTemperature']); ?>°C</td>
                            <td><?php echo htmlspecialchars($data['MinTemperature']); ?>°C</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">沒有對應天氣資料</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div id="map"></div>
        <script>
            // 初始化地图并设置视图为台湾的中心和适当的缩放级别
            var map = L.map('map').setView([23.5, 121], 7);

            // 添加 OpenStreetMap 图层
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // 加载KML文件并添加到地图
            omnivore.kml('<?php echo htmlspecialchars($trail['TR_KML']); ?>')
                .on('ready', function() {
                    var layer = this;

                    // 调整视图以适应KML文件中的所有线条
                    map.fitBounds(layer.getBounds());

                    // 为每个图层添加鼠标事件
                    layer.eachLayer(function(layer) {
                        if (layer.feature && layer.feature.properties) {
                            var name = "<?php echo htmlspecialchars($trail['TR_CNAME']); ?>"; // 使用 TR_CNAME
                            var description = layer.feature.properties.description || "";
                            var popupContent = "<strong>" + name + "</strong><br>" + description;
                            layer.bindPopup(popupContent);

                            // 添加鼠标悬停事件
                            layer.on('mouseover', function(e) {
                                var popup = L.popup()
                                    .setLatLng(e.latlng)
                                    .setContent(name)
                                    .openOn(map);
                            });

                            // 添加鼠标离开事件，关闭弹出窗口
                            layer.on('mouseout', function() {
                                map.closePopup();
                            });
                        }
                    });
                })
                .addTo(map);
        </script>

        <?php $conn->close(); ?>
    </main>
</body>
</html>
