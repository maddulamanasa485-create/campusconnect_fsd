```php
<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include "../config/db.php";

$message = "";

/* ADD ANNOUNCEMENT */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST['title']);
    $announcement_message = trim($_POST['message']);

    if ($title !== "" && $announcement_message !== "") {

        $admin_id = $_SESSION['user_id'];

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO announcements
            (faculty_id, title, message)
            VALUES (?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "iss",
            $admin_id,
            $title,
            $announcement_message
        );

        if (mysqli_stmt_execute($stmt)) {
            $message = "Announcement posted successfully! 📢";
        } else {
            $message = "Error posting announcement.";
        }

        mysqli_stmt_close($stmt);
    }
}


/* GET ANNOUNCEMENTS */

$announcements = mysqli_query(
    $conn,
    "SELECT id, title, message, created_at
     FROM announcements
     ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Announcements - CampusConnect</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    min-height: 100vh;
    background: linear-gradient(
        135deg,
        #fff7ed,
        #fef2f2,
        #eef2ff
    );
    color: #1e293b;
}

.container {
    width: 92%;
    max-width: 1100px;
    margin: 35px auto;
}

.back {
    display: inline-block;
    margin-bottom: 20px;
    text-decoration: none;
    color: #c2410c;
    font-weight: bold;
}

.header {
    background: linear-gradient(
        135deg,
        #ea580c,
        #f97316
    );
    color: white;
    padding: 35px;
    border-radius: 22px;
    margin-bottom: 25px;
    box-shadow: 0 10px 25px rgba(234, 88, 12, 0.25);
}

.header h1 {
    margin: 0 0 10px;
    font-size: 32px;
}

.header p {
    margin: 0;
    opacity: 0.9;
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
    color: #c2410c;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 8px;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 14px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    font-size: 15px;
    font-family: Arial, sans-serif;
    outline: none;
}

.form-group textarea {
    min-height: 130px;
    resize: vertical;
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: #f97316;
}

.btn {
    padding: 14px 25px;
    border: none;
    border-radius: 11px;
    background: linear-gradient(
        135deg,
        #ea580c,
        #f97316
    );
    color: white;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
}

.btn:hover {
    transform: translateY(-1px);
}

.title {
    color: #c2410c;
    margin-bottom: 18px;
}

.announcement-card {
    background: white;
    padding: 25px;
    border-radius: 18px;
    margin-bottom: 18px;
    box-shadow: 0 7px 20px rgba(0,0,0,0.07);
    border-left: 6px solid #f97316;
}

.announcement-card h2 {
    margin-top: 0;
    color: #c2410c;
}

.announcement-message {
    color: #475569;
    line-height: 1.7;
    margin: 15px 0;
}

.date {
    color: #64748b;
    font-size: 14px;
}

.no-data {
    background: white;
    padding: 30px;
    text-align: center;
    border-radius: 18px;
    color: #64748b;
}

@media (max-width: 700px) {

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


    <!-- HEADER -->

    <div class="header">

        <h1>📢 Announcements Management</h1>

        <p>
            Create and publish important college announcements
        </p>

    </div>


    <?php if ($message != ""): ?>

        <div class="success">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <!-- ADD ANNOUNCEMENT -->

    <div class="form-card">

        <h2>➕ Create Announcement</h2>

        <form method="POST">

            <div class="form-group">

                <label>
                    Announcement Title
                </label>

                <input
                    type="text"
                    name="title"
                    placeholder="Example: Semester Exams Notification"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Announcement Message
                </label>

                <textarea
                    name="message"
                    placeholder="Enter announcement details..."
                    required
                ></textarea>

            </div>


            <button type="submit" class="btn">
                📢 Publish Announcement
            </button>

        </form>

    </div>


    <!-- ANNOUNCEMENTS LIST -->

    <h2 class="title">
        📋 Published Announcements
    </h2>


    <?php if ($announcements && mysqli_num_rows($announcements) > 0): ?>

        <?php while ($row = mysqli_fetch_assoc($announcements)): ?>

            <div class="announcement-card">

                <h2>
                    📢
                    <?php echo htmlspecialchars($row['title']); ?>
                </h2>


                <div class="announcement-message">

                    <?php
                    echo nl2br(
                        htmlspecialchars($row['message'])
                    );
                    ?>

                </div>


                <div class="date">

                    🕒 Posted on:
                    <?php
                    echo date(
                        "d M Y, h:i A",
                        strtotime($row['created_at'])
                    );
                    ?>

                </div>

            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <div class="no-data">

            📭 No announcements available currently.

        </div>

    <?php endif; ?>

</div>

</body>

</html>
```
