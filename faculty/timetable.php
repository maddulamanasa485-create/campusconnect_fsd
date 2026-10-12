<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "faculty") {
    header("Location: ../auth/login.php");
    exit;
}

$name = $_SESSION["name"];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Faculty Timetable - CampusConnect</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <link rel="stylesheet" href="../assets/css/faculty.css">

</head>

<body class="faculty-page faculty-timetable-page">

    <div class="faculty-dashboard">

        <h1>📅 Faculty Timetable</h1>

        <p class="subtitle">
            Faculty: <?php echo htmlspecialchars($name); ?>
        </p>

        <div class="faculty-table-card">

            <h2>Weekly Teaching Schedule</h2>

            <div style="overflow-x:auto;">

                <table>

                    <tr>
                        <th>Day</th>
                        <th>9:00 - 10:00</th>
                        <th>10:00 - 11:00</th>
                        <th>11:15 - 12:15</th>
                        <th>1:15 - 2:15</th>
                        <th>2:15 - 3:15</th>
                    </tr>

                    <tr>
                        <td><strong>Monday</strong></td>
                        <td>PHP</td>
                        <td>DBMS</td>
                        <td>Free</td>
                        <td>AI</td>
                        <td>Lab</td>
                    </tr>

                    <tr>
                        <td><strong>Tuesday</strong></td>
                        <td>DBMS</td>
                        <td>PHP</td>
                        <td>OS</td>
                        <td>Free</td>
                        <td>AI</td>
                    </tr>

                    <tr>
                        <td><strong>Wednesday</strong></td>
                        <td>AI</td>
                        <td>Free</td>
                        <td>PHP</td>
                        <td>DBMS</td>
                        <td>Lab</td>
                    </tr>

                    <tr>
                        <td><strong>Thursday</strong></td>
                        <td>OS</td>
                        <td>PHP</td>
                        <td>DBMS</td>
                        <td>AI</td>
                        <td>Free</td>
                    </tr>

                    <tr>
                        <td><strong>Friday</strong></td>
                        <td>PHP</td>
                        <td>AI</td>
                        <td>OS</td>
                        <td>DBMS</td>
                        <td>Lab</td>
                    </tr>

                </table>

            </div>

        </div>

        <a href="dashboard.php" class="back-link">
            ← Back to Dashboard
        </a>

    </div>

</body>

</html>