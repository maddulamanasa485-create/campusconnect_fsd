<?php

include "config/db.php";

$users = [
    ["Student One", "student@campus.com", "Student@123", "student"],
    ["Faculty One", "faculty@campus.com", "Faculty@123", "faculty"],
    ["Office Staff", "office@campus.com", "Office@123", "office"],
    ["Admin One", "admin@campus.com", "Admin@123", "admin"]
];

foreach ($users as $u) {

    $name = $u[0];
    $email = $u[1];
    $plain_password = $u[2];
    $role = $u[3];

    $password = password_hash($plain_password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password, role, status)
            VALUES (?, ?, ?, ?, 1)
            ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            password = VALUES(password),
            role = VALUES(role),
            status = 1";

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

echo "Users ready successfully!";

?>