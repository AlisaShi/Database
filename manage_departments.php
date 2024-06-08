<?php
include 'db.php';

// Get table that contains trailid trail cname and trid and trname and trphone and traddress
$query = "SELECT * FROM tr_admin ORDER BY TR_ID ASC;";
$result = $conn->query($query);

$message = $_GET['message'] ?? '';
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理單位</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        table {
            width: 99.5%;
            overflow: auto;
            border: 1px solid black;
            border-collapse: collapse;
            margin: 10px;
        }

        th, td {
            text-align: left;
            padding: 10px;
        }
    </style>
</head>
<body>
<header>
    <h1>管理單位</h1>
    <nav>
        <ul>
            <li><a href="admin.php">首頁</a></li>
            <li><a href="manage_trails.php">管理步道</a></li>
            <li><a href="manage_locations.php">管理地點</a></li>
            <li><a href="manage_users.php">管理用戶</a></li>
            <li><a href="manage_departments.php">管理部門</a></li>
        </ul>
    </nav>
</header>
<?php if ($message) : ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>
<table>
    <thead>
    <tr>
        <th>操作</th>
        <th>管理單位ID</th>
        <th>管理單位姓名</th>
        <th>管理單位電話</th>
        <th>管理單位地址</th>
    </tr>
    </thead>
    <tbody>
    <?php if ($result->num_rows > 0) : ?>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <form action="manage_departments_process.php" method="post">
                    <td>
                        <input type="hidden" name="TR_ID" value="<?= $row['TR_ID'] ?>">
                        <button type="submit" name="update">更新</button>
                    </td>
                    <td><?= $row['TR_ID'] ?></td>
                    <td><input type="text" name="TR_Name" value="<?= htmlspecialchars($row['TR_Name']) ?>"></td>
                    <td><input type="text" name="TR_Phone" value="<?= htmlspecialchars($row['TR_Phone']) ?>"></td>
                    <td><input type="text" name="TR_Address" value="<?= htmlspecialchars($row['TR_Address']) ?>"></td>
                </form>
            </tr>
        <?php endwhile; ?>
    <?php else : ?>
        <tr>
            <td colspan="5">無資料</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
</body>
</html>
