<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit();
}

include "../config/db.php";

$student_id = $_SESSION['user_id'];

$sql = "SELECT subject,
               COUNT(*) AS total_classes,
               SUM(status = 'Present') AS present_classes,
               SUM(status = 'Absent') AS absent_classes
        FROM attendance
        WHERE student_id = ?
        GROUP BY subject
        ORDER BY subject ASC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$totalClasses = 0;
$totalPresent = 0;
?>

<!DOCTYPE html>
<html>
<head>

    <title>Attendance - CampusConnect</title>

    <link rel="stylesheet" href="../assets/css/student-pages.css">

    <style>

        .attendance-box {
            max-width: 950px;
            margin: 40px auto;
            padding: 35px;
            background: #eaf7ff;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }

        .attendance-title {
            text-align: center;
            color: #0369a1;
            margin-bottom: 10px;
        }

        .student-name {
            text-align: center;
            color: #555;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
        }

        th {
            background: #0284c7;
            color: white;
            padding: 14px;
        }

        td {
            padding: 14px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        .percentage {
            font-weight: bold;
            color: #0369a1;
        }

        .good {
            color: green;
            font-weight: bold;
        }

        .warning {
            color: orange;
            font-weight: bold;
        }

        .low {
            color: red;
            font-weight: bold;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #777;
            background: white;
            border-radius: 12px;
        }

    </style>

</head>

<body>

<div class="attendance-box">

    <h1 class="attendance-title">📊 Attendance</h1>

    <p class="student-name">
        Student:
        <strong>
            <?php echo htmlspecialchars($_SESSION['name'] ?? 'Student'); ?>
        </strong>
    </p>

    <?php if (mysqli_num_rows($result) > 0) { ?>

        <table>

            <tr>
                <th>Subject</th>
                <th>Total Classes</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Attendance %</th>
                <th>Status</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) {

                $total = (int)$row['total_classes'];
                $present = (int)$row['present_classes'];
                $absent = (int)$row['absent_classes'];

                $percentage = $total > 0
                    ? ($present / $total) * 100
                    : 0;

                $totalClasses += $total;
                $totalPresent += $present;

                if ($percentage >= 75) {
                    $status = "Good ✅";
                    $class = "good";
                } elseif ($percentage >= 65) {
                    $status = "Warning ⚠️";
                    $class = "warning";
                } else {
                    $status = "Low ❌";
                    $class = "low";
                }
            ?>

                <tr>

                    <td>
                        <strong>
                            <?php echo htmlspecialchars($row['subject']); ?>
                        </strong>
                    </td>

                    <td>
                        <?php echo $total; ?>
                    </td>

                    <td>
                        <?php echo $present; ?>
                    </td>

                    <td>
                        <?php echo $absent; ?>
                    </td>

                    <td class="percentage">
                        <?php echo number_format($percentage, 2); ?>%
                    </td>

                    <td class="<?php echo $class; ?>">
                        <?php echo $status; ?>
                    </td>

                </tr>

            <?php } ?>

        </table>

        <?php

        $overallPercentage = $totalClasses > 0
            ? ($totalPresent / $totalClasses) * 100
            : 0;

        ?>

        <div style="
            margin-top:25px;
            padding:20px;
            background:white;
            border-radius:15px;
            text-align:center;
        ">

            <h2>Overall Attendance</h2>

            <div style="
                font-size:38px;
                font-weight:bold;
                color:#0284c7;
            ">
                <?php echo number_format($overallPercentage, 2); ?>%
            </div>

        </div>

    <?php } else { ?>

        <div class="no-data">

            <h3>📭 No Attendance Records</h3>

            <p>
                Faculty has not entered your attendance yet.
            </p>

        </div>

    <?php } ?>

    <br>

    <a href="dashboard.php" class="back-link">
        ← Back to Dashboard
    </a>

</div>

</body>
</html>