<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

$name = $_SESSION["name"];

$sql = "SELECT title, subject, description, video_name, uploaded_at
        FROM recorded_classes
        ORDER BY uploaded_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Recorded Classes - CampusConnect</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <link rel="stylesheet" href="../assets/css/student-pages.css">

</head>

<body class="student-page classes-page">

<div class="student-container">

    <h1>🎥 Recorded Classes</h1>

    <p class="page-subtitle">
        Welcome, <?php echo htmlspecialchars($name); ?> 👋
    </p>


    <?php if ($result && mysqli_num_rows($result) > 0) { ?>

        <div class="classes-list">

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                <div class="recorded-class-card">

                    <div class="class-icon">
                        🎥
                    </div>

                    <div class="class-content">

                        <h2>
                            <?php echo htmlspecialchars($row["title"]); ?>
                        </h2>

                        <p class="class-subject">
                            📚
                            <?php echo htmlspecialchars($row["subject"]); ?>
                        </p>

                        <?php if (!empty($row["description"])) { ?>

                            <p class="class-description">
                                <?php echo htmlspecialchars($row["description"]); ?>
                            </p>

                        <?php } ?>

                        <p class="class-date">
                            📅 Uploaded:
                            <?php echo date("d M Y, h:i A", strtotime($row["uploaded_at"])); ?>
                        </p>

                        <video controls class="class-video">

                            <source
                                src="../uploads/classes/<?php echo htmlspecialchars($row["video_name"]); ?>"
                            >

                            Your browser does not support video playback.

                        </video>

                    </div>

                </div>

            <?php } ?>

        </div>

    <?php } else { ?>

        <div class="no-classes">

            🎥 No recorded classes available yet.

        </div>

    <?php } ?>


    <a href="dashboard.php" class="back-link">
        ← Back to Dashboard
    </a>

</div>

</body>

</html>