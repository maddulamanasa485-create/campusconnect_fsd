<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

$name = $_SESSION["name"];

$sql = "SELECT title, message, created_at
        FROM announcements
        ORDER BY created_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Announcements - CampusConnect</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/student-pages.css">

</head>

<body class="student-page announcements-page">

<div class="student-container">

    <h1>📢 Announcements</h1>

    <p class="page-subtitle">
        Welcome, <?php echo htmlspecialchars($name); ?> 👋
    </p>


    <?php if ($result && mysqli_num_rows($result) > 0) { ?>

        <div class="student-announcement-list">

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                <div class="student-announcement-card">

                    <div class="announcement-icon">
                        📢
                    </div>

                    <div class="announcement-content">

                        <h2>
                            <?php echo htmlspecialchars($row["title"]); ?>
                        </h2>

                        <p class="announcement-message-text">
                            <?php
                            echo nl2br(
                                htmlspecialchars($row["message"])
                            );
                            ?>
                        </p>

                        <p class="announcement-date">

                            📅 Posted:
                            <?php
                            echo date(
                                "d M Y, h:i A",
                                strtotime($row["created_at"])
                            );
                            ?>

                        </p>

                    </div>

                </div>

            <?php } ?>

        </div>

    <?php } else { ?>

        <div class="student-no-announcements">

            📢 No announcements available right now.

        </div>

    <?php } ?>


    <a href="dashboard.php" class="back-link">
        ← Back to Dashboard
    </a>

</div>

</body>

</html>