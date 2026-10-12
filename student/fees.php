<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

$name = $_SESSION["name"];
$student_id = $_SESSION["user_id"];


/* Get student's fee records */

$sql = "SELECT fee_type, amount, paid_amount, due_amount,
               payment_status, payment_date, transaction_id
        FROM fees
        WHERE student_id = ?
        ORDER BY created_at DESC";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $student_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


/* Calculate totals */

$total_fee = 0;
$total_paid = 0;
$total_due = 0;

$fees = [];

while ($row = mysqli_fetch_assoc($result)) {

    $fees[] = $row;

    $total_fee += (float)$row["amount"];
    $total_paid += (float)$row["paid_amount"];
    $total_due += (float)$row["due_amount"];
}


/* Overall status */

if ($total_fee == 0) {

    $overall_status = "No Fee Records";

} elseif ($total_due == 0) {

    $overall_status = "Fully Paid";

} elseif ($total_paid > 0) {

    $overall_status = "Partially Paid";

} else {

    $overall_status = "Pending";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Fees - CampusConnect</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <link rel="stylesheet" href="../assets/css/student-pages.css">

</head>

<body class="student-page fees-page">

<div class="student-container">

    <h1>💰 Fee Details</h1>

    <p class="page-subtitle">
        Student:
        <?php echo htmlspecialchars($name); ?> 👋
    </p>


    <!-- SUMMARY -->

    <div class="dashboard-cards">

        <div class="card">

            <h2>💵 Total Fee</h2>

            <p class="amount">
                ₹<?php echo number_format($total_fee, 2); ?>
            </p>

        </div>


        <div class="card">

            <h2>✅ Paid Fee</h2>

            <p class="amount paid">
                ₹<?php echo number_format($total_paid, 2); ?>
            </p>

        </div>


        <div class="card">

            <h2>⏳ Pending Fee</h2>

            <p class="amount pending">
                ₹<?php echo number_format($total_due, 2); ?>
            </p>

        </div>


        <div class="card">

            <h2>📄 Payment Status</h2>

            <p class="status">
                <?php echo htmlspecialchars($overall_status); ?>
            </p>

        </div>

    </div>


    <!-- FEE RECORDS -->

    <div class="student-fee-records">

        <h2>📋 Fee Payment Details</h2>


        <?php if (count($fees) > 0) { ?>

            <div class="student-fee-list">

                <?php foreach ($fees as $fee) { ?>

                    <div class="student-fee-card">

                        <h3>
                            💰
                            <?php echo htmlspecialchars($fee["fee_type"]); ?>
                        </h3>


                        <div class="fee-details">

                            <p>
                                <strong>Total Amount:</strong>
                                ₹<?php echo number_format($fee["amount"], 2); ?>
                            </p>


                            <p>
                                <strong>Paid Amount:</strong>
                                ₹<?php echo number_format($fee["paid_amount"], 2); ?>
                            </p>


                            <p>
                                <strong>Due Amount:</strong>
                                ₹<?php echo number_format($fee["due_amount"], 2); ?>
                            </p>


                            <p>
                                <strong>Payment Date:</strong>

                                <?php

                                if (!empty($fee["payment_date"])) {

                                    echo date(
                                        "d M Y",
                                        strtotime($fee["payment_date"])
                                    );

                                } else {

                                    echo "-";

                                }

                                ?>

                            </p>


                            <?php if (!empty($fee["transaction_id"])) { ?>

                                <p>
                                    <strong>Transaction ID:</strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $fee["transaction_id"]
                                    );
                                    ?>
                                </p>

                            <?php } ?>


                            <p>

                                <strong>Status:</strong>

                                <span class="student-fee-status
                                    <?php
                                    echo strtolower(
                                        $fee["payment_status"]
                                    );
                                    ?>">

                                    <?php
                                    echo htmlspecialchars(
                                        $fee["payment_status"]
                                    );
                                    ?>

                                </span>

                            </p>

                        </div>

                    </div>

                <?php } ?>

            </div>

        <?php } else { ?>

            <div class="no-fees">

                💰 No fee records available yet.

            </div>

        <?php } ?>

    </div>


    <a href="dashboard.php" class="back-link">

        ← Back to Dashboard

    </a>

</div>

</body>

</html>