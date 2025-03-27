<?php
session_start();
include 'db.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die("Unauthorized access.");
}

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
$attendance_result = $conn->query($attendance_query);

if (!$attendance_result) {
    die("Query failed: " . $conn->error);
}

// Generate the table HTML
$table_html = '<table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Roll Number</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>';

if ($attendance_result->num_rows > 0) {
    while ($row = $attendance_result->fetch_assoc()) {
        $table_html .= '<tr>
                            <td>' . htmlspecialchars($row['student_name']) . '</td>
                            <td>' . htmlspecialchars($row['roll_number']) . '</td>
                            <td>' . htmlspecialchars($row['subject_name']) . '</td>
                            <td>' . ($row['status'] == 'Present' ? '✅ Present' : '❌ Absent') . '</td>
                            <td>' . htmlspecialchars($row['date']) . '</td>
                        </tr>';
    }
} else {
    $table_html .= '<tr>
                        <td colspan="5" class="text-center text-danger">⚠️ No records found!</td>
                    </tr>';
}

$table_html .= '</tbody></table>';

// Output the table HTML
echo $table_html;
?>