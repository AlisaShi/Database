<?php
include('header.php');
?>
<?php
include 'db.php';

// 獲取城市列表
$sql_city = "SELECT * FROM city";
$result_city = $conn->query($sql_city);

// 獲取區域列表
$sql_district = "SELECT * FROM district";
$result_district = $conn->query($sql_district);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>景點查詢</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <main>
        <h2>景點查詢</h2>
        <form action="search_attractions.php" method="GET">
            <label for="city">城市：</label>
            <select name="city" id="city">
                <?php
                if ($result_city->num_rows > 0) {
                    while ($row = $result_city->fetch_assoc()) {
                        echo "<option value='{$row['City_ID']}'>{$row['City']}</option>";
                    }
                } else {
                    echo "<option value=''>沒有城市</option>";
                }
                ?>
            </select><br>
            <label for="district">區域：</label>
            <select name="district" id="district">
                <?php
                if ($result_district->num_rows > 0) {
                    while ($row = $result_district->fetch_assoc()) {
                        echo "<option value='{$row['District_ID']}'>{$row['District']}</option>";
                    }
                } else {
                    echo "<option value=''>沒有區域</option>";
                }
                ?>
            </select><br>
            <input type="submit" value="查詢">
        </form>
    </main>
</body>

</html>

<?php
$conn->close();
?>