<?php
include('header.php');
?>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>

<body>
    <form action="authenticate.php" method="POST">
        <label for="User_email">Email:</label>
        <input type="email" name="User_email" id="User_email" required>
        <br>
        <label for="User_passwd">Password:</label>
        <input type="password" name="User_passwd" id="User_passwd" required>
        <br>
        <button type="submit">Login</button>
    </form>
</body>

</html>