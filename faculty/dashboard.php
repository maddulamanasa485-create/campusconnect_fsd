<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION["role"] !== "faculty") {
    header("Location: ../auth/login.php");
    exit;
}

$name = $_SESSION["name"];

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Faculty Dashboard - CampusConnect</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <link rel="stylesheet" href="../assets/css/faculty.css">

</head>

<body class="faculty-page">

    <div class="faculty-dashboard">

        <h1>
            Welcome, <?php echo htmlspecialchars($name); ?> 👋
        </h1>

        <p class="subtitle">
            Faculty Dashboard
        </p>

        <div class="faculty-cards">

            <a href="timetable.php" class="faculty-card timetable-card">
                <div class="icon">📅</div>
                <h2>Timetable</h2>
                <p>View your teaching timetable.</p>
            </a>

            <a href="students.php" class="faculty-card students-card">
                <div class="icon">👥</div>
                <h2>Students</h2>
                <p>View student information.</p>
            </a>

            <a href="materials.php" class="faculty-card materials-card">
                <div class="icon">📚</div>
                <h2>Upload Materials</h2>
                <p>Upload notes and study materials.</p>
            </a>

            <a href="classes.php" class="faculty-card classes-card">
                <div class="icon">🎥</div>
                <h2>Recorded Classes</h2>
                <p>Upload recorded lectures.</p>
            </a>

            <a href="attendance.php" class="faculty-card attendance-card">
                <div class="icon">✅</div>
                <h2>Attendance</h2>
                <p>Manage student attendance.</p>
            </a>

            <a href="announcements.php" class="faculty-card announcements-card">
                <div class="icon">📢</div>
                <h2>Announcements</h2>
                <p>Post announcements for students.</p>
            </a>

            <a href="marks.php" class="faculty-card marks-card">
                <div class="icon">📝</div>
                <h2>Marks / Results</h2>
                <p>Enter and manage student marks.</p>
            </a>

        </div>

        <a href="../auth/logout.php" class="logout">
            Logout
        </a>

    </div>

</body>

</html>