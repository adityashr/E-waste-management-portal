<?php
session_start();
include("config.php");

/* Login check */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* Check ID */
if (!isset($_GET['id'])) {
    header("Location: mypickups.php");
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM pickups WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Pickup not found";
    exit();
}

$row = $result->fetch_assoc();
$status = $row['status'];
?>

<!DOCTYPE html>
<html>

<head>
    <title>Track Pickup</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #28a745 30%, #f4f7fb 30%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        /* Navbar */
        .navbar {
            background: #1e7e34;
            color: white;
            padding: 18px;
            font-size: 22px;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        /* Container */
        .container {
            padding: 30px;
            display: flex;
            justify-content: center;
        }

        /* Tracking Card */
        .tracking-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
            max-width: 700px;
            width: 100%;
            animation: slideFadeIn 0.8s ease forwards;
        }

        /* Timeline */
        .timeline {
            position: relative;
            margin-top: 30px;
            padding-left: 40px;
        }

        .timeline::before {
            content: "";
            position: absolute;
            left: 20px;
            top: 0;
            width: 4px;
            height: 100%;
            background: #ddd;
            border-radius: 2px;
        }

        .step {
            position: relative;
            margin-bottom: 30px;
            padding-left: 20px;
            opacity: 0;
            transform: translateX(-30px);
            animation: stepSlide 0.6s forwards;
        }

        .step:nth-child(1) {
            animation-delay: 0.2s;
        }

        .step:nth-child(2) {
            animation-delay: 0.4s;
        }

        .step:nth-child(3) {
            animation-delay: 0.6s;
        }

        .step:nth-child(4) {
            animation-delay: 0.8s;
        }

        .step::before {
            content: "";
            position: absolute;
            left: -32px;
            top: 2px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #ccc;
            transition: 0.3s;
        }

        .step.done::before {
            background: #28a745;
            box-shadow: 0 0 10px #28a745;
            transform: scale(1.2);
        }

        .step.done {
            color: #28a745;
            font-weight: bold;
        }

        .step span {
            display: block;
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }

        /* Back Button */
        .back-btn {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 25px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
        }

        .back-btn:hover {
            background: #1e7e34;
            transform: scale(1.05);
        }

        /* Animations */
        @keyframes stepSlide {
            0% {
                opacity: 0;
                transform: translateX(-30px);
            }

            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideFadeIn {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <div class="navbar">📦 Pickup Tracking</div>

    <div class="container">
        <div class="tracking-container">
            <h2>Track Your Pickup</h2>
            <p><b>Waste Type:</b> <?php echo htmlspecialchars($row['waste_type']); ?></p>
            <p><b>Pickup Date:</b> <?php echo $row['pickup_date']; ?></p>

            <div class="timeline">
                <div class="step done">
                    Request Submitted
                    <span>Your pickup request has been received</span>
                </div>

                <div class="step <?php if ($status == "Approved" || $status == "Picked" || $status == "Completed")
                    echo 'done'; ?>">
                    Admin Approved
                    <span>Admin verified your request</span>
                </div>

                <div class="step <?php if ($status == "Picked" || $status == "Completed")
                    echo 'done'; ?>">
                    Pickup In Progress 🚚
                    <span>Pickup team is collecting your waste</span>
                </div>

                <div class="step <?php if ($status == "Completed")
                    echo 'done'; ?>">
                    Recycling Completed ♻
                    <span>Your waste has been safely recycled</span>
                </div>
            </div>

            <a href="mypickups.php" class="back-btn">⬅ Back to My Pickups</a>
        </div>
    </div>

</body>

</html>