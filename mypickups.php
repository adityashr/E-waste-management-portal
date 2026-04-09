<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* Cancel Single Pickup */
if (isset($_GET['cancel'])) {
    $id = intval($_GET['cancel']);

    $stmt = $conn->prepare("DELETE FROM pickups WHERE id=? AND user_id=? AND status='Pending'");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();

    $_SESSION['msg'] = "Pickup Cancelled Successfully";
    header("Location: mypickups.php");
    exit();
}

/* Current user pickups */
$stmt = $conn->prepare("SELECT * FROM pickups WHERE user_id=? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>My Pickup Requests</title>

    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            background: #000;
            overflow-x: hidden;
            color: #fff;
        }

        /* Animated background */
        .animated-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 50vh;
            background: linear-gradient(180deg, #000, #001100);
            overflow: hidden;
            z-index: -1;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(40, 172, 36, 0.6);
            pointer-events: none;
            animation: float 15s linear infinite;
        }

        @keyframes float {
            0% {
                transform: translateY(0) translateX(0) scale(0.5);
            }

            50% {
                transform: translateY(-200px) translateX(50px) scale(1);
            }

            100% {
                transform: translateY(0) translateX(0) scale(0.5);
            }
        }

        /* heading */
        .page-heading {
            text-align: center;
            margin: 30px 0 15px;
            position: relative;
            z-index: 1;
        }

        .page-heading h1 {
            font-size: 36px;
            color: #28a745;
            margin: 0;
        }

        .page-heading p {
            font-size: 16px;
            color: #ddd;
        }

        /* Table Container */
        .container {
            width: 90%;
            max-width: 1000px;
            background: #111;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            margin-top: 20px;
            position: relative;
            z-index: 2;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 15px;
            overflow: hidden;
            border-radius: 15px;
        }

        th {
            background: #28a745;
            color: #fff;
            padding: 15px;
            font-size: 16px;
            text-align: center;
        }

        td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.03);
        }

        tr:hover {
            background: rgba(40, 167, 69, 0.2);
            transition: 0.3s;
        }

        /* Multi-select selected row */
        .pickup-selected {
            background: rgba(40, 167, 69, 0.4) !important;
        }

        .status {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .pending {
            background: #fff3cd;
            color: #856404;
        }

        .completed {
            background: #d4edda;
            color: #155724;
        }

        .rejected {
            background: #f8d7da;
            color: #721c24;
        }

        /* Buttons */
        .btn {
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: bold;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
            transition: 0.3s;
        }

        .track-btn {
            background: #28a745;
        }

        .track-btn:hover {
            background: #218838;
            transform: scale(1.05);
        }

        .cancel-btn {
            background: #ff4b2b;
        }

        .cancel-btn:hover {
            background: #ff416c;
            transform: scale(1.05);
        }

        .review-btn {
            background: #6f42c1;
        }

        .review-btn:hover {
            background: #8e44ad;
            transform: scale(1.05);
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 8px;
        }

        /* Multi-select delete */
        #deleteSelected {
            display: none;
            width: 100%;
            margin-bottom: 15px;
            padding: 12px;
            border: none;
            border-radius: 10px;
            background: #ff4b2b;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        #deleteSelected:hover {
            transform: scale(1.05);
            background: #ff416c;
        }

        #selectedCount {
            font-weight: bold;
            margin-bottom: 10px;
            display: none;
            text-align: center;
        }

        @media(max-width:768px) {

            table,
            th,
            td {
                font-size: 13px;
            }

            .btn {
                font-size: 12px;
                padding: 6px 12px;
            }

            .action-buttons {
                flex-direction: column;
                gap: 8px;
            }
        }
    </style>
</head>

<body>

    <div class="animated-bg"></div>

    <div class="page-heading">
        <h1>📦 My Pickup Requests</h1>
        <p>View and manage your scheduled pickups</p>
    </div>

    <div class="container">
        <button id="deleteSelected">Delete Selected</button>
        <div id="selectedCount"></div>

        <table>
            <tr>
                <th>ID</th>
                <th>Waste Type</th>
                <th>Address</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php if ($result->num_rows > 0) { ?>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr class="pickup-item" data-id="<?= $row['id']; ?>">
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['waste_type']); ?></td>
                        <td><?= htmlspecialchars($row['address']); ?></td>
                        <td><?= $row['pickup_date']; ?></td>
                        <td><span class="status <?= strtolower($row['status']); ?>"><?= $row['status']; ?></span></td>
                        <td>
                            <div class="action-buttons">
                                <?php if ($row['status'] == "Rejected") { ?>
                                    <span style="color:red;font-weight:bold;">Rejected</span>
                                <?php } else { ?>
                                    <a href="trackpickup.php?id=<?= $row['id']; ?>" class="btn track-btn">Track</a>
                                <?php } ?>
                                <?php if ($row['status'] == "Pending") { ?>
                                    <a href="mypickups.php?cancel=<?= $row['id']; ?>" class="btn cancel-btn"
                                        onclick="return confirm('Cancel this pickup?');">Cancel</a>
                                <?php } ?>
                                <?php if ($row['status'] == "Completed") { ?>
                                    <a href="review.php?pickup_id=<?= $row['id']; ?>" class="btn review-btn">Give Review</a>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="6">No pickup requests found.</td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <?php if (isset($_SESSION['msg'])): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({
                    title: "Success 🎉",
                    text: "<?= $_SESSION['msg']; ?>",
                    icon: "success",
                    confirmButtonColor: "#28a745",
                    timer: 3000
                });
            });
        </script>
        <?php unset($_SESSION['msg']); endif; ?>

    <script>
        const particleCount = 30;
        const containerBg = document.querySelector('.animated-bg');
        for (let i = 0; i < particleCount; i++) {
            const p = document.createElement('div');
            p.classList.add('particle');
            const size = Math.random() * 6 + 4;
            p.style.width = size + 'px';
            p.style.height = size + 'px';
            p.style.top = Math.random() * 50 + 'vh';
            p.style.left = Math.random() * 100 + 'vw';
            p.style.animationDuration = (Math.random() * 20 + 10) + 's';
            containerBg.appendChild(p);
        }

        // Multi-select pickup
        let items = document.querySelectorAll('.pickup-item');
        let deleteBtn = document.getElementById('deleteSelected');
        let selectedCount = document.getElementById('selectedCount');
        let multiSelect = false;
        let selectedIds = [];

        items.forEach(item => {
            let pressTimer;
            item.addEventListener('mousedown', () => {
                pressTimer = setTimeout(() => {
                    multiSelect = true;
                    item.classList.toggle('pickup-selected');
                    updateSelection(item.dataset.id);
                    showDeleteButton();
                }, 500);
            });
            item.addEventListener('mouseup', () => clearTimeout(pressTimer));
            item.addEventListener('mouseleave', () => clearTimeout(pressTimer));

            item.addEventListener('click', () => {
                if (multiSelect) {
                    item.classList.toggle('pickup-selected');
                    updateSelection(item.dataset.id);
                    showDeleteButton();
                }
            });
        });

        function updateSelection(id) {
            id = parseInt(id);
            if (selectedIds.includes(id)) {
                selectedIds = selectedIds.filter(x => x !== id);
            } else {
                selectedIds.push(id);
            }
        }

        function showDeleteButton() {
            if (selectedIds.length > 0) {
                deleteBtn.style.display = 'block';
                selectedCount.style.display = 'block';
                selectedCount.textContent = selectedIds.length + " selected";
            } else {
                deleteBtn.style.display = 'none';
                selectedCount.style.display = 'none';
                multiSelect = false;
            }
        }

        deleteBtn.addEventListener('click', () => {
            if (confirm(`Are you sure you want to delete ${selectedIds.length} pickup(s)?`)) {
                fetch('delete_pickups.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ids: selectedIds })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            selectedIds.forEach(id => {
                                let elem = document.querySelector(`.pickup-item[data-id='${id}']`);
                                if (elem) elem.remove();
                            });
                            selectedIds = [];
                            showDeleteButton();
                        } else { alert('Error deleting pickups'); }
                    });
            }
        });
    </script>

</body>

</html>