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
   GET ALL ACTIVE STUDENTS FROM DATABASE
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
   ADD FEE
========================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $student_id = (int)($_POST["student_id"] ?? 0);
    $fee_type = trim($_POST["fee_type"] ?? "");
    $amount = (float)($_POST["amount"] ?? 0);
    $paid_amount = (float)($_POST["paid_amount"] ?? 0);
    $payment_date = trim($_POST["payment_date"] ?? "");
    $transaction_id = trim($_POST["transaction_id"] ?? "");


    /* Validation */

    if ($student_id <= 0) {

        $message = "Please select a student.";
        $message_type = "error";

    } elseif ($fee_type === "") {

        $message = "Please enter fee type.";
        $message_type = "error";

    } elseif ($amount <= 0) {

        $message = "Please enter a valid fee amount.";
        $message_type = "error";

    } elseif ($paid_amount < 0 || $paid_amount > $amount) {

        $message = "Paid amount cannot be greater than total amount.";
        $message_type = "error";

    } else {

        /* Calculate Due Amount */

        $due_amount = $amount - $paid_amount;


        /* Payment Status */

        if ($due_amount == 0) {

            $payment_status = "Paid";

        } elseif ($paid_amount > 0) {

            $payment_status = "Partial";

        } else {

            $payment_status = "Pending";
        }


        /* Empty date becomes NULL */

        if ($payment_date === "") {
            $payment_date = null;
        }


        /* Insert Fee */

        $sql = "INSERT INTO fees
                (
                    student_id,
                    fee_type,
                    amount,
                    paid_amount,
                    due_amount,
                    payment_status,
                    payment_date,
                    transaction_id
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";


        $stmt = mysqli_prepare($conn, $sql);


        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "isdddsss",
                $student_id,
                $fee_type,
                $amount,
                $paid_amount,
                $due_amount,
                $payment_status,
                $payment_date,
                $transaction_id
            );


            if (mysqli_stmt_execute($stmt)) {

                $message = "Fee details added successfully! ✅";
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
   GET FEE RECORDS
========================================== */

$fee_sql = "SELECT
                fees.*,
                users.name AS student_name,
                users.email AS student_email
            FROM fees
            LEFT JOIN users
            ON fees.student_id = users.id
            ORDER BY fees.created_at DESC";

$fee_result = mysqli_query($conn, $fee_sql);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Fee Management - CampusConnect</title>


    <link rel="stylesheet" href="../assets/css/style.css">

    <link rel="stylesheet" href="../assets/css/office.css">


    <style>

        /* =====================================
           FEE MANAGEMENT PAGE
        ===================================== */

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
                    #f8f5ff,
                    #f4f9ff
                );

            font-family: Arial, sans-serif;
        }


        .fee-page {
            width: 100%;
            min-height: 100vh;
            padding: 45px 20px 60px;
        }


        .fee-container {
            width: 100%;
            max-width: 1150px;
            margin: auto;
        }


        /* =====================================
           HEADER
        ===================================== */

        .fee-header {
            text-align: center;
            margin-bottom: 35px;
        }


        .fee-header h1 {
            margin: 0;

            font-size: 40px;

            color: #17365d;

            font-weight: 700;
        }


        .fee-header p {
            margin-top: 10px;

            font-size: 17px;

            color: #64748b;
        }


        /* =====================================
           MESSAGE
        ===================================== */

        .fee-message {
            padding: 16px 20px;

            border-radius: 12px;

            margin-bottom: 25px;

            font-size: 15px;

            font-weight: 600;

            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }


        .fee-message.success {
            background: #ecfdf5;

            color: #047857;

            border: 1px solid #86efac;
        }


        .fee-message.error {
            background: #fef2f2;

            color: #b91c1c;

            border: 1px solid #fecaca;
        }


        /* =====================================
           CARD
        ===================================== */

        .fee-card,
        .records-card {

            background: rgba(255,255,255,0.97);

            border-radius: 20px;

            padding: 32px;

            margin-bottom: 30px;

            box-shadow:
                0 10px 30px rgba(31,41,55,0.08);

            border: 1px solid #e5e7eb;
        }


        .fee-card h2,
        .records-card h2 {

            margin-top: 0;

            margin-bottom: 28px;

            font-size: 25px;

            color: #17365d;

            display: flex;

            align-items: center;

            gap: 10px;
        }


        /* =====================================
           FORM
        ===================================== */

        .fee-form-grid {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 22px;
        }


        .fee-form-group {

            display: flex;

            flex-direction: column;
        }


        .fee-form-group label {

            margin-bottom: 8px;

            color: #334155;

            font-size: 14px;

            font-weight: 700;
        }


        .fee-form-group input,
        .fee-form-group select {

            width: 100%;

            padding: 13px 14px;

            border-radius: 10px;

            border: 1px solid #cbd5e1;

            background: #f8fafc;

            color: #1e293b;

            font-size: 15px;

            outline: none;

            transition: all 0.2s ease;
        }


        .fee-form-group input:focus,
        .fee-form-group select:focus {

            border-color: #6366f1;

            background: white;

            box-shadow:
                0 0 0 3px rgba(99,102,241,0.12);
        }


        /* =====================================
           BUTTON
        ===================================== */

        .fee-submit-button {

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


        .fee-submit-button:hover {

            transform: translateY(-2px);

            box-shadow:
                0 10px 24px rgba(79,70,229,0.3);
        }


        /* =====================================
           TABLE
        ===================================== */

        .fee-table-wrapper {

            width: 100%;

            overflow-x: auto;

            border-radius: 12px;

            border: 1px solid #e2e8f0;
        }


        .fee-table {

            width: 100%;

            min-width: 900px;

            border-collapse: collapse;

            background: white;
        }


        .fee-table th {

            padding: 15px 13px;

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


        .fee-table td {

            padding: 15px 13px;

            border-bottom: 1px solid #e5e7eb;

            color: #334155;

            font-size: 14px;
        }


        .fee-table tbody tr:nth-child(even) {

            background: #f8fafc;
        }


        .fee-table tbody tr:hover {

            background: #eef2ff;

            transition: 0.2s;
        }


        /* =====================================
           STUDENT NAME
        ===================================== */

        .student-name {

            font-weight: 700;

            color: #1e3a8a;
        }


        .student-email {

            display: block;

            margin-top: 3px;

            font-size: 12px;

            color: #64748b;
        }


        /* =====================================
           STATUS
        ===================================== */

        .fee-status {

            display: inline-block;

            padding: 6px 13px;

            border-radius: 30px;

            font-size: 12px;

            font-weight: 700;
        }


        .fee-status.paid {

            background: #dcfce7;

            color: #15803d;
        }


        .fee-status.partial {

            background: #dbeafe;

            color: #1d4ed8;
        }


        .fee-status.pending {

            background: #fef3c7;

            color: #b45309;
        }


        /* =====================================
           NO RECORDS
        ===================================== */

        .no-fees {

            text-align: center;

            padding: 40px 20px;

            background: #f8fafc;

            border-radius: 12px;

            color: #64748b;

            font-size: 16px;
        }


        /* =====================================
           BACK BUTTON
        ===================================== */

        .fee-back {

            display: inline-block;

            padding: 12px 20px;

            background: white;

            color: #4f46e5;

            text-decoration: none;

            font-weight: 700;

            border-radius: 10px;

            box-shadow:
                0 5px 15px rgba(0,0,0,0.06);

            border: 1px solid #e5e7eb;

            transition: 0.2s;
        }


        .fee-back:hover {

            transform: translateX(-4px);

            background: #eef2ff;
        }


        /* =====================================
           MOBILE
        ===================================== */

        @media (max-width: 850px) {

            .fee-form-grid {

                grid-template-columns: 1fr 1fr;
            }

            .fee-header h1 {

                font-size: 32px;
            }
        }


        @media (max-width: 600px) {

            .fee-page {

                padding: 25px 12px 40px;
            }


            .fee-card,
            .records-card {

                padding: 20px;
            }


            .fee-form-grid {

                grid-template-columns: 1fr;
            }


            .fee-header h1 {

                font-size: 28px;
            }
        }

    </style>

</head>


<body class="office-page">


<div class="fee-page">

<div class="fee-container">


    <!-- =====================================
         HEADER
    ===================================== -->

    <div class="fee-header">

        <h1>💰 Fee Management</h1>

        <p>
            Office Staff:
            <strong>
                <?php echo htmlspecialchars($name); ?>
            </strong>
        </p>

    </div>


    <!-- =====================================
         MESSAGE
    ===================================== -->

    <?php if ($message !== "") { ?>

        <div class="fee-message <?php echo htmlspecialchars($message_type); ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php } ?>


    <!-- =====================================
         ADD FEE
    ===================================== -->

    <div class="fee-card">

        <h2>
            ➕
            Add Student Fee
        </h2>


        <form method="POST" action="">


            <div class="fee-form-grid">


                <!-- STUDENT -->

                <div class="fee-form-group">

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
                                value="<?php echo (int)$student["id"]; ?>"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $student["name"]
                                );
                                ?>

                                -

                                <?php
                                echo htmlspecialchars(
                                    $student["email"]
                                );
                                ?>

                            </option>

                        <?php } ?>


                    </select>

                </div>


                <!-- FEE TYPE -->

                <div class="fee-form-group">

                    <label>
                        Fee Type
                    </label>

                    <input
                        type="text"
                        name="fee_type"
                        placeholder="Example: Tuition Fee"
                        required
                    >

                </div>


                <!-- TOTAL -->

                <div class="fee-form-group">

                    <label>
                        Total Amount
                    </label>

                    <input
                        type="number"
                        name="amount"
                        step="0.01"
                        min="1"
                        placeholder="Enter total amount"
                        required
                    >

                </div>


                <!-- PAID -->

                <div class="fee-form-group">

                    <label>
                        Paid Amount
                    </label>

                    <input
                        type="number"
                        name="paid_amount"
                        step="0.01"
                        min="0"
                        placeholder="Enter paid amount"
                        required
                    >

                </div>


                <!-- DATE -->

                <div class="fee-form-group">

                    <label>
                        Payment Date
                    </label>

                    <input
                        type="date"
                        name="payment_date"
                    >

                </div>


                <!-- TRANSACTION -->

                <div class="fee-form-group">

                    <label>
                        Transaction ID
                    </label>

                    <input
                        type="text"
                        name="transaction_id"
                        placeholder="Optional"
                    >

                </div>


            </div>


            <button
                type="submit"
                class="fee-submit-button"
            >

                💾 Save Fee Details

            </button>


        </form>

    </div>


    <!-- =====================================
         FEE RECORDS
    ===================================== -->

    <div class="records-card">

        <h2>
            📋
            Fee Records
        </h2>


        <?php if ($fee_result && mysqli_num_rows($fee_result) > 0) { ?>


            <div class="fee-table-wrapper">


                <table class="fee-table">


                    <thead>

                        <tr>

                            <th>Student</th>

                            <th>Fee Type</th>

                            <th>Total</th>

                            <th>Paid</th>

                            <th>Due</th>

                            <th>Status</th>

                            <th>Date</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php while ($row = mysqli_fetch_assoc($fee_result)) { ?>


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


                                <span class="student-email">

                                    ID:
                                    <?php
                                    echo htmlspecialchars(
                                        $row["student_id"]
                                    );
                                    ?>

                                </span>

                            </td>


                            <!-- FEE TYPE -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $row["fee_type"]
                                );

                                ?>

                            </td>


                            <!-- TOTAL -->

                            <td>

                                ₹<?php

                                echo number_format(
                                    (float)$row["amount"],
                                    2
                                );

                                ?>

                            </td>


                            <!-- PAID -->

                            <td>

                                ₹<?php

                                echo number_format(
                                    (float)$row["paid_amount"],
                                    2
                                );

                                ?>

                            </td>


                            <!-- DUE -->

                            <td>

                                ₹<?php

                                echo number_format(
                                    (float)$row["due_amount"],
                                    2
                                );

                                ?>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <span
                                    class="fee-status <?php
                                    echo strtolower(
                                        htmlspecialchars(
                                            $row["payment_status"]
                                        )
                                    );
                                    ?>"
                                >

                                    <?php

                                    echo htmlspecialchars(
                                        $row["payment_status"]
                                    );

                                    ?>

                                </span>

                            </td>


                            <!-- DATE -->

                            <td>

                                <?php

                                if (!empty($row["payment_date"])) {

                                    echo date(
                                        "d M Y",
                                        strtotime(
                                            $row["payment_date"]
                                        )
                                    );

                                } else {

                                    echo "-";
                                }

                                ?>

                            </td>


                        </tr>


                    <?php } ?>


                    </tbody>


                </table>


            </div>


        <?php } else { ?>


            <div class="no-fees">

                💰 No fee records available yet.

            </div>


        <?php } ?>


    </div>


    <!-- =====================================
         BACK
    ===================================== -->

    <a
        href="dashboard.php"
        class="fee-back"
    >

        ← Back to Dashboard

    </a>


</div>

</div>


</body>

</html>