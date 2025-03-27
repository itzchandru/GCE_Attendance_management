<?php
session_start();
include 'db.php';

// Fetch subjects
$subjectQuery = "SELECT subject_code, subject_name FROM subjects"; 
$subjectResult = $conn->query($subjectQuery);

// Fetch students
$studentQuery = "SELECT roll_number, name FROM students"; 
$studentResult = $conn->query($studentQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /* Dynamic Background */
        body {
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Dark Mode */
        .dark-mode body {
            background: #121212;
            color: #ffffff;
        }

        .dark-mode .container {
            background: #1e1e1e;
            color: #ffffff;
        }

        .dark-mode .table {
            background: #2d2d2d;
            color: #ffffff;
        }

        .dark-mode .table th {
            background: #007bff;
            color: #ffffff;
        }

        .dark-mode .form-control, .dark-mode .form-select {
            background: #333;
            color: #fff;
            border: 1px solid #444;
        }

        .dark-mode .form-control:focus, .dark-mode .form-select:focus {
            background: #444;
            color: #fff;
            border-color: #555;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Container and Cards */
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            max-width: 800px;
            width: 100%;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Gradient Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            border: none;
            font-weight: bold;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2575fc, #6a11cb);
        }

        /* Floating Action Button (FAB) */
        .fab {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            font-size: 24px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        /* Progress Bar */
        .progress {
            height: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .progress-bar {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
        }

        /* Table Styling */
        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .table th {
            background: #007bff;
            color: white;
            text-align: center;
        }

        .table td {
            text-align: center;
        }

        .table-hover tbody tr:hover {
            background: #f1f1f1;
        }

        /* Dark Mode Toggle */
        .dark-mode-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container p-4">
        <!-- Dark Mode Toggle -->
        <div class="dark-mode-toggle" onclick="toggleDarkMode()">
            <i class="bi bi-moon-fill"></i>
        </div>

        <!-- Progress Bar -->
        <div class="progress">
            <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">Step 1</div>
        </div>
<!-- Back Button -->
<a href="faculty_dashboard.php" class="btn btn-secondary mb-3">
    <i class="bi bi-arrow-left"></i> Back
</a>

        <h2>📌 Mark Attendance</h2>

        <?php if(isset($_SESSION['success'])) { ?>
            <div class="alert alert-success text-center"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php } ?>

        <form action="submit_attendance.php" method="POST" id="attendanceForm">
            <!-- Select Date -->
            <div class="card mb-3">
                <div class="card-body">
                    <label for="date" class="form-label"><i class="bi bi-calendar"></i> 📅 Date:</label>
                    <input type="date" name="date" class="form-control" required onchange="updateProgressBar(33)">
                </div>
            </div>

            <!-- Select Time Slot -->
            <div class="card mb-3">
                <div class="card-body">
                    <label for="time_slot" class="form-label"><i class="bi bi-clock"></i> ⏰ Time Slot:</label>
                    <select name="time_slot" class="form-select" required onchange="updateProgressBar(66)">
                        <option value="">-- Select Time Slot --</option>
                        <option value="9:10 AM - 10:00 AM">9:10 AM - 10:00 AM</option>
                        <option value="10:00 AM - 10:50 AM">10:00 AM - 10:50 AM</option>
                        <option value="11:00 AM - 11:50 PM">11:00 AM - 11:50 PM</option>
                        <option value="11:50 AM - 12:40 PM">11:50 AM - 12:40 PM</option>
                        <option value="01:30 PM - 02:20 PM">01:30 PM - 02:20 PM</option>
                        <option value="02:20 PM - 03:10 PM">02:20 PM - 03:10 PM</option>
                        <option value="03:10 PM - 04:00 PM">03:10 PM - 04:00 PM</option>
                        <option value="04:00 PM - 04:50 PM">04:00 PM - 04:50 PM</option>
                    </select>
                </div>
            </div>

            <!-- Select Subject -->
            <div class="card mb-3">
                <div class="card-body">
                    <label for="subject_code" class="form-label"><i class="bi bi-book"></i> 📘 Select Subject:</label>
                    <select name="subject_code" class="form-select" required onchange="updateProgressBar(100)">
                        <option value="">-- Select Subject --</option>
                        <?php while ($row = $subjectResult->fetch_assoc()) { ?>
                            <option value="<?= $row['subject_code']; ?>" data-subject-name="<?= $row['subject_name']; ?>">
                                <?= $row['subject_code'] . " - " . $row['subject_name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                    <input type="hidden" name="subject_name" id="subject_name">
                </div>
            </div>

            <!-- Students List -->
            <div class="card mb-3">
                <div class="card-body">
                    <label class="form-label"><i class="bi bi-people"></i> 🎓 Select Attendance:</label>
                    <input type="text" id="searchStudent" class="form-control mb-3" placeholder="🔍 Search student by name or roll number...">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>Roll No</th>
                                    <th>Student Name</th>
                                    <th>
                                        Status <br>
                                        <input type="checkbox" id="selectAll" onchange="toggleAll(this)">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $studentResult->fetch_assoc()) { ?>
                                <tr>
                                    <td><?= $row['roll_number']; ?></td>
                                    <td><?= $row['name']; ?></td>
                                    <td>
                                        <select name="status[<?= $row['roll_number']; ?>]" class="form-select">
                                            <option value="Present" style="color: green;">✅ Present</option>
                                            <option value="Absent" style="color: red;">❌ Absent</option>
                                        </select>
                                        <input type="hidden" name="student_roll[]" value="<?= $row['roll_number']; ?>">
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Submit Button (opens modal) -->
            <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#confirmationModal">📥 Submit Attendance</button>

            <!-- Confirmation Modal -->
                <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmationModalLabel">Confirm Submission</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to submit the attendance?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <!-- Submit Button (inside modal) -->
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
        </form>

        <!-- Floating Action Button (FAB) -->
        <button class="btn btn-primary fab" onclick="document.getElementById('attendanceForm').submit()">📥</button>

        <!-- Real-Time Clock -->
        <div class="text-center mt-3">
            <span id="liveClock" class="badge bg-primary"></span>
        </div>

        <!-- Logout Button -->
        <div class="text-center mt-3">
            <a href="faculty_login.php" class="btn btn-danger">🚪 Logout</a>
        </div>
    </div>
<a href="javascript:history.back()" class="btn btn-secondary mb-3">
    <i class="bi bi-arrow-left"></i> Back
</a>

    <script>
        // Dark Mode Toggle
        function toggleDarkMode() {
            document.body.classList.toggle("dark-mode");
            document.querySelector(".dark-mode-toggle i").classList.toggle("bi-moon-fill");
            document.querySelector(".dark-mode-toggle i").classList.toggle("bi-sun-fill");
        }


        // Real-Time Clock
        function updateClock() {
            const now = new Date();
            const time = now.toLocaleTimeString();
            document.getElementById("liveClock").textContent = `⏰ ${time}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Search Students
        document.getElementById("searchStudent").addEventListener("input", function() {
            let searchTerm = this.value.toLowerCase();
            let rows = document.querySelectorAll(".table tbody tr");
            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(searchTerm) ? "" : "none";
            });
        });

        // Select All Toggle
        function toggleAll(checkbox) {
            let statusSelects = document.querySelectorAll("select[name^='status']");
            statusSelects.forEach(select => {
                select.value = checkbox.checked ? "Present" : "Absent";
            });
        }

        // Subject Name Update
        document.querySelector("select[name='subject_code']").addEventListener("change", function() {
            let selectedOption = this.options[this.selectedIndex];
            document.getElementById("subject_name").value = selectedOption.getAttribute("data-subject-name");
        });

        // Update Progress Bar
        function updateProgressBar(percentage) {
            const progressBar = document.getElementById("progressBar");
            progressBar.style.width = percentage + "%";
            progressBar.setAttribute("aria-valuenow", percentage);
            progressBar.textContent = `Step ${Math.floor(percentage / 33)}`;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>