<?php 

session_start(); 

include "../config/db.php"; 

$message = ""; 

if ($_SERVER["REQUEST_METHOD"] === "POST") { 

    $email = trim($_POST["email"] ?? ""); 
    $password = trim($_POST["password"] ?? ""); 
    $role = trim($_POST["role"] ?? ""); 

    if ($email === "" || $password === "" || $role === "") { 

        $message = "Please fill all fields."; 

    } else { 

        $sql = "SELECT id, name, email, password, role, status 
                FROM users 
                WHERE email = ? 
                LIMIT 1"; 

        $stmt = mysqli_prepare($conn, $sql); 

        if (!$stmt) { 

            $message = "Database error."; 

        } else { 

            mysqli_stmt_bind_param($stmt, "s", $email); 
            mysqli_stmt_execute($stmt); 

            $result = mysqli_stmt_get_result($stmt); 
            $user = mysqli_fetch_assoc($result); 

            if (!$user) { 

                $message = "User not found."; 

            } elseif ((int)$user["status"] !== 1) { 

                $message = "This account is inactive."; 

            } elseif (!password_verify($password, $user["password"])) { 

                $message = "Incorrect password."; 

            } elseif ($user["role"] !== $role) { 

                $message = "Selected role does not match this account."; 

            } else { 

                $_SESSION["user_id"] = $user["id"]; 
                $_SESSION["name"] = $user["name"]; 
                $_SESSION["role"] = $user["role"]; 

                switch ($user["role"]) { 

                    case "student": 
                        header("Location: ../student/dashboard.php"); 
                        exit; 

                    case "faculty": 
                        header("Location: ../faculty/dashboard.php"); 
                        exit; 

                    case "office": 
                        header("Location: ../office/dashboard.php"); 
                        exit; 

                    case "admin": 
                        header("Location: ../admin/dashboard.php"); 
                        exit; 

                    default: 
                        $message = "Invalid user role."; 
                } 
            } 

            mysqli_stmt_close($stmt); 
        } 
    } 
} 

?> 

<!DOCTYPE html> 

<html lang="en"> 

<head> 

    <meta charset="UTF-8"> 

    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 

    <title>CampusConnect Login</title> 

    <link rel="stylesheet" href="../assets/css/style.css"> 
    <link rel="stylesheet" href="../assets/css/login.css"> 

</head> 

<body class="login-page"> 

    <div class="login-container"> 

        <div class="login-box"> 

            <h1>CampusConnect</h1> 

            <p>College Management Portal</p> 

            <?php if ($message !== "") { ?> 

                <div class="message"> 
                    <?php echo htmlspecialchars($message); ?> 
                </div> 

            <?php } ?> 

            <form method="POST"> 

                <label>Login As</label> 

                <select name="role" required> 

                    <option value="">Select Role</option> 
                    <option value="student">Student</option> 
                    <option value="faculty">Faculty</option> 
                    <option value="office">Office / Staff</option> 
                    <option value="admin">Admin</option> 

                </select> 

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
                    placeholder="Enter your password" 
                    required 
                > 

                <button type="submit"> 
                    Login 
                </button> 

            </form>


            <!-- REGISTER LINK -->

            <p style="margin-top: 20px;">

                Don't have an account?

                <a href="register.php">
                    Create Student Account
                </a>

            </p>


        </div> 

    </div> 

</body> 

</html>