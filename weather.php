<?php
include 'db.php';


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
<?php
include('header.php');
?>
    <header>
        <nav>
            <h1>天氣預報</h1>
        </nav>
    </header>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>天氣預報</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    
    
    <main>
        <h2>天氣預報列表</h2>
        <form method="GET" action="">
            <label for="district_name">District Name:</label>
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
                while ($row = $result->fetch_assoc()) {
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