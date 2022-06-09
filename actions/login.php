<?php

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0) {
        header("Location: /");
        die;
    }

    session_start();

    require_once('../lib/params.php');

    $params = parseParams(body: [
        'username' => new StringParam(min_len: 1),
        'password' => new StringParam(min_len: 1),
        'referer'
    ]);

    require_once('../database/models/user.php');
    require_once('../database/models/query.php');

    $candidateUser = User::getWithFilters([new Equals('name', $params['username'])]);

    $user = (count($candidateUser) > 0) ? $candidateUser[0] : null;

    if ($user === null || !$user->validatePassword($params['password'])) {
        $_SESSION['login-error'] = 'Incorrect username or password!'; // to be handled by the login page
        $_SESSION['referer'] = $params['referer'];
        header('Location: /login/');
        die();
    }

    unset($_SESSION['referer']);
    $_SESSION['user'] = $user->id;
    header('Location: ' . $params['referer']); 
?>