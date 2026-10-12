```php
<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include "../config/db.php";

$message = "";

/* ADD CLUB */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $coordinator = trim($_POST['coordinator']);
    $meeting_day = trim($_POST['meeting_day']);
    $meeting_time = trim($_POST['meeting_time']);
    $location = trim($_POST['location']);

    if (
        $name !== "" &&
        $description !== "" &&
        $coordinator !== "" &&
        $meeting_day !== "" &&
        $meeting_time !== "" &&
        $location !== ""
    ) {

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO clubs
            (name, description, coordinator, meeting_day, meeting_time, location)
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssssss",
            $name,
            $description,
            $coordinator,
            $meeting_day,
            $meeting_time,
            $location
        );

        if (mysqli_stmt_execute($stmt)) {
            $message = "Club added successfully! 🎉";
        } else {
            $message = "Error adding club.";
        }

        mysqli_stmt_close($stmt);
    }
}

/* GET CLUBS */
$clubs = mysqli_query(
    $conn,
    "SELECT id, name, description, coordinator,
            meeting_day, meeting_time, location
     FROM clubs
     ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Clubs - CampusConnect</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    min-height: 100vh;
    background: linear-gradient(135deg, #ecfdf5, #eff6ff, #f8fafc);
    color: #1e293b;
}

.container {
    width: 92%;
    max-width: 1150px;
    margin: 35px auto;
}

.back {
    display: inline-block;
    margin-bottom: 20px;
    text-decoration: none;
    color: #047857;
    font-weight: bold;
}

.header {
    background: linear-gradient(135deg, #047857, #10b981);
    color: white;
    padding: 35px;
    border-radius: 22px;
    margin-bottom: 25px;
    box-shadow: 0 10px 25px rgba(16,185,129,0.25);
}

.header h1 {
    margin: 0 0 10px;
    font-size: 32px;
}

.header p {
    margin: 0;
}

.success {
    background: #dcfce7;
    color: #166534;
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-weight: bold;
}

.form-card {
    background: white;
    padding: 30px;
    border-radius: 20px;
    margin-bottom: 30px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.form-card h2 {
    margin-top: 0;
    color: #047857;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 18px;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 7px;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 13px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    font-size: 15px;
    font-family: Arial, sans-serif;
}

.form-group textarea {
    min-height: 100px;
    resize: vertical;
}

.full {
    grid-column: 1 / -1;
}

.btn {
    margin-top: 22px;
    padding: 14px 25px;
    border: none;
    border-radius: 11px;
    background: linear-gradient(135deg, #047857, #10b981);
    color: white;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
}

.clubs-title {
    color: #047857;
    margin-bottom: 18px;
}

.club-card {
    background: white;
    padding: 25px;
    border-radius: 18px;
    margin-bottom: 18px;
    box-shadow: 0 7px 20px rgba(0,0,0,0.07);
    border-left: 6px solid #10b981;
}

.club-card h2 {
    margin-top: 0;
    color: #047857;
}

.description {
    color: #475569;
    margin-bottom: 20px;
}

.details {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.detail {
    background: #f0fdf4;
    padding: 13px;
    border-radius: 10px;
}

.detail strong {
    display: block;
    font-size: 13px;
    color: #475569;
    margin-bottom: 5px;
}

.detail span {
    font-weight: bold;
}

.no-clubs {
    background: white;
    padding: 30px;
    text-align: center;
    border-radius: 18px;
    color: #64748b;
}

@media (max-width: 700px) {

    .form-grid,
    .details {
        grid-template-columns: 1fr;
    }

    .full {
        grid-column: auto;
    }

    .header h1 {
        font-size: 25px;
    }
}

</style>

</head>

<body>

<div class="container">

    <a href="dashboard.php" class="back">
        ← Back to Admin Dashboard
    </a>

    <div class="header">

        <h1>🏆 Clubs Management</h1>

        <p>
            Add and manage college clubs
        </p>

    </div>


    <?php if ($message != ""): ?>

        <div class="success">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <!-- ADD CLUB -->

    <div class="form-card">

        <h2>➕ Add New Club</h2>

        <form method="POST">

            <div class="form-grid">

                <div class="form-group">

                    <label>Club Name</label>

                    <input
                        type="text"
                        name="name"
                        placeholder="Example: Coding Club"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>Coordinator</label>

                    <input
                        type="text"
                        name="coordinator"
                        placeholder="Example: Dr. Ramesh"
                        required
                    >

                </div>


                <div class="form-group full">

                    <label>Description</label>

                    <textarea
                        name="description"
                        placeholder="Enter club description"
                        required
                    ></textarea>

                </div>


                <div class="form-group">

                    <label>Meeting Day</label>

                    <input
                        type="text"
                        name="meeting_day"
                        placeholder="Example: Saturday"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>Meeting Time</label>

                    <input
                        type="text"
                        name="meeting_time"
                        placeholder="Example: 3:00 PM - 4:00 PM"
                        required
                    >

                </div>


                <div class="form-group full">

                    <label>Location</label>

                    <input
                        type="text"
                        name="location"
                        placeholder="Example: Main Block"
                        required
                    >

                </div>

            </div>


            <button type="submit" class="btn">
                🏆 Add Club
            </button>

        </form>

    </div>


    <!-- CLUB LIST -->

    <h2 class="clubs-title">
        📋 Available Clubs
    </h2>


    <?php if ($clubs && mysqli_num_rows($clubs) > 0): ?>

        <?php while ($club = mysqli_fetch_assoc($clubs)): ?>

            <div class="club-card">

                <h2>
                    🏆
                    <?php echo htmlspecialchars($club['name']); ?>
                </h2>

                <div class="description">

                    <?php echo htmlspecialchars($club['description']); ?>

                </div>


                <div class="details">

                    <div class="detail">

                        <strong>👨‍🏫 Coordinator</strong>

                        <span>
                            <?php echo htmlspecialchars($club['coordinator']); ?>
                        </span>

                    </div>


                    <div class="detail">

                        <strong>📅 Meeting Day</strong>

                        <span>
                            <?php echo htmlspecialchars($club['meeting_day']); ?>
                        </span>

                    </div>


                    <div class="detail">

                        <strong>⏰ Meeting Time</strong>

                        <span>
                            <?php echo htmlspecialchars($club['meeting_time']); ?>
                        </span>

                    </div>


                    <div class="detail">

                        <strong>📍 Location</strong>

                        <span>
                            <?php echo htmlspecialchars($club['location']); ?>
                        </span>

                    </div>

                </div>

            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <div class="no-clubs">
            🏆 No clubs available currently.
        </div>

    <?php endif; ?>

</div>

</body>

</html>
```
