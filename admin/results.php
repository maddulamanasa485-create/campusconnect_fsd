```php
<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include "../config/db.php";

$message = "";

/* ADD RESULT */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_id = intval($_POST['student_id']);
    $subject = trim($_POST['subject']);
    $exam = trim($_POST['exam']);
    $marks = intval($_POST['marks']);
    $max_marks = intval($_POST['max_marks']);
    $credits = intval($_POST['credits']);
    $semester = intval($_POST['semester']);

    $faculty_id = $_SESSION['user_id'];

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO marks
        (faculty_id, student_id, subject, exam, marks, max_marks, credits, semester)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "iissiiii",
        $faculty_id,
        $student_id,
        $subject,
        $exam,
        $marks,
        $max_marks,
        $credits,
        $semester
    );

    if (mysqli_stmt_execute($stmt)) {
        $message = "Result added successfully!";
    } else {
        $message = "Error adding result: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

/* STUDENTS */
$students = mysqli_query(
    $conn,
    "SELECT id, name, email
     FROM users
     WHERE role='student'
     ORDER BY name ASC"
);

/* RESULTS */
$results = mysqli_query(
    $conn,
    "SELECT 
        marks.id,
        marks.subject,
        marks.exam,
        marks.marks,
        marks.max_marks,
        marks.credits,
        marks.semester,
        users.name AS student_name,
        users.email
     FROM marks
     INNER JOIN users
     ON marks.student_id = users.id
     ORDER BY marks.id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Results - CampusConnect</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    min-height: 100vh;
    background: linear-gradient(135deg, #dbeafe, #eef2ff, #f8fafc);
    color: #1e293b;
}

/* MAIN CONTAINER */

.container {
    width: 92%;
    max-width: 1150px;
    margin: 35px auto;
}

/* BACK */

.back {
    display: inline-block;
    text-decoration: none;
    color: #2563eb;
    font-weight: bold;
    margin-bottom: 20px;
}

/* HEADER */

.header {
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    color: white;
    padding: 35px;
    border-radius: 22px;
    margin-bottom: 25px;
    box-shadow: 0 10px 25px rgba(37, 99, 235, 0.25);
}

.header h1 {
    margin: 0 0 10px;
    font-size: 32px;
}

.header p {
    margin: 0;
    opacity: 0.9;
}

/* SUCCESS */

.success {
    background: #dcfce7;
    color: #166534;
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-weight: bold;
}

/* FORM */

.form-card {
    background: white;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}

.form-card h2 {
    margin-top: 0;
    color: #1e3a8a;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 18px;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 7px;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 13px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    font-size: 15px;
    outline: none;
}

.form-group input:focus,
.form-group select:focus {
    border-color: #2563eb;
}

/* BUTTON */

.btn {
    margin-top: 22px;
    padding: 14px 25px;
    border: none;
    border-radius: 11px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: white;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
}

.btn:hover {
    transform: translateY(-1px);
}

/* RESULTS TITLE */

.results-title {
    color: #1e3a8a;
    margin-bottom: 18px;
}

/* RESULT CARD */

.result-card {
    background: white;
    border-radius: 18px;
    padding: 24px;
    margin-bottom: 18px;
    box-shadow: 0 7px 20px rgba(0,0,0,0.07);
    border-left: 6px solid #2563eb;
}

.student-name {
    font-size: 22px;
    font-weight: bold;
    color: #1e3a8a;
    margin-bottom: 5px;
}

.email {
    color: #64748b;
    margin-bottom: 18px;
}

.details {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

.detail {
    background: #f8fafc;
    padding: 13px;
    border-radius: 10px;
}

.detail strong {
    display: block;
    color: #475569;
    font-size: 13px;
    margin-bottom: 5px;
}

.detail span {
    font-weight: bold;
    color: #1e293b;
}

/* NO RESULTS */

.no-results {
    background: white;
    padding: 30px;
    text-align: center;
    border-radius: 18px;
    color: #64748b;
}

/* MOBILE */

@media (max-width: 700px) {

    .form-grid {
        grid-template-columns: 1fr;
    }

    .details {
        grid-template-columns: 1fr;
    }

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

        <h1>📊 Results Management</h1>

        <p>
            Add and manage student examination results
        </p>

    </div>


    <?php if ($message != ""): ?>

        <div class="success">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <!-- ADD RESULT -->

    <div class="form-card">

        <h2>➕ Add Student Result</h2>

        <form method="POST">

            <div class="form-grid">

                <!-- STUDENT -->

                <div class="form-group">

                    <label>Student</label>

                    <select name="student_id" required>

                        <option value="">
                            Select Student
                        </option>

                        <?php while ($student = mysqli_fetch_assoc($students)): ?>

                            <option value="<?php echo $student['id']; ?>">

                                <?php
                                echo htmlspecialchars($student['name']);
                                ?>
                                -
                                <?php
                                echo htmlspecialchars($student['email']);
                                ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>


                <!-- SUBJECT -->

                <div class="form-group">

                    <label>Subject</label>

                    <input
                        type="text"
                        name="subject"
                        placeholder="Example: PHP"
                        required
                    >

                </div>


                <!-- EXAM -->

                <div class="form-group">

                    <label>Exam</label>

                    <select name="exam" required>

                        <option value="">
                            Select Exam
                        </option>

                        <option value="Internal 1">
                            Internal 1
                        </option>

                        <option value="Internal 2">
                            Internal 2
                        </option>

                        <option value="Semester Exam">
                            Semester Exam
                        </option>

                    </select>

                </div>


                <!-- MARKS -->

                <div class="form-group">

                    <label>Marks Obtained</label>

                    <input
                        type="number"
                        name="marks"
                        min="0"
                        placeholder="Example: 90"
                        required
                    >

                </div>


                <!-- MAX MARKS -->

                <div class="form-group">

                    <label>Maximum Marks</label>

                    <input
                        type="number"
                        name="max_marks"
                        value="100"
                        min="1"
                        required
                    >

                </div>


                <!-- CREDITS -->

                <div class="form-group">

                    <label>Credits</label>

                    <input
                        type="number"
                        name="credits"
                        value="3"
                        min="1"
                        max="10"
                        required
                    >

                </div>


                <!-- SEMESTER -->

                <div class="form-group">

                    <label>Semester</label>

                    <select name="semester" required>

                        <option value="">
                            Select Semester
                        </option>

                        <?php for ($i = 1; $i <= 8; $i++): ?>

                            <option value="<?php echo $i; ?>">
                                Semester <?php echo $i; ?>
                            </option>

                        <?php endfor; ?>

                    </select>

                </div>

            </div>


            <button class="btn" type="submit">
                📊 Add Result
            </button>

        </form>

    </div>


    <!-- RESULTS -->

    <h2 class="results-title">
        📋 Student Results
    </h2>


    <?php if ($results && mysqli_num_rows($results) > 0): ?>

        <?php while ($row = mysqli_fetch_assoc($results)): ?>

            <div class="result-card">

                <div class="student-name">

                    👨‍🎓
                    <?php echo htmlspecialchars($row['student_name']); ?>

                </div>

                <div class="email">

                    📧
                    <?php echo htmlspecialchars($row['email']); ?>

                </div>


                <div class="details">

                    <div class="detail">

                        <strong>📚 Subject</strong>

                        <span>
                            <?php echo htmlspecialchars($row['subject']); ?>
                        </span>

                    </div>


                    <div class="detail">

                        <strong>📝 Exam</strong>

                        <span>
                            <?php echo htmlspecialchars($row['exam']); ?>
                        </span>

                    </div>


                    <div class="detail">

                        <strong>📊 Marks</strong>

                        <span>
                            <?php
                            echo $row['marks'];
                            echo " / ";
                            echo $row['max_marks'];
                            ?>
                        </span>

                    </div>


                    <div class="detail">

                        <strong>⭐ Credits</strong>

                        <span>
                            <?php echo $row['credits']; ?>
                        </span>

                    </div>


                    <div class="detail">

                        <strong>📖 Semester</strong>

                        <span>
                            <?php echo $row['semester']; ?>
                        </span>

                    </div>


                    <div class="detail">

                        <strong>🎯 Percentage</strong>

                        <span>

                            <?php
                            $percentage =
                                ($row['marks'] / $row['max_marks']) * 100;

                            echo number_format($percentage, 2);
                            ?>%

                        </span>

                    </div>

                </div>

            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <div class="no-results">

            📭 No results available currently.

        </div>

    <?php endif; ?>

</div>

</body>

</html>
```
