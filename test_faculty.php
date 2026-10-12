<?php

include "config/db.php";

$email = "faculty@campus.com";
$password = "Faculty@123";

$sql = "SELECT password FROM users WHERE email = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo "Faculty user NOT FOUND";
} else {

    if (password_verify($password, $user["password"])) {
        echo "Faculty password is CORRECT";
    } else {
        echo "Faculty password is WRONG";
    }
}

?>