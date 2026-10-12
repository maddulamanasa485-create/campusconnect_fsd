<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include "../config/db.php";

$sql = "SELECT id, name, email, role, status
        FROM users
        WHERE role = 'faculty'
        ORDER BY id ASC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Faculty - CampusConnect</title>
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

        .faculty-card {
            background: white;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .faculty-card h2 {
            margin-bottom: 10px;
        }

        .faculty-card p {
            margin: 7px 0;
        }

        .status {
            color: green;
            font-weight: bold;
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
        <h1>👨‍🏫 Faculty Management</h1>
        <p>View registered faculty members</p>
    </div>

    <?php if (mysqli_num_rows($result) > 0): ?>

        <?php while ($faculty = mysqli_fetch_assoc($result)): ?>

            <div class="faculty-card">

                <h2>👨‍🏫 <?php echo htmlspecialchars($faculty['name']); ?></h2>

                <p>
                    🆔 <strong>Faculty ID:</strong>
                    <?php echo $faculty['id']; ?>
                </p>

                <p>
                    📧 <strong>Email:</strong>
                    <?php echo htmlspecialchars($faculty['email']); ?>
                </p>

                <p>
                    👤 <strong>Role:</strong>
                    <?php echo htmlspecialchars($faculty['role']); ?>
                </p>

                <p class="status">
                    ● <?php echo $faculty['status'] == 1 ? 'Active' : 'Inactive'; ?>
                </p>

            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <p>No faculty members found.</p>

    <?php endif; ?>

</div>

</body>
</html>