<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../auth/login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Timetable - CampusConnect</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <link rel="stylesheet" href="../assets/css/student-pages.css">

</head>

<body class="student-page timetable-page">

    <div class="dashboard">

        <h1>📅 My Timetable</h1>

        <p>Student Class Timetable</p>

        <div class="card">

            <h2>Weekly Timetable</h2>

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
                        <td>DBMS</td>
                        <td>PHP</td>
                        <td>OS</td>
                        <td>AI</td>
                        <td>Lab</td>
                    </tr>

                    <tr>
                        <td><strong>Tuesday</strong></td>
                        <td>PHP</td>
                        <td>CN</td>
                        <td>DBMS</td>
                        <td>Lab</td>
                        <td>AI</td>
                    </tr>

                    <tr>
                        <td><strong>Wednesday</strong></td>
                        <td>OS</td>
                        <td>AI</td>
                        <td>PHP</td>
                        <td>CN</td>
                        <td>Lab</td>
                    </tr>

                    <tr>
                        <td><strong>Thursday</strong></td>
                        <td>CN</td>
                        <td>DBMS</td>
                        <td>AI</td>
                        <td>PHP</td>
                        <td>Library</td>
                    </tr>

                    <tr>
                        <td><strong>Friday</strong></td>
                        <td>AI</td>
                        <td>PHP</td>
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