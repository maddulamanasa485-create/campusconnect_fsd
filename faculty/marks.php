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


/* GET ACTIVE STUDENTS FROM DATABASE */

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


/* ADD MARKS */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_id = intval($_POST["student_id"] ?? 0);
    $semester = intval($_POST["semester"] ?? 0);
    $subject = trim($_POST["subject"] ?? "");
    $exam = trim($_POST["exam"] ?? "");
    $marks_value = intval($_POST["marks"] ?? -1);
    $max_marks = intval($_POST["max_marks"] ?? 100);
    $credits = intval($_POST["credits"] ?? 3);


    if ($student_id <= 0) {

        $message = "Please select a student.";
        $message_type = "error";

    } elseif ($semester <= 0 || $semester > 8) {

        $message = "Please select a valid semester.";
        $message_type = "error";

    } elseif ($subject == "") {

        $message = "Please select subject.";
        $message_type = "error";

    } elseif ($exam == "") {

        $message = "Please select exam.";
        $message_type = "error";

    } elseif ($max_marks <= 0) {

        $message = "Please enter valid maximum marks.";
        $message_type = "error";

    } elseif ($marks_value < 0 || $marks_value > $max_marks) {

        $message = "Please enter valid marks.";
        $message_type = "error";

    } elseif ($credits <= 0 || $credits > 10) {

        $message = "Please enter valid credits.";
        $message_type = "error";

    } else {

        /* Check selected student really exists */
        $check_sql = "SELECT id
                      FROM users
                      WHERE id = ?
                      AND role = 'student'
                      AND status = 1";

        $check_stmt = mysqli_prepare($conn, $check_sql);

        if ($check_stmt) {

            mysqli_stmt_bind_param(
                $check_stmt,
                "i",
                $student_id
            );

            mysqli_stmt_execute($check_stmt);

            $check_result = mysqli_stmt_get_result($check_stmt);

            if (mysqli_num_rows($check_result) == 0) {

                $message = "Invalid student selected.";
                $message_type = "error";

            } else {

                /* Insert marks */

                $sql = "INSERT INTO marks
                        (faculty_id, student_id, semester, subject, exam, marks, max_marks, credits)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt = mysqli_prepare($conn, $sql);

                if ($stmt) {

                    mysqli_stmt_bind_param(
                        $stmt,
                        "iiissiii",
                        $faculty_id,
                        $student_id,
                        $semester,
                        $subject,
                        $exam,
                        $marks_value,
                        $max_marks,
                        $credits
                    );

                    if (mysqli_stmt_execute($stmt)) {

                        $message = "Marks added successfully! ✅";
                        $message_type = "success";

                    } else {

                        $message = "Database error while saving marks.";
                        $message_type = "error";
                    }

                    mysqli_stmt_close($stmt);

                } else {

                    $message = "Database prepare error.";
                    $message_type = "error";
                }
            }

            mysqli_stmt_close($check_stmt);

        } else {

            $message = "Unable to verify student.";
            $message_type = "error";
        }
    }
}


/* GET RECENT MARKS WITH STUDENT DETAILS */

$sql = "SELECT 
            marks.student_id,
            marks.semester,
            marks.subject,
            marks.exam,
            marks.marks,
            marks.max_marks,
            marks.credits,
            marks.created_at,
            users.name AS student_name,
            users.email AS student_email
        FROM marks
        INNER JOIN users
            ON marks.student_id = users.id
        WHERE marks.faculty_id = ?
        AND users.role = 'student'
        ORDER BY marks.created_at DESC";

$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $faculty_id
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

} else {

    $result = false;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Marks / Results - CampusConnect</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

    <link
        rel="stylesheet"
        href="../assets/css/faculty.css"
    >

</head>


<body class="faculty-page faculty-marks-page">


<div class="faculty-dashboard">


    <h1>📝 Marks / Results</h1>


    <p class="subtitle">

        Faculty:
        <?php echo htmlspecialchars($name); ?>

    </p>


    <!-- MESSAGE -->

    <?php if ($message != "") { ?>

        <div class="marks-message <?php echo htmlspecialchars($message_type); ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php } ?>


    <!-- MARKS FORM -->

    <div class="marks-upload-card">

        <h2>📝 Enter Student Marks</h2>


        <form action="" method="POST">


            <!-- STUDENT -->

            <div class="marks-form-group">

                <label for="student_id">
                    Student
                </label>


                <select
                    id="student_id"
                    name="student_id"
                    required
                >

                    <option value="">
                        Select Student
                    </option>


                    <?php foreach ($students as $student) { ?>

                        <option
                            value="<?php echo $student["id"]; ?>"
                        >

                            <?php
                            echo htmlspecialchars(
                                "ID " .
                                $student["id"] .
                                " - " .
                                $student["name"]
                            );
                            ?>

                        </option>

                    <?php } ?>

                </select>

            </div>


            <!-- SEMESTER -->

            <div class="marks-form-group">

                <label for="semester">
                    Semester
                </label>


                <select
                    id="semester"
                    name="semester"
                    required
                >

                    <option value="">
                        Select Semester
                    </option>

                    <option value="1">1st Semester</option>
                    <option value="2">2nd Semester</option>
                    <option value="3">3rd Semester</option>
                    <option value="4">4th Semester</option>
                    <option value="5">5th Semester</option>
                    <option value="6">6th Semester</option>
                    <option value="7">7th Semester</option>
                    <option value="8">8th Semester</option>

                </select>

            </div>


            <!-- SUBJECT -->

            <div class="marks-form-group">

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


            <!-- EXAM -->

            <div class="marks-form-group">

                <label for="exam">
                    Exam
                </label>


                <select
                    id="exam"
                    name="exam"
                    required
                >

                    <option value="">
                        Select Exam
                    </option>

                    <option value="Internal 1">
                        Internal 1
                    </option>

                    <option value="Internal 2">
                        Internal 2
                    </option>

                    <option value="Semester Exam">
                        Semester Exam
                    </option>

                </select>

            </div>


            <!-- MARKS -->

            <div class="marks-row">


                <div class="marks-form-group">

                    <label for="marks">
                        Obtained Marks
                    </label>


                    <input
                        type="number"
                        id="marks"
                        name="marks"
                        min="0"
                        max="100"
                        placeholder="Enter marks"
                        required
                    >

                </div>


                <div class="marks-form-group">

                    <label for="max_marks">
                        Maximum Marks
                    </label>


                    <input
                        type="number"
                        id="max_marks"
                        name="max_marks"
                        min="1"
                        value="100"
                        required
                    >

                </div>


            </div>


            <!-- CREDITS -->

            <div class="marks-form-group">

                <label for="credits">
                    Subject Credits
                </label>


                <input
                    type="number"
                    id="credits"
                    name="credits"
                    min="1"
                    max="10"
                    value="3"
                    required
                >

                <small>
                    Enter the credit value of this subject.
                </small>

            </div>


            <!-- BUTTON -->

            <button type="submit">

                ✅ Save Marks

            </button>


        </form>

    </div>


    <!-- RECENT MARKS -->

    <div class="recent-marks">

        <h2>
            📋 Recently Added Marks
        </h2>


        <?php if ($result && mysqli_num_rows($result) > 0) { ?>


            <div class="marks-table-wrapper">


                <table class="marks-table">


                    <thead>

                        <tr>

                            <th>Student</th>
                            <th>Semester</th>
                            <th>Subject</th>
                            <th>Exam</th>
                            <th>Marks</th>
                            <th>Credits</th>
                            <th>Date</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>


                        <tr>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    "ID " .
                                    $row["student_id"] .
                                    " - " .
                                    $row["student_name"]
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $row["semester"]
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $row["subject"]
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $row["exam"]
                                );
                                ?>

                            </td>


                            <td>

                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $row["marks"]
                                    );
                                    ?>

                                </strong>

                                /

                                <?php
                                echo htmlspecialchars(
                                    $row["max_marks"]
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $row["credits"]
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo date(
                                    "d M Y",
                                    strtotime(
                                        $row["created_at"]
                                    )
                                );
                                ?>

                            </td>


                        </tr>


                    <?php } ?>


                    </tbody>

                </table>

            </div>


        <?php } else { ?>


            <div class="no-marks">

                📝 No marks added yet.

            </div>


        <?php } ?>


    </div>


    <a
        href="dashboard.php"
        class="back-link"
    >

        ← Back to Dashboard

    </a>


</div>


</body>

</html>
```
