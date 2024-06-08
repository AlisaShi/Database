<?php
include 'db.php';

session_start();

//check to see if the user is logged in.
if(isset($_SESSION['email'])) {
    // User is logged in
    $email = $_SESSION['email'];
    $user_id = $_SESSION['user_id'];
}


$sql = "SELECT id, location_name, page_url, opening_time, closing_time, address, altitude_min, altitude_max, description, managing_department, ST_AsText(coordinates) as coordinates, small_vehicle_allowed, large_vehicle_allowed, District_ID, Type_ID, activity_intensity FROM location_info";
$result = $conn->query($sql);

$message = $_GET['message'] ?? '';
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>管理景點位置</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 10px;
        }
        th, td {
            text-align: left;
        }
    </style>
</head>
<body>
    <header>
    <h1>管理景點位置</h1>
    <nav>
        <ul>
        <li><a href="admin.php">首頁</a></li>
        <li><a href="manage_locations.php">管理景點</a></li>
        <li><a href="manage_trails.php">管理步道</a></li>
        <li><a href="manage_users.php">管理使用者</a></li>
        <li><a href="manage_departments.php">管理部門</a></li>
        <!-- 你可以在這裡添加更多的管理頁面鏈接 -->
    </ul>
    </nav>
    </header>
    
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <table>
        <thead>
            <tr>
                <th>操作</th>
                <th>ID</th>
                <th>Location Name</th>
                <th>Page URL</th>
                <th>Opening Time</th>
                <th>Closing Time</th>
                <th>Address</th>
                <th>Altitude Min</th>
                <th>Altitude Max</th>
                <th>Description</th>
                <th>Managing Department</th>
                <th>Coordinates (X)</th>
                <th>Coordinates (Y)</th>
                <th>Small Vehicle Allowed</th>
                <th>Large Vehicle Allowed</th>
                <th>District ID</th>
                <th>Type ID</th>
                <th>Activity Intensity</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0) : ?>
                <?php while($row = $result->fetch_assoc()) : ?>
                    <?php
                        // 解析 coordinates 字段
                        preg_match('/POINT\(([^ ]+) ([^ ]+)\)/', $row['coordinates'], $matches);
                        $x_coord = $matches[1] ?? '';
                        $y_coord = $matches[2] ?? '';
                    ?>
                    <tr>
                        <form action="manage_locations_process.php" method="post">
                            <td>
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button type="submit" name="update">更新</button>
                                <button type="submit" name="delete" onclick="return confirm('確定要刪除這個紀錄嗎?')">刪除</button>
                            </td>
                            <td><?= $row['id'] ?></td>
                            <td><input type="text" name="location_name" value="<?= htmlspecialchars($row['location_name']) ?>"></td>
                            <td><input type="text" name="page_url" value="<?= htmlspecialchars($row['page_url']) ?>"></td>
                            <td><input type="time" name="opening_time" value="<?= htmlspecialchars($row['opening_time']) ?>"></td>
                            <td><input type="time" name="closing_time" value="<?= htmlspecialchars($row['closing_time']) ?>"></td>
                            <td><input type="text" name="address" value="<?= htmlspecialchars($row['address']) ?>"></td>
                            <td><input type="number" name="altitude_min" value="<?= htmlspecialchars($row['altitude_min']) ?>"></td>
                            <td><input type="number" name="altitude_max" value="<?= htmlspecialchars($row['altitude_max']) ?>"></td>
                            <td><input type="text" name="description" value="<?= htmlspecialchars($row['description']) ?>"></td>
                            <td><input type="text" name="managing_department" value="<?= htmlspecialchars($row['managing_department']) ?>"></td>
                            <td><input type="text" name="x_coord" value="<?= htmlspecialchars($x_coord) ?>"></td>
                            <td><input type="text" name="y_coord" value="<?= htmlspecialchars($y_coord) ?>"></td>
                            <td><input type="checkbox" name="small_vehicle_allowed" value="1" <?= $row['small_vehicle_allowed'] ? 'checked' : '' ?>></td>
                            <td><input type="checkbox" name="large_vehicle_allowed" value="1" <?= $row['large_vehicle_allowed'] ? 'checked' : '' ?>></td>
                            <td><input type="text" name="District_ID" value="<?= htmlspecialchars($row['District_ID']) ?>"></td>
                            <td><input type="number" name="Type_ID" value="<?= htmlspecialchars($row['Type_ID']) ?>"></td>
                            <td><input type="number" name="activity_intensity" value="<?= htmlspecialchars($row['activity_intensity']) ?>"></td>
                        </form>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr>
                    <td colspan="17">無資料</td>
                </tr>
            <?php endif; ?>
            <tr>
                <form action="manage_locations_process.php" method="post">
                    <td><button type="submit" name="add">新增</button></td>
                    <td></td>
                    <td><input type="text" name="location_name" required></td>
                    <td><input type="text" name="page_url" required></td>
                    <td><input type="time" name="opening_time" required></td>
                    <td><input type="time" name="closing_time"></td>
                    <td><input type="text" name="address"></td>
                    <td><input type="number" name="altitude_min"></td>
                    <td><input type="number" name="altitude_max"></td>
                    <td><input type="text" name="description"></td>
                    <td><input type="text" name="managing_department"></td>
                    <td><input type="text" name="x_coord"></td>
                    <td><input type="text" name="y_coord"></td>
                    <td><input type="checkbox" name="small_vehicle_allowed" value="1"></td>
                    <td><input type="checkbox" name="large_vehicle_allowed" value="1"></td>
                    <td><input type="text" name="District_ID"></td>
                    <td><input type="number" name="Type_ID"></td>
                    <td><input type="number" name="activity_intensity"></td>
                </form>
            </tr>
        </tbody>
    </table>
</body>
</html>
