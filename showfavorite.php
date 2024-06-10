<?php
include 'db.php'; // Include your database connection file

session_start(); // Start the session to retrieve user information

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "您需要先登入才能查看收藏的景點。";
    exit();
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Handle remove favorite action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_id'])) {
    $remove_id = $_POST['remove_id'];
    $sql_remove = "DELETE FROM user_favorite WHERE user_id = ? AND location_id = ?";
    $stmt_remove = $conn->prepare($sql_remove);
    $stmt_remove->bind_param("ii", $user_id, $remove_id);
    $stmt_remove->execute();
    $stmt_remove->close();
}

// Query to retrieve favorite locations for the logged-in user
$sql = "
    SELECT location_info.id, location_info.location_name 
    FROM user_favorites 
    JOIN location_info ON user_favorites.location_id = location_info.id 
    WHERE user_favorites.user_id = ?";

// Prepare the statement to prevent SQL injection
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$favorites = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $favorites[] = $row;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的收藏景點</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .btn {
            display: inline-block;
            padding: 5px 10px;
            margin: 5px;
            font-size: 14px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            outline: none;
            color: #fff;
            background-color: #4CAF50;
            border: none;
            border-radius: 5px;
            box-shadow: 0 4px #999;
        }

        .btn:hover {
            background-color: #3e8e41;
        }

        .btn:active {
            background-color: #3e8e41;
            box-shadow: 0 2px #666;
            transform: translateY(2px);
        }

        .btn-remove {
            background-color: #f44336;
        }

        .btn-remove:hover {
            background-color: #da190b;
        }

        .btn-remove:active {
            background-color: #da190b;
        }
    </style>
</head>
<body>
<header>
    <h1>我的收藏景點</h1>
    <nav>
        <ul>
            <li><a href="index.php">首頁</a></li>
            <li><a href="trails.php">步道地圖</a></li>
            <li><a href="leaflet.php">林道地圖</a></li>
            <li><a href="news.php">最新消息</a></li>
            <li><a href="weather.php">天氣預報</a></li>
            <?php if (isset($_SESSION['User_first_name'])) : ?>
                <li><a href="logout.php">登出</a></li>
            <?php else : ?>
                <li><a href="login.php">會員登入</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <h2>收藏的景點列表</h2>
    <?php if (!empty($favorites)): ?>
        <ul>
            <?php foreach ($favorites as $favorite): ?>
                <li>
                    <a href="details.php?id=<?php echo htmlspecialchars($favorite['id']); ?>"><?php echo htmlspecialchars($favorite['location_name']); ?></a>
                    <a href="notes.php?location_id=<?php echo htmlspecialchars($favorite['id']); ?>" class="btn">查看筆記</a>
                    <form method="POST" action="showfavorite.php" style="display:inline;">
                        <input type="hidden" name="remove_id" value="<?php echo htmlspecialchars($favorite['id']); ?>">
                        <input type="submit" value="移除" class="btn btn-remove">
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>您還沒有收藏任何景點。</p>
    <?php endif; ?>
</main>

</body>
</html>
