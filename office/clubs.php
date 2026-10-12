<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "office") {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

$message = "";
$message_type = "";


/* =========================
   ADD CLUB
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $club_name = trim($_POST["club_name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $coordinator = trim($_POST["coordinator"] ?? "");
    $meeting_day = trim($_POST["meeting_day"] ?? "");
    $meeting_time = trim($_POST["meeting_time"] ?? "");
    $location = trim($_POST["location"] ?? "");

    if (
        $club_name === "" ||
        $description === "" ||
        $coordinator === "" ||
        $meeting_day === "" ||
        $meeting_time === "" ||
        $location === ""
    ) {

        $message = "Please fill all fields.";
        $message_type = "error";

    } else {

        $sql = "INSERT INTO clubs
        (name, description, coordinator, meeting_day, meeting_time, location)
        VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "ssssss",
                $club_name,
                $description,
                $coordinator,
                $meeting_day,
                $meeting_time,
                $location
            );

            if (mysqli_stmt_execute($stmt)) {

                $message = "Club added successfully! 🎉";
                $message_type = "success";

            } else {

                $message = "Error adding club: " . mysqli_error($conn);
                $message_type = "error";
            }

            mysqli_stmt_close($stmt);

        } else {

            $message = "Database error: " . mysqli_error($conn);
            $message_type = "error";
        }
    }
}


/* =========================
   GET CLUBS
========================= */

$result = mysqli_query(
    $conn,
    "SELECT *
     FROM clubs
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Clubs - CampusConnect</title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

    <link rel="stylesheet"
          href="../assets/css/office.css">

    <style>

        body {
            background: #f5f7fb;
        }

        .clubs-page {
            width: 95%;
            max-width: 1200px;
            margin: 40px auto;
        }

        .clubs-page h1 {
            text-align: center;
            color: #1f3c88;
            margin-bottom: 8px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        .message {
            max-width: 800px;
            margin: 0 auto 25px;
            padding: 14px;
            border-radius: 10px;
            text-align: center;
            font-weight: bold;
        }

        .message.success {
            background: #d1e7dd;
            color: #0f5132;
        }

        .message.error {
            background: #f8d7da;
            color: #842029;
        }

        .club-form-card,
        .clubs-list-card {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .club-form-card h2,
        .clubs-list-card h2 {
            color: #1f3c88;
            margin-top: 0;
            margin-bottom: 25px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-weight: bold;
            color: #444;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #1f3c88;
        }

        .add-club-btn {
            width: 100%;
            margin-top: 25px;
            padding: 13px;
            border: none;
            border-radius: 8px;
            background: #1f3c88;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .add-club-btn:hover {
            background: #162d68;
        }

        .clubs-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .club-card {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 22px;
            background: #fafbff;
        }

        .club-card h3 {
            color: #1f3c88;
            margin-top: 0;
            margin-bottom: 12px;
        }

        .club-description {
            color: #555;
            line-height: 1.6;
            margin-bottom: 18px;
        }

        .club-info {
            margin: 8px 0;
            color: #444;
        }

        .club-info strong {
            color: #222;
        }

        .no-clubs {
            text-align: center;
            color: #777;
            padding: 30px;
        }

        .back-link {
            display: inline-block;
            color: #1f3c88;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 30px;
        }

        @media (max-width: 700px) {

            .form-grid,
            .clubs-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
            }

            .club-form-card,
            .clubs-list-card {
                padding: 20px;
            }
        }

    </style>

</head>

<body class="office-page">

<div class="clubs-page">

    <a href="dashboard.php" class="back-link">
        ← Back to Office Dashboard
    </a>

    <h1>🏆 Clubs Management</h1>

    <p class="subtitle">
        Add and manage college clubs
    </p>


    <?php if ($message !== "") { ?>

        <div class="message <?php echo $message_type; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php } ?>


    <!-- =========================
         ADD CLUB FORM
    ========================== -->

    <div class="club-form-card">

        <h2>➕ Add New Club</h2>

        <form method="POST">

            <div class="form-grid">


                <div class="form-group">

                    <label>Club Name</label>

                    <input
                        type="text"
                        name="club_name"
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
                        placeholder="Enter club description..."
                        required
                    ></textarea>

                </div>


                <div class="form-group">

                    <label>Meeting Day</label>

                    <select name="meeting_day" required>

                        <option value="">
                            Select Day
                        </option>

                        <option>Monday</option>
                        <option>Tuesday</option>
                        <option>Wednesday</option>
                        <option>Thursday</option>
                        <option>Friday</option>
                        <option>Saturday</option>
                        <option>Sunday</option>

                    </select>

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
                        placeholder="Example: Seminar Hall"
                        required
                    >

                </div>

            </div>


            <button
                type="submit"
                class="add-club-btn"
            >

                🏆 Add Club

            </button>

        </form>

    </div>


    <!-- =========================
         CLUB LIST
    ========================== -->

    <div class="clubs-list-card">

        <h2>📋 Available Clubs</h2>

        <?php if (
            $result &&
            mysqli_num_rows($result) > 0
        ) { ?>

            <div class="clubs-grid">

                <?php while (
                    $club = mysqli_fetch_assoc($result)
                ) { ?>

                    <div class="club-card">

                        <h3>
                            🏆
                            <?php
                            echo htmlspecialchars(
                                $club["name"]
                            );
                            ?>
                        </h3>


                        <div class="club-description">

                            <?php
                            echo nl2br(
                                htmlspecialchars(
                                    $club["description"]
                                )
                            );
                            ?>

                        </div>


                        <div class="club-info">

                            👨‍🏫
                            <strong>Coordinator:</strong>

                            <?php
                            echo htmlspecialchars(
                                $club["coordinator"]
                            );
                            ?>

                        </div>


                        <div class="club-info">

                            📅
                            <strong>Meeting Day:</strong>

                            <?php
                            echo htmlspecialchars(
                                $club["meeting_day"]
                            );
                            ?>

                        </div>


                        <div class="club-info">

                            ⏰
                            <strong>Meeting Time:</strong>

                            <?php
                            echo htmlspecialchars(
                                $club["meeting_time"]
                            );
                            ?>

                        </div>


                        <div class="club-info">

                            📍
                            <strong>Location:</strong>

                            <?php
                            echo htmlspecialchars(
                                $club["location"]
                            );
                            ?>

                        </div>

                    </div>

                <?php } ?>

            </div>

        <?php } else { ?>

            <div class="no-clubs">

                🏆 No clubs added yet.

            </div>

        <?php } ?>

    </div>

</div>

</body>

</html>