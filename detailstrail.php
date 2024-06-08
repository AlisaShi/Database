<?php
include 'db.php';
session_start();

// Check if the user is logged in
if(isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $user_id = $_SESSION['user_id'];
}

// Handle like request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like'])) {
    $item_id = $_POST['item_id'];
    $item_type = $_POST['item_type'];

    $stmt = $conn->prepare("INSERT INTO user_like (User_ID, Item_ID, Item_Type) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $item_id, $item_type);
    $stmt->execute();
    $stmt->close();
}

// Get trail details
$trail_id = $_GET['trail_id'];
$sql = "SELECT * FROM `trails` WHERE Trail_ID = $trail_id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $trail = $result->fetch_assoc();
} else {
    echo "找不到該步道的詳細訊息。";
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
$tr_id = $trail['TR_ID'];
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
    <title>步道詳細資訊</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
<h1><?php echo $trail['Trail_Name']; ?></h1>
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
        <p><strong>區域:</strong> <?php echo $district['District']; ?></p>
        <p><strong>總長度:</strong> <?php echo $trail['Trail_Length']; ?> 公里</p>
        <p><strong>描述:</strong> <?php echo $trail['Trail_Description']; ?></p>
        <p><strong>開始位置:</strong> <?php echo $trail['Trail_Start']; ?></p>
        <p><strong>結束位置:</strong> <?php echo $trail['Trail_End']; ?></p>
        <p><strong>是否循環:</strong> <?php echo $trail['Is_Loop'] ? '是' : '否'; ?></p>
        <p><strong>海拔高度:</strong> <?php echo $trail['Altitude_Min']; ?> ~ <?php echo $trail['Altitude_Max']; ?> (公尺)</p>
        <p><strong>負責機構:</strong> 林業及自然保育署 <?php echo $managing_department; ?></p>

        <!-- Like Button -->
        <form method="POST" action="detailstrail.php?trail_id=<?php echo $trail_id; ?>">
            <input type="hidden" name="item_id" value="<?php echo $trail_id; ?>">
            <input type="hidden" name="item_type" value="trail">
            <button type="submit" name="like">Like</button>
        </form>

        <h2>該地區一周天氣預報: <?php echo $district['District']; ?></h2>
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
                            <td><?php echo date("H:i", strtotime($data['Start_Time'])) . " - " . date("H:i", strtotime($data['End_Time'])); ?></td>
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
