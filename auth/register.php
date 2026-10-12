<?php

session_start();

include "../config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";
    $role = trim($_POST["role"] ?? "");

    if (
        $name === "" ||
        $email === "" ||
        $password === "" ||
        $confirm_password === "" ||
        $role === ""
    ) {

        $message = "Please fill all fields.";

    } elseif (!in_array($role, ["student", "faculty", "office"])) {

        $message = "Invalid registration role.";

    } elseif ($password !== $confirm_password) {

        $message = "Passwords do not match.";

    } elseif (strlen($password) < 6) {

        $message = "Password must contain at least 6 characters.";

    } else {

        $check = mysqli_prepare(
            $conn,
            "SELECT id FROM users WHERE email = ? LIMIT 1"
        );

        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {

            $message = "Email already registered.";

        } else {

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO users
                (name, email, password, role, status)
                VALUES (?, ?, ?, ?, 1)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $name,
                $email,
                $hashed_password,
                $role
            );

            if (mysqli_stmt_execute($stmt)) {

                $message = "Registration successful! You can login now. ✅";

            } else {

                $message = "Registration failed. Please try again.";

            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($check);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Account - CampusConnect</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/login.css">

</head>

<body class="login-page">

    <div class="login-container">

        <div class="login-box">

            <h1>CampusConnect</h1>

            <p>Create Your Account</p>

            <?php if ($message !== "") { ?>

                <div class="message">
                    <?php echo htmlspecialchars($message); ?>
                </div>

            <?php } ?>

            <form method="POST">

                <label>Full Name</label>

                <input
                    type="text"
                    name="name"
                    placeholder="Enter your full name"
                    required
                >

                <label>Email</label>

                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >

                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    placeholder="Create password"
                    required
                >

                <label>Confirm Password</label>

                <input
                    type="password"
                    name="confirm_password"
                    placeholder="Confirm password"
                    required
                >

                <label>Register As</label>

                <select name="role" required>

                    <option value="">
                        Select Role
                    </option>

                    <option value="student">
                        Student
                    </option>

                    <option value="faculty">
                        Faculty
                    </option>

                    <option value="office">
                        Office / Staff
                    </option>

                </select>

                <button type="submit">
                    Register
                </button>

            </form>

            <p style="margin-top: 20px;">

                Already have an account?

                <a href="login.php">
                    Login
                </a>

            </p>

        </div>

    </div>

</body>

</html>