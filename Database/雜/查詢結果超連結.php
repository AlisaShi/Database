<?php
include 'db.php';

// 獲取用戶輸入的關鍵字
$search_term = $_GET['search'];

// 根據關鍵字查詢相應的景點
$sql = "SELECT * FROM location_info WHERE address LIKE '%$search_term%' OR description LIKE '%$search_term%'";
$result = $conn->query($sql);

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
        if ($result->num_rows > 0) {
            echo "<ul>";
            while($row = $result->fetch_assoc()) {
                echo "<li>";
                echo "<a href='details.php?id={$row['id']}'>{$row['location_name']} - {$row['description']}</a>";
                echo "</li>";
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
