<?php
include 'db.php';

session_start();

//check to see if the user is logged in.
if(isset($_SESSION['email'])) {
    // User is logged in
    $email = $_SESSION['email'];
    $user_id = $_SESSION['user_id'];
}

$sql = "SELECT tr_info.*, trail.TR_CNAME FROM tr_info
        JOIN trail ON tr_info.TR_ID = trail.TRAILID";

$trail_name = isset($_GET['trail_name']) ? $_GET['trail_name'] : '';

if (!empty($trail_name)) {
    $sql .= " WHERE trail.TR_CNAME LIKE '%" . $conn->real_escape_string($trail_name) . "%'";
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>最新消息</title>
            <link rel="stylesheet" href="styles.css">
        </head>
        <body>
            <header>
                <h1>最新消息</h1>
                <nav>
            <ul>
                <li><a href="index.php">首頁</a></li>
                <li><a href="trails.php">步道地圖</a></li>
                <li><a href="leaflet.php">林道地圖</a></li>
                <li><a href="news.php">最新消息</a></li>
                <li><a href="weather.php">天氣預報</a></li>
                <li><a href="login.php">會員登入</a></li>

                <li>
                    <form method="GET" action="results.php">
                        <input type="text" id="search" name="search" placeholder="輸入景點名稱或描述">
                        <input type="submit" value="查詢">
                    </form>
                </li>
            </ul>
        </nav>
            </header>
            <main>
                <form method="GET" action="">
                    <label for="trail_name">Trail Name:</label>
                    <input type="text" id="trail_name" name="trail_name">
                    <input type="submit" value="Filter">
                </form>
                <h2>消息列表</h2>
                <table border="1">
                    <tr>
                        <th>Trail ID</th>
                        <th>Trail Name</th>
                        <th>Trail Type</th>
                        <th>Content</th>
                        <th>Announcement Date</th>
                        <th>Open Date</th>
                        <th>Close Date</th>
                        <th>Trail Subject</th>
                        <th>Trail ID</th>
                    </tr>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "
                                <tr>
                                    <td>{$row['TRAILID']}</td>
                                    <td>{$row['TR_CNAME']}</td>
                                    <td>{$row['TR_TYP']}</td>
                                    <td>" . (!empty($row['CONTENT']) ? $row['CONTENT'] : $row['TITLE']) . "</td>
                                    <td>{$row['ANN_DATE']}</td>
                                    <td>{$row['opendate']}</td>
                                    <td>{$row['closedate']}</td>
                                    <td>{$row['TR_SUB']}</td>
                                    <td>{$row['TR_ID']}</td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10'>沒有資料</td></tr>";
                    }
                    $conn->close();
                    ?>
                </table>
            </main>
        </body>
    </html>