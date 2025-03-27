<?php
session_start();
include 'db.php';

// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "❌ Invalid CSRF token!";
    } else {
        // Sanitize inputs
        $name = htmlspecialchars(trim($_POST['name']));
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

        // Email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "❌ Invalid email format!";
        } 
        // Password strength validation
        elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
            $error = "❌ Password must be at least 8 characters, include a number, a letter, and a special character!";
        } 
        // Confirm password check
        elseif ($password !== $confirmPassword) {
            $error = "❌ Passwords do not match!";
        } 
        else {
            // Secure password hashing
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Check if the email already exists
            $checkQuery = "SELECT * FROM admin WHERE email = ?";
            $stmt = $conn->prepare($checkQuery);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error = "❌ Email is already registered!";
            } else {
                // Insert new admin
                $query = "INSERT INTO admin (name, email, password) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sss", $name, $email, $hashedPassword);

                if ($stmt->execute()) {
                    $_SESSION['success'] = "✅ Registration successful! Please log in.";
                    header("Location: admin_login.php");
                    exit();
                } else {
                    $error = "❌ Registration failed! Please try again.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #1a1a1a; /* Dark background */
            overflow: hidden;
            font-family: 'Arial', sans-serif;
        }

        /* Particle background */
        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            background: #1a1a1a; /* Dark background */
            z-index: 1;
        }

        /* Registration container */
        .register-container {
            background: rgba(255, 255, 255, 0.1); /* Glassmorphism effect */
            padding: 30px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            text-align: start;
            width: 100%;
            max-width: 450px;
            position: relative;
            z-index: 2;
            animation: fadeIn 1.5s ease-in-out;
        }

        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        h2 {
            font-weight: bold;
            color: #ffffff; /* White text */
            margin-bottom: 20px;
            animation: slideIn 1s ease-in-out;
        }

        @keyframes slideIn {
            0% { opacity: 0; transform: translateX(-50px); }
            100% { opacity: 1; transform: translateX(0); }
        }

        .form-control {
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1); /* Glassmorphism effect */
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff; /* White text */
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5); /* Light placeholder text */
        }

        .btn-custom {
            border-radius: 8px;
            background: #4A55A2;
            color: #fff;
            font-weight: bold;
            padding: 10px 20px;
            margin: 10px 0;
            width: 100%;
            text-align: center;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            background: #3B4E9A;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
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
            color: rgba(17, 16, 16, 0.5); /* Light icon color */
        }

        .password-strength {
            margin-top: 5px;
            font-size: 14px;
            color: #fff; /* White text */
        }

        .link-text {
            color: #4A55A2;
            font-weight: bold;
        }

        .link-text:hover {
            color: #3B4E9A;
            text-decoration: none;
        }
        .form-label{
            color:#ffffff;
        }
        #link{
            color:#ffffff;
        }
    </style>
</head>
<body>

    <!-- Particle background -->
    <div id="particles-js"></div>

    <!-- Registration container -->
    <div class="register-container">
        <h2 class="text-center mb-3">🔐 Admin Registration</h2>

        <?php if (isset($error)) { ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="mb-3">
                <label class="form-label">👤 Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">📧 Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <div class="mb-3">
                <label class="form-label">🔑 Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Create a strong password" required>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('password', this)"></i>
                </div>
                <div id="password-strength" class="password-strength"></div>
            </div>
            <div class="mb-3">
                <label class="form-label">🔑 Confirm Password</label>
                <div class="password-wrapper">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Re-enter your password" required>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm_password', this)"></i>
                </div>
            </div>
            <button type="submit" class="btn btn-custom w-100 py-2">Register</button>
        </form>

        <p id="link" class="mt-3 text-center">Already have an account? <a href="admin_login.php" class="link-text">Login here</a></p>
    </div>

    <!-- Include Particles.js -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        // Particles.js configuration
        particlesJS('particles-js', {
            particles: {
                number: {
                    value: 80,
                    density: {
                        enable: true,
                        value_area: 800
                    }
                },
                color: {
                    value: '#ffffff' // White particles
                },
                shape: {
                    type: 'circle',
                    stroke: {
                        width: 0,
                        color: '#000000'
                    },
                    polygon: {
                        nb_sides: 5
                    }
                },
                opacity: {
                    value: 0.5,
                    random: false,
                    anim: {
                        enable: false,
                        speed: 1,
                        opacity_min: 0.1,
                        sync: false
                    }
                },
                size: {
                    value: 3,
                    random: true,
                    anim: {
                        enable: false,
                        speed: 40,
                        size_min: 0.1,
                        sync: false
                    }
                },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: '#ffffff',
                    opacity: 0.4,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 6,
                    direction: 'none',
                    random: false,
                    straight: false,
                    out_mode: 'out',
                    bounce: false,
                    attract: {
                        enable: false,
                        rotateX: 600,
                        rotateY: 1200
                    }
                }
            },
            interactivity: {
                detect_on: 'canvas',
                events: {
                    onhover: {
                        enable: true,
                        mode: 'repulse'
                    },
                    onclick: {
                        enable: true,
                        mode: 'push'
                    },
                    resize: true
                },
                modes: {
                    grab: {
                        distance: 400,
                        line_linked: {
                            opacity: 1
                        }
                    },
                    bubble: {
                        distance: 400,
                        size: 40,
                        duration: 2,
                        opacity: 8,
                        speed: 3
                    },
                    repulse: {
                        distance: 200,
                        duration: 0.4
                    },
                    push: {
                        particles_nb: 4
                    },
                    remove: {
                        particles_nb: 2
                    }
                }
            },
            retina_detect: true
        });
    </script>

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

        // Real-time password strength feedback
        const passwordField = document.getElementById('password');
        const strengthText = document.getElementById('password-strength');

        passwordField.addEventListener('input', () => {
            const password = passwordField.value;
            let strength = 0;

            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;

            let strengthMessage = '';
            if (strength === 4) {
                strengthMessage = 'Strong password!';
                strengthText.style.color = 'green';
            } else if (strength >= 2) {
                strengthMessage = 'Medium strength';
                strengthText.style.color = 'orange';
            } else {
                strengthMessage = 'Weak password';
                strengthText.style.color = 'red';
            }

            strengthText.textContent = strengthMessage;
        });
    </script>

</body>
</html>