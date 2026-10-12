<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit();
}

include "../config/db.php";

$sql = "SELECT name, description, coordinator, meeting_day, meeting_time, location
        FROM clubs
        ORDER BY id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Clubs - CampusConnect</title>
    <link rel="stylesheet" href="../assets/css/student-pages.css">

    <style>
        .clubs-container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }

        .club-card {
            background: #ecfff5;
            border-radius: 18px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .club-card h2 {
            color: #176b4d;
            margin-bottom: 15px;
        }

        .club-card p {
            margin: 10px 0;
            color: #444;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #176b4d;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="clubs-container">

    <a href="dashboard.php" class="back-link">
        ← Back to Dashboard
    </a>

    <h1>🏆 College Clubs</h1>
    <p>Explore clubs and their meeting details.</p>

    <?php if (mysqli_num_rows($result) > 0): ?>

        <?php while ($club = mysqli_fetch_assoc($result)): ?>

            <div class="club-card">

                <h2>🏆 <?php echo htmlspecialchars($club['name']); ?></h2>

                <p>
                    <?php echo htmlspecialchars($club['description']); ?>
                </p>

                <p>
                    👨‍🏫 <strong>Coordinator:</strong>
                    <?php echo htmlspecialchars($club['coordinator']); ?>
                </p>

                <p>
                    📅 <strong>Meeting Day:</strong>
                    <?php echo htmlspecialchars($club['meeting_day']); ?>
                </p>

                <p>
                    ⏰ <strong>Meeting Time:</strong>
                    <?php echo htmlspecialchars($club['meeting_time']); ?>
                </p>

                <p>
                    📍 <strong>Location:</strong>
                    <?php echo htmlspecialchars($club['location']); ?>
                </p>

            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <p>No clubs available currently.</p>

    <?php endif; ?>

</div>

</body>
</html>