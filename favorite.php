<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$location_id = $_GET['location_id'];

$action = $_GET['action'];

if ($action == 'add') {
    $sql = "INSERT INTO user_favorites (user_id, location_id) VALUES ('$user_id', '$location_id')";
    if ($conn->query($sql) === TRUE) {
        echo "收藏成功！";
    } else {
        echo "收藏失敗：" . $conn->error;
    }
} elseif ($action == 'remove') {
    $sql = "DELETE FROM user_favorites WHERE user_id = '$user_id' AND location_id = '$location_id'";
    if ($conn->query($sql) === TRUE) {
        echo "已取消收藏！";
    } else {
        echo "取消收藏失敗：" . $conn->error;
    }
}

header("Location: details.php?id=$location_id");
exit();
?>
