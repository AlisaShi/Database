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
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>地址</th><th>描述</th></tr>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row['location_name']}</td>";
                echo "<td>{$row['description']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "沒有找到相關景點。";
        }

        $conn->close();
        ?>
    </main>
</body>
</html>
