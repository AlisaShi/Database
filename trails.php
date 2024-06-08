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
                <li><a href="leaflet.php">步道地圖</a></li>
                <li><a href="news.php">最新消息</a></li>
                <li><a href="weather.php">天氣預報</a></li>
                <li><a href="login.php">會員登入</a></li>
                
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

    // Fetch trail data from the server
    fetch('get_kml_urls.php')
      .then(response => response.json())
      .then(trails => {
        trails.forEach(trail => {
          omnivore.kml(trail.TR_KML)
            .on('ready', function() {
              var layer = this;

              // Adjust the view to fit all lines in the KML file
              map.fitBounds(layer.getBounds());

              // Add click event to each layer
              layer.eachLayer(function(layer) {
                var popupContent = `<strong>${trail.TR_CNAME}</strong>`;
                layer.bindPopup(popupContent);
              });
            })
            .addTo(map);
        });
      })
      .catch(error => {
        console.error('Error fetching trail data:', error);
      });
  </script>
</body>
</html>
