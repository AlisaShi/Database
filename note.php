<?php
include('header.php');
?>
<?php
include 'db.php';
session_start();

// Check if the user is logged in
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $user_id = $_SESSION['user_id'];
} else {
    header('Location: login.php');
    exit;
}

// Handle form submission for adding a note
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_note'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("INSERT INTO user_note (User_ID, Title, Content) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $title, $content);
    $stmt->execute();
    $stmt->close();
}

// Fetch liked locations
$sql_likes_locations = "SELECT li.location_name AS Name, 'location' AS Type
                        FROM user_like ul
                        JOIN location_info li ON ul.Item_ID = li.id
                        WHERE ul.User_ID = $user_id AND ul.Item_Type = 'location'";

$result_likes_locations = $conn->query($sql_likes_locations);

$liked_locations = [];
if ($result_likes_locations->num_rows > 0) {
    while ($row = $result_likes_locations->fetch_assoc()) {
        $liked_locations[] = $row;
    }
}

// Fetch liked trails
$sql_likes_trails = "SELECT t.TR_CNAME AS Name, 'trail' AS Type
                     FROM user_like ul
                     JOIN trail t ON ul.Item_ID = t.TRAILID
                     WHERE ul.User_ID = $user_id AND ul.Item_Type = 'trail'";

$result_likes_trails = $conn->query($sql_likes_trails);

$liked_trails = [];
if ($result_likes_trails->num_rows > 0) {
    while ($row = $result_likes_trails->fetch_assoc()) {
        $liked_trails[] = $row;
    }
}

// Combine liked locations and trails
$liked_items = array_merge($liked_locations, $liked_trails);

// Retrieve user notes
$stmt = $conn->prepare("SELECT * FROM user_note WHERE User_ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notes = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>筆記</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php
    include('header.php');
    ?>
    <h1>筆記</h1>
    
    <main>
        <h2>收藏的景點與步道</h2>
        <table>
            <thead>
                <tr>
                    <th>名稱</th>
                    <th>類型</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($liked_items)) : ?>
                    <?php foreach ($liked_items as $item) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['Name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo $item['Type'] === 'location' ? '景點' : '步道'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="2">尚未收藏任何景點或步道。</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <br>
        <br>
        <h2>新增筆記</h2>
        <form method="POST" action="note.php">
            <label for="title">標題:</label>
            <input type="text" id="title" name="title" required>
            <label for="content">內容:</label>
            <textarea id="content" name="content" required></textarea>
            <button type="submit" name="add_note">新增</button>
        </form>

        <h2>我的筆記</h2>
        <ul>
            <?php foreach ($notes as $note) : ?>
                <li>
                    <h3><?php echo htmlspecialchars($note['Title']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($note['Content'])); ?></p>
                    <small>Created at: <?php echo $note['Created_At']; ?></small>
                </li>
            <?php endforeach; ?>
        </ul>

    </main>
</body>

</html>