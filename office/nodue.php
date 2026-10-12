<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "office") {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

/* GET ALL FEE RECORDS */

$sql = "SELECT
            fees.id,
            fees.student_id,
            users.name AS student_name,
            fees.fee_type,
            fees.amount,
            fees.paid_amount,
            fees.due_amount,
            fees.payment_status,
            fees.payment_date
        FROM fees
        LEFT JOIN users
        ON fees.student_id = users.id
        ORDER BY fees.id DESC";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>No Due Certificate - CampusConnect</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/office.css">

    <style>

        body {
            background: #f5f7fb;
        }

        .nodue-container {
            width: 95%;
            max-width: 1200px;
            margin: 40px auto;
        }

        .nodue-container h1 {
            text-align: center;
            color: #1f3c88;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        .nodue-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(330px, 1fr));
            gap: 25px;
        }

        .nodue-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            border-top: 5px solid #198754;
        }

        .nodue-card.pending {
            border-top-color: #dc3545;
        }

        .nodue-header {
            text-align: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .nodue-header h2 {
            margin: 0;
            color: #1f3c88;
        }

        .nodue-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 9px 0;
            border-bottom: 1px solid #f1f1f1;
        }

        .nodue-row strong {
            color: #444;
        }

        .nodue-row span {
            color: #555;
            text-align: right;
        }

        .no-due-status {
            color: #198754 !important;
            font-weight: bold;
        }

        .due-status {
            color: #dc3545 !important;
            font-weight: bold;
        }

        .print-button {
            display: block;
            width: 100%;
            margin-top: 20px;
            padding: 11px;
            background: #1f3c88;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            font-size: 15px;
        }

        .print-button:hover {
            background: #162d68;
        }

        .back-link {
            display: inline-block;
            margin-top: 30px;
            text-decoration: none;
            color: #1f3c88;
            font-weight: bold;
        }

        .no-records {
            background: white;
            padding: 30px;
            text-align: center;
            border-radius: 12px;
            color: #777;
        }

        @media print {

            body {
                background: white;
            }

            .back-link,
            .print-button {
                display: none;
            }

            .nodue-container {
                width: 100%;
                margin: 0;
            }

            .nodue-card {
                box-shadow: none;
                border: 1px solid #ddd;
                page-break-inside: avoid;
            }

        }

    </style>

</head>

<body>

<div class="nodue-container">

    <h1>📄 No Due Status</h1>

    <p class="subtitle">
        Check student fee clearance status
    </p>


    <?php if ($result && mysqli_num_rows($result) > 0): ?>

        <div class="nodue-list">

            <?php while ($row = mysqli_fetch_assoc($result)): ?>

                <?php

                $has_due = ((float)$row["due_amount"] > 0);

                ?>

                <div class="nodue-card <?php echo $has_due ? 'pending' : ''; ?>">

                    <div class="nodue-header">

                        <h2>CampusConnect College</h2>

                    </div>


                    <div class="nodue-row">

                        <strong>Student Name</strong>

                        <span>
                            <?php
                            echo htmlspecialchars(
                                $row["student_name"] ?? "Unknown"
                            );
                            ?>
                        </span>

                    </div>


                    <div class="nodue-row">

                        <strong>Student ID</strong>

                        <span>
                            <?php echo $row["student_id"]; ?>
                        </span>

                    </div>


                    <div class="nodue-row">

                        <strong>Fee Type</strong>

                        <span>
                            <?php
                            echo htmlspecialchars($row["fee_type"]);
                            ?>
                        </span>

                    </div>


                    <div class="nodue-row">

                        <strong>Total Fee</strong>

                        <span>
                            ₹<?php
                            echo number_format($row["amount"], 2);
                            ?>
                        </span>

                    </div>


                    <div class="nodue-row">

                        <strong>Paid Amount</strong>

                        <span>
                            ₹<?php
                            echo number_format($row["paid_amount"], 2);
                            ?>
                        </span>

                    </div>


                    <div class="nodue-row">

                        <strong>Due Amount</strong>

                        <span>
                            ₹<?php
                            echo number_format($row["due_amount"], 2);
                            ?>
                        </span>

                    </div>


                    <div class="nodue-row">

                        <strong>Status</strong>

                        <?php if (!$has_due): ?>

                            <span class="no-due-status">
                                ✅ NO DUE
                            </span>

                        <?php else: ?>

                            <span class="due-status">
                                ⚠️ DUE PENDING
                            </span>

                        <?php endif; ?>

                    </div>


                    <?php if (!empty($row["payment_date"])): ?>

                        <div class="nodue-row">

                            <strong>Payment Date</strong>

                            <span>
                                <?php
                                echo date(
                                    "d-m-Y",
                                    strtotime($row["payment_date"])
                                );
                                ?>
                            </span>

                        </div>

                    <?php endif; ?>


                    <?php if (!$has_due): ?>

                        <button
                            type="button"
                            class="print-button"
                            onclick="window.print()"
                        >
                            🖨️ Print / Download No Due
                        </button>

                    <?php endif; ?>

                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="no-records">

            📄 No fee records available.

        </div>

    <?php endif; ?>


    <a href="dashboard.php" class="back-link">
        ← Back to Office Dashboard
    </a>

</div>

</body>

</html>