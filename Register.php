<?php
date_default_timezone_set('Asia/Manila'); // Adjust to your timezone
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $otp = rand(100000, 999999);
    $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

    // First check if email exists
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "This email is already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (email, password, otp, otp_expiry) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $email, $password, $otp, $expiry);
        
        if ($stmt->execute()) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';  // Gmail SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'lorbelleganzan@gmail.com';  // Your Gmail address
                // Replace with your new 16-character App Password
                $mail->Password = 'owhi ofvh evnt zvxs
';  // Format: 4 groups of 4 characters
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('lorbelleganzan@gmail.com', 'lorbelle');  // Set your Gmail and name
                $mail->addAddress($email);
                $mail->Subject = "Your OTP Code";
                $mail->Body    = "Your OTP is $otp. It expires in 10 minutes.";

                $mail->send();
                echo "OTP sent to your email. <a href='verify.php'>Verify Now</a>";
            } catch (Exception $e) {
                echo "Mailer Error: " . $mail->ErrorInfo;
            }
        } else {
            echo "Error occurred during registration!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <h2 class="auth-title">Register</h2>
        
        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
        
        <div class="text-center mt-3">
            <a href="login.php">Already have an account? Login</a>
        </div>
    </div>
</body>
</html>
