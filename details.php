<?php
include 'db.php';

// 獲取URL中的景點ID
$id = $_GET['id'];

// 根據ID查詢景點的詳細信息
$sql = "SELECT * FROM `location_info` WHERE id = $id";
$result = $conn->query($sql);

// 檢查是否查到結果
if ($result->num_rows > 0) {
    $location = $result->fetch_assoc();
} else {
    echo "找不到該景點的詳細訊息。";
    exit();
}

// Fetch the district information
$district_id = $location['District_ID'];  // Assuming you have District_ID in location_info
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
$tr_id = $location['TR_ID']?? null; // Use null coalescing operator to avoid undefined index error
$sql_tr = "SELECT TR_Name, TR_Phone FROM tr_admin WHERE TR_ID = '$tr_id'";
$result_tr = $conn->query($sql_tr);

if ($result_tr->num_rows > 0) {
    $tr_info = $result_tr->fetch_assoc();
    $managing_department = $tr_info['TR_Name'] . ', 連絡電話: ' . $tr_info['TR_Phone'];
} else {
    $managing_department = "未知";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>景點詳細資訊</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<header>
<h1><?php echo $location['location_name']; ?></h1>
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
        <p><strong>地址:</strong> <?php echo $location['address']; ?></p>
        <p><strong>描述:</strong> <?php echo $location['description']; ?></p>
        <p><strong>網址:</strong> <a href="<?php echo $location['page_url']; ?>" target="_blank"><?php echo $location['page_url']; ?></a></p>
        <p><strong>營業時間:</strong> <?php echo $location['opening_time']; ?> ~ <?php echo $location['closing_time']; ?></p>
        <p><strong>海拔高度:</strong> <?php echo $location['altitude_min']; ?> ~ <?php echo $location['altitude_max']; ?> (公尺)</p>
        <p><strong>負責機構:</strong> 林業及自然保育署 <?php echo $managing_department; ?></p>
        <p><strong>是否允許小型車進入:</strong> <?php echo $location['small_vehicle_allowed'] ? 'Yes' : 'No'; ?></p>
        <p><strong>是否允許大型車進入:</strong> <?php echo $location['large_vehicle_allowed'] ? 'Yes' : 'No'; ?></p>


        <h2>該地區一周天氣預報:  <?php echo $district['District']; ?></h2>
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
                        <td colspan="5">沒有對應天氣資料</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
    </main>
</body>
</html>