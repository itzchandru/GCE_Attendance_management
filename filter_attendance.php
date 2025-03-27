<?php
session_start();
include 'db.php'; // Database connection

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch subjects for dropdown
$subject_query = "SELECT DISTINCT subject FROM attendance";
$subjects = $conn->query($subject_query);

// Handle filters
$where = "WHERE 1=1"; // Default condition
if (isset($_POST['filter'])) {
    if (!empty($_POST['subject'])) {
        $subject = $_POST['subject'];
        $where .= " AND a.subject = '$subject'";
    }
    if (!empty($_POST['date'])) {
        $date = $_POST['date'];
        $where .= " AND a.date = '$date'";
    }
    if (!empty($_POST['week'])) {
        $week = $_POST['week'];
        $where .= " AND YEARWEEK(a.date, 1) = YEARWEEK('$week', 1)";
    }
    if (!empty($_POST['search'])) {
        $search = $_POST['search'];
        $where .= " AND (s.name LIKE '%$search%' OR s.roll_number LIKE '%$search%')";
    }
}

// Fetch attendance data based on filters
$query = "SELECT a.*, s.name, s.roll_number, s.department FROM attendance a 
          JOIN students s ON a.student_id = s.id $where ORDER BY a.date DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - View Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4 p-4 bg-white shadow rounded">
    <h3 class="text-center">📊 Attendance Records</h3>

    <!-- Filter Form -->
    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label">📚 Select Subject</label>
            <select name="subject" class="form-select">
                <option value="">All Subjects</option>
                <?php while ($row = $subjects->fetch_assoc()): ?>
                    <option value="<?= $row['subject'] ?>"><?= $row['subject'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">📅 Select Date</label>
            <input type="date" name="date" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">📆 Select Week</label>
            <input type="date" name="week" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">🔍 Search (Name/Roll)</label>
            <input type="text" name="search" class="form-control" placeholder="Enter Name or Roll Number">
        </div>
        <div class="col-12 text-center">
            <button type="submit" name="filter" class="btn btn-primary">📊 Apply Filters</button>
            <a href="admin_dashboard.php" class="btn btn-secondary">🔄 Reset</a>
        </div>
    </form>

    <!-- Attendance Records Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>📛 Roll Number</th>
                <th>👨‍🎓 Student Name</th>
                <th>📚 Subject</th>
                <th>📅 Date</th>
                <th>✔ Attendance</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['roll_number'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['subject'] ?></td>
                <td><?= $row['date'] ?></td>
                <td><?= $row['status'] == 'Present' ? '✅ Present' : '❌ Absent' ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="admin_dashboard.php" class="btn btn-dark mt-3">🔙 Back to Dashboard</a>
</div>
</body>
</html>
