<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | Attendance System</title>
    
    <!-- Bootstrap & Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Jost:ital,wght@0,100..900;1,100..900&family=Kalam:wght@300;400;700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Public+Sans:ital,wght@0,100..900;1,100..900&family=Red+Hat+Display:ital,wght@0,300..900;1,300..900&family=Spicy+Rice&display=swap" rel="stylesheet">
    <style>
        /* Apply Fonts */
h1 {
    font-family: "Archivo Black", sans-serif;
    font-size: 3rem;
    color: #ffc40d; /* Warm Gold */
    text-shadow: 3px 3px 10px rgba(0, 0, 0, 0.5);
}

p {
    font-family: "Poppins", sans-serif;
    font-size: 1.5rem;
    color: #e5e9ec;
    letter-spacing: 1px;
}

.marquee {
    font-family: "Kalam", cursive;
    font-size: 1.2rem;
    color: #ffcc00; /* Bright Yellow */
    font-weight: 700;
}

.btn-start {
    font-family: "Public Sans", sans-serif;
    font-size: 1.4rem;
    background: linear-gradient(45deg, #00c6ff, #0072ff);
    color: white;
    padding: 14px 40px;
    border-radius: 30px;
    transition: all 0.3s ease-in-out;
}

.btn-start:hover {
    background: linear-gradient(45deg, #ff416c, #ff4b2b);
    transform: scale(1.1);
}

/* Bubble Effect */
.bubble {
    background: rgba(255, 255, 255, 0.3);
    box-shadow: 0 0 15px rgba(255, 255, 255, 0.5);
}

         
        
        body {
            background: url('./college_background.jpeg') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .content {
            position: relative;
            z-index: 2;
            max-width: 850px;
            color: white;
            padding: 40px;
            border-radius: 20px;
            background: rgba(0, 0, 0, 0.5);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 1.5s ease-in-out;
        }
        h1 {
            font-size: 3rem;
            font-weight: 600;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
        }
        p {
            font-size: 1.3rem;
            font-weight: 300;
            margin-top: 10px;
            letter-spacing: 1px;
        }
        .btn-start {
            padding: 14px 40px;
            font-size: 1.4rem;
            font-weight: 500;
            border-radius: 30px;
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            color: white;
            text-decoration: none;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            border: none;
        }
        .btn-start:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Floating Bubbles */
        .bubble-container {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }
        .bubble {
            position: absolute;
            bottom: -50px;
            border-radius: 50%;
            background: rgba(173, 216, 230, 0.5);
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.8);
            animation: floatUp linear infinite;
        }
        @keyframes floatUp {
            0% { transform: translateY(100vh) scale(0.3); opacity: 0.5; }
            50% { opacity: 1; }
            100% { transform: translateY(-10vh) scale(1.1); opacity: 0; }
        }
        
        /* Random Bubbles */
        .bubble:nth-child(1) { width: 40px; height: 40px; left: 10%; animation-duration: 7s; }
        .bubble:nth-child(2) { width: 60px; height: 60px; left: 20%; animation-duration: 8s; }
        .bubble:nth-child(3) { width: 50px; height: 50px; left: 35%; animation-duration: 6s; }
        .bubble:nth-child(4) { width: 80px; height: 80px; left: 50%; animation-duration: 10s; }
        .bubble:nth-child(5) { width: 30px; height: 30px; left: 65%; animation-duration: 5s; }
        .bubble:nth-child(6) { width: 70px; height: 70px; left: 80%; animation-duration: 9s; }
        .bubble:nth-child(7) { width: 50px; height: 50px; left: 90%; animation-duration: 7s; }

        /* Marquee */
        .marquee-container {
            position: absolute;
            bottom: 20px;
            width: 100%;
            z-index: 3;
        }
        .marquee {
            color: white;
            font-size: 1.2rem;
            font-weight: 400;
            padding: 10px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            overflow: hidden;
            white-space: nowrap;
            animation: scrollText 16s linear infinite;
        }
        @keyframes scrollText {
            from { transform: translateX(100%); }
            to { transform: translateX(-100%); }
        }
    </style>
</head>
<body>

    <!-- Floating Bubbles -->
    <div class="bubble-container">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>

    <div class="content">
        <h1>Smart Attendance Management</h1>
        <p>Effortless Attendance Tracking for Faculty & Admin</p>
        <div class="mt-4">
            <a href="index.php" class="btn btn-start">Get Started</a>
        </div>
    </div>

    <!-- Marquee for Announcements -->
    <div class="marquee-container">
        <div class="marquee">
            🚀 New Feature: Real-time Attendance Reports | 📅 Upcoming: AI-Based Face Recognition System | ✅ Faculty Login Enabled!
        </div>
    </div>

</body>
</html>
