<?php
session_start();
include("config.php");

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$msg = "";

/* Add Center */
if (isset($_POST['add_center'])) {
    $name = trim($_POST['name']);
    $latitude = trim($_POST['latitude']);
    $longitude = trim($_POST['longitude']);
    $address = trim($_POST['address']);

    if ($name != "" && $latitude != "" && $longitude != "" && $address != "") {
        $stmt = $conn->prepare("INSERT INTO centers (name, latitude, longitude, address) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $latitude, $longitude, $address);
        if ($stmt->execute()) {
            $msg = "Center Added Successfully!";
        } else {
            $msg = "Error Adding Center!";
        }
        $stmt->close();
    } else {
        $msg = "All fields are required!";
    }
}

/* Delete Center */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM centers WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $msg = "Center Deleted Successfully!";
    }
    $stmt->close();
}

/* Fetch Centers */
$result = mysqli_query($conn, "SELECT * FROM centers ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Centers | Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #18191a;
            color: white;
        }

        .topbar {
            background: white;
            color: #111827;
            padding: 20px 30px;
            border-radius: 14px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .topbar h2 {
            color: #1e3a8a;
        }

        .topbar .logout-btn {
            padding: 10px 20px;
            border-radius: 8px;
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }

        .topbar .logout-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .main-container {
            display: flex;
            gap: 30px;
            padding: 20px;
        }

        .left-panel {
            flex: 1;
            background: #1e293b;
            padding: 20px;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .left-panel h3 {
            margin-bottom: 15px;
            color: #facc15;
        }

        .left-panel input,
        .left-panel textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 8px;
            border: none;
        }

        .left-panel button {
            margin-top: 10px;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .left-panel button:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .right-panel {
            flex: 1;
            position: relative;
            padding-left: 20px;
        }

        .timeline {
            position: relative;
            margin-left: 20px;
        }

        .timeline::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #28a745;
            border-radius: 2px;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 30px;
            padding-left: 20px;
        }

        .timeline-item::before {
            content: "";
            position: absolute;
            left: -11px;
            top: 0;
            width: 20px;
            height: 20px;
            background: #28a745;
            border-radius: 50%;
            border: 3px solid white;
        }

        .timeline-item p {
            margin: 5px 0;
            color: #f0f0f0;
        }

        .timeline-item a {
            display: inline-block;
            margin-top: 5px;
            color: #f87171;
            text-decoration: none;
        }

        .msg {
            padding: 10px;
            margin-bottom: 15px;
            background: #16a34a;
            border-radius: 8px;
            color: white;
            opacity: 1;
            transition: opacity 0.5s;
        }

        @media(max-width:900px) {
            .main-container {
                flex-direction: column;
            }

            .right-panel {
                padding-left: 0;
            }

            .timeline::before {
                left: 10px;
            }

            .timeline-item::before {
                left: -1px;
            }
        }
    </style>
</head>

<body>

    <div class="topbar">
        <h2>Manage Centers 🏢</h2>
        <a href="admin_logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="main-container">

        <!-- Left Panel: Add Center -->
        <div class="left-panel">
            <h3>Add New Center</h3>
            <?php if ($msg != "" && isset($_POST['add_center'])) { ?>
                <div class="msg" id="msgDiv"><?php echo $msg; ?></div>
            <?php } ?>
            <form method="POST">
                <input type="text" name="name" placeholder="Center Name" required>
                <input type="text" name="latitude" placeholder="Latitude" required>
                <input type="text" name="longitude" placeholder="Longitude" required>
                <textarea name="address" placeholder="Address" required></textarea>
                <button type="submit" name="add_center">Add Center</button>
            </form>
        </div>

        <!-- Right Panel: Timeline / Center List -->
        <div class="right-panel">
            <h3>All Centers</h3>
            <div class="timeline">
                <?php if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) { ?>
                        <div class="timeline-item">
                            <p><strong><?php echo htmlspecialchars($row['name']); ?></strong></p>
                            <p>Lat: <?php echo htmlspecialchars($row['latitude']); ?> | Lon:
                                <?php echo htmlspecialchars($row['longitude']); ?>
                            </p>
                            <p><?php echo htmlspecialchars($row['address']); ?></p>
                            <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </div>
                    <?php }
                } else { ?>
                    <p style="color:#f0f0f0;">No Centers Added Yet.</p>
                <?php } ?>
            </div>
        </div>

    </div>

    <script>
        // Auto-hide message after 2 seconds
        window.addEventListener('DOMContentLoaded', (event) => {
            const msgDiv = document.getElementById('msgDiv');
            if (msgDiv) {
                setTimeout(() => {
                    msgDiv.style.opacity = 0;
                    setTimeout(() => msgDiv.remove(), 500);
                }, 2000);
            }
        });
    </script>

</body>

</html>