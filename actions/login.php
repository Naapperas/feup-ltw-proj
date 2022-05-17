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

    if (userExists($_POST['username'])) {

        $userCandidate = User::get(array("name" => $_POST['username']))[0];

        if ($userCandidate->validatePassword($_POST['password']))
            $_SESSION['user'] = $userCandidate->id;

        header('Location: ' . $_POST['referer']);
    } else {
        header('Location: /login/');
    }
?>