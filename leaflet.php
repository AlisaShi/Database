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
                <li><a href="index.php">首頁</a></li>
                <li><a href="trails.php">步道地圖</a></li>
                <li><a href="leaflet.php">林道地圖</a></li>
                <li><a href="news.php">最新消息</a></li>
                <li><a href="weather.php">天氣預報</a></li>
                <li><a href="login.php">會員登入</a></li>

                <li>
                    <form method="GET" action="results.php">
                        <input type="text" id="search" name="search" placeholder="輸入景點名稱或描述">
                        <input type="submit" value="查詢">
                    </form>
                </li>
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
