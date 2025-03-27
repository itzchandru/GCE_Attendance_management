<?php
include 'db.php'; // Ensure database connection is included

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $roll_number = $_POST['roll_number'];
    $department = $_POST['department'];
    $year = intval($_POST['year']);

    // Insert into students table
    $stmt = $conn->prepare("INSERT INTO students (name, roll_number, department, year) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $name, $roll_number, $department, $year);

    if ($stmt->execute()) {
        echo "<script>alert('Student added successfully!'); window.location.href='add_student.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4 p-4 bg-white shadow rounded">
    <h3 class="text-center">📝 Add Student</h3>
    
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">👤 Name:</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">📛 Roll Number:</label>
            <input type="text" name="roll_number" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">🏫 Department:</label>
            <input type="text" name="department" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">📅 Year:</label>
            <select name="year" class="form-control" required>
                <option value="1">1st Year</option>
                <option value="2">2nd Year</option>
                <option value="3">3rd Year</option>
                <option value="4">4th Year</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success w-100">➕ Add Student</button>
    </form>

    <a href="admin_dashboard.php" class="btn btn-secondary mt-3 w-100">🔙 Back to Dashboard</a>
</div>
</body>
</html>
