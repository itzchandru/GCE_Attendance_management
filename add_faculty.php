<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $department = $_POST['department'];

    $query = "INSERT INTO faculty (name, department) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $name, $department);
    
    if ($stmt->execute()) {
        header("Location: manage_faculty.php?msg=Faculty Added Successfully");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Faculty</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-4">
    <h2 class="text-center">➕ Add Faculty</h2>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Name:</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Department:</label>
            <input type="text" name="department" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">✅ Add Faculty</button>
        <a href="manage_faculty.php" class="btn btn-secondary">🔙 Back</a>
    </form>
</div>

</body>
</html>
