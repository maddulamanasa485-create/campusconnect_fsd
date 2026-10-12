<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit();
}

include "../config/db.php";

$student_id = $_SESSION['user_id'];

/* Attendance Percentage */
$attendance_sql = "
    SELECT
        COUNT(*) AS total_classes,
        SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS present_classes
    FROM attendance
    WHERE student_id = ?
";

$stmt = mysqli_prepare($conn, $attendance_sql);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$attendance = mysqli_fetch_assoc($result);

$total_classes = (int)($attendance['total_classes'] ?? 0);
$present_classes = (int)($attendance['present_classes'] ?? 0);

$attendance_percentage = $total_classes > 0
    ? round(($present_classes / $total_classes) * 100, 2)
    : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard - CampusConnect</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">

    <style>
        .attendance-card {
            background: #eaf6ff;
            border-left: 5px solid #2196f3;
        }

        .attendance-percentage {
            font-size: 30px;
            font-weight: bold;
            color: #1976d2;
            margin: 10px 0;
        }

        .dashboard-card a {
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="dashboard-container">

    <div class="dashboard-header">
        <div>
            <h1>Student Dashboard</h1>
            <p>Welcome to CampusConnect 🎓</p>
        </div>

        <a href="../auth/logout.php" class="logout-btn">
            Logout
        </a>
    </div>


    <div class="dashboard-grid">

        <!-- Timetable -->
        <div class="dashboard-card">
            <div class="card-icon">📅</div>
            <h3>Timetable</h3>
            <p>View your class timetable</p>
            <a href="timetable.php">
                View Timetable →
            </a>
        </div>


        <!-- Materials -->
        <div class="dashboard-card">
            <div class="card-icon">📚</div>
            <h3>Materials</h3>
            <p>Access study materials</p>
            <a href="materials.php">
                View Materials →
            </a>
        </div>


        <!-- Recorded Classes -->
        <div class="dashboard-card">
            <div class="card-icon">🎥</div>
            <h3>Recorded Classes</h3>
            <p>Watch recorded lectures</p>
            <a href="classes.php">
                Watch Classes →
            </a>
        </div>


        <!-- Fees -->
        <div class="dashboard-card">
            <div class="card-icon">💰</div>
            <h3>Fees</h3>
            <p>Check your fee details</p>
            <a href="fees.php">
                View Fees →
            </a>
        </div>


        <!-- Results -->
        <div class="dashboard-card">
            <div class="card-icon">📊</div>
            <h3>Results</h3>
            <p>Check your examination results</p>
            <a href="results.php">
                View Results →
            </a>
        </div>


        <!-- SGPA -->
        <div class="dashboard-card">
            <div class="card-icon">⭐</div>
            <h3>SGPA</h3>
            <p>View semester SGPA</p>
            <a href="sgpa.php">
                View SGPA →
            </a>
        </div>


        <!-- CGPA -->
        <div class="dashboard-card">
            <div class="card-icon">🏆</div>
            <h3>CGPA</h3>
            <p>View overall CGPA</p>
            <a href="cgpa.php">
                View CGPA →
            </a>
        </div>


        <!-- Hall Ticket -->
        <div class="dashboard-card">
            <div class="card-icon">🎫</div>
            <h3>Hall Ticket</h3>
            <p>View your examination hall ticket</p>
            <a href="hallticket.php">
                View Hall Ticket →
            </a>
        </div>


        <!-- Clubs -->
        <div class="dashboard-card">
            <div class="card-icon">🎯</div>
            <h3>Clubs</h3>
            <p>Explore college clubs</p>
            <a href="clubs.php">
                View Clubs →
            </a>
        </div>


        <!-- Announcements -->
        <div class="dashboard-card">
            <div class="card-icon">📢</div>
            <h3>Announcements</h3>
            <p>View latest college announcements</p>
            <a href="announcements.php">
                View Announcements →
            </a>
        </div>


        <!-- Attendance -->
        <div class="dashboard-card attendance-card">
            <div class="card-icon">📊</div>

            <h3>Attendance</h3>

            <p>Your current attendance</p>

            <div class="attendance-percentage">
                <?php echo $attendance_percentage; ?>%
            </div>

            <p>
                <?php echo $present_classes; ?>
                / 
                <?php echo $total_classes; ?>
                classes present
            </p>

            <a href="attendance.php">
                View Attendance →
            </a>
        </div>

    </div>

</div>

</body>
</html>