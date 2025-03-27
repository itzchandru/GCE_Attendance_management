<?php
session_start();
include "db.php"; // Database connection

require 'vendor/autoload.php'; // Load PHPWord

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

// 🟢 Get Filter Inputs
$student = $_GET['student'] ?? '';  
$subject = $_GET['subject'] ?? '';  
$startDate = $_GET['start_date'] ?? ''; 
$endDate = $_GET['end_date'] ?? ''; 

// 🟢 Get Student Name for File Naming
$studentName = "Attendance_Report"; // Default name
if (!empty($student)) {
    $stmtStudent = $conn->prepare("SELECT name FROM students WHERE roll_number = ?");
    $stmtStudent->bind_param("s", $student);
    $stmtStudent->execute();
    $resultStudent = $stmtStudent->get_result();
    if ($row = $resultStudent->fetch_assoc()) {
        $studentName = "Attendance_" . str_replace(" ", "_", $row['name']);
    }
}

// 🟢 Set Headers for Word Document Download
header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
header("Content-Disposition: attachment; filename=$studentName.docx");

$phpWord = new PhpWord();
$section = $phpWord->addSection();

// 📌 Header Section
$section->addText("Government College of Engineering, Thanjavur", ['bold' => true, 'size' => 14, 'color' => '1D4ED8'], ['alignment' => 'center']);
$section->addText("📅 Report Date: " . date("d-M-Y"), ['italic' => true, 'size' => 10]);
$section->addTextBreak(1);

// 🟢 SQL Query with Filters
$query = "SELECT students.name AS student_name, students.roll_number, students.department, students.year, 
                 attendance.date, attendance.subject_code, attendance.status, subjects.subject_name
          FROM attendance 
          JOIN students ON attendance.roll_number = students.roll_number
          JOIN subjects ON attendance.subject_code = subjects.subject_code
          WHERE 1 = 1"; 

$params = [];
$types = "";

// Apply filters dynamically
if (!empty($student)) {
    $query .= " AND attendance.roll_number = ?";
    $params[] = $student;
    $types .= "s";
}
if (!empty($subject)) {
    $query .= " AND attendance.subject_code = ?";
    $params[] = $subject;
    $types .= "s";
}
if (!empty($startDate) && !empty($endDate)) {
    $query .= " AND attendance.date BETWEEN ? AND ?";
    $params[] = $startDate;
    $params[] = $endDate;
    $types .= "ss";
}

$query .= " ORDER BY attendance.date DESC";
$stmt = $conn->prepare($query);

// Bind parameters dynamically
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// 📝 Add Title
$section->addText("📌 Student Attendance Report", ['bold' => true, 'size' => 12, 'color' => '0D9488']);
$section->addTextBreak(1);

// 🟢 Check if Data Exists
if ($result->num_rows == 0) {
    $section->addText("⚠️ No attendance records found for the given filters.", ['bold' => true, 'color' => 'FF0000']);
} else {
    // 📊 Calculate Attendance Data
    $attendanceData = [];
    
    while ($row = $result->fetch_assoc()) {
        $roll = $row['roll_number'];
        $subjectCode = $row['subject_code'];
        $subjectName = $row['subject_name'];
        $status = $row['status'];

        if (!isset($attendanceData[$roll][$subjectCode])) {
            $attendanceData[$roll][$subjectCode] = [
                'subject_name' => $subjectName,
                'present' => 0,
                'absent' => 0,
                'total' => 0
            ];
        }
        
        // Count attendance
        $attendanceData[$roll][$subjectCode]['total']++;
        if ($status === 'Present') {
            $attendanceData[$roll][$subjectCode]['present']++;
        } else {
            $attendanceData[$roll][$subjectCode]['absent']++;
        }
    }

    // 📝 Add Table with Optimized Widths
    $tableStyle = [
        'borderSize' => 12, 
        'borderColor' => '000000', 
        'cellMargin' => 80
    ];
    $phpWord->addTableStyle('AttendanceTable', $tableStyle);
    $table = $section->addTable('AttendanceTable');

    // 📌 Define Column Widths (Compact)
    $columnWidths = [1000, 2000, 1500, 1000, 1500, 2500, 1000, 1000, 1000, 1000];

    // 🟢 Table Headers
    $headers = ["Roll No", "Student Name", "Department", "Year", "Subject Code", "Subject Name", "Total", "Present", "Absent", "Attendance %"];
    $table->addRow();
    foreach ($headers as $index => $header) {
        $table->addCell($columnWidths[$index], ['bgColor' => 'D1FAE5'])->addText($header, ['bold' => true, 'size' => 9]);
    }

    // 🟢 Add Attendance Records
    foreach ($attendanceData as $rollNumber => $subjects) {
        foreach ($subjects as $subjectCode => $data) {
            $percentage = ($data['total'] > 0) ? round(($data['present'] / $data['total']) * 100, 2) : 0;
            $color = ($percentage >= 75) ? 'C6F6D5' : 'FCA5A5';

            // Fetch student details safely
            $stmtStudent = $conn->prepare("SELECT name, department, year FROM students WHERE roll_number = ?");
            $stmtStudent->bind_param("s", $rollNumber);
            $stmtStudent->execute();
            $studentResult = $stmtStudent->get_result();
            $studentRow = $studentResult->fetch_assoc();

            $studentName = $studentRow['name'] ?? "N/A";
            $department = $studentRow['department'] ?? "N/A";
            $year = $studentRow['year'] ?? "N/A";
    
            $table->addRow();
            $table->addCell($columnWidths[0])->addText($rollNumber, ['size' => 9]);
            $table->addCell($columnWidths[1])->addText($studentName, ['size' => 9]);
            $table->addCell($columnWidths[2])->addText($department, ['size' => 9]);
            $table->addCell($columnWidths[3])->addText($year, ['size' => 9]);
            $table->addCell($columnWidths[4])->addText($subjectCode, ['size' => 9]);
            $table->addCell($columnWidths[5])->addText($data['subject_name'], ['size' => 9]);
            $table->addCell($columnWidths[6])->addText($data['total'], ['size' => 9]);
            $table->addCell($columnWidths[7])->addText($data['present'], ['size' => 9]);
            $table->addCell($columnWidths[8])->addText($data['absent'], ['size' => 9]);
            $table->addCell($columnWidths[9], ['bgColor' => $color])->addText("$percentage%", ['bold' => true, 'size' => 9]);
        }
    }
}

// 📝 Save and Output
$writer = IOFactory::createWriter($phpWord, 'Word2007');
$tempFile = tempnam(sys_get_temp_dir(), 'attendance_') . '.docx';
$writer->save($tempFile);
readfile($tempFile);
unlink($tempFile);
exit();
?>
