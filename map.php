<?php
include("config.php");

// Fetch all centers
$sql = "SELECT * FROM centers";
$result = mysqli_query($conn, $sql);

$centers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $centers[] = $row;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Nearest E-Waste Centers</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <!-- MarkerCluster CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #26ac24 50%, #18191a 50%);
            background-size: 200% 200%;
            animation: bgDiagonal 15s linear infinite alternate;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            color: white;
        }

        @keyframes bgDiagonal {
            0% {
                background-position: 0% 0%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 100%;
            }
        }

        h2 {
            margin-top: 40px;
            margin-bottom: 10px;
            font-size: 36px;
            color: white;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
            animation: fadeSlideDown 1.2s ease forwards;
        }

        @keyframes fadeSlideDown {
            0% {
                opacity: 0;
                transform: translateY(-30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #search {
            margin: 10px 0 20px 0;
            padding: 10px;
            border-radius: 10px;
            width: 90%;
            max-width: 400px;
            font-size: 16px;
            border: none;
            outline: none;
        }

        #map {
            height: 500px;
            width: 90%;
            max-width: 1000px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            animation: fadeInMap 1s ease forwards;
        }

        @keyframes fadeInMap {
            0% {
                opacity: 0;
                transform: scale(0.95);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .back-btn {
            display: inline-block;
            margin: 25px 0 50px 0;
            padding: 12px 25px;
            background: #28a745;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 10px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            transition: 0.3s;
            animation: fadeSlideUp 1s ease forwards;
        }

        .back-btn:hover {
            background: #20c997;
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
        }

        @keyframes fadeSlideUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .leaflet-popup-content-wrapper {
            border-radius: 15px;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            font-weight: bold;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .leaflet-popup-tip {
            background: #28a745;
        }

        .leaflet-marker-icon {
            transition: transform 0.3s;
        }

        .leaflet-marker-icon:hover {
            transform: scale(1.2);
            z-index: 1000;
        }

        @media(max-width:600px) {
            #map {
                height: 400px;
            }

            h2 {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>

    <h2>Nearest E-Waste Centers</h2>
    <input type="text" id="search" placeholder="Search E-Waste Center..." />
    <div id="map"></div>
    <a href="dashboard.php" class="back-btn">⬅ Back to Dashboard</a>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <!-- MarkerCluster JS -->
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>

    <script>

        var map = L.map('map').setView([28.6139, 77.2090], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var centers = <?php echo json_encode($centers); ?>;

        var markersCluster = L.markerClusterGroup();

        var greenIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/854/854878.png',
            iconSize: [35, 45],
            iconAnchor: [17, 45],
            popupAnchor: [0, -40]
        });

        var markers = [];

        centers.forEach(function (center) {

            if (center.latitude && center.longitude) {

                var marker = L.marker([center.latitude, center.longitude], { icon: greenIcon })
                    .bindPopup(`
        <b>${center.name}</b><br>
        ${center.address || ''}<br>
        ${center.phone ? '📞 <a href="tel:' + center.phone + '">' + center.phone + '</a><br>' : ''}
        ${center.hours ? '🕒 ' + center.hours : ''}
        `);

                // CLICK PAR ZOOM (NEW FEATURE)
                marker.on('click', function () {
                    map.setView([center.latitude, center.longitude], 15);
                });

                marker.addTo(markersCluster);
                markers.push(marker);
            }

        });

        map.addLayer(markersCluster);

        if (markers.length > 0) {
            var group = L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.2));
        }

        map.locate({ setView: false, maxZoom: 15 });

        map.on('locationfound', function (e) {
            L.circleMarker(e.latlng, { radius: 8, color: '#ff6b6b', fillOpacity: 0.9 })
                .bindPopup("You are here")
                .addTo(map)
                .openPopup();
        });

        document.getElementById('search').addEventListener('input', function (e) {

            var query = e.target.value.toLowerCase();

            markers.forEach(function (marker, i) {

                var name = centers[i].name.toLowerCase();

                if (name.includes(query)) {
                    markersCluster.addLayer(marker);
                } else {
                    markersCluster.removeLayer(marker);
                }

            });

        });

    </script>

</body>

</html>