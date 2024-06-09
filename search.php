<?php 

session_start();

//check to see if the user is logged in.
if(isset($_SESSION['email'])) {
    // User is logged in
    $email = $_SESSION['email'];
    $user_id = $_SESSION['user_id'];
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>景點查詢</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <header>
            <h1>景點查詢</h1>
        </header>
        <main>
            <form method="GET" action="results.php">
                <label for="search">輸入景點關鍵字:</label>
                <input type="text" id="search" name="search" placeholder="輸入景點名稱或描述">
                <input type="submit" value="查詢">
            </form>
        </main>
    </body>
</html>