<?php
session_start();
include("config.php");

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {

        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {
            /* SESSION STORE */
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_email'] = $row['email'];

            header("Location: dashboard.php");
            exit();

        } else {
            $error = "Invalid Email or Password!";
        }

    } else {
        $error = "Invalid Email or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>♻ Login - E-Waste Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        /* BODY & SPLIT DIAGONAL BACKGROUND */
        body {
            min-height: 100vh;
            background: #18191a;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        /* DIAGONAL BACKGROUND */
        .diagonal-bg {
            position: absolute;
            top: 0;
            left: -120%;
            width: 220%;
            height: 220%;
            background: linear-gradient(135deg, #28a745, #20c997);
            transform: rotate(-45deg);
            animation: diagonalSlide 1s forwards 0.5s;
            z-index: 1;
        }

        /* HERO TITLE */
        .hero {
            position: absolute;
            top: 50px;
            width: 100%;
            text-align: center;
            color: white;
            z-index: 2;
            opacity: 0;
            transform: translateY(-40px);
            animation: heroSlide 1s forwards 0.8s;
        }

        .hero h1 {
            font-size: 42px;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.6);
        }

        .hero p {
            font-size: 20px;
            color: #eee;
        }

        /* LOGIN CARD */
        .login-card {
            position: relative;
            z-index: 2;
            width: 400px;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            text-align: center;
            opacity: 0;
            transform: translateY(50px);
            animation: cardSlide 1s forwards 1.2s;
        }

        /* CARD TITLE */
        .login-card h2 {
            margin-bottom: 25px;
            color: #28a745;
            font-size: 26px;
        }

        /* INPUTS */
        .login-card input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
            transition: 0.3s;
        }

        .login-card input:focus {
            border-color: #28a745;
            outline: none;
            box-shadow: 0 0 8px rgba(40, 167, 69, 0.3);
        }

        /* PASSWORD BOX */
        .password-box {
            position: relative;
            width: 100%;
            margin: 10px 0;
        }

        .password-box input {
            width: 100%;
            padding: 12px;
            padding-right: 45px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .password-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #777;
            font-size: 16px;
        }

        .password-box i:hover {
            color: #28a745;
        }

        /* BUTTON */
        .login-card button {
            width: 100%;
            padding: 14px;
            background: #28a745;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 8px;
            margin-top: 15px;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-card button:hover {
            background: #20c997;
            transform: scale(1.05);
        }

        /* ERROR MESSAGE */
        .error {
            color: red;
            margin-bottom: 10px;
            font-size: 14px;
        }

        /* REGISTER LINK */
        .register-link {
            margin-top: 15px;
            font-size: 14px;
        }

        .register-link a {
            color: #28a745;
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* ANIMATIONS */
        @keyframes diagonalSlide {
            0% {
                left: -120%;
            }

            100% {
                left: -20%;
            }
        }

        @keyframes heroSlide {
            0% {
                opacity: 0;
                transform: translateY(-40px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes cardSlide {
            0% {
                opacity: 0;
                transform: translateY(50px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* RESPONSIVE */
        @media(max-width:500px) {
            .login-card {
                width: 90%;
                padding: 30px;
            }

            .hero h1 {
                font-size: 32px;
            }

            .hero p {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>

    <!-- DIAGONAL BACKGROUND -->
    <div class="diagonal-bg"></div>

    <!-- HERO TITLE -->
    <div class="hero">
        <h1>♻ E-Waste Management Portal</h1>
        <p>Login to manage your pickups and reviews</p>
    </div>

    <!-- LOGIN CARD -->
    <div class="login-card">
        <h2>Welcome Back</h2>

        <?php if (isset($error)) {
            echo "<div class='error'>$error</div>";
        } ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>

            <div class="password-box">
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
                <i class="fa-solid fa-eye" id="eye" onclick="togglePassword()"></i>
            </div>

            <button type="submit" name="login">Login</button>
        </form>

        <div class="register-link">
            Don't have an account? <a href="register.php">Register</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            var password = document.getElementById("password");
            var eye = document.getElementById("eye");

            if (password.type === "password") {
                password.type = "text";
                eye.classList.remove("fa-eye");
                eye.classList.add("fa-eye-slash");
            } else {
                password.type = "password";
                eye.classList.remove("fa-eye-slash");
                eye.classList.add("fa-eye");
            }
        }
    </script>

</body>

</html>