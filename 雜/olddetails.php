<?php
include 'db.php';

// 獲取URL中的景點ID
$id = $_GET['id'];

// 根據ID查詢景點的詳細信息
$sql = "SELECT * FROM location_info WHERE id = $id";
$result = $conn->query($sql);

// 檢查是否查到結果
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "找不到該景點的詳細信息。";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>景點詳細資訊</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1><?php echo $row['location_name']; ?></h1>
    </header>
    <main>
        <p><strong>Address:</strong> <?php echo $row['address']; ?></p>
        <p><strong>Description:</strong> <?php echo $row['description']; ?></p>
        <p><strong>URL:</strong> <a href="<?php echo $row['page_url']; ?>" target="_blank"><?php echo $row['page_url']; ?></a></p>
        <p><strong>Opening Time:</strong> <?php echo $row['opening_time']; ?></p>
        <p><strong>Closing Time:</strong> <?php echo $row['closing_time']; ?></p>
        <p><strong>Altitude Min:</strong> <?php echo $row['altitude_min']; ?></p>
        <p><strong>Altitude Max:</strong> <?php echo $row['altitude_max']; ?></p>
        <p><strong>Managing Department:</strong> <?php echo $row['managing_department']; ?></p>
        <p><strong>Small Vehicle Allowed:</strong> <?php echo $row['small_vehicle_allowed'] ? 'Yes' : 'No'; ?></p>
        <p><strong>Large Vehicle Allowed:</strong> <?php echo $row['large_vehicle_allowed'] ? 'Yes' : 'No'; ?></p>
    </main>
</body>
</html>
