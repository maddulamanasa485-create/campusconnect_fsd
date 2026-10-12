<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "office") {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

/* GET VERIFIED PAYMENTS */

$sql = "SELECT
            fees.id,
            fees.student_id,
            users.name AS student_name,
            fees.fee_type,
            fees.amount,
            fees.paid_amount,
            fees.due_amount,
            fees.payment_status,
            fees.payment_date,
            fees.transaction_id
        FROM fees
        LEFT JOIN users
        ON fees.student_id = users.id
        WHERE fees.payment_status = 'Paid'
        ORDER BY fees.payment_date DESC, fees.id DESC";

$receipts = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Payment Receipts - CampusConnect</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/office.css">

    <style>

        body {
            background: #f5f7fb;
        }

        .receipts-container {
            width: 95%;
            max-width: 1200px;
            margin: 40px auto;
        }

        .receipts-container h1 {
            text-align: center;
            color: #1f3c88;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        .receipt-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(330px, 1fr));
            gap: 25px;
        }

        .receipt-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            border-top: 5px solid #198754;
        }

        .receipt-header {
            text-align: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .receipt-header h2 {
            margin: 0;
            color: #1f3c88;
        }

        .receipt-number {
            font-size: 13px;
            color: #777;
            margin-top: 5px;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 9px 0;
            border-bottom: 1px solid #f1f1f1;
        }

        .receipt-row strong {
            color: #444;
        }

        .receipt-row span {
            color: #555;
            text-align: right;
        }

        .paid-status {
            color: green !important;
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

        .no-receipts {
            background: white;
            padding: 30px;
            text-align: center;
            border-radius: 12px;
            color: #777;
        }

        .back-link {
            display: inline-block;
            margin-top: 30px;
            text-decoration: none;
            color: #1f3c88;
            font-weight: bold;
        }

        @media print {

            body {
                background: white;
            }

            .back-link {
                display: none;
            }

            .print-button {
                display: none;
            }

            .receipts-container {
                width: 100%;
                margin: 0;
            }

            .receipt-card {
                box-shadow: none;
                border: 1px solid #ddd;
                page-break-inside: avoid;
            }

        }

    </style>

</head>

<body>

<div class="receipts-container">

    <h1>🧾 Payment Receipts</h1>

    <p class="subtitle">
        View verified student payment receipts
    </p>


    <?php if ($receipts && mysqli_num_rows($receipts) > 0): ?>

        <div class="receipt-list">

            <?php while ($receipt = mysqli_fetch_assoc($receipts)): ?>

                <div class="receipt-card">

                    <div class="receipt-header">

                        <h2>CampusConnect College</h2>

                        <div class="receipt-number">

                            Receipt No:
                            CC-<?php echo str_pad($receipt["id"], 5, "0", STR_PAD_LEFT); ?>

                        </div>

                    </div>


                    <div class="receipt-row">

                        <strong>Student Name</strong>

                        <span>
                            <?php echo htmlspecialchars($receipt["student_name"] ?? "Unknown"); ?>
                        </span>

                    </div>


                    <div class="receipt-row">

                        <strong>Student ID</strong>

                        <span>
                            <?php echo $receipt["student_id"]; ?>
                        </span>

                    </div>


                    <div class="receipt-row">

                        <strong>Fee Type</strong>

                        <span>
                            <?php echo htmlspecialchars($receipt["fee_type"]); ?>
                        </span>

                    </div>


                    <div class="receipt-row">

                        <strong>Total Amount</strong>

                        <span>
                            ₹<?php echo number_format($receipt["amount"], 2); ?>
                        </span>

                    </div>


                    <div class="receipt-row">

                        <strong>Paid Amount</strong>

                        <span>
                            ₹<?php echo number_format($receipt["paid_amount"], 2); ?>
                        </span>

                    </div>


                    <div class="receipt-row">

                        <strong>Due Amount</strong>

                        <span>
                            ₹<?php echo number_format($receipt["due_amount"], 2); ?>
                        </span>

                    </div>


                    <div class="receipt-row">

                        <strong>Transaction ID</strong>

                        <span>
                            <?php
                            echo htmlspecialchars(
                                $receipt["transaction_id"] ?? "-"
                            );
                            ?>
                        </span>

                    </div>


                    <div class="receipt-row">

                        <strong>Payment Date</strong>

                        <span>
                            <?php
                            if (!empty($receipt["payment_date"])) {
                                echo date(
                                    "d-m-Y",
                                    strtotime($receipt["payment_date"])
                                );
                            } else {
                                echo "-";
                            }
                            ?>
                        </span>

                    </div>


                    <div class="receipt-row">

                        <strong>Status</strong>

                        <span class="paid-status">
                            ✅ Paid
                        </span>

                    </div>


                    <button
                        type="button"
                        class="print-button"
                        onclick="window.print()"
                    >
                        🖨️ Print / Download Receipt
                    </button>

                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="no-receipts">

            🧾 No verified payment receipts available.

        </div>

    <?php endif; ?>


    <a href="dashboard.php" class="back-link">
        ← Back to Office Dashboard
    </a>

</div>

</body>

</html>