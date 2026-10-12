<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include "../config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name']);
    $hod = trim($_POST['hod']);
    $description = trim($_POST['description']);

    if ($name !== "" && $hod !== "") {

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO departments (name, hod, description)
             VALUES (?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sss",
            $name,
            $hod,
            $description
        );

        if (mysqli_stmt_execute($stmt)) {
            $message = "Department added successfully! 🎉";
        }

        mysqli_stmt_close($stmt);
    }
}

$result = mysqli_query(
    $conn,
    "SELECT id, name, hod, description
     FROM departments
     ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Departments - CampusConnect</title>
    <link rel="stylesheet" href="../assets/css/office.css">

    <style>
        .admin-container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }

        .header-box {
            background: linear-gradient(135deg, #182848, #4b6cb7);
            color: white;
            padding: 25px;
            border-radius: 18px;
            margin-bottom: 25px;
        }

        .form-box,
        .department-card {
            background: white;
            padding: 25px;
            border-radius: 18px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        input,
        textarea {
            width: 100%;
            padding: 12px;
            margin: 8px 0 18px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-sizing: border-box;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        button {
            padding: 12px 22px;
            border: none;
            border-radius: 10px;
            background: #4b6cb7;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }

        .success {
            background: #e8fff0;
            color: #16803c;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .department-card h2 {
            color: #182848;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #182848;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="admin-container">

    <a href="dashboard.php" class="back-link">
        ← Back to Admin Dashboard
    </a>

    <div class="header-box">
        <h1>🏢 Department Management</h1>
        <p>Add and manage college departments</p>
    </div>

    <?php if ($message): ?>
        <div class="success">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="form-box">

        <h2>➕ Add New Department</h2>

        <form method="POST">

            <label><strong>Department Name</strong></label>
            <input
                type="text"
                name="name"
                placeholder="Example: Computer Science and Engineering"
                required
            >

            <label><strong>Head of Department</strong></label>
            <input
                type="text"
                name="hod"
                placeholder="Example: Dr. Ramesh"
                required
            >

            <label><strong>Description</strong></label>
            <textarea
                name="description"
                placeholder="Enter department description"
            ></textarea>

            <button type="submit">
                🏢 Add Department
            </button>

        </form>

    </div>

    <h2>📋 Available Departments</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>

        <?php while ($department = mysqli_fetch_assoc($result)): ?>

            <div class="department-card">

                <h2>
                    🏢 <?php echo htmlspecialchars($department['name']); ?>
                </h2>

                <p>
                    👨‍🏫 <strong>HOD:</strong>
                    <?php echo htmlspecialchars($department['hod']); ?>
                </p>

                <p>
                    📝 <strong>Description:</strong>
                    <?php echo htmlspecialchars($department['description']); ?>
                </p>

            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <p>No departments available.</p>

    <?php endif; ?>

</div>

</body>
</html>