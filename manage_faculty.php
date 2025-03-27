<?php
session_start();
include 'db.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Delete faculty
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = "DELETE FROM faculty WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: manage_faculty.php?msg=Faculty Deleted Successfully");
    exit();
}

// Fetch all faculty members
$faculty_query = "SELECT * FROM faculty";
$faculty_result = $conn->query($faculty_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Faculty</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-4">
    <h2 class="text-center">👨‍🏫 Manage Faculty</h2>
    <?php if (isset($_GET['msg'])) { echo "<div class='alert alert-success'>" . $_GET['msg'] . "</div>"; } ?>

    <a href="add_faculty.php" class="btn btn-success mb-3">➕ Add Faculty</a>

    <table class="table table-bordered table-striped mt-3">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Department</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $faculty_result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['name']; ?></td>
                    <td><?= $row['department']; ?></td>
                    <td>
                        <a href="manage_faculty.php?delete_id=<?= $row['id']; ?>" class="btn btn-danger btn-sm">❌ Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="admin_dashboard.php" class="btn btn-secondary">🔙 Back to Dashboard</a>
</div>

</body>
</html>
