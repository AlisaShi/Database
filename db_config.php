<?php
// db_config.php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "20240604";

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 读取 GeoJSON 文件
$geojsonFile = 'path/to/your/file.geojson';
$geojsonData = file_get_contents($geojsonFile);

// 插入数据
$sql = "INSERT INTO geo_data (geojson_data) VALUES ('$geojsonData')";

if ($conn->query($sql) === TRUE) {
    echo "GeoJSON 数据已成功插入数据库。";
} else {
    echo "插入数据时出错: " . $conn->error;
}

$conn->close();
?>
