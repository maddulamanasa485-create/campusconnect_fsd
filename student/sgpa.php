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


/* GRADE POINT CALCULATION */

function getGradePoint($percentage)
{
    if ($percentage >= 90) {
        return 10;
    } elseif ($percentage >= 80) {
        return 9;
    } elseif ($percentage >= 70) {
        return 8;
    } elseif ($percentage >= 60) {
        return 7;
    } elseif ($percentage >= 50) {
        return 6;
    } elseif ($percentage >= 40) {
        return 5;
    } else {
        return 0;
    }
}


/* GET SEMESTER EXAM MARKS */

$sql = "SELECT semester, subject, marks, max_marks, credits
        FROM marks
        WHERE student_id = ?
        AND exam = 'Semester Exam'
        ORDER BY semester ASC, subject ASC";

$stmt = mysqli_prepare($conn, $sql);

$semester_data = [];

if ($stmt) {

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $student_id
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);


    while ($row = mysqli_fetch_assoc($result)) {

        $semester = intval($row["semester"]);

        $marks = floatval($row["marks"]);
        $max_marks = floatval($row["max_marks"]);
        $credits = intval($row["credits"]);


        /* Calculate percentage */

        if ($max_marks > 0) {

            $percentage =
                ($marks / $max_marks) * 100;

        } else {

            $percentage = 0;

        }


        /* Calculate grade point */

        $grade_point =
            getGradePoint($percentage);


        /* Create semester */

        if (!isset($semester_data[$semester])) {

            $semester_data[$semester] = [
                "total_points" => 0,
                "total_credits" => 0
            ];

        }


        /* SGPA calculation data */

        $semester_data[$semester]["total_points"] +=
            $grade_point * $credits;

        $semester_data[$semester]["total_credits"] +=
            $credits;

    }


    mysqli_stmt_close($stmt);

}


/* SORT SEMESTERS */

if (!empty($semester_data)) {

    ksort($semester_data);

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

    <title>SGPA - CampusConnect</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

    <link
        rel="stylesheet"
        href="../assets/css/student-pages.css"
    >

</head>


<body class="student-page sgpa-page">


<div class="dashboard">


    <h1>📊 Semester-wise SGPA</h1>


    <p>

        Student:
        <?php echo htmlspecialchars($name); ?>

    </p>


    <div class="card">


        <h2>Semester SGPA Details</h2>


        <div style="overflow-x:auto;">


            <table>


                <tr>

                    <th>Semester</th>

                    <th>SGPA</th>

                    <th>Credits</th>

                    <th>Status</th>

                </tr>


                <?php if (!empty($semester_data)) { ?>


                    <?php foreach ($semester_data as $semester => $data) { ?>


                        <?php

                        if ($data["total_credits"] > 0) {

                            $sgpa =
                                $data["total_points"]
                                /
                                $data["total_credits"];

                        } else {

                            $sgpa = 0;

                        }

                        ?>


                        <tr>


                            <td>

                                Semester
                                <?php
                                echo htmlspecialchars($semester);
                                ?>

                            </td>


                            <td>

                                <strong>

                                    <?php
                                    echo number_format(
                                        $sgpa,
                                        2
                                    );
                                    ?>

                                </strong>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $data["total_credits"]
                                );
                                ?>

                            </td>


                            <td class="completed">

                                Completed ✅

                            </td>


                        </tr>


                    <?php } ?>


                <?php } else { ?>


                    <tr>

                        <td colspan="4">

                            No Semester Exam marks available yet.

                        </td>

                    </tr>


                <?php } ?>


            </table>


        </div>


    </div>


    <?php

    /* FIND LATEST SEMESTER */

    $latest_sgpa = 0;
    $latest_semester = 0;


    if (!empty($semester_data)) {

        $latest_semester =
            max(array_keys($semester_data));


        $latest_data =
            $semester_data[$latest_semester];


        if ($latest_data["total_credits"] > 0) {

            $latest_sgpa =
                $latest_data["total_points"]
                /
                $latest_data["total_credits"];

        }

    }

    ?>


    <?php if (!empty($semester_data)) { ?>


        <div class="latest-sgpa">

            Latest SGPA:

            <?php
            echo number_format(
                $latest_sgpa,
                2
            );
            ?>

            🎓

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
