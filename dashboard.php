<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* Pickup Stats */
$total = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) as count FROM pickups WHERE user_id='$user_id'"
))['count'];

$pending = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) as count FROM pickups WHERE user_id='$user_id' AND status='Pending'"
))['count'];

$completed = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) as count FROM pickups WHERE user_id='$user_id' AND status='Completed'"
))['count'];

/* Recent Pickups */
$recent = mysqli_query(
    $conn,
    "SELECT * FROM pickups WHERE user_id='$user_id' ORDER BY id DESC LIMIT 5"
);

/* Last Completed Pickup for Review */
$review_pickup = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT id FROM pickups 
WHERE user_id='$user_id' AND status='Completed'
ORDER BY id DESC LIMIT 1"
));
?>

<!DOCTYPE html>
<html>

<head>
    <title>User Dashboard</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        /* BODY */
        body {
            background: linear-gradient(135deg, #26ac24 10%, #f4f6f9 100%);
            animation: bgAnimation 10s ease-in-out infinite alternate;
        }

        /* Header */
        .header {
            background: #28a745;
            padding: 25px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            border-radius: 0 0 25px 25px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            transform: translateY(-20px);
            animation: slideDown 1s forwards;
        }

        .logo {
            font-size: 26px;
            font-weight: bold;
        }

        .welcome {
            font-size: 18px;
        }

        /* Dashboard Container */
        .dashboard {
            padding: 30px 40px;
            transform: translateY(-15px);
            animation: fadeUp 1s forwards;
        }

        /* Stats */
        .stats {
            display: flex;
            gap: 25px;
            margin-bottom: 40px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .stat-card {
            flex: 1;
            min-width: 220px;
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transition: 0.4s;
            position: relative;
            top: -10px;
        }

        .stat-card:hover {
            transform: translateY(-15px) scale(1.05);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .stat-card h3 {
            font-size: 34px;
            color: #28a745;
            transition: 0.3s;
        }

        .stat-card p {
            color: #555;
            margin-top: 5px;
        }

        /* Actions */
        .actions {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            margin-bottom: 40px;
            justify-content: center;
            align-items: stretch;
        }

        .action-card {
            width: 250px;
            min-height: 180px;
            background: white;
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            transition: 0.4s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .action-card:hover {
            transform: translateY(-12px) scale(1.03);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.2);
        }

        .action-card a {
            display: inline-block;
            padding: 12px 25px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            margin-top: 15px;
            transition: 0.3s;
        }

        .action-card a:hover {
            background: #218838;
            transform: scale(1.05);
        }

        /* Center Card */
        .view-centers {
            animation: popIn 0.7s ease forwards;
        }

        .view-btn {
            background: linear-gradient(45deg, #28a745, #20c997);
            font-weight: bold;
        }

        .view-btn:hover {
            background: linear-gradient(45deg, #20c997, #28a745);
        }

        /* Buttons */
        .pickup-btn {
            background: #007bff !important;
        }

        .logout-btn {
            background: #dc3545 !important;
        }

        .review-btn {
            background: #6f42c1 !important;
        }

        /* Table */
        .table-box {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            transform: translateY(-10px);
            transition: 0.4s;
        }

        .table-box:hover {
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        th {
            background: #28a745;
            color: white;
        }

        .track-btn {
            padding: 8px 14px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }

        .track-btn:hover {
            background: #0069d9;
            transform: scale(1.05);
        }

        /* Animations */
        @keyframes slideDown {
            0% {
                transform: translateY(-50px);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes bgAnimation {
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

        @keyframes popIn {
            0% {
                opacity: 0;
                transform: scale(0.8) translateY(20px);
            }

            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
    </style>

</head>

<body>

    <div class="header">
        <div class="logo">♻ E-Waste Portal</div>
        <div class="welcome">
            Welcome <?php echo $_SESSION['user_name']; ?> 👋
        </div>
    </div>

    <div class="dashboard">

        <!-- Stats -->
        <div class="stats">
            <div class="stat-card">
                <h3><?php echo $total; ?></h3>
                <p>Total Pickups</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $pending; ?></h3>
                <p>Pending</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $completed; ?></h3>
                <p>Completed</p>
            </div>
        </div>

        <!-- Actions -->
        <div class="actions">
            <div class="action-card">
                <h3>Schedule Pickup</h3>
                <p>Book pickup for e-waste</p>
                <a href="schedule.php">Schedule</a>
            </div>
            <div class="action-card">
                <h3>My Pickups</h3>
                <p>View your pickup requests</p>
                <a href="mypickups.php" class="pickup-btn">View</a>
            </div>

            <!-- VIEW CENTERS CARD -->
            <div class="action-card view-centers">
                <h3>View Centers</h3>
                <p>See all nearby e-waste collection centers</p>
                <a href="map.php" class="view-btn">View Map</a>
            </div>

            <div class="action-card">
                <h3>Reviews</h3>
                <p>Give review for completed pickup</p>
                <?php if ($review_pickup) { ?>
                    <a href="review.php?pickup_id=<?php echo $review_pickup['id']; ?>" class="review-btn">
                        Give Review
                    </a>
                <?php } else { ?>
                    <a href="#" class="review-btn" onclick="alert('No completed pickup yet');">
                        Give Review
                    </a>
                <?php } ?>
            </div>
            <div class="action-card">
                <h3>Logout</h3>
                <p>Exit from your account</p>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <!-- Recent Pickups -->
        <div class="table-box">
            <h3>Recent Pickups</h3>
            <br>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Waste</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Track</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($recent)) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['waste_type']; ?></td>
                        <td><?php echo $row['pickup_date']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td><a class="track-btn" href="trackpickup.php?id=<?php echo $row['id']; ?>">Track</a></td>
                    </tr>
                <?php } ?>
            </table>
        </div>

    </div>
    <!-- Chat Button -->
    <div id="chat-btn" onclick="toggleChat()">💬</div>

    <!-- Chat Window -->
    <div id="chat-box-ui">
        <iframe src="chatbot_ui.php"></iframe>
    </div>

    <style>
        #chat-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #25d366;
            color: white;
            padding: 15px;
            border-radius: 50%;
            cursor: pointer;
        }

        #chat-box-ui {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 300px;
            height: 450px;
            display: none;
        }

        #chat-box-ui iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>

    <script>
        function toggleChat() {
            let box = document.getElementById("chat-box-ui");
            box.style.display = (box.style.display == "none") ? "block" : "none";
        }
    </script>
</body>

</html>