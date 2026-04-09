<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Logout | E-Waste Portal</title>
    <style>
        /* Body with animated gradient background */
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(45deg, #26ac24, #18191a, #28a745, #1e7e34);
            background-size: 400% 400%;
            animation: bgAnimation 15s ease infinite;
            color: white;
        }

        @keyframes bgAnimation {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* Message Box */
        .logout-box {
            background: rgba(0, 0, 0, 0.5);
            padding: 40px 60px;
            border-radius: 25px;
            text-align: center;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 1.2s ease forwards;
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(50px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h1 {
            font-size: 38px;
            margin-bottom: 15px;
            color: #ffffff;
            text-shadow: 2px 2px 15px rgba(0, 0, 0, 0.5);
        }

        p {
            font-size: 18px;
            margin-bottom: 25px;
        }

        /* Countdown */
        .countdown {
            font-weight: bold;
            font-size: 20px;
            color: #28a745;
            animation: pulse 1.2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }
        }

        /* Redirect Button (optional) */
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            background: #28a745;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            transition: 0.3s;
        }

        .back-btn:hover {
            background: #20c997;
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
        }

        /* Responsive */
        @media(max-width:500px) {
            .logout-box {
                padding: 25px 20px;
            }

            h1 {
                font-size: 28px;
            }

            p {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>

    <div class="logout-box">
        <h1>✅ Logged Out!</h1>
        <p>You have successfully logged out of <b>E-Waste Portal</b>.</p>
        <p>Redirecting to login page in <span class="countdown" id="counter">5</span> seconds...</p>
        <a href="login.php" class="back-btn">Go to Login Now</a>
    </div>

    <script>
        let countdown = 5;
        const counter = document.getElementById("counter");

        const interval = setInterval(() => {
            countdown--;
            counter.textContent = countdown;
            if (countdown <= 0) {
                clearInterval(interval);
                window.location.href = "login.php";
            }
        }, 1000);
    </script>

</body>

</html>