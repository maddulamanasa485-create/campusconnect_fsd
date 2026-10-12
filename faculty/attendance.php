```php
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

/* Get actual students from database */
$sql_students = "SELECT id, name, email
                 FROM users
                 WHERE role = 'student'
                 AND status = 1
                 ORDER BY id ASC";

$result_students = mysqli_query($conn, $sql_students);

$students = [];

if ($result_students) {
    while ($row = mysqli_fetch_assoc($result_students)) {
        $students[] = $row;
    }
}


/* Save Attendance */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $subject = trim($_POST["subject"] ?? "");
    $attendance_date = $_POST["attendance_date"] ?? "";
    $attendance = $_POST["attendance"] ?? [];

    if ($subject === "" || $attendance_date === "") {

        $message = "Please select subject and date.";
        $message_type = "error";

    } elseif (count($students) === 0) {

        $message = "No active students found.";
        $message_type = "error";

    } else {

        $success = true;

        foreach ($students as $student) {

            $student_id = $student["id"];

            /* If not selected, mark Absent */
            $status = $attendance[$student_id] ?? "Absent";

            $sql = "INSERT INTO attendance
                    (faculty_id, student_id, subject, attendance_date, status)
                    VALUES (?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $sql);

            if ($stmt) {

                mysqli_stmt_bind_param(
                    $stmt,
                    "iisss",
                    $faculty_id,
                    $student_id,
                    $subject,
                    $attendance_date,
                    $status
                );

                if (!mysqli_stmt_execute($stmt)) {
                    $success = false;
                }

                mysqli_stmt_close($stmt);

            } else {
                $success = false;
            }
        }

        if ($success) {
            $message = "Attendance saved successfully! ✅";
            $message_type = "success";
        } else {
            $message = "Error while saving attendance.";
            $message_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Attendance - CampusConnect</title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

    <link rel="stylesheet"
          href="../assets/css/faculty.css">

</head>

<body class="faculty-page">

<div class="faculty-dashboard">

    <h1>✅ Student Attendance</h1>

    <p class="subtitle">
        Faculty:
        <?php echo htmlspecialchars($name); ?>
    </p>


    <?php if ($message !== "") { ?>

        <div class="class-message <?php echo htmlspecialchars($message_type); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php } ?>


    <div class="attendance-card">

        <h2>Mark Attendance</h2>

        <form method="POST">

            <div class="attendance-form">

                <div>

                    <label>Subject</label>

                    <select name="subject" required>

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


                <div>

                    <label>Date</label>

                    <input
                        type="date"
                        name="attendance_date"
                        value="<?php echo date('Y-m-d'); ?>"
                        required
                    >

                </div>

            </div>


            <h3>Students</h3>


            <div class="attendance-table-wrapper">

                <table class="attendance-table">

                    <thead>

                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php if (count($students) > 0) { ?>

                        <?php foreach ($students as $student) { ?>

                            <tr>

                                <td>
                                    <?php
                                    echo htmlspecialchars($student["id"]);
                                    ?>
                                </td>


                                <td>
                                    <?php
                                    echo htmlspecialchars($student["name"]);
                                    ?>
                                </td>


                                <td>
                                    <?php
                                    echo htmlspecialchars($student["email"]);
                                    ?>
                                </td>


                                <td>

                                    <label class="attendance-radio">

                                        <input
                                            type="radio"
                                            name="attendance[<?php echo $student["id"]; ?>]"
                                            value="Present"
                                            checked
                                        >

                                        Present

                                    </label>


                                    <label class="attendance-radio">

                                        <input
                                            type="radio"
                                            name="attendance[<?php echo $student["id"]; ?>]"
                                            value="Absent"
                                        >

                                        Absent

                                    </label>

                                </td>

                            </tr>

                        <?php } ?>

                    <?php } else { ?>

                        <tr>

                            <td colspan="4"
                                style="text-align:center;">

                                No active students found.

                            </td>

                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>


            <?php if (count($students) > 0) { ?>

                <button type="submit">
                    ✅ Save Attendance
                </button>

            <?php } ?>

        </form>

    </div>


    <a href="dashboard.php" class="back-link">
        ← Back to Dashboard
    </a>

</div>

</body>

</html>
```
