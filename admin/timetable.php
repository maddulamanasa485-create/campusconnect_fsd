<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include "../config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $department = trim($_POST['department']);
    $semester = (int) $_POST['semester'];
    $day = trim($_POST['day']);
    $subject = trim($_POST['subject']);
    $faculty = trim($_POST['faculty']);
    $start_time = trim($_POST['start_time']);
    $end_time = trim($_POST['end_time']);
    $room = trim($_POST['room']);

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO timetable
        (department, semester, day, subject, faculty, start_time, end_time, room)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sissssss",
        $department,
        $semester,
        $day,
        $subject,
        $faculty,
        $start_time,
        $end_time,
        $room
    );

    if (mysqli_stmt_execute($stmt)) {
        $message = "Timetable added successfully! 🎉";
    }

    mysqli_stmt_close($stmt);
}

$result = mysqli_query(
    $conn,
    "SELECT * FROM timetable ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Timetable - CampusConnect</title>

    <link rel="stylesheet" href="../assets/css/office.css">

    <style>
        .admin-container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }

        .header-box {
            background: linear-gradient(135deg, #182848, #4b6cb7);
            color: white;
            padding: 25px;
            border-radius: 18px;
            margin-bottom: 25px;
        }

        .form-box,
        .timetable-card {
            background: white;
            padding: 25px;
            border-radius: 18px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            margin: 8px 0 18px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-sizing: border-box;
        }

        button {
            padding: 12px 22px;
            border: none;
            border-radius: 10px;
            background: #4b6cb7;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }

        .success {
            background: #e8fff0;
            color: #16803c;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .timetable-card h2 {
            color: #182848;
            margin-bottom: 15px;
        }

        .timetable-card p {
            margin: 8px 0;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #182848;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="admin-container">

    <a href="dashboard.php" class="back-link">
        ← Back to Admin Dashboard
    </a>

    <div class="header-box">
        <h1>📅 Timetable Management</h1>
        <p>Add and manage college timetable</p>
    </div>

    <?php if ($message): ?>
        <div class="success">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="form-box">

        <h2>➕ Add Timetable</h2>

        <form method="POST">

            <label><strong>Department</strong></label>
            <input
                type="text"
                name="department"
                placeholder="Example: CSE"
                required
            >

            <label><strong>Semester</strong></label>
            <select name="semester" required>
                <option value="">Select Semester</option>
                <?php for ($i = 1; $i <= 8; $i++): ?>
                    <option value="<?php echo $i; ?>">
                        Semester <?php echo $i; ?>
                    </option>
                <?php endfor; ?>
            </select>

            <label><strong>Day</strong></label>
            <select name="day" required>
                <option value="">Select Day</option>
                <option>Monday</option>
                <option>Tuesday</option>
                <option>Wednesday</option>
                <option>Thursday</option>
                <option>Friday</option>
                <option>Saturday</option>
            </select>

            <label><strong>Subject</strong></label>
            <input
                type="text"
                name="subject"
                placeholder="Example: PHP"
                required
            >

            <label><strong>Faculty</strong></label>
            <input
                type="text"
                name="faculty"
                placeholder="Example: Faculty One"
                required
            >

            <label><strong>Start Time</strong></label>
            <input
                type="text"
                name="start_time"
                placeholder="Example: 9:00 AM"
                required
            >

            <label><strong>End Time</strong></label>
            <input
                type="text"
                name="end_time"
                placeholder="Example: 10:00 AM"
                required
            >

            <label><strong>Room</strong></label>
            <input
                type="text"
                name="room"
                placeholder="Example: CSE Lab 1"
                required
            >

            <button type="submit">
                📅 Add Timetable
            </button>

        </form>

    </div>

    <h2>📋 Available Timetable</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>

        <?php while ($row = mysqli_fetch_assoc($result)): ?>

            <div class="timetable-card">

                <h2>
                    📚 <?php echo htmlspecialchars($row['subject']); ?>
                </h2>

                <p>
                    🏢 <strong>Department:</strong>
                    <?php echo htmlspecialchars($row['department']); ?>
                </p>

                <p>
                    📖 <strong>Semester:</strong>
                    <?php echo $row['semester']; ?>
                </p>

                <p>
                    📅 <strong>Day:</strong>
                    <?php echo htmlspecialchars($row['day']); ?>
                </p>

                <p>
                    👨‍🏫 <strong>Faculty:</strong>
                    <?php echo htmlspecialchars($row['faculty']); ?>
                </p>

                <p>
                    ⏰ <strong>Time:</strong>
                    <?php echo htmlspecialchars($row['start_time']); ?>
                    -
                    <?php echo htmlspecialchars($row['end_time']); ?>
                </p>

                <p>
                    🚪 <strong>Room:</strong>
                    <?php echo htmlspecialchars($row['room']); ?>
                </p>

            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <p>No timetable available.</p>

    <?php endif; ?>

</div>

</body>
</html>