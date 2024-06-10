<?php
include('header.php');
?>
<?php
include 'db.php';

// Get user input
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// Get selected difficulties and tours
$selected_difficulties = isset($_GET['difficulty']) ? $_GET['difficulty'] : [];
$selected_tours = isset($_GET['tour']) ? $_GET['tour'] : [];

// Get the location first preference
$location_first = isset($_GET['location_first']) ? true : false;

// Query location_info table
$sql_location = "SELECT Type_ID, id, location_name, ST_X(coordinates) as longitude, ST_Y(coordinates) as latitude, address 
                 FROM location_info 
                 WHERE address LIKE '%$search_term%' OR description LIKE '%$search_term%'";
$result_location = $conn->query($sql_location);

// Check for errors in location_info query
if ($conn->error) {
    die("Location query failed: " . $conn->error);
}

// Convert results to array
$locations = [];
while ($row = $result_location->fetch_assoc()) {
    $locations[] = $row;
}

// Prepare SQL query for trails
$sql_trail = "SELECT trail.tr_cname, trail.trailid, city.city, district.district, trail.tr_dif_class, trail.tr_length, trail.tr_tour, trail.tr_kml, trail.tr_id
              FROM trail 
              LEFT JOIN city ON trail.city_id = city.city_id 
              LEFT JOIN district ON trail.district_id = district.district_id 
              WHERE (trail.tr_cname LIKE ? OR city.city LIKE ? OR district.district LIKE ?)";

// Append difficulty and tour filters to SQL query if selected
$bind_types = 'sss'; // Initial types for LIKE parameters
$bind_values = ["%$search_term%", "%$search_term%", "%$search_term%"];

if (!empty($selected_difficulties)) {
    $difficulty_placeholders = implode(',', array_fill(0, count($selected_difficulties), '?'));
    $sql_trail .= " AND trail.tr_dif_class IN ($difficulty_placeholders)";
    $bind_types .= str_repeat('i', count($selected_difficulties));
    $bind_values = array_merge($bind_values, $selected_difficulties);
}

if (!empty($selected_tours)) {
    $tour_placeholders = implode(',', array_fill(0, count($selected_tours), '?'));
    $sql_trail .= " AND trail.tr_tour IN ($tour_placeholders)";
    $bind_types .= str_repeat('s', count($selected_tours));
    $bind_values = array_merge($bind_values, $selected_tours);
}

$stmt_trail = $conn->prepare($sql_trail);
$stmt_trail->bind_param($bind_types, ...array_values($bind_values));
$stmt_trail->execute();
$result_trail = $stmt_trail->get_result();

// Check for errors in trail query
if ($conn->error) {
    die("Trail query failed: " . $conn->error);
}

// Convert trail results to array
$trails = [];
while ($row = $result_trail->fetch_assoc()) {
    $trails[] = $row;
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查詢結果</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        #map {
            height: 600px;
            margin-top: 20px;
        }

        #info {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <h1>查詢結果</h1>
    
    <main>
        <form method="GET" action="results.php" id="search-form">
            <input type="hidden" id="search" name="search" value="<?php echo htmlspecialchars($search_term, ENT_QUOTES); ?>">
            <div>
                <label for="location_first">地點優先:</label>
                <input type="checkbox" id="location_first" name="location_first" <?php echo $location_first ? 'checked' : ''; ?>>
            </div>
            <div>
                <label>選擇難度:</label>
                <div>
                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <input type="checkbox" id="difficulty<?php echo $i; ?>" name="difficulty[]" value="<?php echo $i; ?>" <?php echo in_array($i, $selected_difficulties) ? 'checked' : ''; ?>>
                        <label for="difficulty<?php echo $i; ?>"><?php echo $i; ?></label>
                    <?php endfor; ?>
                </div>
            </div>
            <div>
                <label>選擇遊覽時間:</label>
                <div>
                    <?php
                    $tour_options = ['半天', '一天', '一天以上', '少於半天'];
                    foreach ($tour_options as $option) :
                    ?>
                        <input type="checkbox" id="tour<?php echo $option; ?>" name="tour[]" value="<?php echo $option; ?>" <?php echo in_array($option, $selected_tours) ? 'checked' : ''; ?>>
                        <label for="tour<?php echo $option; ?>"><?php echo $option; ?></label>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="submit">篩選</button>
        </form>

        <?php if (count($locations) > 0 || count($trails) > 0) : ?>
            <div id="map"></div>

            <?php if (empty($search_term)) : ?>
                <p>請輸入要搜尋的景點或步道名稱</p>
            <?php else : ?>
                <?php if ($location_first) : ?>
                    <!-- Output location_info results first -->
                    <?php if (count($locations) > 0) : ?>
                        <h2>Location Info Results</h2>
                        <ul>
                            <?php foreach ($locations as $row) : ?>
                                <li>
                                    <a href='details.php?id=<?php echo $row['id']; ?>'><?php echo $row['location_name']; ?></a><br>
                                    <?php echo $row['address']; ?>
                                </li><br>
                            <?php endforeach; ?>
                        </ul>
                    <?php else : ?>
                        <h2>Location Info Results</h2>
                        <p>沒有找到相關景點。</p>
                    <?php endif; ?>

                    <!-- Output trail results -->
                    <?php if (count($trails) > 0) : ?>
                        <h2>Trail Results</h2>
                        <ul>
                            <?php foreach ($trails as $row) : ?>
                                <li>
                                    <a href='detailstrail.php?id=<?php echo $row['trailid']; ?>'><?php echo $row['tr_cname']; ?></a><br>
                                    <?php echo "{$row['city']} {$row['district']}<br>長度: {$row['tr_length']}<br>難度: {$row['tr_dif_class']}<br>遊覽時間: {$row['tr_tour']}"; ?>
                                </li><br>
                            <?php endforeach; ?>
                        </ul>
                    <?php else : ?>
                        <h2>Trail Results</h2>
                        <p>沒有找到相關步道。</p>
                    <?php endif; ?>
                <?php else : ?>
                    <!-- Output trail results first -->
                    <?php if (count($trails) > 0) : ?>
                        <h2>Trail Results</h2>
                        <ul>
                            <?php foreach ($trails as $row) : ?>
                                <li>
                                    <a href='detailstrail.php?id=<?php echo $row['trailid']; ?>'><?php echo $row['tr_cname']; ?></a><br>
                                    <?php echo "{$row['city']} {$row['district']}<br>長度: {$row['tr_length']}<br>難度: {$row['tr_dif_class']}<br>遊覽時間: {$row['tr_tour']}"; ?>
                                </li><br>
                            <?php endforeach; ?>
                        </ul>
                    <?php else : ?>
                        <h2>Trail Results</h2>
                        <p>沒有找到相關步道。</p>
                    <?php endif; ?>

                    <!-- Output location_info results -->
                    <?php if (count($locations) > 0) : ?>
                        <h2>Location Info Results</h2>
                        <ul>
                            <?php foreach ($locations as $row) : ?>
                                <li>
                                    <a href='details.php?id=<?php echo $row['id']; ?>'><?php echo $row['location_name']; ?></a><br>
                                    <?php echo $row['address']; ?>
                                </li><br>
                            <?php endforeach; ?>
                        </ul>
                    <?php else : ?>
                        <h2>Location Info Results</h2>
                        <p>沒有找到相關景點。</p>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>

            <div id="info">將游標移到地圖上的標記點以查看詳細資訊</div>

            <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
            <script src="https://unpkg.com/leaflet-omnivore@0.3.4/leaflet-omnivore.min.js"></script>
            <script>
                // Initialize the map
                var map = L.map('map').setView([23.6978, 120.9605], 7);

                // Add OpenStreetMap layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                // Define styles for different colors
                var styles = {
                    1: {
                        color: 'red',
                        fillColor: 'red',
                        fillOpacity: 0.5,
                        radius: 8
                    },
                    2: {
                        color: 'blue',
                        fillColor: 'blue',
                        fillOpacity: 0.5,
                        radius: 8
                    },
                    3: {
                        color: 'green',
                        fillColor: 'green',
                        fillOpacity: 0.5,
                        radius: 8
                    },
                    4: {
                        color: 'yellow',
                        fillColor: 'yellow',
                        fillOpacity: 0.5,
                        radius: 8
                    },
                    5: {
                        color: 'purple',
                        fillColor: 'purple',
                        fillOpacity: 0.5,
                        radius: 8
                    },
                };

                // Location data
                var locations = <?php echo json_encode($locations); ?>;
                console.log(locations);

                // Info box
                var info = document.getElementById('info');

                // Add markers and event listeners
                locations.forEach(function(location) {
                    var style = styles[location.Type_ID] || styles[1]; // Default to style for Type_ID 1
                    var popupContent = `<a href="details.php?id=${location.id}" target="_blank">${location.location_name}</a>`;
                    var marker = L.circleMarker([location.latitude, location.longitude], style).addTo(map)
                        .bindPopup(popupContent);

                    var clicked = false; // Track whether the popup was clicked
                    var isHovered = false; // Track whether the marker is hovered

                    marker.on('click', function() {
                        clicked = true; // Set clicked to true when the marker is clicked
                    });

                    marker.on('mouseover', function() {
                        info.innerHTML =
                            `<b>${location.location_name}</b>
                            <br>${location.address}`;
                        this.openPopup(); // Open popup on mouseover
                        if (isHovered && isHovered !== this) {
                            isHovered.closePopup();
                        }
                        isHovered = this;
                    });

                    marker.on('mouseout', function() {});

                    map.on('click', function() {
                        if (isHovered) {
                            isHovered.closePopup(); // Close the last hovered marker's popup
                        }
                    });

                    marker.on('dblclick', function() {
                        window.location.href = 'details.php?id=' + location.id;
                    });
                });

                // Add KML layers for trails
                var trails = <?php echo json_encode($trails); ?>;
                trails.forEach(function(trail) {
                    omnivore.kml(trail.tr_kml)
                        .on('ready', function() {
                            var layer = this;

                            // Adjust the view to fit all lines in the KML file
                            map.fitBounds(layer.getBounds());

                            // Add mouse events for each layer
                            layer.eachLayer(function(layer) {
                                if (layer.feature && layer.feature.properties) {
                                    var name = trail.tr_cname;
                                    var description = layer.feature.properties.description || "";
                                    var popupContent = `<a href="detailstrail.php?id=${trail.trailid}" target="_blank">${trail.tr_cname}</a>`;
                                    layer.bindPopup(popupContent);

                                    // Add mouseover event
                                    layer.on('mouseover', function(e) {
                                        info.innerHTML =
                                            `<b>${trail.tr_cname}</b>`;
                                        this.openPopup();
                                    });

                                    // Add mouseout event to close popup
                                    layer.on('mouseout', function() {

                                    });

                                    layer.on('click', function() {
                                        if (isHovered) {
                                            isHovered.closePopup(); // Close popup on map click
                                        }
                                        isHovered = false;
                                    });
                                }
                            });
                        })
                        .addTo(map);
                });
            </script>
        <?php else : ?>
            <p>沒有找到相關景點或步道。</p>
        <?php endif; ?>
    </main>
</body>

</html>