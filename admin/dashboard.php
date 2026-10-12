<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Admin Dashboard - CampusConnect</title>

    <link rel="stylesheet" href="../assets/css/office.css">

    <style>

        /* =========================
           ADMIN CONTAINER
        ========================= */

        .admin-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }


        /* =========================
           ADMIN HEADER
        ========================= */

        .admin-header {
            background: linear-gradient(135deg, #182848, #4b6cb7);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
        }

        .admin-header h1 {
            margin: 0 0 10px;
        }


        /* =========================
           ADMIN GRID
        ========================= */

        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }


        /* =========================
           COMMON CARD DESIGN
        ========================= */

        .admin-card {
            display: block;

            padding: 25px;

            border-radius: 18px;

            text-decoration: none;

            color: #222 !important;

            box-shadow: 0 5px 15px rgba(0,0,0,0.08);

            transition: all 0.3s ease;

            border-left: 6px solid transparent;
        }


        /* =========================
           1. STUDENTS
        ========================= */

        .admin-card:nth-child(1) {
            background: #E3F2FD !important;
            border-left-color: #2196F3;
        }


        /* =========================
           2. FACULTY
        ========================= */

        .admin-card:nth-child(2) {
            background: #F3E5F5 !important;
            border-left-color: #9C27B0;
        }


        /* =========================
           3. DEPARTMENTS
        ========================= */

        .admin-card:nth-child(3) {
            background: #E8F5E9 !important;
            border-left-color: #4CAF50;
        }


        /* =========================
           4. TIMETABLE
        ========================= */

        .admin-card:nth-child(4) {
            background: #FFF3E0 !important;
            border-left-color: #FF9800;
        }


        /* =========================
           5. FEES
        ========================= */

        .admin-card:nth-child(5) {
            background: #FFF8E1 !important;
            border-left-color: #FFC107;
        }


        /* =========================
           6. RESULTS
        ========================= */

        .admin-card:nth-child(6) {
            background: #E0F7FA !important;
            border-left-color: #00ACC1;
        }


        /* =========================
           7. CLUBS
        ========================= */

        .admin-card:nth-child(7) {
            background: #FCE4EC !important;
            border-left-color: #E91E63;
        }


        /* =========================
           8. ANNOUNCEMENTS
        ========================= */

        .admin-card:nth-child(8) {
            background: #FDE7F3 !important;
            border-left-color: #D81B60;
        }


        /* =========================
           9. USERS
        ========================= */

        .admin-card:nth-child(9) {
            background: #EDE7F6 !important;
            border-left-color: #673AB7;
        }


        /* =========================
           CARD TITLE
        ========================= */

        .admin-card h2 {
            margin-top: 0;
            margin-bottom: 10px;
        }


        /* =========================
           CARD DESCRIPTION
        ========================= */

        .admin-card p {
            margin: 0;
            line-height: 1.5;
        }


        /* =========================
           HOVER EFFECT
        ========================= */

        .admin-card:hover {
            transform: translateY(-8px);

            box-shadow:
                0 12px 25px rgba(0,0,0,0.15);
        }


        /* =========================
           LOGOUT BUTTON
        ========================= */

        .logout {
            display: inline-block;

            margin-top: 25px;

            padding: 12px 20px;

            background: #dc3545;

            color: white !important;

            text-decoration: none;

            border-radius: 10px;

            transition: 0.3s;
        }

        .logout:hover {
            background: #b02a37;
            transform: translateY(-2px);
        }

    </style>

</head>


<body>


<div class="admin-container">


    <!-- ADMIN HEADER -->

    <div class="admin-header">

        <h1>🛡️ Admin Dashboard</h1>

        <p>
            Welcome to CampusConnect Administration
        </p>

    </div>



    <!-- ADMIN CARDS -->

    <div class="admin-grid">


        <!-- STUDENTS -->

        <a href="students.php" class="admin-card">

            <h2>👨‍🎓 Students</h2>

            <p>
                Manage student records
            </p>

        </a>



        <!-- FACULTY -->

        <a href="faculty.php" class="admin-card">

            <h2>👨‍🏫 Faculty</h2>

            <p>
                Manage faculty records
            </p>

        </a>



        <!-- DEPARTMENTS -->

        <a href="departments.php" class="admin-card">

            <h2>🏢 Departments</h2>

            <p>
                Manage departments
            </p>

        </a>



        <!-- TIMETABLE -->

        <a href="timetable.php" class="admin-card">

            <h2>📅 Timetable</h2>

            <p>
                Manage college timetable
            </p>

        </a>



        <!-- FEES -->

        <a href="fees.php" class="admin-card">

            <h2>💰 Fees</h2>

            <p>
                Manage student fees
            </p>

        </a>



        <!-- RESULTS -->

        <a href="results.php" class="admin-card">

            <h2>📊 Results</h2>

            <p>
                Manage examinations and results
            </p>

        </a>



        <!-- CLUBS -->

        <a href="clubs.php" class="admin-card">

            <h2>🏆 Clubs</h2>

            <p>
                Manage college clubs
            </p>

        </a>



        <!-- ANNOUNCEMENTS -->

        <a href="announcements.php" class="admin-card">

            <h2>📢 Announcements</h2>

            <p>
                Manage announcements
            </p>

        </a>



        <!-- USERS -->

        <a href="users.php" class="admin-card">

            <h2>👥 Users</h2>

            <p>
                Manage system users
            </p>

        </a>


    </div>



    <!-- LOGOUT -->

    <a href="../auth/logout.php" class="logout">
        🚪 Logout
    </a>


</div>


</body>
</html>