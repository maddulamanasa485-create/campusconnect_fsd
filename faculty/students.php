```php
<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "faculty") {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

$name = $_SESSION["name"];

// Get only students from database
$sql = "SELECT id, name, email, status
        FROM users
        WHERE role = 'student'
        ORDER BY id ASC";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Students - CampusConnect</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/faculty.css">

</head>

<body class="faculty-page faculty-students-page">

<div class="faculty-dashboard">

    <h1>👥 Student Information</h1>

    <p class="subtitle">
        Faculty: <?php echo htmlspecialchars($name); ?>
    </p>

    <div class="student-table-card">

        <h2>Student List</h2>

        <div style="overflow-x:auto;">

            <table>

                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Year</th>
                    <th>Status</th>
                </tr>

                <?php if (mysqli_num_rows($result) > 0): ?>

                    <?php while ($student = mysqli_fetch_assoc($result)): ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars($student["id"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($student["name"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($student["email"]); ?>
                            </td>

                            <td>CSE</td>

                            <td>4th Year</td>

                            <td class="active">
                                <?php
                                echo ($student["status"] == 1)
                                    ? "Active"
                                    : "Inactive";
                                ?>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="6" style="text-align:center;">
                            No students found.
                        </td>

                    </tr>

                <?php endif; ?>

            </table>

        </div>

    </div>

    <a href="dashboard.php" class="back-link">
        ← Back to Dashboard
    </a>

</div>

</body>

</html>
```
