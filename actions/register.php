<?php

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0) {
        header("Location: /index.php");
        die;
    }

    session_start();

    if (!isset($_POST['email']) ||
        !isset($_POST['username']) ||
        !isset($_POST['password']) ||
        !isset($_POST['fname']) ||
        !isset($_POST['lname']) ||
        !isset($_POST['address']) ||
        !isset($_POST['phone'])) {
        die("Error: some form data isn't set");
    }

    require_once('../database/models/user.php');
    require_once('../lib/user.php');

    if (!userExists($_POST['username'], $_POST['password'])) {
        $_SESSION['user'] = createUser(
            $_POST['username'],
            $_POST['password'],
            $_POST['email'],
            $_POST['address'],
            $_POST['phone']);
    }

    print_r($_SESSION);

    // header('Location: ' . $_POST['referer']);
?>