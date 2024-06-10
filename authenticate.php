<?php
include('db_config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['User_email'];
    $password = $_POST['User_passwd'];

    $sql = "SELECT User_ID, User_first_name, User_passwd FROM user WHERE User_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['User_passwd'])) {
            // 密碼正確
            $_SESSION['User_ID'] = $row['User_ID'];
            $_SESSION['User_first_name'] = $row['User_first_name'];
            $_SESSION['user_id'] = $row['User_ID'];
            header('Location: index.php');
            exit();
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
}
