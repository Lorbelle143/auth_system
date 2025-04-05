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
    <title>Home - Authentication System</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your stylesheets -->
</head>
<body>
    <div class="container">
        <h1>Welcome to the Authentication System</h1>
        <p>Please log in to continue, or register if you don't have an account yet.</p>
        
        <div class="auth-links">
            <a href="login.php">Login</a> | <a href="register.php">Register</a>
        </div>
    </div>
</body>
</html>
