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
    $location_name = $_POST['location_name'];
    $page_url = $_POST['page_url'];
    $opening_time = $_POST['opening_time'];
    $closing_time = $_POST['closing_time'] ?? null;
    $address = $_POST['address'] ?? null;
    $altitude_min = $_POST['altitude_min'] ?? null;
    $altitude_max = $_POST['altitude_max'] ?? null;
    $description = $_POST['description'] ?? null;
    $managing_department = $_POST['managing_department'] ?? null;
    $coordinates = $_POST['coordinates'] ?? null;
    $small_vehicle_allowed = isset($_POST['small_vehicle_allowed']) ? 1 : 0;
    $large_vehicle_allowed = isset($_POST['large_vehicle_allowed']) ? 1 : 0;
    $District_ID = $_POST['District_ID'];
    $Type_ID = $_POST['Type_ID'];
    $activity_intensity = $_POST['activity_intensity'] ?? null;

    $stmt = $conn->prepare("INSERT INTO location_info (location_name, page_url, opening_time, closing_time, address, altitude_min, altitude_max, description, managing_department, coordinates, small_vehicle_allowed, large_vehicle_allowed, District_ID, Type_ID, activity_intensity) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, PointFromText(?), ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssssiisssiiiii', $location_name, $page_url, $opening_time, $closing_time, $address, $altitude_min, $altitude_max, $description, $managing_department, $coordinates, $small_vehicle_allowed, $large_vehicle_allowed, $District_ID, $Type_ID, $activity_intensity);

    if ($stmt->execute()) {
        $message = "Record added successfully";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $location_name = $_POST['location_name'];
    $page_url = $_POST['page_url'];
    $opening_time = $_POST['opening_time'];
    $closing_time = $_POST['closing_time'] ?? null;
    $address = $_POST['address'] ?? null;
    $altitude_min = $_POST['altitude_min'] ?? null;
    $altitude_max = $_POST['altitude_max'] ?? null;
    $description = $_POST['description'] ?? null;
    $managing_department = $_POST['managing_department'] ?? null;
    $coordinates = $_POST['coordinates'] ?? null;
    $small_vehicle_allowed = isset($_POST['small_vehicle_allowed']) ? 1 : 0;
    $large_vehicle_allowed = isset($_POST['large_vehicle_allowed']) ? 1 : 0;
    $District_ID = $_POST['District_ID'];
    $Type_ID = $_POST['Type_ID'];
    $activity_intensity = $_POST['activity_intensity'] ?? null;

    $stmt = $conn->prepare("UPDATE location_info SET location_name=?, page_url=?, opening_time=?, closing_time=?, address=?, altitude_min=?, altitude_max=?, description=?, managing_department=?, coordinates=PointFromText(?), small_vehicle_allowed=?, large_vehicle_allowed=?, District_ID=?, Type_ID=?, activity_intensity=? WHERE id=?");
    $stmt->bind_param('sssssiisssiiiiii', $location_name, $page_url, $opening_time, $closing_time, $address, $altitude_min, $altitude_max, $description, $managing_department, $coordinates, $small_vehicle_allowed, $large_vehicle_allowed, $District_ID, $Type_ID, $activity_intensity, $id);

    if ($stmt->execute()) {
        $message = "Record updated successfully";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM location_info WHERE id=?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        $message = "Record deleted successfully";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
header("Location: manage_locations.php?message=".urlencode($message));
exit();
?>
