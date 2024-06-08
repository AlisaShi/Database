<?php
include 'db.php';

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT User_ID, User_passwd FROM user WHERE User_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['User_passwd'])) {
        // 密碼正確
        echo "登入成功！";
        // 這裡可以設置會話或重定向到用戶主頁
    } else {
        // 密碼錯誤
        echo "密碼錯誤，請重試。";
    }
} else {
    // 電子郵件不存在
    echo "此電子郵件未註冊。";
}

$stmt->close();
$conn->close();
?>
