<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$location_id = $_GET['location_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = $_POST['note'];
    $todo_list = $_POST['todo_list'];

    $sql = "SELECT * FROM user_notes WHERE user_id = '$user_id' AND location_id = '$location_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $sql = "UPDATE user_notes SET note = ?, todo_list = ? WHERE user_id = ? AND location_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssii', $note, $todo_list, $user_id, $location_id);
    } else {
        $sql = "INSERT INTO user_notes (user_id, location_id, note, todo_list) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iiss', $user_id, $location_id, $note, $todo_list);
    }

    if ($stmt->execute()) {
        echo "筆記已保存！";
    } else {
        echo "保存失敗：" . $conn->error;
    }
}

$sql = "SELECT * FROM user_notes WHERE user_id = '$user_id' AND location_id = '$location_id'";
$result = $conn->query($sql);
$note = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理筆記與待辦事項</title>
</head>
<body>
<h1>管理筆記與待辦事項</h1>
<form method="POST">
    <label for="note">筆記:</label><br>
    <textarea id="note" name="note" rows="10" cols="50"><?php echo $note['note'] ?? ''; ?></textarea><br>
    <label for="todo_list">待辦事項:</label><br>
    <textarea id="todo_list" name="todo_list" rows="10" cols="50"><?php echo $note['todo_list'] ?? ''; ?></textarea><br>
    <input type="submit" value="保存">
</form>
<button onclick="window.location.href='details.php?id=<?php echo $location_id; ?>'">返回景點詳情</button>
</body>
</html>
