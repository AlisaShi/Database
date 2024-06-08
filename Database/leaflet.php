<!DOCTYPE html>
<html>
<head>
  <title>步道地圖</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet-omnivore@0.3.4/leaflet-omnivore.min.js"></script>
  <style>
    body {
      display: flex;
      flex-direction: column;
      height: 100vh;
      margin: 0;
    }

    header {
      background-color: #333;
      color: white;
      padding: 10px;
      text-align: center;
    }

    nav ul {
      list-style-type: none;
      padding: 0;
      margin: 0;
      display: flex;
      justify-content: center;
    }

    nav ul li {
      margin: 0 10px;
    }

    nav ul li a {
      color: white;
      text-decoration: none;
      padding: 5px 10px;
      display: block;
    }

    nav ul li a:hover {
      background-color: #555;
    }

    #map {
      flex-grow: 1;
    }
  </style>
</head>
<body>
<header>
    <h1>步道地圖</h1>
    <nav>
        <ul>
        <li><a href="admin.php">首頁</a></li>
        <li><a href="manage_trails.php">Manage Trails</a></li>
        <li><a href="manage_locations.php">Manage Locations</a></li>
        <li><a href="manage_users.php">Manage Users</a></li>
        <li><a href="manage_departments.php">Manage Departments (假連結)</a></li>
        <!-- 你可以在这里添加更多的管理页面链接 -->
    </ul>
    </nav>
</header>
  <div id="map"></div>
  <script>
    // 初始化地图并设置视图为台湾的中心和适当的缩放级别
    var map = L.map('map').setView([23.5, 121], 7);

    // 添加 OpenStreetMap 图层
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // 加载KML文件并添加到地图
    omnivore.kml('http://localhost/Database/林道分布圖1131.kml')
      .on('ready', function() {
        var layer = this;

        // 调整视图以适应KML文件中的所有线条
        map.fitBounds(layer.getBounds());

        // 为每个图层添加点击事件
        layer.eachLayer(function(layer) {
          if (layer.feature && layer.feature.properties) {
            var name = layer.feature.properties.name || "未知";
            var description = layer.feature.properties.description || "";
            var popupContent = "<strong>" + name + "</strong><br>" + description;
            layer.bindPopup(popupContent);
          }
        });
      })
      .addTo(map);
  </script>
</body>
</html>
