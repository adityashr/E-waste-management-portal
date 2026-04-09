<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";

/* SUCCESS MESSAGE (only once) */

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $msg = "Review Submitted Successfully";
}

/* GET PICKUP */

if (isset($_GET['pickup_id'])) {
    $pickup_id = $_GET['pickup_id'];
} else {

    $q = mysqli_query($conn, "SELECT id FROM pickups 
    WHERE user_id='$user_id' AND status='Completed'
    ORDER BY id DESC LIMIT 1");

    if (mysqli_num_rows($q) > 0) {
        $row = mysqli_fetch_assoc($q);
        $pickup_id = $row['id'];
    } else {
        echo "No completed pickup found";
        exit();
    }
}

/* SUBMIT REVIEW */

if (isset($_POST['submit'])) {

    $rating = $_POST['rating'];
    $review = $_POST['review'];

    mysqli_query($conn, "INSERT INTO reviews(user_id,pickup_id,rating,review)
    VALUES('$user_id','$pickup_id','$rating','$review')");

    // Redirect (prevent duplicate + clean flow)
    header("Location: review.php?success=1");
    exit();
}

/* FETCH REVIEWS */

$reviews = mysqli_query($conn, "
SELECT reviews.*, users.name
FROM reviews
JOIN users ON reviews.user_id = users.id
ORDER BY reviews.id DESC
");

?>

<!DOCTYPE html>
<html>

<head>

    <title>User Reviews</title>

    <style>
        body {
            font-family: 'Segoe UI';
            background: linear-gradient(135deg, #26ac24 50%, #18191a 50%);
            min-height: 100vh;
            padding: 50px;
        }

        .wrapper {
            display: flex;
            gap: 40px;
            align-items: flex-start;
            justify-content: center;
        }

        .review-form {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            width: 420px;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(8px);
        }

        .review-form h2 {
            margin-bottom: 15px;
            color: #333;
        }

        select,
        textarea {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        textarea {
            height: 100px;
            resize: none;
        }

        button {
            margin-top: 15px;
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            font-size: 15px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            transform: scale(1.05);
        }

        .review-list {
            background: rgba(255, 255, 255, 0.95);
            padding: 25px;
            width: 450px;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            max-height: 500px;
            overflow-y: auto;
        }

        .review-list h3 {
            margin-bottom: 15px;
        }

        .review-card {
            display: flex;
            gap: 12px;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 12px;
            background: #f8f9fb;
            transition: 0.3s;
        }

        .review-card:hover {
            background: #eef6ff;
            transform: translateY(-3px);
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #28a745;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .review-content {
            flex: 1;
        }

        .review-name {
            font-weight: bold;
            color: #333;
        }

        .review-stars {
            color: #ffc107;
            font-size: 14px;
            margin: 4px 0;
        }

        .review-text {
            font-size: 14px;
            color: #555;
        }

        .success {
            color: #155724;
            background: #d4edda;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

</head>

<body>

    <div class="wrapper">

        <div class="review-form">

            <h2>Leave Your Review</h2>

            <?php if ($msg != "") { ?>
                <div class="success"><?php echo $msg; ?></div>
            <?php } ?>

            <form method="POST">

                <label>Rating</label>

                <select name="rating" required>
                    <option value="">Select Rating</option>
                    <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                    <option value="4">⭐⭐⭐⭐ Good</option>
                    <option value="3">⭐⭐⭐ Average</option>
                    <option value="2">⭐⭐ Poor</option>
                    <option value="1">⭐ Bad</option>
                </select>

                <label>Review</label>

                <textarea name="review" placeholder="Share your experience..." required></textarea>

                <button name="submit">Submit Review</button>

            </form>

        </div>

        <div class="review-list">

            <h3>Users Reviews</h3>

            <?php while ($r = mysqli_fetch_assoc($reviews)) { ?>

                <div class="review-card">

                    <div class="avatar">
                        <?php echo strtoupper(substr($r['name'], 0, 1)); ?>
                    </div>

                    <div class="review-content">

                        <div class="review-name">
                            <?php echo $r['name']; ?>
                        </div>

                        <div class="review-stars">
                            <?php echo str_repeat("⭐", $r['rating']); ?>
                        </div>

                        <div class="review-text">
                            <?php echo $r['review']; ?>
                        </div>

                    </div>

                </div>

            <?php } ?>

        </div>

    </div>

    <!-- AUTO HIDE + CLEAN URL -->
    <script>
        setTimeout(() => {
            let msg = document.querySelector(".success");
            if (msg) {
                msg.style.display = "none";
            }

            // remove ?success=1 from URL
            if (window.location.search.includes("success")) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }

        }, 3000);
    </script>

</body>

</html>