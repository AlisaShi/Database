<?php
include 'db.php';
session_start();

//check to see if the user is logged in.
if(isset($_SESSION['email'])) {
    // User is logged in
    $email = $_SESSION['email'];
    $user_id = $_SESSION['user_id'];
}

// 獲取 URL 中的 trail ID
$id = $_GET['id'];

// 查詢 trail 表中的指定數據
$sql = "SELECT * FROM trail WHERE TRAILID = $id";;
$result = $conn->query($sql);

// 檢查是否有查到結果
if ($result->num_rows > 0) {
    $trail = $result->fetch_assoc();
} else {
    echo "找不到該步道的資料。";
    exit();
}

$city_id = $trail['City_ID'];
$sql_city = "SELECT * FROM city WHERE City_ID = '$city_id'";
$result_city = $conn->query($sql_city);

if ($result_city->num_rows > 0) {
    $city = $result_city->fetch_assoc();
} else {
    echo "找不到該縣市";
    exit();
}

// Fetch the district information
$district_id = $trail['District_ID'];  // Assuming you have District_ID in location_info
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
$tr_id = $trail['TR_ID'];
$sql_tr = "SELECT TR_Name, TR_Phone FROM tr_admin WHERE TR_ID = '$tr_id'";
$result_tr = $conn->query($sql_tr);

if ($result_tr->num_rows > 0) {
    $tr_info = $result_tr->fetch_assoc();
    $managing_department = $tr_info['TR_Name'] . ', 連絡電話: ' . $tr_info['TR_Phone'];
} else {
    $managing_department = "未知";
}

$TRAILID = $trail['TRAILID'];
$sql_condition = "SELECT * FROM tr_info WHERE TRAILID = '$TRAILID'";
$result_condition = $conn->query($sql_condition);

$trail_condition = [];
if ($result_condition->num_rows > 0) {
    while ($row = $result_condition->fetch_assoc()) {
        $trail_condition[] = $row;
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
<h1><?php echo $trail['TR_CNAME']; ?></h1>
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
            </ul>
        </nav>
    </header>
    
    <main>
    <p><strong>縣市、鄉鎮市區:</strong> <?php echo $city['City']; ?><?php echo $district['District']; ?></p>
        <p><strong>步道長度:</strong> <?php echo $trail['TR_LENGTH']; ?></p>
        <p><strong>海拔高度:</strong> <?php echo $trail['TR_ALT']; ?> ~ <?php echo $trail['TR_ALT_LOW']; ?>(公尺)</p>
        <p><strong>是否需要入山申請:</strong> <?php echo $trail['TR_permit_stop'] ? '是' : '否'; ?></p>
        <p><strong>負責機構:</strong> 林業及自然保育署 <?php echo $managing_department; ?></p>
        <p><strong>步道路況簡述:</strong> <?php echo $trail['TR_PAVE']; ?></p>
        <p><strong>難度分級:</strong> <?php echo $trail['TR_DIF_CLASS']; ?></p>
        <p><strong>步道耗費時長:</strong> <?php echo $trail['TR_TOUR']; ?></p>
        <p><strong>推薦前往季節:</strong> <?php echo $trail['TR_BEST_SEASON']; ?></p>

        <h2>步道路況</h2>
        <table>
            <thead>
                <tr>
                    <th>公告日期</th>
                    <th>路況類型</th>
                    <th>標題</th>
                    <th>內容</th>
                    <th>開始日期</th>
                    <th>結束日期</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($trail_condition)): ?>
                    <?php foreach ($trail_condition as $condition): ?>
                        <tr>
                            <td><?php echo $condition['ANN_DATE']; ?></td>
                            <td><?php echo $condition['TR_TYP']; ?></td>
                            <td><?php echo $condition['TITLE']; ?></td>
                            <td><?php echo $condition['CONTENT']; ?></td>
                            <td><?php echo $condition['opendate']; ?></td>
                            <td><?php echo $condition['closedate']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">該步道一切正常</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h2>該地區一周天氣預報:  <?php echo $district['District']; ?></h2>
        <div> 早上: 06:00:00 ~ 18:00:00(半夜更新) | 12:00:00 ~ 18:00:00(中午更新)</div>
        <div> 晚上: 00:00:00 ~ 06:00:00(半夜更新) | 18:00:00 ~ (隔日)06:00:00(中午更新)</div>
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
                                    if ($end_time == "06:00:00") {
                                        echo '晚上';
                                    } elseif ($end_time == "18:00:00") {
                                        echo '早上';
                                    }
                                ?>
                            </td>
                            <td><?php echo date("l", strtotime($data['Start_Time'])); ?></td>
                            <td><?php echo $data['Weather_Type']; ?></td>
                            <td><?php echo $data['MaxTemperature']; ?>°C</td>
                            <td><?php echo $data['MinTemperature']; ?>°C</td>
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
    omnivore.kml('<?php echo $trail['TR_KML']; ?>')
        .on('ready', function() {
            var layer = this;

            // 调整视图以适应KML文件中的所有线条
            map.fitBounds(layer.getBounds());

            // 为每个图层添加鼠标事件
            layer.eachLayer(function(layer) {
                if (layer.feature && layer.feature.properties) {
                    var name = "<?php echo $trail['TR_CNAME']; ?>"; // 使用 TR_CNAME
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