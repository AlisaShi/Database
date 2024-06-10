<?php
include 'db.php'; // Include your database connection file

session_start(); // Start the session to retrieve user information

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "您需要先登入才能查看收藏的景點和步道。";
    exit();
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Handle remove favorite action for locations
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_location_id'])) {
    $remove_location_id = $_POST['remove_location_id'];
    $sql_remove_location = "DELETE FROM user_favorites WHERE user_id = ? AND location_id = ?";
    $stmt_remove_location = $conn->prepare($sql_remove_location);
    $stmt_remove_location->bind_param("ii", $user_id, $remove_location_id);
    $stmt_remove_location->execute();
    $stmt_remove_location->close();
}

// Handle remove favorite action for trails
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_trail_id'])) {
    $remove_trail_id = $_POST['remove_trail_id'];
    $sql_remove_trail = "DELETE FROM user_favorites_trails WHERE user_id = ? AND trailid = ?";
    $stmt_remove_trail = $conn->prepare($sql_remove_trail);
    $stmt_remove_trail->bind_param("ii", $user_id, $remove_trail_id);
    $stmt_remove_trail->execute();
    $stmt_remove_trail->close();
}

// Query to retrieve favorite locations for the logged-in user
$sql_locations = "
    SELECT location_info.id, location_info.location_name 
    FROM user_favorites 
    JOIN location_info ON user_favorites.location_id = location_info.id 
    WHERE user_favorites.user_id = ?";

// Prepare the statement to prevent SQL injection
$stmt_locations = $conn->prepare($sql_locations);
if (!$stmt_locations) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt_locations->bind_param("i", $user_id);
$stmt_locations->execute();
$result_locations = $stmt_locations->get_result();

$favorites_locations = [];
if ($result_locations->num_rows > 0) {
    while ($row_location = $result_locations->fetch_assoc()) {
        $favorites_locations[] = $row_location;
    }
}

$stmt_locations->close();

// Query to retrieve favorite trails for the logged-in user
$sql_trails = "
    SELECT user_favorites_trails.trailid, trail.tr_cname 
    FROM user_favorites_trails
    JOIN trail ON user_favorites_trails.trailid = trail.trailid 
    WHERE user_favorites_trails.user_id = ?";

// Prepare the statement to prevent SQL injection
$stmt_trails = $conn->prepare($sql_trails);
if (!$stmt_trails) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt_trails->bind_param("i", $user_id);
$stmt_trails->execute();
$result_trails = $stmt_trails->get_result();

$favorites_trails = [];
if ($result_trails->num_rows > 0) {
    while ($row_trail = $result_trails->fetch_assoc()) {
        $favorites_trails[] = $row_trail;
    }
}

$stmt_trails->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的收藏景點和步道</title>
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
    <h1>我的收藏景點和步道</h1>
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
    <?php if (!empty($favorites_locations)): ?>
        <h3>景點：</h3>
        <ul>
            <?php foreach ($favorites_locations as $favorite_location): ?>
                <li>
                    <a href="details.php?id=<?php echo htmlspecialchars($favorite_location['id']); ?>"><?php echo htmlspecialchars($favorite_location['location_name']); ?></a>
                    <form method="POST" action="showfavorite.php" style="display:inline;">
                        <input type="hidden" name="remove_location_id" value="<?php echo htmlspecialchars($favorite_location['id']); ?>">
                        <input type="submit" value="移除" class="btn btn-remove">
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>您還沒有收藏任何景點。</p>
    <?php endif; ?>

    <?php if (!empty($favorites_trails)): ?>
        <h3>步道：</h3>
        <ul>
            <?php foreach ($favorites_trails as $favorite_trail): ?>
                <li>
                    <a href="detailstrail.php?id=<?php echo htmlspecialchars($favorite_trail['trailid']); ?>"><?php echo htmlspecialchars($favorite_trail['tr_cname']); ?></a>
                    <form method="POST" action="showfavorite.php" style="display:inline;">
                        <input type="hidden" name="remove_trail_id" value="<?php echo htmlspecialchars($favorite_trail['trailid']); ?>">
                        <input type="submit" value="移除" class="btn btn-remove">
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>您還沒有收藏任何步道。</p>
    <?php endif; ?>
</main>
</body>
</html>

