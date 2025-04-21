<?php
date_default_timezone_set('Asia/Manila'); // Adjust to your timezone
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $username = $_POST['username'];  // Get the username from the form

    // First, check if the email or username exists
    $check_stmt = $conn->prepare("SELECT id, username, email, password, otp, otp_expiry FROM user WHERE email = ? OR username = ?");
    $check_stmt->bind_param("ss", $email, $username);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check if the password is correct
        if (password_verify($password, $user['password'])) {
            // Check if OTP is expired or needs to be resent
            $otp_expiry = new DateTime($user['otp_expiry']);
            $now = new DateTime();

            if ($now > $otp_expiry) {
                // OTP expired, generate a new one and send via email
                $otp = rand(100000, 999999);
                $expiry = $now->modify('+10 minutes')->format("Y-m-d H:i:s");

                // Update OTP and its expiry in the database
                $update_stmt = $conn->prepare("UPDATE user SET otp = ?, otp_expiry = ? WHERE email = ? OR username = ?");
                $update_stmt->bind_param("ssss", $otp, $expiry, $email, $username);
                $update_stmt->execute();

                // Send OTP via email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'your_email@gmail.com'; // Your Gmail address
                    $mail->Password = 'your_gmail_app_password'; // Your Gmail App password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('your_email@gmail.com', 'Your Name');  // Your Gmail and name
                    $mail->addAddress($email);
                    $mail->Subject = "Your OTP Code";
                    $mail->Body    = "Your OTP is $otp. It expires in 10 minutes.";

                    $mail->send();

                    // Redirect to OTP verification page
                    header("Location: verify_otp.php?email=$email&username=$username");
                    exit();
                } catch (Exception $e) {
                    echo "Mailer Error: " . $mail->ErrorInfo;
                }
            } else {
                // OTP still valid, ask user to enter the OTP
                echo "<form method='POST' action='verify_otp.php'>
                        <input type='hidden' name='email' value='$email'>
                        <input type='hidden' name='username' value='$username'>
                        <div class='mb-3'>
                            <label for='otp' class='form-label'>OTP</label>
                            <input type='text' class='form-control' id='otp' name='otp' required>
                        </div>
                        <button type='submit' class='btn btn-primary w-100'>Verify OTP</button>
                    </form>";
            }
        } else {
            echo "Invalid email/username or password.";
        }
    } else {
        echo "Email or username not found.";
    }
}
?>
