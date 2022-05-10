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
        !isset($_POST['phone']) ||
        !isset($_POST['referer'])) {
        die("Error: some form data isn't set");
    }

    require_once('../database/models/user.php');
    require_once('../lib/user.php');

    if (!userExists($_POST['username'], $_POST['password'])) {
        $_SESSION['user'] = User::create(array(
            $_POST['username'],
            $_POST['email'],
            $_POST['password'],
            $_POST['address'],
            $_POST['phone'],
            0,
            0));
    }

    header('Location: ' . $_POST['referer']);
?>