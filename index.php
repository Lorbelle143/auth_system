<?php
session_start();

// Check if user is logged in
if (isset($_SESSION['username'])) {
    // If logged in, redirect to dashboard or home page
    header("Location: dashboard.php"); // Change this to your actual dashboard or home page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication</title>
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('https://www.startpage.com/av/proxy-image?piurl=https%3A%2F%2Ftse2.mm.bing.net%2Fth%3Fid%3DOIP.-qfzIT4CgqCFPRo5OsjItwHaMJ%26pid%3DApi&sp=1743826271T7c93ac00e94f0814063f50464d4b909d688c1d9fa16e227d4e882e01a00ff28b') no-repeat center center fixed; /* Change this URL to your anime image URL */
            background-size: cover; /* Make the background cover the whole page */
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .auth-container {
            text-align: center;
            background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent white background */
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 20px;
        }

        p {
            color: #777;
            margin-bottom: 20px;
            font-size: 1rem;
        }

        .auth-buttons {
            display: flex;
            justify-content: space-around;
        }

        .auth-btn {
            display: inline-block;
            padding: 12px 20px;
            font-size: 1rem;
            color: #fff;
            background-color: #007BFF;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .auth-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h1>Welcome dear</h1>
        <p>Log in to continue or register for a new account.</p>

        <div class="auth-buttons">
            <a href="login.php" class="auth-btn">Login</a>
            <a href="register.php" class="auth-btn">Register</a>
        </div>
    </div>
</body>
</html>
