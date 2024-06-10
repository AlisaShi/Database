<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$TRAILID = $_GET['TRAILID']; // 注意這裡用 TRAILID

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = $_POST['note'];
    $todo_list = $_POST['todo_list'];

    // 檢查是否已經有筆記
    $sql = "SELECT * FROM user_notes_trails WHERE user_id = '$user_id' AND TRAILID = '$TRAILID'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // 更新筆記
        $sql = "UPDATE user_notes_trails SET note = ?, todo_list = ? WHERE user_id = ? AND TRAILID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssis', $note, $todo_list, $user_id, $TRAILID);
    } else {
        // 插入新筆記
        $sql = "INSERT INTO user_notes_trails (user_id, TRAILID, note, todo_list) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('isss', $user_id, $TRAILID, $note, $todo_list);
    }

    if ($stmt->execute()) {
        echo "<script>alert('筆記已保存！');</script>";
    } else {
        echo "<script>alert('保存失敗：" . $stmt->error . "');</script>";
    }
}

// 獲取現有的筆記和待辦事項
$sql = "SELECT * FROM user_notes_trails WHERE user_id = '$user_id' AND TRAILID = '$TRAILID'";
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
    <textarea id="note" name="note" rows="10" cols="50"><?php echo htmlspecialchars($note['note'] ?? ''); ?></textarea><br>
    <label for="todo_list">待辦事項:</label><br>
    <textarea id="todo_list" name="todo_list" rows="10" cols="50"><?php echo htmlspecialchars($note['todo_list'] ?? ''); ?></textarea><br>
    <input type="submit" value="保存">
</form>
<button onclick="window.location.href='detailstrail.php?id=<?php echo $TRAILID; ?>'">返回景點詳情</button>
</body>
</html>
