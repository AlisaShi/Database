<?php
include 'db.php';

$sql = "SELECT * FROM trail";
$result = $conn->query($sql);

$message = $_GET['message'] ?? '';
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>管理步道資訊</title>
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
<h1>管理步道資訊</h1>
    <nav>
        <ul>
        <li><a href="admin.php">首頁</a></li>
        <li><a href="manage_locations.php">管理景點</a></li>
        <li><a href="manage_trails.php">管理步道</a></li>
        <li><a href="manage_users.php">管理使用者</a></li>
        <li><a href="manage_departments.php">管理部門</a></li>
        <!-- 你可以在这里添加更多的管理页面链接 -->
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
                <th>TRAIL ID</th>
                <th>步道名稱</th>
                <th>步道編號</th>
                <th>城市ID</th>
                <th>區域ID</th>
                <th>步道長度</th>
                <th>步道海拔</th>
                <th>最低海拔</th>
                <th>需要許可</th>
                <th>步道路面</th>
                <th>難度等級</th>
                <th>遊覽時間</th>
                <th>最佳季節</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0) : ?>
                <?php while($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <form action="manage_trails_process.php" method="post">
                            <td>
                                <input type="hidden" name="TRAILID" value="<?= $row['TRAILID'] ?>">
                                <button type="submit" name="update">更新</button>
                                <button type="submit" name="delete" onclick="return confirm('確定要刪除這個紀錄嗎?')">刪除</button>

                            </td>
                            <td><?= $row['TRAILID'] ?></td>
                            <td><input type="text" name="TR_CNAME" value="<?= htmlspecialchars($row['TR_CNAME']) ?>"></td>
                            <td><input type="number" name="TR_ID" value="<?= htmlspecialchars($row['TR_ID']) ?>"></td>
                            <td><input type="text" name="City_ID" value="<?= htmlspecialchars($row['City_ID']) ?>"></td>
                            <td><input type="text" name="District_ID" value="<?= htmlspecialchars($row['District_ID']) ?>"></td>
                            <td><input type="text" name="TR_LENGTH" value="<?= htmlspecialchars($row['TR_LENGTH']) ?>"></td>
                            <td><input type="number" step="0.01" name="TR_ALT" value="<?= htmlspecialchars($row['TR_ALT']) ?>"></td>
                            <td><input type="number" step="0.01" name="TR_ALT_LOW" value="<?= htmlspecialchars($row['TR_ALT_LOW']) ?>"></td>
                            <td><input type="checkbox" name="TR_permit_stop" value="1" <?= $row['TR_permit_stop'] ? 'checked' : '' ?>></td>
                            <td><input type="text" name="TR_PAVE" value="<?= htmlspecialchars($row['TR_PAVE']) ?>"></td>
                            <td><input type="number" name="TR_DIF_CLASS" value="<?= htmlspecialchars($row['TR_DIF_CLASS']) ?>"></td>
                            <td><input type="text" name="TR_TOUR" value="<?= htmlspecialchars($row['TR_TOUR']) ?>"></td>
                            <td><input type="text" name="TR_BEST_SEASON" value="<?= htmlspecialchars($row['TR_BEST_SEASON']) ?>"></td>
                        </form>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr>
                    <td colspan="14">無資料</td>
                </tr>
            <?php endif; ?>
            <tr>
                <form action="manage_trails_process.php" method="post">
                    <td><button type="submit" name="add">新增</button></td>
                    <td><input type="text" name="TRAILID" required></td>
                    <td><input type="text" name="TR_CNAME" required></td>
                    <td><input type="number" name="TR_ID" required></td>
                    <td><input type="text" name="City_ID" required></td>
                    <td><input type="text" name="District_ID" required></td>
                    <td><input type="text" name="TR_LENGTH"></td>
                    <td><input type="number" step="0.01" name="TR_ALT"></td>
                    <td><input type="number" step="0.01" name="TR_ALT_LOW"></td>
                    <td><input type="checkbox" name="TR_permit_stop" value="1"></td>
                    <td><input type="text" name="TR_PAVE"></td>
                    <td><input type="number" name="TR_DIF_CLASS"></td>
                    <td><input type="text" name="TR_TOUR"></td>
                    <td><input type="text" name="TR_BEST_SEASON"></td>
                </form>
            </tr>
        </tbody>
    </table>
</body>
</html>
