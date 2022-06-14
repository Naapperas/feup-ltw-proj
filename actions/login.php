<?php

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0) {
        header("Location: /");
        die;
    }

    require_once('../lib/session.php');
    $session = new Session();

    if ($session->isAuthenticated()) {
        header("Location: /");
        die();
    }

    require_once('../lib/params.php');
    require_once('../lib/page.php');

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
        $session->set('login-error', 'Incorrect username or password!'); // to be handled by the login page
        $session->set('referer', $params['referer']);
        header('Location: /login/');
        die();
    }

    $session->set('referer', null);
    $session->set('user', $user->id);
    header('Location: ' . $params['referer']); 
?>