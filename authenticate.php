<?php
// 启用错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

// 获取表单数据
$email = $_POST['email'];
$password = $_POST['password'];

// 检查电子邮件是否存在
$sql = "SELECT * FROM user WHERE User_email = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('准备语句失败: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // 获取用户数据
    $user = $result->fetch_assoc();

    // 验证密码
    if (password_verify($password, $user['User_passwd'])) {
        // 密码正确，开始会话并设置会话变量
        session_start();
        $_SESSION['email'] = $email;

        // 检查权限并重定向
        if ($user['User_permission'] == '1') {
            header("Location: admin.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        // 密码错误
        echo "密碼錯誤，請重試。";
        header("Location: index.php");
        exit();
    }
} else {
    // 用户不存在
    echo "用戶不存在。";
    exit();
}

// 关闭连接
$stmt->close();
$conn->close();
?>
