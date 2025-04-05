<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
    $verification_code = md5(uniqid(rand(), true));

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, verification_code) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $verification_code);

    if ($stmt->execute()) {
        $verification_link = "http://localhost/auth_system/verify.php?code=$verification_code";
        echo "Verify your account using this link: <a href='$verification_link'>$verification_link</a>";
    } else {
        echo "Registration failed!";
    }
}