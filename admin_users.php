<?php
session_start();
include("config.php");

/* Admin check */
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

/* Delete user if requested */
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);

    // user pickups delete
    mysqli_query($conn, "DELETE FROM pickups WHERE user_id=$del_id");

    // user delete
    mysqli_query($conn, "DELETE FROM users WHERE id=$del_id");

    $_SESSION['delete_msg'] = "User deleted successfully";

    header("Location: admin_users.php");
    exit();
}

/* Fetch all users */
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Users | E-Waste Portal</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ⚠️ Tumhara pura CSS same hai — kuch change nahi kiya */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #111827;
            color: white;
            overflow-x: hidden;
        }

        /* Success message */

        .alert-success {
            background: linear-gradient(45deg, #28a745, #20c997);
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.5s ease;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
        }

        @keyframes slideDown {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Top diagonal pattern */
        .diagonal-bg {
            position: fixed;
            top: 0;
            left: -120%;
            width: 250%;
            height: 60%;
            background: linear-gradient(135deg, #20c997, #28a745);
            transform: rotate(-45deg) translateX(0);
            animation: diagonalSlide 5s linear infinite;
            z-index: 0;
            opacity: 0.18;
        }

        /* Sidebar */
        .sidebar {
            width: 220px;
            height: 100vh;
            background: linear-gradient(180deg, #1e3a8a, #3b82f6);
            color: white;
            position: fixed;
            padding-top: 30px;
            z-index: 1;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #facc15;
            font-size: 22px;
            animation: glow 2s infinite alternate;
        }

        .sidebar a {
            display: block;
            padding: 14px 24px;
            text-decoration: none;
            color: #d1d5db;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background: linear-gradient(45deg, #28a745, #20c997);
            padding-left: 30px;
            color: white;
        }

        /* Main */
        .main {
            margin-left: 220px;
            padding: 30px;
            position: relative;
            z-index: 1;
        }

        /* Topbar */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .topbar h2 {
            color: #facc15;
        }

        .topbar a.logout-btn {
            padding: 10px 18px;
            border-radius: 8px;
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }

        .topbar a.logout-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        /* Table box */
        .table-box {
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
            animation: fadeInUp 0.8s ease forwards;
        }

        .table-box h3 {
            margin-bottom: 20px;
            color: #28a745;
            font-size: 22px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            color: white;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: center;
        }

        th {
            background: rgba(40, 167, 69, 0.8);
            color: white;
            text-transform: uppercase;
            font-size: 14px;
            border-radius: 8px;
        }

        tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.05);
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: scale(1.01);
            transition: 0.3s;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            transition: 0.3s;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .action-btn.delete {
            background: linear-gradient(45deg, #dc3545, #b02a37);
        }

        .action-btn.delete:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.25);
        }

        @keyframes diagonalSlide {
            0% {
                transform: rotate(-45deg) translateX(-120%);
            }

            100% {
                transform: rotate(-35deg) translateX(120%);
            }
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(100px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

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
    </style>
</head>

<body>

    <div class="diagonal-bg"></div>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="admin_users.php"><i class="fa-solid fa-users"></i> Users</a>
        <a href="admin_pickups.php"><i class="fa-solid fa-box"></i> Pickups</a>
        <a href="admin_logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>

    <div class="main">

        <div class="topbar">
            <h2>All Users</h2>
            <a href="admin_logout.php" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>

        <?php
        if (isset($_SESSION['delete_msg'])) {
            ?>

            <div class="alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <?php
                echo $_SESSION['delete_msg'];
                unset($_SESSION['delete_msg']);
                ?>
            </div>

            <?php
        }
        ?>

        <div class="table-box">

            <h3>Registered Users</h3>

            <table>

                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Joined On</th>
                    <th>Action</th>
                </tr>

                <?php while ($user = mysqli_fetch_assoc($users)) { ?>

                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo date("d-m-Y", strtotime($user['created_at'])); ?></td>

                        <td>

                            <a class="action-btn delete" href="admin_users.php?delete_id=<?php echo $user['id']; ?>"
                                onclick="return confirm('Are you sure you want to delete this user?');">

                                <i class="fa-solid fa-trash"></i> Delete

                            </a>

                        </td>
                    </tr>

                <?php } ?>

            </table>

        </div>
    </div>

</body>

</html>