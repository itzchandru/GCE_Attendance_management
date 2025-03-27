<?php
session_start();
session_destroy(); // Destroy all session data
header("Location: faculty_login.php"); // Redirect to the login page
exit();
?>
