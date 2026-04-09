<?php
session_start();
include("config.php");

// Redirect if not logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle status updates
if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = intval($_GET['id']);
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    mysqli_query($conn, "UPDATE pickups SET status='$status' WHERE id='$id'");
    header("Location: admin_pickups.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM pickups WHERE id='$id'");
    header("Location: admin_pickups.php");
    exit();
}

// Fetch all pickups with user info
$result = mysqli_query($conn, "
    SELECT pickups.*, users.name AS user_name 
    FROM pickups 
    LEFT JOIN users ON pickups.user_id = users.id
    ORDER BY pickups.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Pickups | E-Waste Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            background: #18191a;
            color: white;
            overflow-x: hidden;
            display: flex;
        }

        /* Animated Diagonal Background */
        .diagonal-bg {
            position: fixed;
            top: 0;
            left: -120%;
            width: 220%;
            height: 220%;
            background: linear-gradient(135deg, #20c997, #28a745);
            transform: rotate(-45deg);
            animation: diagonalSlide 2s linear infinite;
            z-index: 0;
            opacity: 0.1;
        }

        /* Layout */
        .admin-layout {
            display: flex;
            width: 100%;
            z-index: 1;
            position: relative;
        }

        /* Sidebar */
        .admin-sidebar {
            width: 260px;
            background: linear-gradient(180deg, #1e3a8a, #3b82f6);
            color: white;
            height: 100vh;
            padding: 25px 15px;
            position: fixed;
            top: 0;
            left: 0;
        }

        .admin-sidebar h2 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 26px;
            color: #facc15;
            animation: glow 2s infinite alternate;
        }

        .admin-sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            color: #d1d5db;
            text-decoration: none;
            border-radius: 10px;
            margin: 8px 0;
            transition: 0.3s;
        }

        .admin-sidebar a:hover {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            padding-left: 28px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .admin-sidebar a i {
            width: 20px;
        }

        /* Main Content */
        .admin-main {
            margin-left: 260px;
            width: 100%;
            padding: 30px;
        }

        /* Header */
        .admin-header {
            background: #ffffff10;
            padding: 20px 25px;
            border-radius: 14px;
            margin-bottom: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .admin-header h1 {
            color: #28a745;
        }

        /* Pickup Card */
        .pickup-card {
            background: white;
            color: #111827;
            padding: 22px 25px;
            border-radius: 14px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            margin-bottom: 18px;
            transition: 0.4s;
            position: relative;
        }

        .pickup-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        /* Highlight new pickup */
        .pickup-card.Pending {
            border-left: 5px solid #ffc107;
        }

        /* Status Badge */
        .status {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: inline-block;
            margin-top: 10px;
        }

        .status.Pending {
            background: #ffc107;
            color: #111;
        }

        .status.Approved {
            background: #28a745;
            color: white;
        }

        .status.Rejected {
            background: #dc3545;
            color: white;
        }

        .status.Completed {
            background: #155724;
            color: white;
        }

        /* Buttons */
        .btn {
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            transition: 0.3s;
            display: inline-block;
            margin-right: 6px;
            margin-top: 10px;
        }

        .approve-btn {
            background: #28a745;
            color: white;
        }

        .reject-btn {
            background: #dc3545;
            color: white;
        }

        .complete-btn {
            background: #20c997;
            color: white;
        }

        .delete-btn {
            background: #6c757d;
            color: white;
        }

        .btn:hover {
            opacity: 0.85;
        }

        /* Animations */
        @keyframes glow {
            0% {
                text-shadow: 0 0 5px #facc15;
            }

            50% {
                text-shadow: 0 0 20px #fde047;
            }

            100% {
                text-shadow: 0 0 5px #facc15;
            }
        }

        @keyframes diagonalSlide {
            0% {
                left: -120%;
            }

            100% {
                left: -20%;
            }
        }

        /* Responsive */
        @media(max-width:768px) {
            .admin-sidebar {
                width: 200px;
            }

            .admin-main {
                margin-left: 200px;
            }
        }

        @media(max-width:600px) {
            .admin-layout {
                flex-direction: column;
            }

            .admin-sidebar {
                width: 100%;
                position: relative;
                height: auto;
            }

            .admin-main {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

    <div class="diagonal-bg"></div>

    <div class="admin-layout">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <h2>Admin Panel</h2>
            <a href="admin_dashboard.php"><i class="fa-solid fa-gauge"></i>Dashboard</a>
            <a href="admin_users.php"><i class="fa-solid fa-users"></i>Users</a>
            <a href="admin_pickups.php"><i class="fa-solid fa-box"></i>Pickups</a>
            <a href="admin_logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logout</a>
        </div>

        <!-- Main -->
        <div class="admin-main">
            <div class="admin-header">
                <h1>Pickup Requests 📦</h1>
            </div>

            <?php if (mysqli_num_rows($result) > 0) { ?>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <div class="pickup-card <?php echo $row['status']; ?>">
                        <p><strong>User:</strong> <?php echo $row['user_name'] ?? 'Unknown'; ?></p>
                        <p><strong>Waste Type:</strong> <?php echo $row['waste_type']; ?></p>
                        <p><strong>Address:</strong> <?php echo $row['address']; ?></p>
                        <p><strong>Date:</strong> <?php echo $row['pickup_date']; ?></p>

                        <span class="status <?php echo $row['status']; ?>"><?php echo $row['status']; ?></span>

                        <br>

                        <?php if ($row['status'] == "Pending") { ?>
                            <a class="btn approve-btn"
                                href="admin_pickups.php?id=<?php echo $row['id']; ?>&status=Approved">Approve</a>
                            <a class="btn reject-btn"
                                href="admin_pickups.php?id=<?php echo $row['id']; ?>&status=Rejected">Reject</a>
                        <?php } ?>

                        <?php if ($row['status'] == "Approved") { ?>
                            <a class="btn complete-btn" href="admin_pickups.php?id=<?php echo $row['id']; ?>&status=Completed">Mark
                                Completed</a>
                        <?php } ?>

                        <a class="btn delete-btn" href="admin_pickups.php?delete=<?php echo $row['id']; ?>"
                            onclick="return confirm('Are you sure you want to delete this request?')">Delete</a>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="pickup-card" style="text-align:center;">No Pickup Requests Found.</div>
            <?php } ?>
        </div>
    </div>

</body>

</html>