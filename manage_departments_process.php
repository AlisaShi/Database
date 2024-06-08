<?php
include 'db.php';

$message = '';

if (isset($_POST['update'])) {
    // 更新數據
    $TR_ID = $_POST['TR_ID'];
    $TR_Name = $_POST['TR_Name'];
    $TR_Phone = $_POST['TR_Phone'];
    $TR_Address = $_POST['TR_Address'];

    $stmt = $conn->prepare("UPDATE tr_admin SET TR_Name = ?, TR_Phone = ?, TR_Address = ? WHERE TR_ID = ?");
    $stmt->bind_param('sssi', $TR_Name, $TR_Phone, $TR_Address, $TR_ID);

    if ($stmt->execute()) {
        $message = "更新成功";
    } else {
        $message = "更新失敗: " . $stmt->error;
    }

    $stmt->close();
}

if (isset($_POST['delete'])) {
    // 刪除數據
    $TR_ID = $_POST['TR_ID'];

    $stmt = $conn->prepare("DELETE FROM tr_admin WHERE TR_ID = ?");
    $stmt->bind_param('i', $TR_ID);

    if ($stmt->execute()) {
        $message = "刪除成功";
    } else {
        $message = "刪除失敗: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
header("Location: manage_departments.php?message=" . urlencode($message));
exit();
?>
