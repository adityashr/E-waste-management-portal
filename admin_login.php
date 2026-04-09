<?php
session_start();
include("config.php");

if (isset($_SESSION['admin'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$error = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $res = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username' AND password='$password'");

    if (mysqli_num_rows($res) > 0) {
        $admin = mysqli_fetch_assoc($res);
        $_SESSION['admin'] = $admin['username'];
        $_SESSION['admin_id'] = $admin['id'];
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid Username or Password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - E-Waste Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #0a0a0a;
            overflow: hidden;
            position: relative;
        }

        /* Animated Background */

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(135deg,
                    #28a745 0px,
                    #28a745 2px,
                    rgba(40, 167, 69, 0.1) 2px,
                    rgba(40, 167, 69, 0.1) 30px);
            animation: diagonalMove 15s linear infinite;
            z-index: 0;
            opacity: 0.3;
        }

        @keyframes diagonalMove {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(-150px, -150px);
            }
        }

        /* Floating Shapes */

        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(40, 167, 69, 0.05);
            animation: floatShape 20s ease-in-out infinite;
        }

        .shape1 {
            width: 200px;
            height: 200px;
            top: 10%;
            left: 15%;
        }

        .shape2 {
            width: 300px;
            height: 300px;
            top: 70%;
            left: 70%;
        }

        .shape3 {
            width: 150px;
            height: 150px;
            top: 50%;
            left: 30%;
        }

        @keyframes floatShape {

            0%,
            100% {
                transform: translateY(0) translateX(0);
            }

            50% {
                transform: translateY(-20px) translateX(20px);
            }
        }

        /* Login Card */

        .login-card {
            position: relative;
            z-index: 2;
            width: 400px;
            padding: 45px;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(15px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            text-align: center;
            color: white;
            transition: 0.3s;
        }

        .login-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
        }

        /* Title */

        .login-card h2 {
            font-size: 30px;
            font-weight: 600;
            margin-bottom: 30px;
        }

        .login-card h2::before {
            content: "\f3ed";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            margin-right: 10px;
            font-size: 32px;
            color: #28a745;
        }

        /* Inputs */

        .login-card input[type="text"],
        .login-card input[type="password"] {
            width: 100%;
            padding: 15px 18px;
            margin: 12px 0;
            border-radius: 14px;
            border: none;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 15px;
            transition: 0.3s;
        }

        .login-card input:focus {
            background: rgba(40, 167, 69, 0.2);
            outline: none;
            box-shadow: 0 0 12px #28a745;
        }

        .password-box {
            position: relative;
            width: 100%;
            margin: 12px 0;
        }

        .password-box input {
            width: 100%;
            padding-right: 45px;
        }

        .password-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: white;
            font-size: 18px;
        }

        .password-box i:hover {
            color: #28a745;
        }

        /* Button */

        .login-card button {
            width: 100%;
            padding: 15px;
            margin-top: 15px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-card button:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.5);
        }

        /* Error */

        .error {
            color: #ff6b6b;
            margin-bottom: 12px;
            font-size: 14px;
            background: rgba(255, 0, 0, 0.1);
            padding: 8px 12px;
            border-radius: 8px;
        }

        /* Hero Title */

        .hero {
            position: absolute;
            top: 50px;
            width: 100%;
            text-align: center;
            color: white;
            z-index: 2;
        }

        .hero h1 {
            font-size: 44px;
            text-shadow: 2px 2px 12px rgba(0, 0, 0, 0.6);
        }

        .hero p {
            font-size: 20px;
            color: #ccc;
        }

        /* Responsive */

        @media(max-width:500px) {

            .login-card {
                width: 90%;
                padding: 35px;
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

    <div class="shape shape1"></div>
    <div class="shape shape2"></div>
    <div class="shape shape3"></div>

    <div class="hero">
        <h1>E-Waste Management Portal</h1>
        <p>Admin Login</p>
    </div>

    <div class="login-card">

        <h2>Welcome Admin</h2>

        <?php if ($error != "") {
            echo "<div class='error'>$error</div>";
        } ?>

        <form method="POST">

            <input type="text" name="username" placeholder="Enter Username" required>

            <div class="password-box">
                <input type="password" name="password" id="password" placeholder="Enter Password" required>
                <i class="fa-solid fa-eye" id="eye" onclick="togglePassword()"></i>
            </div>

            <button type="submit" name="login">Login</button>

        </form>

    </div>

    <script>

        function togglePassword() {

            const pass = document.getElementById('password');
            const eye = document.getElementById('eye');

            if (pass.type === "password") {
                pass.type = "text";
                eye.classList.remove("fa-eye");
                eye.classList.add("fa-eye-slash");
            }
            else {
                pass.type = "password";
                eye.classList.remove("fa-eye-slash");
                eye.classList.add("fa-eye");
            }

        }

    </script>

</body>

</html>