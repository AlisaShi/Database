<?php
include 'db.php';

$id = $_POST['id'];
$location_name = $_POST['location_name'];
$page_url = $_POST['page_url'];
$opening_time = $_POST['opening_time'];
$closing_time = $_POST['closing_time'];
$address = $_POST['address'];
$altitude_min = $_POST['altitude_min'];
$altitude_max = $_POST['altitude_max'];
$description = $_POST['description'];
$managing_department = $_POST['managing_department'];
$coordinates = $_POST['coordinates'];
$small_vehicle_allowed = isset($_POST['small_vehicle_allowed']) ? 1 : 0;
$large_vehicle_allowed = isset($_POST['large_vehicle_allowed']) ? 1 : 0;
$District_ID = $_POST['District_ID'];
$Type_ID = $_POST['Type_ID'];
$activity_intensity = $_POST['activity_intensity'];

$sql = "INSERT INTO location_info SET location_name='$location_name', page_url='$page_url', opening_time='$opening_time', closing_time='$closing_time', address='$address', altitude_min='$altitude_min', altitude_max='$altitude_max', description='$description', managing_department='$managing_department', coordinates=PointFromText('POINT($coordinates)'), small_vehicle_allowed='$small_vehicle_allowed', large_vehicle_allowed='$large_vehicle_allowed', District_ID='$District_ID', Type_ID='$Type_ID', activity_intensity='$activity_intensity' WHERE id='$id'";

if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
header("Location: manage_locations.php");
exit();
?>
