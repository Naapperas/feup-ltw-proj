<?php

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0) {
        header("Location: /");
        die;
    }

    session_start();

    if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['referer'])) {
        die("Error: some form data isn't set");
    }

    require_once('../database/models/user.php');
    require_once('../lib/user.php');

    if (userExists($_POST['username'], $_POST['password'])) {
        $_SESSION['user'] = User::get(array("name" => $_POST['username']), true)[0]; // need to put [0] because we are doing a "named" query
        header('Location: ' . $_POST['referer']);
    } else {
        header('Location: /login/');
    }
?>