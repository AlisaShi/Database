<?php
include 'db.php';

// 獲取用戶輸入的關鍵字
$search_term = $_GET['search'];

// 查詢 location_info 表
$sql_location = "SELECT * FROM location_info WHERE address LIKE '%$search_term%' OR description LIKE '%$search_term%'";
$result_location = $conn->query($sql_location);

// 檢查 location_info 查詢是否有錯誤
if ($conn->error) {
    die("Location query failed: " . $conn->error);
}

// 查詢 tr_info 和 trail 表
$sql_trail = "SELECT trail.TR_CNAME 
              FROM trail 
              WHERE trail.TR_CNAME LIKE '%$search_term%'";
$result_trail = $conn->query($sql_trail);

// 檢查 tr_info 和 trail 查詢是否有錯誤
if ($conn->error) {
    die("Trail query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查詢結果</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>查詢結果</h1>
    </header>
    <main>
        <?php
        if ($result_location->num_rows > 0 || $result_trail->num_rows > 0) {
            echo "<ul>";

            // 顯示 location_info 表的結果
            if ($result_location->num_rows > 0) {
                while ($row = $result_location->fetch_assoc()) {
                    echo "<li>";
                    echo "<a href='details.php?id={$row['id']}'>{$row['location_name']}</a>";
                    echo "</li>";
                }
            }

            // 顯示 tr_info 和 trail 表的結果
            if ($result_trail->num_rows > 0) {
                while ($row = $result_trail->fetch_assoc()) {
                    echo "<li>";
                    echo "<a href='detailstrail.php?id={$row['TRAILID']}'>{$row['location_name']} ({$row['TR_CNAME']})</a>";
                    echo "</li>";
                }
            }

            echo "</ul>";
        } else {
            echo "沒有找到相關景點。";
        }

        $conn->close();
        ?>
    </main>
</body>
</html>
