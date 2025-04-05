<?php
session_start();
include 'config.php';

// Define variables and initialize with empty values
$username = $email = $password = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = "";

// Process form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $username = trim($_POST["username"]);

        // Check if the username already exists
        $sql = "SELECT id FROM users WHERE username = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $username_err = "This username is already taken.";
            }
            $stmt->close();
        }
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        $email = trim($_POST["email"]);

        // Check if the email already exists
        $sql = "SELECT id FROM users WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $email_err = "This email is already registered.";
            }
            $stmt->close();
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm the password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if ($password != $confirm_password) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before inserting into database
    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {

        // Prepare an insert statement
        $verification_code = md5(uniqid(rand(), true)); // Generate a unique code for verification
        $sql = "INSERT INTO users (username, email, password, verification_code, is_verified) VALUES (?, ?, ?, ?, 0)";

        if ($stmt = $conn->prepare($sql)) {
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssss", $param_username, $param_email, $param_password, $verification_code);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Prepare the verification link
                $verification_link = "http://localhost/auth_system/verify.php?code=$verification_code";

                // Send the verification email
                $subject = "Verify Your Account";
                $message = "Hello $username,\n\nPlease verify your account by clicking the link below:\n$verification_link\n\nThank you!";
                $headers = "From: no-reply@yourdomain.com";

                // Send the email
                if (mail($email, $subject, $message, $headers)) {
                    echo "<div class='success-message'>A verification link has been sent to your email. Please check your inbox to verify your account.</div>";
                } else {
                    echo "<div class='error-message'>Failed to send the verification email. Please try again later.</div>";
                }
            } else {
                echo "<div class='error-message'>Something went wrong. Please try again later.</div>";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Authentication System</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-image: url('https://www.startpage.com/av/proxy-image?piurl=https%3A%2F%2Ftse1.mm.bing.net%2Fth%3Fid%3DOIP.mLCpICilgHD2jQVRxI5aRAHaEG%26pid%3DApi&sp=1743824960Tfb34b38ac882779d3552bf616dc682d4ece0face1dc30584aa2300a15a929a95'); /* Add your anime background image here */
            background-size: cover;
            background-position: center;
            color: white;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .register-container {
            background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent white background */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h1 {
            text-align: center;
            color: #333;
            font-size: 28px;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-group input:focus {
            outline: none;
            border-color: #4CAF50;
        }

        .form-group .error {
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }

        .form-group button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #45a049;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }

        p {
            text-align: center;
            font-size: 14px;
        }

        p a {
            color: #4CAF50;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="register-container">
        <h1>Register</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" value="<?php echo $username; ?>" required>
                <div class="error"><?php echo $username_err; ?></div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?php echo $email; ?>" required>
                <div class="error"><?php echo $email_err; ?></div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
                <div class="error"><?php echo $password_err; ?></div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
                <div class="error"><?php echo $confirm_password_err; ?></div>
            </div>

            <div class="form-group">
                <button type="submit">Register</button>
            </div>

            <p>Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </div>

</body>
</html>
