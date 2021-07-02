<?php
use JetBrains\PhpStorm\NoReturn;

# -- Signup Functions --
function emptyInputSignup($username, $email, $password, $passwordConf): bool
{
    if (empty($username) || empty($email) || empty($password) || empty($passwordConf)) {
        return true;
    } else {
        return false;
    }
}

function invalidUsername($username): bool
{
    if (preg_match("/^[a-zA-Z0-9]*?/", $username)) {
        return false;
    } else {
        return true;
    }
}

function invalidEmail($email): bool
{
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    } else {
        return true;
    }
}

function confirmPassword($password, $passwordConf): bool
{
    if ($password !== $passwordConf) {
        return true;
    } else {
        return false;
    }
}

function usernameExists($conn, $username): array|bool|null
{
    $sql = "SELECT * FROM users WHERE usersUsername = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../signup.php?error=internalStmtFailure");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)) {
        return $row;
    } else {
        return false;
    }

    # mysqli_stmt_close($stmt);
}

#[NoReturn] function createUser($conn, $username, $email, $password) {
    $sql = "INSERT INTO users (usersUsername, usersEmail, usersPassword) VALUES (?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../signup.php?error=internalStmtFailure");
        exit();
    }

    # $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("location: ../index.php?error=none");
    exit();
}

# -- Login Functions --
function emptyInputLogin($username, $password): bool
{
    if (empty($username) ||empty($password)) {
        return true;
    } else {
        return false;
    }
}

function loginUser($conn, $username, $password) {
    $usernameExists = usernameExists($conn, $username);

    if (!$usernameExists) {
        header("location: ../login.php?error=invalidLogin");
        exit();
    }

    # $passwordHashed = $usernameExists["userPassword"]
    # $checkPassword = password_verify($pwd, $passwordHashed);
    if ($password !== $usernameExists["usersPassword"]) {
        header("location: ../login.php?error=invalidLogin");
        exit();
    } elseif ($password === $usernameExists["usersPassword"]) {
        session_start();
        $_SESSION['userId'] = $usernameExists["usersId"];
        $_SESSION['userPermId'] = $usernameExists["usersPermId"];
        $_SESSION['userUsername'] = $usernameExists["usersUsername"];
        header("location: ../index.php?error=invalidLogin");
        exit();
    }
}












