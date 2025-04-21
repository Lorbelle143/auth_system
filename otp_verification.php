<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $otp = $_POST['otp'];

    // Check if the OTP entered is correct
    $check_stmt = $conn->prepare("SELECT otp, otp_expiry FROM user WHERE email = ? OR username = ?");
    $check_stmt->bind_param("ss", $email, $username);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        $otp_expiry = new DateTime($user['otp_expiry']);
        $now = new DateTime();

        if ($now <= $otp_expiry) {
            // OTP is valid, check if the entered OTP matches
            if ($otp == $user['otp']) {
                echo "OTP verified successfully! You are now logged in.";
                // Redirect to the dashboard or home page after successful login
                header("Location: dashboard.php");
                exit();
            } else {
                echo "Invalid OTP. Please try again.";
            }
        } else {
            echo "OTP has expired. Please request a new one.";
        }
    } else {
        echo "No user found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <h2 class="auth-title">Verify OTP</h2>

        <form method="POST">
            <input type="hidden" name="email" value="<?php echo $_GET['email']; ?>">
            <input type="hidden" name="username" value="<?php echo $_GET['username']; ?>">
            <div class="mb-3">
                <label for="otp" class="form-label">Enter OTP</label>
                <input type="text" class="form-control" id="otp" name="otp" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Verify OTP</button>
        </form>
    </div>
</body>
</html>
