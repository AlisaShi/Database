<?php
include 'db.php';

session_start();

//check to see if the user is logged in.
if(isset($_SESSION['email'])) {
    // User is logged in
    $email = $_SESSION['email'];
    $user_id = $_SESSION['user_id'];
}

$message = '';

if (isset($_POST['add'])) {
    $TRAILID = $_POST['TRAILID'];
    $TR_CNAME = $_POST['TR_CNAME'];
    $TR_ID = $_POST['TR_ID'];
    $City_ID = $_POST['City_ID'];
    $District_ID = $_POST['District_ID'];
    $TR_LENGTH = $_POST['TR_LENGTH'] ?? null;
    $TR_ALT = $_POST['TR_ALT'] ?? null;
    $TR_ALT_LOW = $_POST['TR_ALT_LOW'] ?? null;
    $TR_permit_stop = isset($_POST['TR_permit_stop']) ? 1 : 0;
    $TR_PAVE = $_POST['TR_PAVE'] ?? null;
    $TR_DIF_CLASS = $_POST['TR_DIF_CLASS'] ?? null;
    $TR_TOUR = $_POST['TR_TOUR'] ?? null;
    $TR_BEST_SEASON = $_POST['TR_BEST_SEASON'] ?? null;

    $stmt = $conn->prepare("INSERT INTO trail (TRAILID, TR_CNAME, TR_ID, City_ID, District_ID, TR_LENGTH, TR_ALT, TR_ALT_LOW, TR_permit_stop, TR_PAVE, TR_DIF_CLASS, TR_TOUR, TR_BEST_SEASON) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssisssdidisis', $TRAILID, $TR_CNAME, $TR_ID, $City_ID, $District_ID, $TR_LENGTH, $TR_ALT, $TR_ALT_LOW, $TR_permit_stop, $TR_PAVE, $TR_DIF_CLASS, $TR_TOUR, $TR_BEST_SEASON);

    if ($stmt->execute()) {
        $message = "Record added successfully";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

if (isset($_POST['update'])) {
    $TRAILID = $_POST['TRAILID'];
    $TR_CNAME = $_POST['TR_CNAME'];
    $TR_ID = $_POST['TR_ID'];
    $City_ID = $_POST['City_ID'];
    $District_ID = $_POST['District_ID'];
    $TR_LENGTH = $_POST['TR_LENGTH'] ?? null;
    $TR_ALT = $_POST['TR_ALT'] ?? null;
    $TR_ALT_LOW = $_POST['TR_ALT_LOW'] ?? null;
    $TR_permit_stop = isset($_POST['TR_permit_stop']) ? 1 : 0;
    $TR_PAVE = $_POST['TR_PAVE'] ?? null;
    $TR_DIF_CLASS = $_POST['TR_DIF_CLASS'] ?? null;
    $TR_TOUR = $_POST['TR_TOUR'] ?? null;
    $TR_BEST_SEASON = $_POST['TR_BEST_SEASON'] ?? null;

    $stmt = $conn->prepare("UPDATE trail SET TR_CNAME=?, TR_ID=?, City_ID=?, District_ID=?, TR_LENGTH=?, TR_ALT=?, TR_ALT_LOW=?, TR_permit_stop=?, TR_PAVE=?, TR_DIF_CLASS=?, TR_TOUR=?, TR_BEST_SEASON=? WHERE TRAILID=?");
    $stmt->bind_param('sisssdidissis', $TR_CNAME, $TR_ID, $City_ID, $District_ID, $TR_LENGTH, $TR_ALT, $TR_ALT_LOW, $TR_permit_stop, $TR_PAVE, $TR_DIF_CLASS, $TR_TOUR, $TR_BEST_SEASON, $TRAILID);

    if ($stmt->execute()) {
        $message = "Record updated successfully";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

if (isset($_POST['delete'])) {
    $TRAILID = $_POST['TRAILID'];

    $stmt = $conn->prepare("DELETE FROM trail WHERE TRAILID=?");
    $stmt->bind_param('s', $TRAILID);

    if ($stmt->execute()) {
        $message = "Record deleted successfully";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
header("Location: manage_trails.php?message=".urlencode($message));
exit();
?>
