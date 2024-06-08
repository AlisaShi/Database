<?php
include 'db.php';

session_start();

//check to see if the user is logged in.
if(isset($_SESSION['email'])) {
    // User is logged in
    $email = $_SESSION['email'];
    $user_id = $_SESSION['user_id'];
}

$district_name = isset($_GET['district_name']) ? $_GET['district_name'] : '';

$sql = "SELECT weather_forecast.*, district.District, weather_types.Weather_Type FROM weather_forecast
        JOIN district ON weather_forecast.District_ID = district.District_ID
        JOIN weather_types ON weather_forecast.Weather_Type_ID = weather_types.Weather_Type_ID";


if (!empty($district_name)) {
    $sql .= " WHERE district.District LIKE '%" . $conn->real_escape_string($district_name) . "%'";
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>天氣預報</title>
            <link rel="stylesheet" href="styles.css">
        </head>
    <body>
        <header>
            <h1>天氣預報</h1>
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
            <h2>天氣預報列表</h2>
            <form method="GET" action="" >
                    <label for="district_name" >District Name:</label>
                    <input type="text" id="district_name" name="district_name">
                    <input type="submit" value="Filter">
                </form>
            <table border="1">
                <tr>
                    <th>District</th>
                    <th>Weather Type</th>
                    <th>Max Temp</th>
                    <th>Min Temp</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Remarks</th>
                </tr>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['District']}</td>
                                <td>{$row['Weather_Type']}</td>
                                <td>{$row['MaxTemperature']}C</td>
                                <td>{$row['MinTemperature']}C</td>
                                <td>{$row['Start_Time']}</td>
                                <td>{$row['End_Time']}</td>
                                <td>" . (!empty($row['Remarks']) ? $row['Remarks'] : "None") . "</td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>沒有資料</td></tr>";
                }
                $conn->close();
                ?>
            </table>
        </main>
    </body>
</html>