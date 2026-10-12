<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

$name = $_SESSION["name"];
$student_id = $_SESSION["user_id"];


/* Get student's hall tickets */

$sql = "SELECT exam_name, semester, exam_date, exam_time,
               exam_center, status
        FROM hall_tickets
        WHERE student_id = ?
        AND status = 'Available'
        ORDER BY exam_date ASC";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $student_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Hall Ticket - CampusConnect</title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

    <link rel="stylesheet"
          href="../assets/css/student-pages.css">

</head>

<body class="student-page hallticket-page">

<div class="dashboard">

    <h1>🎫 Hall Ticket</h1>

    <p>
        Student Examination Hall Ticket
    </p>


    <?php if ($result && mysqli_num_rows($result) > 0) { ?>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

            <div class="hallticket-card">

                <!-- College Header -->

                <div class="college-header">

                    <h2>CampusConnect College</h2>

                    <p>
                        College Examination Department
                    </p>

                    <h3>
                        <?php
                        echo htmlspecialchars($row["exam_name"]);
                        ?>
                        - HALL TICKET
                    </h3>

                </div>


                <!-- Student Details -->

                <div class="student-details">

                    <div>

                        <strong>Student Name</strong>

                        <span>
                            <?php
                            echo htmlspecialchars($name);
                            ?>
                        </span>

                    </div>


                    <div>

                        <strong>Student ID</strong>

                        <span>
                            <?php
                            echo htmlspecialchars($student_id);
                            ?>
                        </span>

                    </div>


                    <div>

                        <strong>Department</strong>

                        <span>
                            Computer Science & Engineering
                        </span>

                    </div>


                    <div>

                        <strong>Semester</strong>

                        <span>
                            <?php
                            echo htmlspecialchars(
                                $row["semester"]
                            );
                            ?> Semester
                        </span>

                    </div>

                </div>


                <!-- Examination Details -->

                <h3 class="exam-title">
                    Examination Details
                </h3>


                <div style="overflow-x:auto;">

                    <table>

                        <tr>

                            <th>Exam Date</th>

                            <th>Exam Time</th>

                            <th>Exam Center</th>

                            <th>Status</th>

                        </tr>


                        <tr>

                            <td>

                                <?php

                                echo date(
                                    "d-m-Y",
                                    strtotime(
                                        $row["exam_date"]
                                    )
                                );

                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $row["exam_time"]
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $row["exam_center"]
                                );
                                ?>

                            </td>


                            <td>

                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $row["status"]
                                    );
                                    ?>
                                </strong>

                            </td>

                        </tr>

                    </table>

                </div>


                <!-- Instructions -->

                <div class="hallticket-note">

                    <strong>
                        Important Instructions:
                    </strong>

                    <ul>

                        <li>
                            Carry this hall ticket to the examination hall.
                        </li>

                        <li>
                            Carry your valid college ID card.
                        </li>

                        <li>
                            Report to the examination hall 15 minutes
                            before the exam.
                        </li>

                    </ul>

                </div>


                <!-- Print -->

                <button
                    class="print-btn"
                    onclick="window.print()">

                    🖨️ Print / Download Hall Ticket

                </button>

            </div>

        <?php } ?>


    <?php } else { ?>

        <div class="hallticket-card">

            <div class="college-header">

                <h2>🎫 No Hall Ticket Available</h2>

                <p>
                    Your hall ticket has not been issued yet.
                </p>

                <p>
                    Please contact the examination department.
                </p>

            </div>

        </div>

    <?php } ?>


    <a href="dashboard.php"
       class="back-link">

        ← Back to Dashboard

    </a>

</div>

</body>

</html>