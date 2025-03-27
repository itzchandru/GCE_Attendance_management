<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $time_slot = $_POST['time_slot'];
    $subject_code = $_POST['subject_code'];
    $subject_name = $_POST['subject_name'];
    $status = $_POST['status']; // Array of roll numbers with attendance status

    foreach ($status as $roll_number => $attendance_status) {
        // Fetch student name from the database
        $studentQuery = "SELECT name FROM students WHERE roll_number = ?";
        $stmt = $conn->prepare($studentQuery);
        $stmt->bind_param("s", $roll_number);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        $student_name = $student['name'] ?? 'Unknown';

        // Insert attendance record into database
        $insertQuery = "INSERT INTO attendance (roll_number, student_name, subject_code, subject_name, date, time_slot, status) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("sssssss", $roll_number, $student_name, $subject_code, $subject_name, $date, $time_slot, $attendance_status);
        $stmt->execute();
    }

    // Redirect to success page
    header("Location: attendance_success.php");
    exit();
} else {
    header("Location: mark_attendance.php");
    exit();
}
?>
