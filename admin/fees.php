<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include "../config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $student_id = (int) $_POST['student_id'];
    $fee_type = trim($_POST['fee_type']);
    $amount = (float) $_POST['amount'];

    if ($student_id > 0 && $fee_type !== "" && $amount > 0) {

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO fees
            (student_id, fee_type, amount, paid_amount, due_amount, payment_status)
            VALUES (?, ?, ?, 0, ?, 'Pending')"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "isdd",
            $student_id,
            $fee_type,
            $amount,
            $amount
        );

        if (mysqli_stmt_execute($stmt)) {
            $message = "Fee added successfully! 🎉";
        }

        mysqli_stmt_close($stmt);
    }
}

$students = mysqli_query(
    $conn,
    "SELECT id, name, email
     FROM users
     WHERE role = 'student'
     ORDER BY id ASC"
);

$fees = mysqli_query(
    $conn,
    "SELECT fees.*, users.name AS student_name, users.email
     FROM fees
     LEFT JOIN users ON fees.student_id = users.id
     ORDER BY fees.id DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fees - CampusConnect</title>
    <link rel="stylesheet" href="../assets/css/office.css">

    <style>
        .admin-container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }

        .header-box {
            background: linear-gradient(135deg, #182848, #4b6cb7);
            color: white;
            padding: 25px;
            border-radius: 18px;
            margin-bottom: 25px;
        }

        .form-box,
        .fee-card {
            background: white;
            padding: 25px;
            border-radius: 18px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            margin: 8px 0 18px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-sizing: border-box;
        }

        button {
            padding: 12px 22px;
            border: none;
            border-radius: 10px;
            background: #4b6cb7;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }

        .success {
            background: #e8fff0;
            color: #16803c;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .fee-card h2 {
            color: #182848;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #182848;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="admin-container">

    <a href="dashboard.php" class="back-link">
        ← Back to Admin Dashboard
    </a>

    <div class="header-box">
        <h1>💰 Fee Management</h1>
        <p>Add and manage student fees</p>
    </div>

    <?php if ($message): ?>
        <div class="success">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="form-box">

        <h2>➕ Add Student Fee</h2>

        <form method="POST">

            <label><strong>Student</strong></label>

            <select name="student_id" required>
                <option value="">Select Student</option>

                <?php while ($student = mysqli_fetch_assoc($students)): ?>

                    <option value="<?php echo $student['id']; ?>">
                        <?php
                        echo htmlspecialchars($student['name'])
                             . " - "
                             . htmlspecialchars($student['email']);
                        ?>
                    </option>

                <?php endwhile; ?>

            </select>

            <label><strong>Fee Type</strong></label>

            <input
                type="text"
                name="fee_type"
                placeholder="Example: Tuition Fee"
                required
            >

            <label><strong>Total Amount</strong></label>

            <input
                type="number"
                name="amount"
                placeholder="Example: 50000"
                min="1"
                required
            >

            <button type="submit">
                💰 Add Fee
            </button>

        </form>

    </div>

    <h2>📋 Student Fees</h2>

    <?php if (mysqli_num_rows($fees) > 0): ?>

        <?php while ($fee = mysqli_fetch_assoc($fees)): ?>

            <div class="fee-card">

                <h2>
                    👨‍🎓 <?php echo htmlspecialchars($fee['student_name']); ?>
                </h2>

                <p>
                    📧 <strong>Email:</strong>
                    <?php echo htmlspecialchars($fee['email']); ?>
                </p>

                <p>
                    💳 <strong>Fee Type:</strong>
                    <?php echo htmlspecialchars($fee['fee_type']); ?>
                </p>

                <p>
                    💰 <strong>Total:</strong>
                    ₹<?php echo number_format($fee['amount'], 2); ?>
                </p>

                <p>
                    ✅ <strong>Paid:</strong>
                    ₹<?php echo number_format($fee['paid_amount'], 2); ?>
                </p>

                <p>
                    ⚠️ <strong>Due:</strong>
                    ₹<?php echo number_format($fee['due_amount'], 2); ?>
                </p>

                <p>
                    📌 <strong>Status:</strong>
                    <?php echo htmlspecialchars($fee['payment_status']); ?>
                </p>

            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <p>No fee records available.</p>

    <?php endif; ?>

</div>

</body>
</html>