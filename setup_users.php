<?php

include "config/db.php";

$users = [
    ["Student One", "student@campus.com", "Student@123", "student"],
    ["Faculty One", "faculty@campus.com", "Faculty@123", "faculty"],
    ["Office Staff", "office@campus.com", "Office@123", "office"],
    ["Admin One", "admin@campus.com", "Admin@123", "admin"]
];

foreach ($users as $user) {

    $name = $user[0];
    $email = $user[1];
    $plain_password = $user[2];
    $role = $user[3];

    $password = password_hash($plain_password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users
            (name, email, password, role, status)
            VALUES (?, ?, ?, ?, 1)";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ssss",
        $name,
        $email,
        $password,
        $role
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
}

echo "4 USERS CREATED SUCCESSFULLY";

?>