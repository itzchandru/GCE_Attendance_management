# Attendance Management System

## Overview
The **Attendance Management System** is a web-based application designed to efficiently track student attendance in the ECE department of Government College of Engineering, Thanjavur. It provides faculty members with an easy-to-use interface for marking attendance and an admin panel for managing records.

## Features
- **Faculty Login**: Faculty members can log in and mark attendance.
- **Admin Panel (GOD Login)**: Admin can view all attendance records and export them.
- **Real-Time Attendance Display**: Uses ESP8266 with an LCD/OLED display to show attendance details outside the classroom.
- **User-Friendly Interface**: Built with modern web technologies for smooth navigation.

## Tech Stack
- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Database**: MySQL
- **Progressive Web App (PWA) Support**
- **Hardware**: ESP8266 with LCD/OLED display

## Installation & Setup
### Prerequisites
- XAMPP (for local development)
- PHP and MySQL installed
- ESP8266 setup for real-time display

### Steps to Run the Project
1. Clone this repository:
   ```sh
   git clone https://github.com/your-username/attendance-management-system.git
   ```
2. Move the project files to the XAMPP `htdocs` directory.
3. Start Apache and MySQL from the XAMPP control panel.
4. Import the database:
   - Open phpMyAdmin (`http://localhost/phpmyadmin`)
   - Create a new database (e.g., `attendance_db`)
   - Import the `attendance_db.sql` file.
5. Update database configurations in `config.php`.
6. Open the project in a browser:
   ```
   http://localhost/attendance-management-system
   ```

## Future Enhancements
- Implement a mobile app version using a modern tech stack.
- Enhance UI/UX with improved design.
- Add analytics and reporting features.

## Contributing
Contributions are welcome! Feel free to submit issues and pull requests.

## License
This project is open-source and available under the [MIT License](LICENSE).
