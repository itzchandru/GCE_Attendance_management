<?php
$servername = "localhost";
$username = "root";  // Change if needed
$password = "";
$database = "new_attendance_db"; // Change if needed

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Debugging: Check if file is uploaded
if (!isset($_FILES['attendance_file']) || $_FILES['attendance_file']['error'] !== UPLOAD_ERR_OK) {
    die("Error: No file uploaded!");
}

// ✅ Get the uploaded file
$file = $_FILES['attendance_file']['tmp_name'];

if (!file_exists($file)) {
    die("Error: Uploaded file not found!");
}

// ✅ Open CSV file
$handle = fopen($file, "r");

if ($handle === FALSE) {
    die("Error opening file.");
}

// ✅ Skip the first row (headers)
fgetcsv($handle);

// ✅ Read and insert data
while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    if (count($data) < 9) continue; // Skip incomplete rows

    $date = mysqli_real_escape_string($conn, trim($data[1]));
    $roll_number = mysqli_real_escape_string($conn, trim($data[2]));
    $student_name = mysqli_real_escape_string($conn, trim($data[3]));
    $time_slot = mysqli_real_escape_string($conn, trim($data[4]));
    $subject_code = mysqli_real_escape_string($conn, trim($data[5]));
    $subject_name = mysqli_real_escape_string($conn, trim($data[6]));
    $status = mysqli_real_escape_string($conn, trim($data[7]));
    $marked_by = mysqli_real_escape_string($conn, trim($data[8]));
    $year = mysqli_real_escape_string($conn, trim($data[9]));

    $sql = "INSERT INTO attendance (date, roll_number, student_name, time_slot, subject_code, subject_name, status, marked_by, year) 
            VALUES ('$date', '$roll_number', '$student_name', '$time_slot', '$subject_code', '$subject_name', '$status', '$marked_by', '$year')";

    if (!$conn->query($sql)) {
        echo "Error: " . $conn->error . "<br>";
    }
}

fclose($handle);
echo "CSV file successfully uploaded and data stored!";
$conn->close();
?>
