<?php
include 'config.php';

if (isset($_GET['code'])) {
    $verification_code = $_GET['code'];

    // Check if the verification code exists in the database
    $stmt = $conn->prepare("SELECT id FROM users WHERE verification_code = ?");
    $stmt->bind_param("s", $verification_code);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Code found, activate user
        $stmt->close();
        
        // Update the user to mark the email as verified
        $stmt = $conn->prepare("UPDATE users SET is_verified = 1 WHERE verification_code = ?");
        $stmt->bind_param("s", $verification_code);
        if ($stmt->execute()) {
            echo "Your account has been verified. You can now log in.";
        } else {
            echo "There was an issue verifying your account. Please try again later.";
        }
    } else {
        echo "Invalid verification link.";
    }
} else {
    echo "No verification code provided.";
}
?>
