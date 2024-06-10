<?php
session_start();
include 'db.php';

// 確認用戶是否已登入
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 獲取動作和景點ID
$action = $_GET['action'] ?? '';
$location_id = $_GET['location_id'] ?? '';
$user_id = $_SESSION['user_id']; // 確保這裡有用戶ID

if ($action == 'add' && !empty($location_id)) {
    // 檢查是否已收藏
    $stmt = $conn->prepare("SELECT * FROM user_favorites WHERE user_id = ? AND location_id = ?");
    $stmt->bind_param("ii", $user_id, $location_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // 插入收藏記錄
        $stmt = $conn->prepare("INSERT INTO user_favorites (user_id, location_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $location_id);
        if ($stmt->execute()) {
            $message = "收藏成功！";
        } else {
            $message = "收藏失敗: " . $stmt->error;
        }
    } else {
        $message = "已經收藏過了。";
    }

    $stmt->close();
} else {
    $message = "無效的操作。";
}

// 重定向回詳情頁面並附上訊息
header("Location: details.php?id=$location_id&message=" . urlencode($message));
exit();
?>
