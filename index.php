<?php include("config.php"); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>♻ E-Waste Management Portal</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        /* BODY */
        body {
            height: 100vh;
            background: #18191a;
            overflow: hidden;
            position: relative;
        }

        /* HERO TITLE */
        .hero {
            position: relative;
            z-index: 2;
            height: 40vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            padding: 20px;
            opacity: 0;
            animation: heroAppear 1s forwards 0.3s;
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 10px;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.6);
        }

        .hero p {
            font-size: 20px;
            color: #eee;
        }

        /* DIAGONAL BACKGROUND */
        .diagonal-bg {
            position: absolute;
            top: 0;
            left: -100%;
            width: 200%;
            height: 200%;
            background: #26ac24;
            transform: rotate(-45deg);
            animation: diagonalMove 1s ease forwards 0.8s;
            z-index: 1;
        }

        /* FLIP CARD CONTAINER */
        .flip-card {
            perspective: 1200px;
            width: 500px;
            /* increased width */
            height: 350px;
            /* increased height */
            margin: 0 auto;
            position: relative;
            top: -50px;
            z-index: 2;
            opacity: 0;
            animation: formAppear 1s forwards 1.6s;
        }

        /* FLIP INNER */
        .flip-card-inner {
            position: relative;
            width: 100%;
            height: 100%;
            transition: transform 0.8s;
            transform-style: preserve-3d;
        }

        /* FLIP ON CLICK */
        .flip-card.flipped .flip-card-inner {
            transform: rotateY(180deg);
        }

        /* FRONT AND BACK STYLES */
        .flip-card-front,
        .flip-card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 20px;
            padding: 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            transition: 0.3s;
        }

        /* FRONT SIDE */
        .flip-card-front {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            font-weight: bold;
            text-align: center;
        }

        /* BACK SIDE */
        .flip-card-back {
            background: #000;
            color: white;
            transform: rotateY(180deg);
            font-weight: bold;
            text-align: center;
        }

        /* HEADINGS */
        .flip-card h3 {
            margin-bottom: 25px;
            font-size: 28px;
        }

        /* LINKS / BUTTONS */
        .flip-card a {
            display: inline-block;
            margin: 15px;
            /* increased margin */
            padding: 18px 40px;
            /* increased padding for bigger button */
            text-decoration: none;
            font-weight: bold;
            border-radius: 14px;
            /* slightly larger radius */
            color: white;
            font-size: 18px;
            /* increased font size */
            transition: 0.3s;
        }

        /* FRONT SIDE BUTTONS - BLACK */
        .flip-card-front a {
            background: #000;
            /* black color */
        }

        /* BACK SIDE BUTTONS - GREEN */
        .flip-card-back a {
            background: linear-gradient(45deg, #28a745, #20c997);
        }

        /* HOVER EFFECT */
        .flip-card a:hover {
            transform: scale(1.08);
            /* slightly more pop */
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        }

        /* ANIMATIONS */
        @keyframes diagonalMove {
            0% {
                left: -120%;
            }

            100% {
                left: -20%;
            }
        }

        @keyframes heroAppear {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes formAppear {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* RESPONSIVE */
        @media(max-width:600px) {
            .hero h1 {
                font-size: 32px;
            }

            .hero p {
                font-size: 16px;
            }

            .flip-card {
                width: 90%;
                height: auto;
            }

            .flip-card a {
                padding: 14px 30px;
                font-size: 16px;
                margin: 12px;
            }
        }
    </style>
</head>

<body>

    <!-- HERO TITLE -->
    <div class="hero">
        <h1>♻ E-Waste Management Portal</h1>
        <p>Manage electronic waste responsibly and easily</p>
    </div>

    <!-- DIAGONAL BACKGROUND -->
    <div class="diagonal-bg"></div>

    <!-- FLIP CARD -->
    <div class="flip-card" id="card">
        <div class="flip-card-inner">
            <!-- FRONT SIDE -->
            <div class="flip-card-front">
                <h3>Get Started</h3>
                <a href="register.php">Register</a>
                <a href="login.php">Login</a>
            </div>
            <!-- BACK SIDE -->
            <div class="flip-card-back">
                <h3>Explore</h3>
                <a href="map.php">View Centers</a>
            </div>
        </div>
    </div>

    <script>
        const card = document.getElementById("card");
        card.addEventListener("click", function (e) {
            if (e.target.tagName === "A") return; // links click na ho
            card.classList.toggle("flipped");
        });
    </script>

</body>

</html>