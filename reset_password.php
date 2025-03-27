<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db.php'; // Ensure this file exists and has the correct connection

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);

    // Check if passwords match
    if ($password !== $confirmPassword) {
        $message = "❌ Passwords do not match!";
    } else {
        // Check if the token is valid and not expired
        $query = "SELECT * FROM faculty WHERE reset_token = ? AND reset_token_expiry > NOW()";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            die("Database error: " . $conn->error);
        }

        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updateQuery = "UPDATE faculty SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("ss", $hashedPassword, $token);
            $updateStmt->execute();

            $message = "✅ Password reset successfully! You can now login.";
        } else {
            $message = "❌ Invalid or expired token!";
        }

        $stmt->close();
        $conn->close();
    }
} elseif (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    header("Location: forgot_password.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #ffcc00; /* Bright & energetic solid color */
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            overflow: hidden;
            position: relative;
        }

        .reset-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            width: 350px;
            position: relative;
            z-index: 2;
        }

        h2 {
            font-weight: bold;
            color: #333;
        }

        .form-control {
            border-radius: 8px;
        }

        .btn-primary {
            border-radius: 8px;
            background: #ff5733; /* Bright button color */
            border: none;
            font-weight: bold;
        }

        .btn-primary:hover {
            background: #e64a19;
        }

        .link-text {
            color: #ff5733;
            font-weight: bold;
        }

        .link-text:hover {
            color: #e64a19;
            text-decoration: none;
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px;
            color: #6c757d;
        }
    </style>
</head>
<body>

    <div class="reset-container">
        <h2 class="p-3">🔑 Reset Password</h2>

        <?php if ($message): ?>
            <div class="alert alert-info text-center"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <div class="mb-3">
                <label class="form-label">
                    <i class="fas fa-key icon-label"></i> New Password
                </label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter new password" required>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('password', this)"></i>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">
                    <i class="fas fa-key icon-label"></i> Confirm Password
                </label>
                <div class="password-wrapper">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm new password" required>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm_password', this)"></i>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">Reset Password</button>
        </form>

        <div class="text-center mt-3">
            <a href="faculty_login.php" class="link-text">Back to Login</a>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword(fieldId, iconElement) {
            var passwordField = document.getElementById(fieldId);
            if (passwordField.type === "password") {
                passwordField.type = "text";
                iconElement.classList.remove("fa-eye");
                iconElement.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                iconElement.classList.remove("fa-eye-slash");
                iconElement.classList.add("fa-eye");
            }
        }
    </script>

</body>
</html>