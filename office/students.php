<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "office") {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

/* GET STUDENTS */
$sql = "SELECT id, name, email, status
        FROM users
        WHERE role = 'student'
        ORDER BY id ASC";

$students = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Records - CampusConnect</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/office.css">

    <style>

        body {
            background: #f5f7fb;
        }

        .students-container {
            width: 90%;
            max-width: 1100px;
            margin: 40px auto;
        }

        .students-container h1 {
            text-align: center;
            color: #1f3c88;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        .students-table-box {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #1f3c88;
            color: white;
            padding: 14px;
            text-align: left;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background: #f7f9ff;
        }

        .active {
            color: green;
            font-weight: bold;
        }

        .inactive {
            color: red;
            font-weight: bold;
        }

        .back-link {
            display: inline-block;
            margin-top: 25px;
            text-decoration: none;
            color: #1f3c88;
            font-weight: bold;
        }

    </style>

</head>

<body>

<div class="students-container">

    <h1>👥 Student Records</h1>

    <p class="subtitle">
        View registered student information
    </p>

    <div class="students-table-box">

        <?php if ($students && mysqli_num_rows($students) > 0): ?>

            <table>

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Year</th>
                        <th>Status</th>
                    </tr>

                </thead>

                <tbody>

                    <?php while ($student = mysqli_fetch_assoc($students)): ?>

                        <tr>

                            <td>
                                <?php echo $student["id"]; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($student["name"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($student["email"]); ?>
                            </td>

                            <td>
                                Computer Science & Engineering
                            </td>

                            <td>
                                4th Year
                            </td>

                            <td>

                                <?php if ((int)$student["status"] === 1): ?>

                                    <span class="active">
                                        Active
                                    </span>

                                <?php else: ?>

                                    <span class="inactive">
                                        Inactive
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        <?php else: ?>

            <p style="text-align:center;">
                No students found.
            </p>

        <?php endif; ?>

    </div>

    <a href="dashboard.php" class="back-link">
        ← Back to Office Dashboard
    </a>

</div>

</body>

</html>