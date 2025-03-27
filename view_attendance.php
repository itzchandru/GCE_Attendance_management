<?php
session_start();
include 'db.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch distinct subjects from the database
$subject_query = "SELECT DISTINCT subject_name FROM attendance ORDER BY subject_name";
$subject_result = $conn->query($subject_query);

// Initialize filters
$search_name = $_GET['search_name'] ?? '';
$search_roll = $_GET['search_roll'] ?? '';
$search_subject = $_GET['search_subject'] ?? '';
$search_date = $_GET['search_date'] ?? '';
$search_status = $_GET['search_status'] ?? '';
$search_week = $_GET['search_week'] ?? '';
$search_month = $_GET['search_month'] ?? '';

// Pagination variables
$limit = 10; // Number of rows per page
$page = $_GET['page'] ?? 1; // Current page
$offset = ($page - 1) * $limit; // Offset for SQL query

// Construct the SQL query with filters
$attendance_query = "SELECT students.name AS student_name, students.roll_number, 
                            attendance.subject_name, attendance.status, attendance.date 
                     FROM attendance 
                     JOIN students ON attendance.roll_number = students.roll_number 
                     WHERE 1=1";

// Apply filters if provided
if (!empty($search_name)) {
    $attendance_query .= " AND students.name LIKE '%$search_name%'";
}
if (!empty($search_roll)) {
    $attendance_query .= " AND students.roll_number LIKE '%$search_roll%'";
}
if (!empty($search_subject)) {
    $attendance_query .= " AND attendance.subject_name = '$search_subject'";
}
if (!empty($search_date)) {
    $attendance_query .= " AND attendance.date = '$search_date'";
}
if (!empty($search_status)) {
    $attendance_query .= " AND attendance.status = '$search_status'";
}
if (!empty($search_week)) {
    $attendance_query .= " AND WEEK(attendance.date, 1) = WEEK('$search_week', 1) AND YEAR(attendance.date) = YEAR('$search_week')";
}
if (!empty($search_month)) {
    $attendance_query .= " AND MONTH(attendance.date) = MONTH('$search_month') AND YEAR(attendance.date) = YEAR('$search_month')";
}

// Add pagination to the query
$attendance_query .= " ORDER BY attendance.date DESC LIMIT $limit OFFSET $offset";

// Debugging: Output the query to check for errors
// echo "Query: " . $attendance_query . "<br>";

// Execute the query
$attendance_result = $conn->query($attendance_query);

if (!$attendance_result) {
    die("Query failed: " . $conn->error); // Display the error if the query fails
}

// Fetch total number of rows for pagination
$total_rows_query = str_replace("LIMIT $limit OFFSET $offset", "", $attendance_query); // Remove LIMIT and OFFSET
$total_rows_result = $conn->query($total_rows_query);

if (!$total_rows_result) {
    die("Total rows query failed: " . $conn->error); // Display the error if the query fails
}

$total_rows = $total_rows_result->num_rows;
$total_pages = ceil($total_rows / $limit); // Calculate total pages
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Custom Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
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
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table {
            border-radius: 12px;
            overflow: hidden;
            min-width: 600px; /* Minimum width for the table */
        }

        .table thead {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: white;
        }

        .table th, .table td {
            vertical-align: middle;
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

        .dark-mode .table {
            background: #1e1e1e;
            color: #ffffff;
        }

        .dark-mode .table thead {
            background: #333;
        }

        .dark-mode .table tbody tr {
            background: #1e1e1e;
        }

        .dark-mode .table tbody tr:hover {
            background: #333;
        }

        /* Mobile-friendly adjustments */
        @media (max-width: 768px) {
            .dashboard-header h1 {
                font-size: 1.5rem;
            }
            .dashboard-header p {
                font-size: 0.9rem;
            }
            .card-title {
                font-size: 1.2rem;
            }
            .form-control, .form-select {
                font-size: 0.9rem;
            }
            .btn {
                font-size: 0.9rem;
            }
            .table th, .table td {
                font-size: 0.9rem;
                padding: 8px;
            }
        }

        /* Custom Pagination Styles */
.pagination .page-link {
    padding: 0.5rem 1rem;
    border-radius: 5px;
    margin: 0 2px;
    background-color: #6a11cb;
    color: white;
    border: none;
}

.pagination .page-link:hover {
    background-color: #2575fc;
}

.pagination .page-item.disabled .page-link {
    background-color: #ccc;
    color: #666;
    cursor: not-allowed;
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
            <h1>📋 View Attendance</h1>
            <p class="lead">Track and analyze attendance records with ease.</p>
        </div>

        <!-- Filter Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">🔍 Filter Options</h5>
                <form method="GET" class="row g-3">
                    <div class="col-12 col-md-6 col-lg-3">
                        <input type="text" name="search_name" class="form-control" placeholder="Student Name" value="<?= htmlspecialchars($search_name); ?>">
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <input type="text" name="search_roll" class="form-control" placeholder="Roll Number" value="<?= htmlspecialchars($search_roll); ?>">
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <select name="search_subject" class="form-control">
                            <option value="">All Subjects</option>
                            <?php while ($subject_row = $subject_result->fetch_assoc()) { ?>
                                <option value="<?= htmlspecialchars($subject_row['subject_name']); ?>" <?= $search_subject == $subject_row['subject_name'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($subject_row['subject_name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <input type="date" name="search_date" class="form-control" value="<?= htmlspecialchars($search_date); ?>">
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <select name="search_status" class="form-control">
                            <option value="">All Status</option>
                            <option value="Present" <?= $search_status == 'Present' ? 'selected' : ''; ?>>✅ Present</option>
                            <option value="Absent" <?= $search_status == 'Absent' ? 'selected' : ''; ?>>❌ Absent</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <input type="week" name="search_week" class="form-control" value="<?= htmlspecialchars($search_week); ?>">
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <input type="month" name="search_month" class="form-control" value="<?= htmlspecialchars($search_month); ?>">
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <button type="submit" class="btn btn-primary w-100">🔍 Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Attendance Table -->
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Roll Number</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($attendance_result->num_rows > 0) { ?>
                        <?php while ($row = $attendance_result->fetch_assoc()) { ?>
                            <tr>
                                <td><?= htmlspecialchars($row['student_name']); ?></td>
                                <td><?= htmlspecialchars($row['roll_number']); ?></td>
                                <td><?= htmlspecialchars($row['subject_name']); ?></td>
                                <td><?= $row['status'] == 'Present' ? '✅ Present' : '❌ Absent'; ?></td>
                                <td><?= htmlspecialchars($row['date']); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="5" class="text-center text-danger">⚠️ No records found!</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mt-4">
                <!-- Previous Button -->
                <?php if ($page > 1) { ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page - 1; ?>&search_name=<?= $search_name; ?>&search_roll=<?= $search_roll; ?>&search_subject=<?= $search_subject; ?>&search_date=<?= $search_date; ?>&search_status=<?= $search_status; ?>&search_week=<?= $search_week; ?>&search_month=<?= $search_month; ?>">
                            &lt; Previous
                        </a>
                    </li>
                <?php } ?>

                <!-- Next Button -->
                <?php if ($page < $total_pages) { ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1; ?>&search_name=<?= $search_name; ?>&search_roll=<?= $search_roll; ?>&search_subject=<?= $search_subject; ?>&search_date=<?= $search_date; ?>&search_status=<?= $search_status; ?>&search_week=<?= $search_week; ?>&search_month=<?= $search_month; ?>">
                            Next &gt;
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </nav>
    </div>
</div>
        <!-- Back Button -->
        <div class="text-center mt-4">
            <a href="admin_dashboard.php" class="btn btn-secondary">🔙 Back to Dashboard</a>
        </div>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Dark Mode Toggle
        function toggleDarkMode() {
            document.body.classList.toggle("dark-mode");
        }
    </script>
    <script>
    // Function to fetch and update the table
    function fetchAttendance() {
        const searchParams = new URLSearchParams(window.location.search);
        fetch(`fetch_attendance.php?${searchParams}`)
            .then(response => response.text())
            .then(data => {
                document.querySelector('.table-responsive').innerHTML = data;
            })
            .catch(error => console.error('Error fetching attendance data:', error));
    }

    // Fetch data immediately when the page loads
    fetchAttendance();

    // Refresh the table every 10 seconds
    setInterval(fetchAttendance, 10000);
</script>
</body>
</html>