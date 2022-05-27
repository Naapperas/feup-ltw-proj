<?php

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0) {
        header("Location: /");
        die();
    }

    session_start();

    require_once('../lib/params.php');

    $params = parseParams(post_params: [
        'email' => new StringParam(
            pattern: '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/',
            case_insensitive: true
        ),
        'username' => new StringParam(min_len: 1),
        'password' => new StringParam(min_len: 8),
        'name' => new StringParam(min_len: 1),
        'address' => new StringParam(min_len: 1),
        'phone' => new StringParam(pattern: '/^\d{9}$/'),
        'referer'
    ]);

    $registrationError = function(string $errorMsg) use ($params): void {
        $_SESSION['register-error'] = $errorMsg;
        $_SESSION['referer'] = $params['referer'];
        header('Location: /register/');
        die;
    };

    require_once('../database/models/user.php');
    require_once('../lib/password.php');

    $userNameExists = count(User::get(['name' => $params['username']])) > 0;
    $userEmailExists = count(User::get(['email' => $params['email']])) > 0;
    $userPhoneExists = count(User::get(['phone_number' => $params['phone']])) > 0;

    if ($userNameExists) {
        $registrationError('User with the same name already registered.');
    } else if ($userEmailExists) {
        $registrationError('User with the same email already registered.');
    } else if ($userPhoneExists) {
        $registrationError('User with the same phone number already registered.');
    }

    $user = User::create([
        'name' => $params['username'],
        'password' => hashPassword($params['password']),
        'email' => $params['email'],
        'address' => $params['address'],
        'phone_number' => $params['phone'],
        'full_name' => $params["name"]
    ]);

    if ($user === null) {
        $registrationError('Error registering user');
    }
    
    unset($_SESSION['referer']);
    $_SESSION['user'] = $user->id;
    header('Location: '.$params['referer']);
?>