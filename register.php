<?php
// Start session and include DB config
session_start();
include("config.php");

// Enable error reporting (for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = "";

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($name != "" && $email != "" && $password != "") {

        // Server-side password validation
        if (strlen($password) < 6) {
            $error = "Password must be at least 6 characters long!";
        } else {
            // Prevent SQL injection
            $name_safe = mysqli_real_escape_string($conn, $name);
            $email_safe = mysqli_real_escape_string($conn, $email);

            $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email_safe'");
            if (mysqli_num_rows($check) > 0) {
                $error = "Email already registered. Please login.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (name, email, password) VALUES ('$name_safe', '$email_safe', '$hashed_password')";
                if (mysqli_query($conn, $sql)) {
                    // Redirect to login page
                    header("Location: login.php");
                    exit();
                } else {
                    $error = "Database error: " . mysqli_error($conn);
                }
            }
        }

    } else {
        $error = "All fields are required!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>♻ Register - E-Waste Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            height: 100vh;
            background: #18191a;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

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

        .register-card {
            position: relative;
            z-index: 2;
            width: 400px;
            background: white;
            padding: 35px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            text-align: center;
            opacity: 0;
            transform: translateY(50px);
            animation: cardSlide 1s forwards 1.2s;
        }

        .register-card h2 {
            margin-bottom: 25px;
            color: #28a745;
            font-size: 26px;
        }

        .register-card input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
            transition: 0.3s;
        }

        .register-card input:focus {
            border-color: #28a745;
            outline: none;
            box-shadow: 0 0 8px rgba(40, 167, 69, 0.3);
        }

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

        .register-card button {
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

        .register-card button:hover {
            background: #20c997;
            transform: scale(1.05);
        }

        .error {
            color: red;
            margin-bottom: 10px;
            font-size: 14px;
            display: block;
        }

        .login-link {
            margin-top: 15px;
            font-size: 14px;
        }

        .login-link a {
            color: #28a745;
            text-decoration: none;
            font-weight: 500;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

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

        @media(max-width:500px) {
            .register-card {
                width: 90%;
                padding: 25px;
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

    <div class="diagonal-bg"></div>

    <div class="hero">
        <h1>♻ E-Waste Management Portal</h1>
        <p>Create your account to manage e-waste easily</p>
    </div>

    <div class="register-card">
        <h2>Create Account</h2>

        <!-- Show PHP error -->
        <?php if ($error != ""): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Password live error -->
        <div id="passwordError" class="error" style="display:none;">Password must be at least 6 characters long!</div>

        <form method="POST" id="registerForm">
            <input type="text" name="name" placeholder="Enter your name" required>
            <input type="email" name="email" placeholder="Enter your email" required>

            <div class="password-box">
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
                <i class="fa-solid fa-eye" id="eye" onclick="togglePassword()"></i>
            </div>

            <button type="submit" name="register">Register</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login</a>
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

        // Client-side live validation
        var passwordInput = document.getElementById("password");
        var passwordError = document.getElementById("passwordError");
        var form = document.getElementById("registerForm");

        passwordInput.addEventListener("input", function () {
            if (passwordInput.value.length > 0 && passwordInput.value.length < 6) {
                passwordError.style.display = "block";
            } else {
                passwordError.style.display = "none";
            }
        });

        form.addEventListener("submit", function (e) {
            if (passwordInput.value.length < 6) {
                e.preventDefault();
                passwordError.style.display = "block";
                passwordInput.focus();
            }
        });
    </script>

</body>

</html>