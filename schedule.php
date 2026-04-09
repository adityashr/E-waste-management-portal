<?php
session_start();
include("config.php");

// Include PHPMailer
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$msg = "";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$user_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT email, name FROM users WHERE id='$user_id'"));
$user_email = $user_info['email'];
$user_name = $user_info['name'];

if (isset($_POST['request'])) {

    $waste_type = trim($_POST['waste_type']);
    $address = trim($_POST['address']);
    $date = trim($_POST['date']);

    if (empty($waste_type) || empty($address) || empty($date)) {
        $msg = "All fields are required!";
    } else {

        $stmt = $conn->prepare("INSERT INTO pickups (user_id, waste_type, address, pickup_date, status) VALUES (?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("isss", $user_id, $waste_type, $address, $date);

        if ($stmt->execute()) {

            try {

                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'aditya31182005@gmail.com';
                $mail->Password = 'papd cgtq njpv igtj';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('aditya31182005@gmail.com', 'E-Waste Portal');
                $mail->addAddress($user_email, $user_name);

                $mail->isHTML(true);
                $mail->Subject = 'Pickup Request Submitted';

                $mail->Body = "
                <h3>Hello {$user_name}</h3>
                <p>Your e-waste pickup request has been submitted.</p>

                <ul>
                <li>Waste Type : {$waste_type}</li>
                <li>Address : {$address}</li>
                <li>Pickup Date : {$date}</li>
                </ul>

                <p>♻ E-Waste Portal</p>
                ";

                $mail->send();

                $adminMail = new PHPMailer(true);
                $adminMail->isSMTP();
                $adminMail->Host = 'smtp.gmail.com';
                $adminMail->SMTPAuth = true;
                $adminMail->Username = 'aditya31182005@gmail.com';
                $adminMail->Password = 'papd cgtq njpv igtj';
                $adminMail->SMTPSecure = 'tls';
                $adminMail->Port = 587;

                $adminMail->setFrom('aditya31182005@gmail.com', 'E-Waste Portal');
                $adminMail->addAddress('aditya31182005@gmail.com', 'Admin');

                $adminMail->isHTML(true);
                $adminMail->Subject = 'New Pickup Request';

                $adminMail->Body = "
                <h3>New Pickup Request</h3>
                <p>User : {$user_name} ({$user_email})</p>

                <ul>
                <li>Waste Type : {$waste_type}</li>
                <li>Address : {$address}</li>
                <li>Pickup Date : {$date}</li>
                </ul>
                ";

                $adminMail->send();

                $_SESSION['msg'] = "Pickup Request Submitted Successfully!";
                header("Location: mypickups.php");
                exit();

            } catch (Exception $e) {
                $msg = "Request submitted but email failed.";
            }

        } else {
            $msg = "Database Error!";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Schedule Pickup</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        /* ===== BODY ===== */
        body {
            margin: 0;
            font-family: 'Segoe UI';
            background: black;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* ===== ANIMATED BG ===== */
        .animated-bg {
            position: fixed;
            width: 100%;
            height: 50vh;
            background: linear-gradient(#000, #001100);
            z-index: -1;
        }

        /* PARTICLES */
        .particle {
            position: absolute;
            background: rgba(40, 172, 36, 0.7);
            border-radius: 50%;
            animation: float 10s infinite ease-in-out;
        }

        /* FLOAT */
        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-150px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        /* ===== HEADING ===== */
        .page-heading {
            text-align: center;
            color: white;
            margin-top: 30px;
        }

        /* ===== CONTAINER ===== */
        .container {
            width: 500px;
            max-width: 95%;
            background: #fff;
            padding: 25px;
            border-radius: 20px;
            margin-top: 20px;
            box-sizing: border-box;
        }

        /* ===== CARDS ===== */
        .card-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .waste-card {
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
        }

        .waste-card.active {
            background: #28a745;
            color: white;
        }

        /* ===== PHONE ===== */
        .phone-input {
            display: flex;
            align-items: center;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 10px;
        }

        .phone-input input {
            border: none;
            outline: none;
            width: 100%;
        }

        /* ===== ADDRESS ===== */
        .address-box {
            position: relative;
        }

        .address-box textarea {
            width: 100%;
            height: 70px;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #ccc;
            resize: none;
            box-sizing: border-box;
        }

        .location-btn {
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
        }

        /* ===== MAP ===== */
        #map {
            width: 100%;
            height: 250px;
            margin-top: 10px;
            border-radius: 10px;
            display: none;
        }

        /* ===== DATE ===== */
        input[type="date"] {
            width: 95%;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #ccc;
        }

        /* ===== BUTTON ===== */
        .btn {
            width: 100%;
            padding: 12px;
            background: #28a745;
            color: white;
            border: none;
            margin-top: 20px;
            border-radius: 10px;
        }

        /* ===== MSG ===== */
        .msg {
            text-align: center;
            margin-top: 10px;
            color: green;
        }
    </style>
</head>

<body>

    <div class="animated-bg"></div>

    <div class="page-heading">
        <h1>E-Waste Management Portal</h1>
        <p>Schedule your pickup easily</p>
    </div>

    <div class="container">

        <h2>Schedule Pickup</h2>

        <form method="POST">

            <input type="hidden" name="waste_type" id="waste_type">

            <label>Waste Type</label>
            <div class="card-container">
                <div class="waste-card active" onclick="selectType(this,'Mobile')">📱 Mobile</div>
                <div class="waste-card" onclick="selectType(this,'Laptop')">💻 Laptop</div>
                <div class="waste-card" onclick="selectType(this,'Battery')">🔋 Battery</div>
                <div class="waste-card" onclick="selectType(this,'CPU)">♻ CPU</div>
                <div class="waste-card" onclick="selectType(this,'Mixed')">🗑 Mixed</div>
                <div class="waste-card" onclick="selectType(this,'Others')">📦 Others</div>
            </div>


            <label>Address</label>
            <div class="address-box">
                <textarea name="address" id="address" placeholder="Enter address..." required></textarea>
                <span class="location-btn" onclick="toggleMap()">📍</span>
            </div>

            <div id="map"></div>

            <label> Pickup Date</label>
            <input type="date" name="date" required>

            <button class="btn" name="request">Submit</button>

        </form>

        <?php if (!empty($msg))
            echo "<div class='msg'>$msg</div>"; ?>

    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>

        /* ===== CARD SELECT ===== */
        function selectType(el, type) {
            document.getElementById("waste_type").value = type;
            document.querySelectorAll(".waste-card").forEach(c => c.classList.remove("active"));
            el.classList.add("active");
        }

        document.getElementById("waste_type").value = "Mobile";

        /* ===== MAP ===== */
        let map, marker, visible = false;

        function toggleMap() {
            visible = !visible;
            document.getElementById("map").style.display = visible ? "block" : "none";

            if (visible && !map) {

                map = L.map('map').setView([28.6139, 77.2090], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

                setTimeout(() => map.invalidateSize(), 200);

                map.on('click', function (e) {

                    if (marker) map.removeLayer(marker);
                    marker = L.marker(e.latlng).addTo(map);

                    fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
                        .then(r => r.json())
                        .then(d => {
                            document.getElementById("address").value = d.display_name;
                        });

                });
            }
        }

        /* ===== LIVE SEARCH FIX ===== */
        let timeout;

        document.getElementById("address").addEventListener("keyup", function () {

            clearTimeout(timeout);

            let query = this.value.trim();

            if (query.length < 3) return;

            timeout = setTimeout(() => {

                fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(query)}&limit=1`)
                    .then(r => r.json())
                    .then(d => {

                        if (d.length > 0) {

                            let lat = parseFloat(d[0].lat);
                            let lon = parseFloat(d[0].lon);

                            if (!map) toggleMap();

                            map.setView([lat, lon], 16);

                            if (marker) map.removeLayer(marker);
                            marker = L.marker([lat, lon]).addTo(map);

                        }

                    });

            }, 600);

        });

        /* ===== PARTICLES ===== */
        const bg = document.querySelector(".animated-bg");

        for (let i = 0; i < 30; i++) {
            let p = document.createElement("div");
            p.className = "particle";

            let size = Math.random() * 8 + 4;
            p.style.width = size + "px";
            p.style.height = size + "px";

            p.style.left = Math.random() * 100 + "vw";
            p.style.top = Math.random() * 50 + "vh";

            p.style.animationDuration = (Math.random() * 10 + 5) + "s";

            bg.appendChild(p);
        }

    </script>

</body>

</html>