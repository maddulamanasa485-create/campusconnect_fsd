<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit();
}

include "../config/db.php";

$student_id = $_SESSION['user_id'];

/* Grade Point Calculation */
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

/* Get Semester Exam Marks */
$sql = "SELECT semester, subject, marks, max_marks, credits
        FROM marks
        WHERE student_id = ?
        AND exam = 'Semester Exam'
        ORDER BY semester ASC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$semesterData = [];

while ($row = mysqli_fetch_assoc($result)) {

    $semester = (int)$row['semester'];
    $marks = (float)$row['marks'];
    $max_marks = (float)$row['max_marks'];
    $credits = (int)$row['credits'];

    if ($max_marks <= 0 || $credits <= 0) {
        continue;
    }

    $percentage = ($marks / $max_marks) * 100;
    $gradePoint = getGradePoint($percentage);

    if (!isset($semesterData[$semester])) {
        $semesterData[$semester] = [
            'weighted_points' => 0,
            'credits' => 0
        ];
    }

    $semesterData[$semester]['weighted_points']
        += $gradePoint * $credits;

    $semesterData[$semester]['credits']
        += $credits;
}

ksort($semesterData);

/* Calculate CGPA */
$totalWeightedPoints = 0;
$totalCredits = 0;

$semesterResults = [];

foreach ($semesterData as $semester => $data) {

    if ($data['credits'] > 0) {

        $sgpa = $data['weighted_points'] / $data['credits'];

        $semesterResults[$semester] = $sgpa;

        $totalWeightedPoints += $data['weighted_points'];
        $totalCredits += $data['credits'];
    }
}

$cgpa = $totalCredits > 0
    ? $totalWeightedPoints / $totalCredits
    : 0;
?>

<!DOCTYPE html>
<html>
<head>

    <title>CGPA - CampusConnect</title>

    <link rel="stylesheet" href="../assets/css/student-pages.css">

    <style>

        .cgpa-box {
            max-width: 900px;
            margin: 40px auto;
            padding: 35px;
            background: #f3ecff;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }

        .cgpa-title {
            text-align: center;
            color: #5b21b6;
            margin-bottom: 10px;
        }

        .student-name {
            text-align: center;
            color: #555;
            margin-bottom: 30px;
        }

        .cgpa-main {
            text-align: center;
            background: white;
            padding: 30px;
            border-radius: 18px;
            margin-bottom: 30px;
        }

        .cgpa-value {
            font-size: 48px;
            font-weight: bold;
            color: #6d28d9;
        }

        .cgpa-label {
            font-size: 18px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
        }

        th {
            background: #6d28d9;
            color: white;
            padding: 14px;
        }

        td {
            padding: 14px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        .completed {
            color: green;
            font-weight: bold;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #777;
        }

    </style>

</head>

<body>

<div class="cgpa-box">

    <h1 class="cgpa-title">📊 Overall CGPA</h1>

    <p class="student-name">
        Student: <strong><?php echo htmlspecialchars($_SESSION['name'] ?? 'Student'); ?></strong>
    </p>

    <div class="cgpa-main">

        <div class="cgpa-value">
            <?php echo number_format($cgpa, 2); ?>
        </div>

        <div class="cgpa-label">
            Overall CGPA 🎓
        </div>

    </div>

    <h2>Semester-wise Details</h2>

    <?php if (!empty($semesterResults)) { ?>

        <table>

            <tr>
                <th>Semester</th>
                <th>SGPA</th>
                <th>Credits</th>
                <th>Status</th>
            </tr>

            <?php foreach ($semesterResults as $semester => $sgpa) { ?>

                <tr>

                    <td>
                        Semester <?php echo $semester; ?>
                    </td>

                    <td>
                        <strong>
                            <?php echo number_format($sgpa, 2); ?>
                        </strong>
                    </td>

                    <td>
                        <?php echo $semesterData[$semester]['credits']; ?>
                    </td>

                    <td class="completed">
                        Completed ✅
                    </td>

                </tr>

            <?php } ?>

        </table>

    <?php } else { ?>

        <div class="no-data">
            No semester results available yet.
        </div>

    <?php } ?>

    <br>

    <a href="dashboard.php" class="back-link">
        ← Back to Dashboard
    </a>

</div>

</body>
</html>