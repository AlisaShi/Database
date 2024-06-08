<?php
include 'db.php';

// 獲取 URL 中的 trail ID
$id = $_GET['id'];

// 查詢 trail 表中的指定數據
$sql = "SELECT * FROM trail ";
$result = $conn->query($sql);

// 檢查是否有查到結果
if ($result->num_rows > 0) {
    $trail = $result->fetch_assoc();
} else {
    echo "找不到該步道的資料。";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>步道資訊</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1><?php echo $trail['TR_CNAME']; ?></h1>
    </header>
    <main>
        <p><strong>Trail ID:</strong> <?php echo $trail['TRAILID']; ?></p>
        <p><strong>City ID:</strong> <?php echo $trail['City_ID']; ?></p>
        <p><strong>District ID:</strong> <?php echo $trail['District_ID']; ?></p>
        <p><strong>Length:</strong> <?php echo $trail['TR_LENGTH']; ?></p>
        <p><strong>Altitude:</strong> <?php echo $trail['TR_ALT']; ?></p>
        <p><strong>Lowest Altitude:</strong> <?php echo $trail['TR_ALT_LOW']; ?></p>
        <p><strong>Permit Stop:</strong> <?php echo $trail['TR_permit_stop'] ? 'Yes' : 'No'; ?></p>
        <p><strong>Paving:</strong> <?php echo $trail['TR_PAVE']; ?></p>
        <p><strong>Difficulty Class:</strong> <?php echo $trail['TR_DIF_CLASS']; ?></p>
        <p><strong>Tour:</strong> <?php echo $trail['TR_TOUR']; ?></p>
        <p><strong>Best Season:</strong> <?php echo $trail['TR_BEST_SEASON']; ?></p>
        <?php $conn->close(); ?>
    </main>
</body>
</html>
