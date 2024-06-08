<?php
include 'db.php';

// 处理表单提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $note_id = $_POST["note_id"];
    $user_id = $_POST["user_id"];
    $note_date = $_POST["note_date"];
    $note_title = $_POST["note_title"];
    $note = $_POST["note"];
    
    $sql = "INSERT INTO user_note (Note_ID, User_ID, Note_Date, Note_title, Note) VALUES ('$note_id', '$user_id', '$note_date', '$note_title', '$note')";
    
    if ($conn->query($sql) === TRUE) {
        echo "新筆記新增成功！";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// 获取所有笔记
$sql = "SELECT * FROM user_note ORDER BY Note_Date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Notes</title>
</head>
<body>
    <h1>User Notes</h1>
    
    <form method="POST" action="note.php">
        <label for="note_id">Note ID:</label>
        <input type="text" name="note_id" required><br>
        <label for="user_id">User ID:</label>
        <input type="text" name="user_id" required><br>
        <label for="note_date">Note Date:</label>
        <input type="datetime-local" name="note_date" required><br>
        <label for="note_title">Note Title:</label>
        <input type="text" name="note_title" required><br>
        <label for="note">Note:</label>
        <textarea name="note" required></textarea><br>
        <button type="submit">新增筆記</button>
    </form>
    
    <h2>筆記列表</h2>
    <ul>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<li><strong>" . $row["Note_title"] . "</strong><br>" . $row["Note"] . "<br>" . $row["Note_Date"] . "</li>";
            }
        } else {
            echo "<li>沒有筆記</li>";
        }
        ?>
    </ul>
</body>
</html>

<?php
$conn->close();
?>
