<?php
include 'db.php';
session_start();

//check to see if the user is logged in.
if(isset($_SESSION['email'])) {
    // User is logged in
    $email = $_SESSION['email'];
    $user_id = $_SESSION['user_id'];
}

// Fetch trails with KML URLs
$query = "SELECT TRAILID, TR_CNAME, TR_KML FROM trail WHERE TR_KML IS NOT NULL;";
$result = $conn->query($query);

$trails = [];
while ($row = $result->fetch_assoc()) {
    $trails[] = $row;
}

$conn->close();

// Return trail data as JSON
header('Content-Type: application/json');
echo json_encode($trails);
?>