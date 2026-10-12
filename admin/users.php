<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include "../config/db.php";

$message = "";


/* ADD USER */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";
    $role = $_POST['role'] ?? "";

    if ($name !== "" && $email !== "" && $password !== "" && $role !== "") {

        /* CHECK EMAIL */

        $check = mysqli_prepare(
            $conn,
            "SELECT id FROM users WHERE email = ?"
        );

        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {

            $message = "Email already exists! ❌";

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

                $message = "User added successfully! 🎉";

            } else {

                $message = "Error adding user.";

            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($check);
    }
}


/* CHANGE USER STATUS */

if (isset($_GET["toggle_id"])) {

    $toggle_id = (int)$_GET["toggle_id"];


    /* CHECK TARGET USER */

    $check_admin = mysqli_prepare(
        $conn,
        "SELECT role FROM users WHERE id = ?"
    );

    mysqli_stmt_bind_param(
        $check_admin,
        "i",
        $toggle_id
    );

    mysqli_stmt_execute($check_admin);

    $admin_result = mysqli_stmt_get_result(
        $check_admin
    );

    $target_user = mysqli_fetch_assoc(
        $admin_result
    );

    mysqli_stmt_close($check_admin);


    /* DO NOT DEACTIVATE ADMIN */

    if ($target_user && $target_user["role"] !== "admin") {

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE users
             SET status = IF(status = 1, 0, 1)
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $toggle_id
        );

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
    }

    header("Location: users.php");
    exit();
}


/* GET USERS */

$users = mysqli_query(
    $conn,
    "SELECT id, name, email, role, status
     FROM users
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Admin Users - CampusConnect</title>


<style>

* {
    box-sizing: border-box;
}

body {

    margin: 0;

    font-family: Arial, sans-serif;

    min-height: 100vh;

    background:
        linear-gradient(
            135deg,
            #eef2ff,
            #f0fdf4,
            #fff7ed
        );

    color: #1e293b;
}


.container {

    width: 92%;

    max-width: 1150px;

    margin: 35px auto;
}


.back {

    display: inline-block;

    margin-bottom: 20px;

    text-decoration: none;

    color: #4338ca;

    font-weight: bold;
}


.header {

    background:
        linear-gradient(
            135deg,
            #4338ca,
            #6366f1
        );

    color: white;

    padding: 35px;

    border-radius: 22px;

    margin-bottom: 25px;

    box-shadow:
        0 10px 25px
        rgba(67,56,202,0.25);
}


.header h1 {

    margin: 0 0 10px;

    font-size: 32px;
}


.header p {

    margin: 0;

    opacity: 0.9;
}


.message {

    padding: 15px 20px;

    border-radius: 12px;

    margin-bottom: 20px;

    background: #dcfce7;

    color: #166534;

    font-weight: bold;
}


.form-card {

    background: white;

    padding: 30px;

    border-radius: 20px;

    margin-bottom: 30px;

    box-shadow:
        0 8px 25px
        rgba(0,0,0,0.08);
}


.form-card h2 {

    margin-top: 0;

    color: #4338ca;
}


.form-grid {

    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

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

    border:
        1px solid
        #cbd5e1;

    border-radius: 10px;

    font-size: 15px;
}


.btn {

    margin-top: 22px;

    padding: 14px 25px;

    border: none;

    border-radius: 11px;

    background:
        linear-gradient(
            135deg,
            #4338ca,
            #6366f1
        );

    color: white;

    font-size: 15px;

    font-weight: bold;

    cursor: pointer;
}


.title {

    color: #4338ca;

    margin-bottom: 18px;
}


.user-card {

    background: white;

    padding: 24px;

    border-radius: 18px;

    margin-bottom: 18px;

    box-shadow:
        0 7px 20px
        rgba(0,0,0,0.07);

    border-left:
        6px solid
        #6366f1;
}


.user-name {

    font-size: 21px;

    font-weight: bold;

    color: #312e81;
}


.email {

    color: #64748b;

    margin: 7px 0 15px;
}


.details {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 12px;
}


.detail {

    background: #f8fafc;

    padding: 13px;

    border-radius: 10px;
}


.detail strong {

    display: block;

    font-size: 13px;

    color: #64748b;

    margin-bottom: 5px;
}


.detail span {

    font-weight: bold;
}


.role {

    text-transform: capitalize;
}


.active {

    color: #15803d;
}


.inactive {

    color: #dc2626;
}


/* STATUS BUTTON */

.status-btn {

    display: inline-block;

    margin-top: 10px;

    padding: 9px 15px;

    border-radius: 9px;

    text-decoration: none;

    font-size: 13px;

    font-weight: bold;
}


.deactivate {

    background: #fee2e2;

    color: #b91c1c;
}


.activate {

    background: #dcfce7;

    color: #166534;
}


.admin-protected {

    display: inline-block;

    margin-top: 10px;

    padding: 9px 15px;

    border-radius: 9px;

    background: #e0e7ff;

    color: #3730a3;

    font-size: 13px;

    font-weight: bold;
}


@media (max-width: 700px) {

    .form-grid,
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


    <a
        href="dashboard.php"
        class="back"
    >
        ← Back to Admin Dashboard
    </a>


    <div class="header">

        <h1>
            👥 User Management
        </h1>

        <p>
            Create and manage CampusConnect users
        </p>

    </div>


    <?php if ($message != ""): ?>

        <div class="message">

            <?php
            echo htmlspecialchars($message);
            ?>

        </div>

    <?php endif; ?>


    <!-- ADD USER -->

    <div class="form-card">

        <h2>
            ➕ Add New User
        </h2>


        <form method="POST">

            <div class="form-grid">


                <div class="form-group">

                    <label>
                        Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        placeholder="Enter full name"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        placeholder="example@campus.com"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        placeholder="Enter password"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Role
                    </label>

                    <select
                        name="role"
                        required
                    >

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
                            Office
                        </option>

                        <option value="admin">
                            Admin
                        </option>

                    </select>

                </div>


            </div>


            <button
                type="submit"
                class="btn"
            >
                👤 Add User
            </button>


        </form>

    </div>


    <!-- ALL USERS -->

    <h2 class="title">

        📋 All Users

    </h2>


    <?php if ($users && mysqli_num_rows($users) > 0): ?>


        <?php while ($user = mysqli_fetch_assoc($users)): ?>


            <div class="user-card">


                <div class="user-name">

                    👤

                    <?php
                    echo htmlspecialchars(
                        $user['name']
                    );
                    ?>

                </div>


                <div class="email">

                    📧

                    <?php
                    echo htmlspecialchars(
                        $user['email']
                    );
                    ?>

                </div>


                <div class="details">


                    <!-- USER ID -->

                    <div class="detail">

                        <strong>
                            🆔 User ID
                        </strong>

                        <span>

                            <?php
                            echo $user['id'];
                            ?>

                        </span>

                    </div>


                    <!-- ROLE -->

                    <div class="detail">

                        <strong>
                            👥 Role
                        </strong>

                        <span class="role">

                            <?php
                            echo htmlspecialchars(
                                $user['role']
                            );
                            ?>

                        </span>

                    </div>


                    <!-- STATUS -->

                    <div class="detail">

                        <strong>
                            🔐 Status
                        </strong>


                        <span class="<?php

                            echo $user['status'] == 1
                                ? 'active'
                                : 'inactive';

                        ?>">

                            <?php

                            echo $user['status'] == 1
                                ? 'Active ✅'
                                : 'Inactive ❌';

                            ?>

                        </span>


                        <?php if ($user['role'] === 'admin'): ?>


                            <div class="admin-protected">

                                🔒 Admin Protected

                            </div>


                        <?php else: ?>


                            <?php if ($user['status'] == 1): ?>

                                <a
                                    href="users.php?toggle_id=<?php echo $user['id']; ?>"
                                    class="status-btn deactivate"
                                    onclick="return confirm('Are you sure you want to deactivate this user?');"
                                >
                                    🚫 Deactivate
                                </a>

                            <?php else: ?>

                                <a
                                    href="users.php?toggle_id=<?php echo $user['id']; ?>"
                                    class="status-btn activate"
                                    onclick="return confirm('Activate this user?');"
                                >
                                    ✅ Activate
                                </a>

                            <?php endif; ?>


                        <?php endif; ?>


                    </div>


                </div>


            </div>


        <?php endwhile; ?>


    <?php else: ?>


        <div class="form-card">

            No users available.

        </div>


    <?php endif; ?>


</div>

</body>

</html>