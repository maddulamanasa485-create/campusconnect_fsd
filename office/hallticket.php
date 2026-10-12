<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "office") {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

$name = $_SESSION["name"];

$message = "";
$message_type = "";


/* ==========================================
   GET ACTIVE STUDENTS FROM DATABASE
========================================== */

$students = [];

$student_sql = "SELECT id, name, email
                FROM users
                WHERE role = 'student'
                AND status = 1
                ORDER BY id ASC";

$student_result = mysqli_query($conn, $student_sql);

if ($student_result) {

    while ($student = mysqli_fetch_assoc($student_result)) {

        $students[] = $student;
    }
}


/* ==========================================
   CREATE HALL TICKET
========================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $student_id = (int)($_POST["student_id"] ?? 0);
    $exam_name = trim($_POST["exam_name"] ?? "");
    $semester = (int)($_POST["semester"] ?? 0);
    $exam_date = trim($_POST["exam_date"] ?? "");
    $exam_time = trim($_POST["exam_time"] ?? "");
    $exam_center = trim($_POST["exam_center"] ?? "");


    /* Validation */

    if ($student_id <= 0) {

        $message = "Please select a student.";
        $message_type = "error";

    } elseif ($exam_name === "") {

        $message = "Please enter exam name.";
        $message_type = "error";

    } elseif ($semester < 1 || $semester > 8) {

        $message = "Please select a valid semester.";
        $message_type = "error";

    } elseif ($exam_date === "") {

        $message = "Please select exam date.";
        $message_type = "error";

    } elseif ($exam_time === "") {

        $message = "Please enter exam time.";
        $message_type = "error";

    } elseif ($exam_center === "") {

        $message = "Please enter exam center.";
        $message_type = "error";

    } else {


        /* ==========================================
           INSERT HALL TICKET
        ========================================== */

        $sql = "INSERT INTO hall_tickets
                (
                    student_id,
                    exam_name,
                    semester,
                    exam_date,
                    exam_time,
                    exam_center,
                    status
                )
                VALUES (?, ?, ?, ?, ?, ?, 'Available')";


        $stmt = mysqli_prepare($conn, $sql);


        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "isisss",
                $student_id,
                $exam_name,
                $semester,
                $exam_date,
                $exam_time,
                $exam_center
            );


            if (mysqli_stmt_execute($stmt)) {

                $message = "Hall ticket created successfully! 🎫";
                $message_type = "success";

            } else {

                $message = "Database error: " . mysqli_error($conn);
                $message_type = "error";
            }


            mysqli_stmt_close($stmt);

        } else {

            $message = "Database prepare error: " . mysqli_error($conn);
            $message_type = "error";
        }
    }
}


/* ==========================================
   GET HALL TICKET RECORDS
========================================== */

$hall_sql = "SELECT
                hall_tickets.*,
                users.name AS student_name,
                users.email AS student_email
             FROM hall_tickets
             LEFT JOIN users
             ON hall_tickets.student_id = users.id
             ORDER BY hall_tickets.created_at DESC";

$hall_result = mysqli_query($conn, $hall_sql);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Hall Ticket Management - CampusConnect</title>


    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

    <link
        rel="stylesheet"
        href="../assets/css/office.css"
    >


    <style>

        /* ==========================================
           PAGE
        ========================================== */

        * {
            box-sizing: border-box;
        }


        body.office-page {

            margin: 0;

            min-height: 100vh;

            background:
                linear-gradient(
                    135deg,
                    #eef4ff,
                    #f7f3ff,
                    #f4f9ff
                );

            font-family: Arial, sans-serif;
        }


        .hall-page {

            width: 100%;

            min-height: 100vh;

            padding: 45px 20px 60px;
        }


        .hall-container {

            width: 100%;

            max-width: 1150px;

            margin: auto;
        }


        /* ==========================================
           HEADER
        ========================================== */

        .hall-header {

            text-align: center;

            margin-bottom: 35px;
        }


        .hall-header h1 {

            margin: 0;

            color: #17365d;

            font-size: 40px;

            font-weight: 700;
        }


        .hall-header p {

            margin-top: 10px;

            color: #64748b;

            font-size: 17px;
        }


        /* ==========================================
           MESSAGE
        ========================================== */

        .hall-message {

            padding: 16px 20px;

            border-radius: 12px;

            margin-bottom: 25px;

            font-weight: 600;

            text-align: center;
        }


        .hall-message.success {

            background: #ecfdf5;

            color: #047857;

            border: 1px solid #86efac;
        }


        .hall-message.error {

            background: #fef2f2;

            color: #b91c1c;

            border: 1px solid #fecaca;
        }


        /* ==========================================
           CARDS
        ========================================== */

        .hall-card {

            background: rgba(255,255,255,0.97);

            border-radius: 20px;

            padding: 32px;

            margin-bottom: 30px;

            box-shadow:
                0 10px 30px rgba(31,41,55,0.08);

            border: 1px solid #e5e7eb;
        }


        .hall-card h2 {

            margin-top: 0;

            margin-bottom: 28px;

            color: #17365d;

            font-size: 25px;

            border-left: 6px solid #6366f1;

            padding-left: 12px;
        }


        /* ==========================================
           FORM
        ========================================== */

        .hall-form-grid {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 22px;
        }


        .hall-form-group {

            display: flex;

            flex-direction: column;
        }


        .hall-form-group label {

            margin-bottom: 8px;

            color: #334155;

            font-size: 14px;

            font-weight: 700;
        }


        .hall-form-group input,
        .hall-form-group select {

            width: 100%;

            padding: 13px 14px;

            border-radius: 10px;

            border: 1px solid #cbd5e1;

            background: #f8fafc;

            color: #1e293b;

            font-size: 15px;

            outline: none;

            transition: 0.2s;
        }


        .hall-form-group input:focus,
        .hall-form-group select:focus {

            border-color: #6366f1;

            background: white;

            box-shadow:
                0 0 0 3px rgba(99,102,241,0.12);
        }


        /* ==========================================
           CREATE BUTTON
        ========================================== */

        .hall-submit-button {

            width: 100%;

            margin-top: 28px;

            padding: 15px;

            border: none;

            border-radius: 11px;

            background:
                linear-gradient(
                    135deg,
                    #6366f1,
                    #4f46e5
                );

            color: white;

            font-size: 16px;

            font-weight: 700;

            cursor: pointer;

            box-shadow:
                0 7px 18px rgba(79,70,229,0.25);

            transition: 0.2s;
        }


        .hall-submit-button:hover {

            transform: translateY(-2px);

            box-shadow:
                0 10px 24px rgba(79,70,229,0.30);
        }


        /* ==========================================
           TABLE
        ========================================== */

        .hall-table-wrapper {

            width: 100%;

            overflow-x: auto;

            border: 1px solid #e2e8f0;

            border-radius: 12px;
        }


        .hall-table {

            width: 100%;

            min-width: 950px;

            border-collapse: collapse;

            background: white;
        }


        .hall-table th {

            padding: 15px 12px;

            background:
                linear-gradient(
                    135deg,
                    #536dfe,
                    #6366f1
                );

            color: white;

            text-align: left;

            font-size: 14px;

            white-space: nowrap;
        }


        .hall-table td {

            padding: 15px 12px;

            border-bottom: 1px solid #e5e7eb;

            color: #334155;

            font-size: 14px;
        }


        .hall-table tbody tr:nth-child(even) {

            background: #f8fafc;
        }


        .hall-table tbody tr:hover {

            background: #eef2ff;
        }


        /* ==========================================
           STUDENT
        ========================================== */

        .student-name {

            display: block;

            font-weight: 700;

            color: #1e3a8a;
        }


        .student-id {

            display: block;

            margin-top: 4px;

            color: #64748b;

            font-size: 12px;
        }


        /* ==========================================
           STATUS
        ========================================== */

        .hall-status {

            display: inline-block;

            padding: 6px 13px;

            border-radius: 30px;

            background: #dcfce7;

            color: #15803d;

            font-size: 12px;

            font-weight: 700;
        }


        /* ==========================================
           EMPTY
        ========================================== */

        .no-hall-tickets {

            text-align: center;

            padding: 40px 20px;

            background: #f8fafc;

            border-radius: 12px;

            color: #64748b;

            font-size: 16px;
        }


        /* ==========================================
           BACK
        ========================================== */

        .hall-back {

            display: inline-block;

            padding: 12px 20px;

            background: white;

            color: #4f46e5;

            text-decoration: none;

            font-weight: 700;

            border-radius: 10px;

            border: 1px solid #e5e7eb;

            box-shadow:
                0 5px 15px rgba(0,0,0,0.06);

            transition: 0.2s;
        }


        .hall-back:hover {

            transform: translateX(-4px);

            background: #eef2ff;
        }


        /* ==========================================
           RESPONSIVE
        ========================================== */

        @media (max-width: 750px) {

            .hall-form-grid {

                grid-template-columns: 1fr;
            }


            .hall-card {

                padding: 20px;
            }


            .hall-header h1 {

                font-size: 30px;
            }
        }


        @media (max-width: 500px) {

            .hall-page {

                padding: 25px 12px 40px;
            }


            .hall-header h1 {

                font-size: 27px;
            }
        }

    </style>

</head>


<body class="office-page">


<div class="hall-page">


<div class="hall-container">


    <!-- ==========================================
         HEADER
    ========================================== -->

    <div class="hall-header">

        <h1>
            🎫 Hall Ticket Management
        </h1>

        <p>

            Office Staff:

            <strong>
                <?php echo htmlspecialchars($name); ?>
            </strong>

        </p>

    </div>


    <!-- ==========================================
         MESSAGE
    ========================================== -->

    <?php if ($message !== "") { ?>

        <div
            class="hall-message
            <?php echo htmlspecialchars($message_type); ?>"
        >

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php } ?>


    <!-- ==========================================
         CREATE HALL TICKET
    ========================================== -->

    <div class="hall-card">

        <h2>
            ➕ Create Hall Ticket
        </h2>


        <form method="POST" action="">


            <div class="hall-form-grid">


                <!-- STUDENT -->

                <div class="hall-form-group">

                    <label>
                        Student
                    </label>


                    <select
                        name="student_id"
                        required
                    >

                        <option value="">
                            Select Student
                        </option>


                        <?php foreach ($students as $student) { ?>

                            <option
                                value="<?php
                                echo (int)$student["id"];
                                ?>"
                            >

                                <?php

                                echo htmlspecialchars(
                                    $student["name"]
                                );

                                ?>

                                -
                                
                                ID <?php
                                echo (int)$student["id"];
                                ?>

                            </option>

                        <?php } ?>


                    </select>

                </div>


                <!-- EXAM NAME -->

                <div class="hall-form-group">

                    <label>
                        Exam Name
                    </label>


                    <input
                        type="text"
                        name="exam_name"
                        placeholder="Example: B.Tech Semester Exams"
                        required
                    >

                </div>


                <!-- SEMESTER -->

                <div class="hall-form-group">

                    <label>
                        Semester
                    </label>


                    <select
                        name="semester"
                        required
                    >

                        <option value="">
                            Select Semester
                        </option>


                        <?php for ($i = 1; $i <= 8; $i++) { ?>

                            <option value="<?php echo $i; ?>">

                                Semester <?php echo $i; ?>

                            </option>

                        <?php } ?>


                    </select>

                </div>


                <!-- EXAM DATE -->

                <div class="hall-form-group">

                    <label>
                        Exam Date
                    </label>


                    <input
                        type="date"
                        name="exam_date"
                        required
                    >

                </div>


                <!-- EXAM TIME -->

                <div class="hall-form-group">

                    <label>
                        Exam Time
                    </label>


                    <input
                        type="text"
                        name="exam_time"
                        placeholder="Example: 11:30 AM - 3:30 PM"
                        required
                    >

                </div>


                <!-- EXAM CENTER -->

                <div class="hall-form-group">

                    <label>
                        Exam Center
                    </label>


                    <input
                        type="text"
                        name="exam_center"
                        placeholder="Example: Main Block - Hall 101"
                        required
                    >

                </div>


            </div>


            <button
                type="submit"
                class="hall-submit-button"
            >

                🎫 Create Hall Ticket

            </button>


        </form>

    </div>


    <!-- ==========================================
         RECORDS
    ========================================== -->

    <div class="hall-card">

        <h2>
            📋 Hall Ticket Records
        </h2>


        <?php if (
            $hall_result &&
            mysqli_num_rows($hall_result) > 0
        ) { ?>


            <div class="hall-table-wrapper">


                <table class="hall-table">


                    <thead>

                        <tr>

                            <th>Student</th>

                            <th>Exam</th>

                            <th>Semester</th>

                            <th>Date</th>

                            <th>Time</th>

                            <th>Center</th>

                            <th>Status</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php while (
                        $row = mysqli_fetch_assoc($hall_result)
                    ) { ?>


                        <tr>


                            <!-- STUDENT -->

                            <td>

                                <span class="student-name">

                                    <?php

                                    echo htmlspecialchars(
                                        $row["student_name"] ??
                                        "Unknown Student"
                                    );

                                    ?>

                                </span>


                                <span class="student-id">

                                    Student ID:

                                    <?php

                                    echo (int)$row["student_id"];

                                    ?>

                                </span>

                            </td>


                            <!-- EXAM -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $row["exam_name"]
                                );

                                ?>

                            </td>


                            <!-- SEMESTER -->

                            <td>

                                Semester

                                <?php

                                echo (int)$row["semester"];

                                ?>

                            </td>


                            <!-- DATE -->

                            <td>

                                <?php

                                echo date(
                                    "d M Y",
                                    strtotime(
                                        $row["exam_date"]
                                    )
                                );

                                ?>

                            </td>


                            <!-- TIME -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $row["exam_time"]
                                );

                                ?>

                            </td>


                            <!-- CENTER -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $row["exam_center"]
                                );

                                ?>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <span class="hall-status">

                                    <?php

                                    echo htmlspecialchars(
                                        $row["status"]
                                    );

                                    ?>

                                </span>

                            </td>


                        </tr>


                    <?php } ?>


                    </tbody>


                </table>


            </div>


        <?php } else { ?>


            <div class="no-hall-tickets">

                🎫 No hall tickets available yet.

            </div>


        <?php } ?>


    </div>


    <!-- ==========================================
         BACK
    ========================================== -->

    <a
        href="dashboard.php"
        class="hall-back"
    >

        ← Back to Dashboard

    </a>


</div>

</div>


</body>

</html>