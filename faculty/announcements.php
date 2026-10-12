<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "faculty") {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

$name = $_SESSION["name"];
$faculty_id = $_SESSION["user_id"];

$message = "";
$message_type = "";

/* ADD ANNOUNCEMENT */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"] ?? "");
    $announcement = trim($_POST["message"] ?? "");

    if ($title == "") {
        $message = "Please enter announcement title.";
        $message_type = "error";
    } elseif ($announcement == "") {
        $message = "Please enter announcement message.";
        $message_type = "error";
    } else {

        $sql = "INSERT INTO announcements
                (faculty_id, title, message)
                VALUES (?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "iss",
                $faculty_id,
                $title,
                $announcement
            );

            if (mysqli_stmt_execute($stmt)) {
                $message = "Announcement posted successfully! 📢";
                $message_type = "success";
            } else {
                $message = "Database error: " . mysqli_error($conn);
                $message_type = "error";
            }

            mysqli_stmt_close($stmt);

        } else {
            $message = "Database prepare error: " . mysqli_error($conn);
            $message_type = "error";
        }
    }
}

/* GET ANNOUNCEMENTS */
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
    <link rel="stylesheet" href="../assets/css/faculty.css">

</head>

<body class="faculty-page faculty-announcements-page">

<div class="faculty-dashboard">

    <h1>📢 Announcements</h1>

    <p class="subtitle">
        Faculty:
        <?php echo htmlspecialchars($name); ?>
    </p>


    <!-- MESSAGE -->

    <?php if ($message != "") { ?>

        <div class="announcement-message <?php echo $message_type; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php } ?>


    <!-- POST ANNOUNCEMENT -->

    <div class="announcement-upload-card">

        <h2>📢 Post New Announcement</h2>

        <form action="" method="POST">

            <div class="announcement-form-group">

                <label for="title">
                    Announcement Title
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    placeholder="Enter announcement title"
                    required
                >

            </div>


            <div class="announcement-form-group">

                <label for="message">
                    Announcement Message
                </label>

                <textarea
                    id="message"
                    name="message"
                    rows="6"
                    placeholder="Enter announcement details"
                    required
                ></textarea>

            </div>


            <button type="submit">
                📢 Post Announcement
            </button>

        </form>

    </div>


    <!-- EXISTING ANNOUNCEMENTS -->

    <div class="existing-announcements">

        <h2>📋 Posted Announcements</h2>


        <?php if ($result && mysqli_num_rows($result) > 0) { ?>

            <div class="announcement-list">

                <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                    <div class="announcement-card">

                        <h3>
                            📢
                            <?php echo htmlspecialchars($row["title"]); ?>
                        </h3>

                        <p class="announcement-text">
                            <?php echo nl2br(htmlspecialchars($row["message"])); ?>
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

                <?php } ?>

            </div>

        <?php } else { ?>

            <div class="no-announcements">

                📢 No announcements posted yet.

            </div>

        <?php } ?>

    </div>


    <a href="dashboard.php" class="back-link">
        ← Back to Dashboard
    </a>

</div>

</body>

</html>