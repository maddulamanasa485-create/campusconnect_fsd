```php
<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

$name = $_SESSION["name"];
$student_id = $_SESSION["user_id"];


/* GET STUDENT MARKS */

$sql = "SELECT semester, subject, exam, marks, max_marks, credits, created_at
        FROM marks
        WHERE student_id = ?
        ORDER BY semester ASC, created_at DESC";

$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {

    mysqli_stmt_bind_param($stmt, "i", $student_id);

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

    <title>Results - CampusConnect</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

    <link
        rel="stylesheet"
        href="../assets/css/student-pages.css"
    >

</head>


<body class="student-page results-page">


<div class="student-container">


    <h1>📝 My Results</h1>


    <p class="page-subtitle">

        Welcome,
        <?php echo htmlspecialchars($name); ?>
        👋

    </p>


    <?php if ($result && mysqli_num_rows($result) > 0) { ?>


        <div class="results-table-wrapper">


            <table class="results-table">


                <thead>

                    <tr>

                        <th>Semester</th>

                        <th>Subject</th>

                        <th>Exam</th>

                        <th>Marks</th>

                        <th>Percentage</th>

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

                            if ($row["max_marks"] > 0) {

                                $percentage =
                                    (
                                        $row["marks"]
                                        /
                                        $row["max_marks"]
                                    ) * 100;

                                echo number_format(
                                    $percentage,
                                    2
                                ) . "%";

                            } else {

                                echo "0%";

                            }

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


        <div class="no-results">

            📝 No results available yet.

            <p>
                Your marks will appear here after faculty uploads them.
            </p>

        </div>


    <?php } ?>


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
