<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

$name = $_SESSION["name"];

$sql = "SELECT title, subject, description, file_name, uploaded_at
        FROM materials
        ORDER BY uploaded_at DESC";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Materials - CampusConnect</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <link rel="stylesheet" href="../assets/css/student-pages.css">

</head>

<body class="student-page materials-page">

    <div class="dashboard">

        <h1>📚 Study Materials</h1>

        <p>
            Student: <?php echo htmlspecialchars($name); ?>
        </p>

        <div class="materials-list">

            <?php if ($result && mysqli_num_rows($result) > 0) { ?>

                <?php while ($material = mysqli_fetch_assoc($result)) { ?>

                    <div class="material-card">

                        <div class="material-icon">
                            📄
                        </div>

                        <div class="material-content">

                            <h2>
                                <?php echo htmlspecialchars($material["title"]); ?>
                            </h2>

                            <p class="subject">
                                Subject:
                                <?php echo htmlspecialchars($material["subject"]); ?>
                            </p>

                            <?php if (!empty($material["description"])) { ?>

                                <p class="description">
                                    <?php echo htmlspecialchars($material["description"]); ?>
                                </p>

                            <?php } ?>

                            <p class="uploaded">
                                Uploaded:
                                <?php echo date("d M Y, h:i A", strtotime($material["uploaded_at"])); ?>
                            </p>

                            <a
                                href="../uploads/materials/<?php echo rawurlencode($material["file_name"]); ?>"
                                target="_blank"
                                class="download-button"
                            >
                                📥 View / Download
                            </a>

                        </div>

                    </div>

                <?php } ?>

            <?php } else { ?>

                <div class="no-materials">

                    <h2>📭 No Materials Available</h2>

                    <p>
                        Faculty has not uploaded any study materials yet.
                    </p>

                </div>

            <?php } ?>

        </div>

        <a href="dashboard.php" class="back-link">
            ← Back to Dashboard
        </a>

    </div>

</body>

</html>