<?php
session_start();
include 'db.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_name = $_SESSION['admin_name'];

// Fetch total counts
$total_students = $conn->query("SELECT COUNT(*) AS total_students FROM students")->fetch_assoc()['total_students'];
$total_faculty = $conn->query("SELECT COUNT(*) AS total_faculty FROM faculty")->fetch_assoc()['total_faculty'];
$total_subjects = $conn->query("SELECT COUNT(*) AS total_subjects FROM subjects")->fetch_assoc()['total_subjects'];
$total_attendance = $conn->query("SELECT COUNT(*) AS total_attendance FROM attendance")->fetch_assoc()['total_attendance'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        /* Dashboard container */
        .dashboard-container {
            background: rgba(255, 255, 255, 0.1); /* Glassmorphism effect */
            padding: 30px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 100%;
            max-width: 1000px;
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

        .card {
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1); /* Glassmorphism effect */
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff; /* White text */
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3);
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

        .logout-btn {
            background: #FF4D4D;
            color: white;
            font-weight: bold;
        }

        .logout-btn:hover {
            background: #E63939;
        }

        .form-label {
            color: #ffffff;
        }

        #link {
            color: #ffffff;
        }
    </style>
</head>
<body>

    <!-- Particle background -->
    <div id="particles-js"></div>

    <!-- Dashboard container -->
    <div class="dashboard-container mt-4">
        <h2>📊 Admin Dashboard</h2>
        <h5 style="color: #ffffff;">Welcome, <span class="text-primary"><?php echo $admin_name; ?></span></h5>

        <!-- Stats Cards -->
        <div class="row mt-4">
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card text-center p-3">
                    <h4>👨‍🎓 Students</h4>
                    <h2><?php echo $total_students; ?></h2>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card text-center p-3">
                    <h4>👨‍🏫 Faculty</h4>
                    <h2><?php echo $total_faculty; ?></h2>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card text-center p-3">
                    <h4>📘 Subjects</h4>
                    <h2><?php echo $total_subjects; ?></h2>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card text-center p-3">
                    <h4>📋 Attendance</h4>
                    <h2><?php echo $total_attendance; ?></h2>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="container text-center mt-4">
            <div class="row g-3">
                <div class="col-md-6"><a href="manage_students.php" class="btn btn-custom w-100">👨‍🎓 Manage Students</a></div>
                <div class="col-md-6"><a href="manage_faculty.php" class="btn btn-custom w-100">👨‍🏫 Manage Faculty</a></div>
                <div class="col-md-6"><a href="view_attendance.php" class="btn btn-custom w-100">📋 View Attendance</a></div>
                <div class="col-md-6"><a href="analytics.php" class="btn btn-custom w-100">📊 View Analytics</a></div>
                <div class="col-md-6"><a href="export_attendance_form.php" class="btn btn-success btn-custom w-100">📥 Export Report</a></div>
                <div class="col-md-6"><a href="admin_logout.php" class="btn btn-danger btn-custom w-100">🚪 Logout</a></div>
            </div>
        </div>
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

</body>
</html>