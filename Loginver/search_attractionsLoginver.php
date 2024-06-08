<?php
include 'db.php';

// 獲取用戶選擇的城市和區域
$city_id = $_GET['city'];
$district_id = $_GET['district'];

// 根據城市和區域查詢相應的景點
$sql = "SELECT * FROM location_info WHERE District_ID = '$district_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>查詢結果</h2>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>地址</th><th>描述</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['address']}</td>";
        echo "<td>{$row['description']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "沒有找到相關景點。";
}

$conn->close();
?>
