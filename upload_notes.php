<?php
session_start();
include "db.php"; // Include database connection

// Check if the user is logged in as a faculty member
if (!isset($_SESSION['faculty_id'])) {
    header("Location: faculty_login.php"); // Redirect to login if not logged in
    exit();
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file_name = $_FILES['notes']['name'];
    $file_tmp = $_FILES['notes']['tmp_name'];
    $file_path = "uploads/notes/" . basename($file_name);

    if (move_uploaded_file($file_tmp, $file_path)) {
        $message = "File uploaded successfully!";
    } else {
        $message = "Failed to upload file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Upload Notes</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
  <div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">📤 Upload Notes</h1>
    <?php if (isset($message)): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        <?php echo $message; ?>
      </div>
    <?php endif; ?>
    <form action="upload_notes.php" method="post" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
      <div class="mb-4">
        <label for="notes" class="block text-gray-700">Select Notes File:</label>
        <input type="file" name="notes" id="notes" class="w-full p-2 border rounded" required>
      </div>
      <button type="submit" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Upload</button>
    </form>
  </div>
</body>
</html>