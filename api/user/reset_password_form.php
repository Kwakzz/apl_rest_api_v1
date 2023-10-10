<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config/database.php';
require_once '../../class/user.php';

// allow all kinds of requests
header('Access-Control-Allow-Origin: *');

// allow only GET requests
header('Access-Control-Allow-Methods: GET');

// content type
header('Content-Type: text/html');

// connect to the database
$database = new Database();
$db = $database->getConnection();

// create a new user object
$user = new User($db);

// get email_address and password reset token from the query parameters
$user->hashed_user_id = $_GET['uid'];
$user->activation_code = $_GET['password_reset_token'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0; /* Remove default margin */
            padding: 0; /* Remove default padding */
        }

        .container {
            max-width: 400px;
            margin: 0 auto; /* Center the container horizontally */
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            box-sizing: border-box; /* Include padding and border in the container's total width */
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .password-wrapper {
            position: relative; /* Create a relative positioning context for the password visibility toggle */
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Password Reset</h1>
    <form id="passwordResetForm" action="reset_password.php" method="POST">
        <!-- Add hidden input fields for UID and password reset token -->
        <input type="hidden" name="uid" value="<?php echo $_GET['uid']; ?>">
        <input type="hidden" name="password_reset_token" value="<?php echo $_GET['password_reset_token']; ?>">

        <div class="form-group">
            <label for="password">New Password:</label>
            <div class="password-wrapper">
                <input type="password" id="password" name="password" required>
                <span class="password-toggle" onclick="togglePasswordVisibility('password')">Show</span>
            </div>
        </div>
        <div class="form-group">
            <label for="confirmPassword">Confirm Password:</label>
            <div class="password-wrapper">
                <input type="password" id="confirmPassword" name="confirmPassword" required>
                <span class="password-toggle" onclick="togglePasswordVisibility('confirmPassword')">Show</span>
            </div>
            <p class="error" id="passwordError"></p>
        </div>
        <button type="submit">Reset Password</button>
    </form>
</div>

<script>
    // JavaScript to toggle password visibility
    function togglePasswordVisibility(inputId) {
        const passwordInput = document.getElementById(inputId);
        const passwordToggle = passwordInput.nextElementSibling;

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordToggle.textContent = 'Hide';
        } else {
            passwordInput.type = 'password';
            passwordToggle.textContent = 'Show';
        }
    }

    // JavaScript to validate password and confirm password
    document.getElementById("passwordResetForm").addEventListener("submit", function(event) {
        var password = document.getElementById("password").value;
        var confirmPassword = document.getElementById("confirmPassword").value;
        var passwordError = document.getElementById("passwordError");

        // Reset error message
        passwordError.textContent = "";

        // Password validation using regex (at least 8 characters, at least one uppercase letter, one lowercase letter, one special character and one number)
        var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&+=!])(?!.*\s).{8,}$/;

        if (!password.match(passwordRegex)) {
            passwordError.textContent = "Password must be at least 8 characters, including one uppercase letter, one lowercase letter, and one number.";
            event.preventDefault(); // Prevent form submission
        } else if (password !== confirmPassword) {
            passwordError.textContent = "Passwords do not match.";
            event.preventDefault(); // Prevent form submission
        }
    });
</script>
</body>
</html>
