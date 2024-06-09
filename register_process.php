<?php
// 启用错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

// 获取表单数据
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$gender = $_POST['gender'];
$email = $_POST['email'];
$birthday = $_POST['birthday'];
$password = $_POST['password'];
$permission = 0;

// 哈希密码
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 检查电子邮件是否已存在
$sql_check = "SELECT * FROM user WHERE User_email = ?";
$stmt_check = $conn->prepare($sql_check);

if ($stmt_check === false) {
    die('准备语句失败: ' . htmlspecialchars($conn->error));
}

$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    echo "此电子邮件已被注册。请使用其他电子邮件。";
} else {
    // 插入新用户信息
    $sql = "INSERT INTO user (User_first_name, User_last_name, User_gender, User_email, User_birthday, User_passwd, User_permission) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('准备语句失败: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("ssssssi", $first_name, $last_name, $gender, $email, $birthday, $hashed_password, $permission);

    if ($stmt->execute() === TRUE) {
        echo "注册成功！";
        header("Location: login.php");
        exit(); // 确保脚本在重定向后停止执行
    } else {
        echo "错误: " . htmlspecialchars($stmt->error);
    }
}

// 关闭连接
$stmt_check->close();
$stmt->close();
$conn->close();
?>
