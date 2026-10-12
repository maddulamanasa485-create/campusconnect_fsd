<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "office") {
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

    <title>Office Dashboard - CampusConnect</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/office.css">
</head>

<body class="office-page">

<div class="office-dashboard">

    <h1>
        Welcome, <?php echo htmlspecialchars($name); ?> 👋
    </h1>

    <p class="subtitle">Office / Staff Dashboard</p>

    <div class="office-cards">

        <a href="fees.php" class="office-card fees-card">
            <div class="icon">💰</div>
            <h2>Fee Management</h2>
            <p>Manage student fees and payments.</p>
        </a>

        <a href="students.php" class="office-card students-card">
            <div class="icon">👥</div>
            <h2>Student Records</h2>
            <p>View and manage student information.</p>
        </a>

        <a href="payments.php" class="office-card payments-card">
            <div class="icon">💳</div>
            <h2>Payment Verification</h2>
            <p>Verify student fee payments.</p>
        </a>

        <a href="receipts.php" class="office-card receipts-card">
            <div class="icon">🧾</div>
            <h2>Receipts</h2>
            <p>View payment receipts.</p>
        </a>

        <a href="nodue.php" class="office-card nodue-card">
            <div class="icon">📄</div>
            <h2>No Due</h2>
            <p>Check student no-due status.</p>
        </a>

        <a href="hallticket.php" class="office-card hallticket-card">
            <div class="icon">🎫</div>
            <h2>Hall Tickets</h2>
            <p>Manage student hall tickets.</p>
        </a>
        <a href="clubs.php" class="office-card">
            <div class="icon">🏆</div>
            <h2>Clubs</h2>
            <p>Manage college clubs and activities.</p>
        </a>

    </div>

    <a href="../auth/logout.php" class="logout">Logout</a>

</div>

</body>
</html>