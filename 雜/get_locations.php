<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "20240523";

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

$sql = "SELECT name, lat, lng, info FROM location_info";
$result = $conn->query($sql);

$locations = array();

if ($result->num_rows > 0) {
    // 输出数据
    while($row = $result->fetch_assoc()) {
        $locations[] = $row;
    }
} else {
    echo "0 结果";
}
$conn->close();

header('Content-Type: application/json');
echo json_encode($locations);
?>
