<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "office") {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

$message = "";
$error = "";

/* VERIFY PAYMENT */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fee_id = (int)($_POST["fee_id"] ?? 0);

    if ($fee_id <= 0) {
        $error = "Invalid payment selected.";
    } else {

        $sql = "UPDATE fees
                SET payment_status = 'Paid',
                    paid_amount = amount,
                    due_amount = 0,
                    payment_date = CURDATE()
                WHERE id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "i", $fee_id);

        if (mysqli_stmt_execute($stmt)) {
            $message = "Payment verified successfully! ✅";
        } else {
            $error = "Payment verification failed.";
        }

        mysqli_stmt_close($stmt);
    }
}


/* GET PAYMENTS */
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
        ORDER BY fees.id DESC";

$payments = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Payment Verification - CampusConnect</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/office.css">

    <style>

        body {
            background: #f5f7fb;
        }

        .payments-container {
            width: 95%;
            max-width: 1200px;
            margin: 40px auto;
        }

        .payments-container h1 {
            text-align: center;
            color: #1f3c88;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        .message {
            background: #dff7e3;
            color: #167a2f;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }

        .error {
            background: #ffe0e0;
            color: #b00020;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .table-box {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 950px;
        }

        th {
            background: #1f3c88;
            color: white;
            padding: 13px;
            text-align: left;
        }

        td {
            padding: 13px;
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background: #f7f9ff;
        }

        .pending {
            color: #d97706;
            font-weight: bold;
        }

        .partial {
            color: #2563eb;
            font-weight: bold;
        }

        .paid {
            color: green;
            font-weight: bold;
        }

        .verify-button {
            background: #198754;
            color: white;
            border: none;
            padding: 9px 14px;
            border-radius: 7px;
            cursor: pointer;
            font-weight: bold;
        }

        .verify-button:hover {
            background: #146c43;
        }

        .verified {
            color: green;
            font-weight: bold;
        }

        .back-link {
            display: inline-block;
            margin-top: 25px;
            text-decoration: none;
            color: #1f3c88;
            font-weight: bold;
        }

    </style>

</head>

<body>

<div class="payments-container">

    <h1>💳 Payment Verification</h1>

    <p class="subtitle">
        Verify and manage student fee payments
    </p>


    <?php if ($message !== ""): ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <?php if ($error !== ""): ?>

        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>


    <div class="table-box">

        <?php if ($payments && mysqli_num_rows($payments) > 0): ?>

            <table>

                <thead>

                    <tr>
                        <th>Student</th>
                        <th>Fee Type</th>
                        <th>Total Amount</th>
                        <th>Paid Amount</th>
                        <th>Due Amount</th>
                        <th>Transaction ID</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>

                </thead>

                <tbody>

                    <?php while ($payment = mysqli_fetch_assoc($payments)): ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars($payment["student_name"] ?? "Unknown"); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($payment["fee_type"]); ?>
                            </td>

                            <td>
                                ₹<?php echo number_format($payment["amount"], 2); ?>
                            </td>

                            <td>
                                ₹<?php echo number_format($payment["paid_amount"], 2); ?>
                            </td>

                            <td>
                                ₹<?php echo number_format($payment["due_amount"], 2); ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $payment["transaction_id"] ?? "-"
                                );
                                ?>
                            </td>

                            <td>

                                <?php if ($payment["payment_status"] === "Paid"): ?>

                                    <span class="paid">
                                        Paid
                                    </span>

                                <?php elseif ($payment["payment_status"] === "Partial"): ?>

                                    <span class="partial">
                                        Partial
                                    </span>

                                <?php else: ?>

                                    <span class="pending">
                                        Pending
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <?php if ($payment["payment_status"] !== "Paid"): ?>

                                    <form method="POST">

                                        <input
                                            type="hidden"
                                            name="fee_id"
                                            value="<?php echo $payment["id"]; ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="verify-button"
                                            onclick="return confirm('Verify this payment?');"
                                        >
                                            ✅ Verify
                                        </button>

                                    </form>

                                <?php else: ?>

                                    <span class="verified">
                                        ✓ Verified
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        <?php else: ?>

            <p style="text-align:center;">
                No payment records found.
            </p>

        <?php endif; ?>

    </div>


    <a href="dashboard.php" class="back-link">
        ← Back to Office Dashboard
    </a>

</div>

</body>

</html>