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

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if ($title === "" || $subject === "" || !isset($_FILES["material"])) {

        $message = "Please fill all required fields.";
        $message_type = "error";

    } else {

        $file = $_FILES["material"];

        if ($file["error"] !== UPLOAD_ERR_OK) {

            $message = "File upload failed.";
            $message_type = "error";

        } else {

            $allowed_extensions = ["pdf", "doc", "docx", "ppt", "pptx"];

            $original_name = basename($file["name"]);
            $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

            if (!in_array($extension, $allowed_extensions)) {

                $message = "Invalid file type. Only PDF, DOC, DOCX, PPT and PPTX are allowed.";
                $message_type = "error";

            } elseif ($file["size"] > 10 * 1024 * 1024) {

                $message = "File size must be less than 10 MB.";
                $message_type = "error";

            } else {

                $new_file_name = time() . "_" . preg_replace(
                    "/[^A-Za-z0-9._-]/",
                    "_",
                    $original_name
                );

                $upload_directory = "../uploads/materials/";

                $file_path = $upload_directory . $new_file_name;

                if (move_uploaded_file($file["tmp_name"], $file_path)) {

                    $sql = "INSERT INTO materials
                            (faculty_id, title, subject, description, file_name)
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
                            $new_file_name
                        );

                        if (mysqli_stmt_execute($stmt)) {

                            $message = "Material uploaded successfully! ✅";
                            $message_type = "success";

                        } else {

                            $message = "Database error while saving material.";
                            $message_type = "error";

                            if (file_exists($file_path)) {
                                unlink($file_path);
                            }
                        }

                        mysqli_stmt_close($stmt);

                    } else {

                        $message = "Database error.";
                        $message_type = "error";

                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }

                } else {

                    $message = "Unable to save uploaded file.";
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

    <title>Upload Materials - CampusConnect</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <link rel="stylesheet" href="../assets/css/faculty.css">

</head>

<body class="faculty-page faculty-materials-page">

    <div class="faculty-dashboard">

        <h1>📚 Upload Materials</h1>

        <p class="subtitle">
            Faculty: <?php echo htmlspecialchars($name); ?>
        </p>

        <?php if ($message !== "") { ?>

            <div class="upload-message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php } ?>

        <div class="material-upload-card">

            <h2>Upload Study Material</h2>

            <form method="POST" enctype="multipart/form-data">

                <div class="form-group">

                    <label for="title">Material Title</label>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        placeholder="Enter material title"
                        required
                    >

                </div>

                <div class="form-group">

                    <label for="subject">Subject</label>

                    <select id="subject" name="subject" required>

                        <option value="">Select Subject</option>

                        <option value="PHP">PHP</option>

                        <option value="DBMS">DBMS</option>

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

                    <label for="description">Description</label>

                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Enter short description"
                    ></textarea>

                </div>

                <div class="form-group">

                    <label for="material">Choose File</label>

                    <input
                        type="file"
                        id="material"
                        name="material"
                        accept=".pdf,.doc,.docx,.ppt,.pptx"
                        required
                    >

                    <small>
                        Allowed: PDF, DOC, DOCX, PPT, PPTX | Maximum size: 10 MB
                    </small>

                </div>

                <button type="submit">
                    📤 Upload Material
                </button>

            </form>

        </div>

        <a href="dashboard.php" class="back-link">
            ← Back to Dashboard
        </a>

    </div>

</body>

</html>