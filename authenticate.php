<?php
// 啟用錯誤報告
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include 'db.php';

// 獲取表單數據
$email = $_POST['email'];
$password = $_POST['password'];

// 檢查電子郵件是否存在
$sql = "SELECT * FROM user WHERE User_email = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('準備語句失敗: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // 獲取用戶數據
    $user = $result->fetch_assoc();

    // 驗證密碼
    if (password_verify($password, $user['User_passwd'])) {
        // 密碼正確，開始會話並設置會話變量
        $_SESSION['email'] = $email;
        $_SESSION['permission'] = $user['User_permission'];
        $_SESSION['user_id'] = $user['User_ID'];
        $_SESSION['username'] = $user['User_first_name'].' '.$user['User_last_name'];

        // 檢查權限並重定向
        if ($user['User_permission'] == '1') {
            header("Location: admin.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        // 密碼錯誤
        echo "密碼錯誤，請重試。";
        header("Location: index.php");
        exit();
    }
} else {
    // 用戶不存在
    echo "用戶不存在。";
    exit();
}

// 關閉連接
$stmt->close();
$conn->close();
?>