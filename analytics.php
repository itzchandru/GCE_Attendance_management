<?php
session_start();
include 'db.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Filter Variables
$filter = $_GET['filter'] ?? '';
$subject_filter = $_GET['subject'] ?? '';
$student_filter = $_GET['student'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// Initialize filter conditions
$filter_conditions = [];

// Apply predefined date filters
if ($filter === "today") {
    $filter_conditions[] = "attendance.date = CURDATE()";
} elseif ($filter === "yesterday") {
    $filter_conditions[] = "attendance.date = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
} elseif ($filter === "week") {
    $filter_conditions[] = "attendance.date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($filter === "month") {
    $filter_conditions[] = "attendance.date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
}

// Apply custom date range filter
if (!empty($start_date) && !empty($end_date)) {
    $filter_conditions[] = "attendance.date BETWEEN '" . $conn->real_escape_string($start_date) . "' AND '" . $conn->real_escape_string($end_date) . "'";
}

// Apply subject filter
if (!empty($subject_filter) && $subject_filter !== "all") {
    $filter_conditions[] = "attendance.subject_name = '" . $conn->real_escape_string($subject_filter) . "'";
}

// Apply student filter
if (!empty($student_filter) && $student_filter !== "all") {
    $filter_conditions[] = "attendance.roll_number = '" . $conn->real_escape_string($student_filter) . "'";
}

// Construct WHERE clause
$where_clause = !empty($filter_conditions) ? " WHERE " . implode(" AND ", $filter_conditions) : "";

// Total present count
$total_present_query = "SELECT COUNT(*) AS count FROM attendance WHERE status='Present' " . (!empty($where_clause) ? " AND " . implode(" AND ", $filter_conditions) : "");
$total_present = $conn->query($total_present_query)->fetch_assoc()['count'];

// Total attendance records
$total_records_query = "SELECT COUNT(*) AS count FROM attendance " . (!empty($where_clause) ? $where_clause : "");
$total_records = $conn->query($total_records_query)->fetch_assoc()['count'];

// Calculate overall attendance percentage
$overall_attendance = ($total_records > 0) ? ($total_present / $total_records) * 100 : 0;

// Fetch all subjects for dropdown
$subject_result = $conn->query("SELECT DISTINCT subject_name FROM attendance");

// Fetch all students for dropdown
$student_result = $conn->query("SELECT DISTINCT roll_number, student_name FROM attendance");

// Fetch student-wise attendance
$student_attendance_query = "
    SELECT roll_number, student_name, 
           SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) AS present_count, 
           COUNT(*) AS total_count 
    FROM attendance
    " . (!empty($where_clause) ? $where_clause . " AND roll_number IS NOT NULL " : " WHERE roll_number IS NOT NULL ") . "
    GROUP BY roll_number, student_name 
    ORDER BY student_name ASC
";

$student_attendance_result = $conn->query($student_attendance_query);

// Prepare data for student-wise chart
$student_names = [];
$student_present_counts = [];
$student_absent_counts = [];

while ($row = $student_attendance_result->fetch_assoc()) {
    $student_names[] = $row['student_name'];
    $student_present_counts[] = $row['present_count'];
    $student_absent_counts[] = $row['total_count'] - $row['present_count']; // Calculate Absent Count
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Analytics</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <style>
        /* Custom Styles */
        body {
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .dashboard-header {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .progress {
            height: 30px;
            border-radius: 15px;
        }

        .progress-bar {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
        }

        .fab {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            font-size: 24px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .dark-mode {
            background: #121212;
            color: #ffffff;
        }

        .dark-mode .card {
            background: #1e1e1e;
            color: #ffffff;
        }

        .dark-mode .form-control, .dark-mode .form-select {
            background: #333;
            color: #fff;
            border: 1px solid #444;
        }

        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body>
    <!-- Dark Mode Toggle -->
    <div class="form-check form-switch position-fixed top-0 end-0 m-3">
        <input class="form-check-input" type="checkbox" id="darkModeToggle" onclick="toggleDarkMode()">
        <label class="form-check-label" for="darkModeToggle">Dark Mode</label>
    </div>
    <div class="container mt-4">
        <!-- Dashboard Header -->
        <div class="dashboard-header text-center">
            <h1>📊 Attendance Analytics</h1>
            <p class="lead">Track and analyze attendance data with ease.</p>
        </div>

        <!-- Filter Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">🔍 Filter Options</h5>
                <form method="GET" class="row">
                    <div class="col-md-2 mb-3">
                        <select name="filter" class="form-select">
                            <option value="">All Time</option>
                            <option value="today" <?= ($filter === "today") ? "selected" : "" ?>>Today</option>
                            <option value="yesterday" <?= ($filter === "yesterday") ? "selected" : "" ?>>Yesterday</option>
                            <option value="week" <?= ($filter === "week") ? "selected" : "" ?>>Last 7 Days</option>
                            <option value="month" <?= ($filter === "month") ? "selected" : "" ?>>Last 1 Month</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                            <input type="date" name="start_date" class="form-control" value="<?= $start_date; ?>">
                        </div>
                    </div>

                    <div class="col-md-2 mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                            <input type="date" name="end_date" class="form-control" value="<?= $end_date; ?>">
                        </div>
                    </div>

                    <div class="col-md-2 mb-3">
                        <select name="subject" class="form-select">
                            <option value="all">All Subjects</option>
                            <?php while ($row = $subject_result->fetch_assoc()) { ?>
                                <option value="<?= $row['subject_name']; ?>" <?= ($subject_filter === $row['subject_name']) ? "selected" : "" ?>>
                                    <?= $row['subject_name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <select name="student" class="form-select">
                            <option value="all">All Students</option>
                            <?php while ($row = $student_result->fetch_assoc()) { ?>
                                <option value="<?= $row['roll_number']; ?>" <?= ($student_filter === $row['roll_number']) ? "selected" : "" ?>>
                                    <?= $row['student_name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">📊 Summary</h5>
                <p><strong>Total Present:</strong> <?= $total_present; ?></p>
                <p><strong>Total Absent:</strong> <?= ($total_records - $total_present); ?></p>
                <p><strong>Total Peroids:</strong> <?= $total_records; ?></p>
                <div class="progress mb-3">
                    <div class="progress-bar" role="progressbar" style="width: <?= $overall_attendance; ?>%;" aria-valuenow="<?= $overall_attendance; ?>" aria-valuemin="0" aria-valuemax="100">
                        <?= round($overall_attendance, 2); ?>%
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-center">📌 Overall Attendance</h5>
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-center">📌 Student-wise Attendance</h5>
                        <canvas id="studentAttendanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Button (FAB) -->
    <button class="btn btn-primary fab" onclick="location.reload()">
        <i class="bi bi-arrow-clockwise"></i>
    </button>

    

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Dark Mode Toggle
        function toggleDarkMode() {
            document.body.classList.toggle("dark-mode");
        }

        // Overall Attendance Chart
        new Chart(document.getElementById('attendanceChart'), {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Absent'],
                datasets: [{
                    data: [<?= $total_present; ?>, <?= ($total_records - $total_present); ?>],
                    backgroundColor: ['#28a745', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuad'
                }
            }
        });

        // Student-wise Attendance Chart
        new Chart(document.getElementById('studentAttendanceChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($student_names); ?>,
                datasets: [
                    {
                        label: 'Present',
                        data: <?= json_encode($student_present_counts); ?>,
                        backgroundColor: '#007bff'
                    },
                    {
                        label: 'Absent',
                        data: <?= json_encode($student_absent_counts); ?>,
                        backgroundColor: '#dc3545'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { position: 'top' }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuad'
                }
            }
        });
    </script>
</body>
</html>