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
        #map { height: 600px; margin-top: 20px; }
        #info { margin-top: 20px; padding: 10px; border: 1px solid #ccc; }
    </style>
</head>

<body>
    <header>
        <h1>查詢結果</h1>
        <nav>
            <ul>
                <li><a href="index.php">首頁</a></li>
                <li><a href="leaflet.php">步道地圖</a></li>
                <li><a href="news.php">最新消息</a></li>
                <li><a href="weather.php">天氣預報</a></li>
                <li><a href="login.php">會員登入</a></li>
                
                <li>
                <form method="GET" action="results.php">
                    <label for="search">輸入景點關鍵字:</label>
                    <input type="text" id="search" name="search" placeholder="輸入景點名稱或描述" value="<?php echo htmlspecialchars($search_term, ENT_QUOTES); ?>">
                    <input type="submit" value="查詢">
                </form>
                <button onclick="window.location.href='index.php'">返回首頁</button>
                </li>
            </ul>
        </nav>
    </header>
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
                    <?php for ($i = 1; $i <= 5; $i++): ?>
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
                    foreach ($tour_options as $option):
                    ?>
                        <input type="checkbox" id="tour<?php echo $option; ?>" name="tour[]" value="<?php echo $option; ?>" <?php echo in_array($option, $selected_tours) ? 'checked' : ''; ?>>
                        <label for="tour<?php echo $option; ?>"><?php echo $option; ?></label>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="submit">篩選</button>
        </form>

        <?php if (count($locations) > 0): ?>
            <div id="map"></div>

            <?php
            if (empty($search_term)) {
                echo "<p>請輸入要搜尋的景點或步道名稱</p>";
            } else {
                // Prepare SQL query and bind parameters
                $sql_trail = "SELECT trail.tr_cname, trail.trailid, city.city, district.district, trail.tr_dif_class, trail.tr_length, trail.tr_tour 
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
                    if (in_array('少於半天', $selected_tours)) {
                        $tour_placeholders .= ', ?';
                        $selected_tours[] = '少於半天';
                        $sql_trail .= " AND (trail.tr_tour IN ($tour_placeholders) OR trail.tr_tour NOT IN ('半天', '一天', '一天以上'))";
                    } else {
                        $sql_trail .= " AND trail.tr_tour IN ($tour_placeholders)";
                    }
                    $bind_types += str_repeat('s', count($selected_tours));
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

                // Output results based on the checkbox state
                if ($location_first) {
                    // Output location_info results first
                    if (count($locations) > 0) {
                        echo "<h2>Location Info Results</h2><ul>";
                        foreach ($locations as $row) {
                            echo "<li>";
                            echo "<a href='details.php?id={$row['id']}'>{$row['location_name']}</a><br>";
                            echo "{$row['address']}";
                            echo "</li><br>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<h2>Location Info Results</h2>";
                        echo "沒有找到相關景點。";
                    }

                    // Output trail results
                    if ($result_trail->num_rows > 0) {
                        echo "<h2>Trail Results</h2><ul>";
                        while ($row = $result_trail->fetch_assoc()) {
                            echo "<li>";
                            echo "<a href='detailstrail.php?id={$row['trailid']}'>{$row['tr_cname']}</a><br>";
                            echo "{$row['city']} {$row['district']}<br>";
                            echo "長度: {$row['tr_length']}<br>";
                            echo "難度: {$row['tr_dif_class']}<br>";
                            echo "遊覽時間: {$row['tr_tour']}";
                            echo "</li><br>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<h2>Trail Results</h2>";
                        echo "沒有找到相關步道。";
                    }
                } else {
                    // Output trail results first
                    if ($result_trail->num_rows > 0) {
                        echo "<h2>Trail Results</h2><ul>";
                        while ($row = $result_trail->fetch_assoc()) {
                            echo "<li>";
                            echo "<a href='detailstrail.php?id={$row['trailid']}'>{$row['tr_cname']}</a><br>";
                            echo "{$row['city']} {$row['district']}<br>";
                            echo "長度: {$row['tr_length']}<br>";
                            echo "難度: {$row['tr_dif_class']}<br>";
                            echo "遊覽時間: {$row['tr_tour']}";
                            echo "</li><br>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<h2>Trail Results</h2>";
                        echo "沒有找到相關步道。";
                    }

                    // Output location_info results
                    if (count($locations) > 0) {
                        echo "<h2>Location Info Results</h2><ul>";
                        foreach ($locations as $row) {
                            echo "<li>";
                            echo "<a href='details.php?id={$row['id']}'>{$row['location_name']}</a><br>";
                            echo "{$row['address']}";
                            echo "</li><br>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<h2>Location Info Results</h2>";
                        echo "沒有找到相關景點。";
                    }
                }

                // Close statements
                $stmt_trail->close();
            }

            // Close the connection
            $conn->close();
            ?>

            <div id="info">將游標移到地圖上的標記點以查看詳細資訊</div>

            <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
            <script>
                // Initialize the map
                var map = L.map('map').setView([23.6978, 120.9605], 7);

                // Add OpenStreetMap layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                // Define styles for different colors
                var styles = {
                    1: { color: 'red', fillColor: 'red', fillOpacity: 0.5, radius: 8 },
                    2: { color: 'blue', fillColor: 'blue', fillOpacity: 0.5, radius: 8 },
                    3: { color: 'green', fillColor: 'green', fillOpacity: 0.5, radius: 8 },
                    4: { color: 'yellow', fillColor: 'yellow', fillOpacity: 0.5, radius: 8 },
                    5: { color: 'purple', fillColor: 'purple', fillOpacity: 0.5, radius: 8 },
                };

                // Location data
                var locations = <?php echo json_encode($locations); ?>;
                console.log(locations);

                // Info box
                var info = document.getElementById('info');

                // Add markers and event listeners
                locations.forEach(function(location) {
                    var style = styles[location.Type_ID] || styles[1]; // Default to style for Type_ID 1
                    var marker = L.circleMarker([location.latitude, location.longitude], style).addTo(map)
                        .bindPopup(location.location_name);
                    
                    var clicked = false; // Track whether the popup was clicked
                    
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

                    marker.on('mouseout', function() {
                    });
                    
                    map.on('click', function() {
                        if (isHovered) {
                            isHovered.closePopup(); // Close the last hovered marker's popup
                        }
                    });

                    marker.on('dblclick', function() {
                        window.location.href = 'details.php?id=' + location.id;
                    });
                });
            </script>
            
        <?php else: ?>
            <p>沒有找到相關景點。</p>
        <?php endif; ?>
    </main>
</body>
</html>
