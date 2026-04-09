<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Logout | E-Waste Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            background: #0a0a0a;
            position: relative;
        }

        /* Gradient Glow Background */
        .background-glow {
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 30%, rgba(40, 167, 69, 0.4), transparent 70%);
            animation: glowMove 15s ease-in-out infinite alternate;
            z-index: 0;
        }

        .background-glow::after {
            content: "";
            position: absolute;
            top: 20%;
            left: 50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 70% 70%, rgba(28, 200, 80, 0.3), transparent 80%);
            animation: glowMove 20s ease-in-out infinite alternate-reverse;
        }

        /* Floating Soft Particles */
        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(40, 167, 69, 0.05);
            animation: float 25s ease-in-out infinite;
        }

        .particle1 {
            width: 250px;
            height: 250px;
            top: 10%;
            left: 15%;
        }

        .particle2 {
            width: 180px;
            height: 180px;
            top: 60%;
            left: 70%;
        }

        .particle3 {
            width: 120px;
            height: 120px;
            top: 40%;
            left: 35%;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0);
            }

            50% {
                transform: translateY(-20px) translateX(20px);
            }
        }

        @keyframes glowMove {
            0% {
                transform: translate(0, 0) scale(1);
            }

            100% {
                transform: translate(50px, 50px) scale(1.2);
            }
        }

        /* Heading */
        .main-heading {
            position: relative;
            z-index: 2;
            text-align: center;
            width: 100%;
            margin-bottom: 30px;
            color: white;
            font-size: 36px;
            font-weight: 600;
            text-shadow: 0 2px 15px rgba(0, 255, 100, 0.3);
            animation: headingPop 1s ease forwards;
        }

        /* Logout Card */
        .logout-card {
            position: relative;
            z-index: 2;
            width: 400px;
            padding: 45px;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(15px);
            box-shadow: 0 20px 50px rgba(0, 255, 100, 0.2);
            text-align: center;
            animation: cardIn 1s ease forwards;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .logout-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 60px rgba(0, 255, 100, 0.4);
        }

        /* Card Heading */
        .logout-card h2 {
            color: #28a745;
            font-size: 32px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            animation: popIn 0.6s ease forwards;
        }

        .logout-card h2::before {
            content: "✔";
            font-size: 45px;
            color: #28a745;
            animation: checkPop 0.6s ease forwards;
        }

        /* Paragraph */
        .logout-card p {
            color: #cfd8dc;
            font-size: 17px;
            margin-bottom: 30px;
        }

        /* Button */
        .logout-card a {
            display: inline-block;
            padding: 15px 35px;
            border-radius: 50px;
            background: linear-gradient(135deg, #28a745, #6be67e);
            color: white;
            font-size: 17px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.4s;
            box-shadow: 0 5px 20px rgba(0, 255, 100, 0.3);
        }

        .logout-card a:hover {
            transform: scale(1.1);
            box-shadow: 0 10px 30px rgba(0, 255, 100, 0.5);
        }

        /* Animations */
        @keyframes cardIn {
            0% {
                opacity: 0;
                transform: translateY(-50px);
            }

            60% {
                opacity: 1;
                transform: translateY(10px);
            }

            100% {
                transform: translateY(0);
            }
        }

        @keyframes popIn {
            0% {
                opacity: 0;
                transform: scale(0.5);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes checkPop {
            0% {
                opacity: 0;
                transform: scale(0);
            }

            60% {
                opacity: 1;
                transform: scale(1.3);
            }

            100% {
                transform: scale(1);
            }
        }

        @keyframes headingPop {
            0% {
                opacity: 0;
                transform: translateY(-30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media(max-width:500px) {
            .main-heading {
                font-size: 28px;
                margin-bottom: 25px;
            }

            .logout-card {
                width: 90%;
                padding: 35px;
            }

            .logout-card h2 {
                font-size: 28px;
            }

            .logout-card h2::before {
                font-size: 40px;
            }

            .logout-card a {
                padding: 12px 30px;
                font-size: 16px;
            }
        }
    </style>
    <script>
        // Auto redirect to login after 3 seconds
        setTimeout(function () {
            window.location.href = "admin_login.php";
        }, 3000);
    </script>
</head>

<body>

    <div class="background-glow"></div>
    <div class="particle particle1"></div>
    <div class="particle particle2"></div>
    <div class="particle particle3"></div>

    <h1 class="main-heading">E-Waste Portal Admin Logout</h1>

    <div class="logout-card">
        <h2>Logged Out</h2>
        <p>You have been safely logged out of the Admin Panel.</p>
        <a href="admin_login.php">Login Again</a>
    </div>

</body>

</html>