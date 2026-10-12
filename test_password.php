<?php

include "config/db.php";

$email = "student@campus.com";
$password = "Student@123";

$sql = "SELECT password FROM users WHERE email = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user) {

    if (password_verify($password, $user["password"])) {
        echo "PASSWORD CORRECT";
    } else {
        echo "PASSWORD WRONG";
    }

} else {
    echo "USER NOT FOUND";
}

?>