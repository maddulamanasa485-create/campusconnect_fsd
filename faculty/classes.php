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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if ($title == "") {
        $message = "Please enter class title.";
        $message_type = "error";

    } elseif ($subject == "") {
        $message = "Please select subject.";
        $message_type = "error";

    } elseif (!isset($_FILES["video"]) || $_FILES["video"]["name"] == "") {
        $message = "Please choose a video.";
        $message_type = "error";

    } else {

        $video = $_FILES["video"];

        if ($video["error"] != 0) {

            $message = "Video upload failed. Error code: " . $video["error"];
            $message_type = "error";

        } else {

            $allowed = ["mp4", "webm", "ogg", "mov"];

            $original_name = basename($video["name"]);
            $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

            if (!in_array($extension, $allowed)) {

                $message = "Only MP4, WEBM, OGG and MOV videos are allowed.";
                $message_type = "error";

            } elseif ($video["size"] > 100 * 1024 * 1024) {

                $message = "Video must be less than 100 MB.";
                $message_type = "error";

            } else {

                $safe_name = preg_replace(
                    "/[^A-Za-z0-9._-]/",
                    "_",
                    $original_name
                );

                $new_name = time() . "_" . $safe_name;

                $folder = "../uploads/classes/";

                if (!is_dir($folder)) {
                    mkdir($folder, 0777, true);
                }

                $file_path = $folder . $new_name;

                if (move_uploaded_file($video["tmp_name"], $file_path)) {

                    $sql = "INSERT INTO recorded_classes
                            (faculty_id, title, subject, description, video_name)
                            VALUES (?, ?, ?, ?, ?)";

                    $stmt = mysqli_prepare($conn, $sql);

                    if ($stmt) {

                        mysqli_stmt_bind_param(
                            $stmt,
                            "issss",
                            $faculty_id,
                            $title,
                            $subject,
                            $description,
                            $new_name
                        );

                        if (mysqli_stmt_execute($stmt)) {

                            $message = "Recorded class uploaded successfully! ✅";
                            $message_type = "success";

                        } else {

                            $message = "Database error: " . mysqli_error($conn);
                            $message_type = "error";

                            if (file_exists($file_path)) {
                                unlink($file_path);
                            }
                        }

                        mysqli_stmt_close($stmt);

                    } else {

                        $message = "Database prepare error: " . mysqli_error($conn);
                        $message_type = "error";

                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }

                } else {

                    $message = "Unable to save video. Check uploads/classes folder.";
                    $message_type = "error";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Recorded Classes - CampusConnect</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <link rel="stylesheet" href="../assets/css/faculty.css">

</head>

<body class="faculty-page faculty-classes-page">

<div class="faculty-dashboard">

    <h1>🎥 Recorded Classes</h1>

    <p class="subtitle">
        Faculty:
        <?php echo htmlspecialchars($name); ?>
    </p>

    <?php if ($message != "") { ?>

        <div class="class-message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php } ?>

    <div class="class-upload-card">

        <h2>Upload Recorded Lecture</h2>

        <form action="" method="POST" enctype="multipart/form-data">

            <div class="form-group">

                <label for="title">
                    Class Title
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    placeholder="Enter class title"
                    required
                >

            </div>


            <div class="form-group">

                <label for="subject">
                    Subject
                </label>

                <select
                    id="subject"
                    name="subject"
                    required
                >

                    <option value="">
                        Select Subject
                    </option>

                    <option value="PHP">
                        PHP
                    </option>

                    <option value="DBMS">
                        DBMS
                    </option>

                    <option value="Operating Systems">
                        Operating Systems
                    </option>

                    <option value="Artificial Intelligence">
                        Artificial Intelligence
                    </option>

                    <option value="Computer Networks">
                        Computer Networks
                    </option>

                </select>

            </div>


            <div class="form-group">

                <label for="description">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    placeholder="Enter short description"
                ></textarea>

            </div>


            <div class="form-group">

                <label for="video">
                    Choose Video
                </label>

                <input
                    type="file"
                    id="video"
                    name="video"
                    accept="video/*"
                    required
                >

                <small>
                    Allowed: MP4, WEBM, OGG, MOV | Maximum size: 100 MB
                </small>

            </div>


            <button type="submit">
                🎥 Upload Recorded Class
            </button>

        </form>

    </div>


    <a href="dashboard.php" class="back-link">
        ← Back to Dashboard
    </a>

</div>

</body>

</html>