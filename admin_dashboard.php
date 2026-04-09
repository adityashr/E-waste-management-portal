<?php
session_start();
include("config.php");

// Redirect to login if admin session not set
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch dynamic counts
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$totalPickups = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pickups"))['total'];
$completedPickups = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pickups WHERE status='Completed'"))['total'];
$pendingPickups = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pickups WHERE status='Pending'"))['total'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | E-Waste Portal</title>
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
            display: flex;
            min-height: 100vh;
            background: #18191a;
            color: white;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: linear-gradient(180deg, #1e3a8a, #3b82f6);
            position: fixed;
            padding-top: 30px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #facc15;
            font-size: 26px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            color: #d1d5db;
            text-decoration: none;
            margin: 6px 10px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            padding-left: 30px;
        }

        /* Main */
        .main {
            margin-left: 260px;
            width: 100%;
            padding: 30px;
        }

        /* Topbar */
        .topbar {
            background: #ffffff10;
            color: #fff;
            padding: 20px 30px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
        }

        /* Logout button */
        .logout-btn {
            padding: 10px 18px;
            background: linear-gradient(135deg, #dc3545, #ff4b4b);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
            transition: 0.3s;
        }

        .logout-btn i {
            font-size: 16px;
        }

        .logout-btn:hover {
            background: linear-gradient(135deg, #b02a37, #ff1a1a);
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.35);
        }

        /* Cards */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            color: #111;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.25);
        }

        .card h3 {
            margin-bottom: 10px;
            font-size: 18px;
        }

        .card p {
            font-size: 28px;
            font-weight: 600;
            color: #16a34a;
        }

        /* Counter animation */
        .counter {
            color: #16a34a;
            font-weight: 600;
            font-size: 28px;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="admin_users.php"><i class="fa-solid fa-users"></i> Users</a>
        <a href="admin_pickups.php"><i class="fa-solid fa-box"></i> Pickups</a>
        <a href="admin_managed_center.php"><i class="fa-solid fa-location-dot"></i> Manage Centers</a>
        <a href="admin_logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>

    <div class="main">
        <div class="topbar">
            <h2>Welcome, <?php echo $_SESSION['admin']; ?></h2>
            <a href="admin_logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>

        <div class="cards">
            <div class="card">
                <h3>Total Users</h3>
                <p class="counter" data-target="<?php echo $totalUsers; ?>">0</p>
            </div>
            <div class="card">
                <h3>Pickup Requests</h3>
                <p class="counter" data-target="<?php echo $totalPickups; ?>">0</p>
            </div>
            <div class="card">
                <h3>Completed</h3>
                <p class="counter" data-target="<?php echo $completedPickups; ?>">0</p>
            </div>
            <div class="card">
                <h3>Pending</h3>
                <p class="counter" data-target="<?php echo $pendingPickups; ?>">0</p>
            </div>
        </div>
    </div>

    <script>
        // Animate counters
        const counters = document.querySelectorAll('.counter');
        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText;
                const increment = Math.ceil(target / 200);
                if (count < target) {
                    counter.innerText = count + increment;
                    setTimeout(updateCount, 15);
                } else {
                    counter.innerText = target;
                }
            };
            updateCount();
        });
    </script>

</body>

</html>