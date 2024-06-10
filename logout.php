<?php
session_start();
session_destroy(); // 销毁会话数据
header("Location: index.php"); // 重定向到主页
exit();
?>
