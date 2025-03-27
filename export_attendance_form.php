<?php
session_start();
include "db.php"; // Database connection

// Fetch unique roll numbers, subjects, and dates for dropdowns
$studentsQuery = "SELECT DISTINCT roll_number, name FROM students ORDER BY name ASC";
$studentsResult = $conn->query($studentsQuery);

$subjectsQuery = "SELECT DISTINCT subject_code, subject_name FROM attendance ORDER BY subject_name ASC";
$subjectsResult = $conn->query($subjectsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Attendance Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #6EE7B7, #3B82F6);
        }
        .container-custom {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .form-control:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 5px rgba(59, 130, 246, 0.5);
        }
    </style>
</head>
<body class="gradient-bg">
    <div class="container-custom mt-5">
        <!-- Back Button -->
        <div class="text-start mb-4">
            <a href="admin_dashboard.php" class="btn btn-outline-secondary">⬅️ Back</a>
        </div>

        <div class="text-center mb-4">
            <img src="./images/immigration.png" alt="Logo" class="mb-3" style="width: 80px;">
            <h3 class="text-center">📋 Export Attendance Report</h3>
        </div>
        <form action="export_attendance_word.php" method="GET">
            <!-- Student Filter -->
            <div class="mb-4">
                <label class="form-label">📌 Select Student</label>
                <select name="student" class="form-control">
                    <option value="">-- All Students --</option>
                    <?php while ($row = $studentsResult->fetch_assoc()) : ?>
                        <option value="<?= $row['roll_number']; ?>"><?= $row['name']; ?> (<?= $row['roll_number']; ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Subject Filter -->
            <div class="mb-4">
                <label class="form-label">📘 Select Subject</label>
                <select name="subject" class="form-control">
                    <option value="">-- All Subjects --</option>
                    <?php while ($row = $subjectsResult->fetch_assoc()) : ?>
                        <option value="<?= $row['subject_code']; ?>"><?= $row['subject_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Date Range Filter -->
            <div class="mb-4">
                <label class="form-label">📅 Select Date Range</label>
                <input type="text" name="date_range" id="dateRange" class="form-control" placeholder="Select Date Range">
            </div>

            <!-- Buttons -->
            <button type="submit" class="btn btn-primary w-100" id="exportButton">
                📥 Export Report
            </button>
            <button type="reset" class="btn btn-outline-secondary w-100 mt-3">🔄 Reset Form</button>
            <button type="button" class="btn btn-outline-info w-100 mt-3" onclick="previewData()">👀 Preview Data</button>
        </form>
    </div>

    <!-- Include Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Initialize Flatpickr for Date Range
        flatpickr("#dateRange", {
            mode: "range",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
            placeholder: "Select Date Range"
        });

        // Preview Data
        function previewData() {
            const form = document.querySelector('form');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();
            window.open(`preview_attendance.php?${params}`, '_blank');
        }
    </script>
</body>
</html>