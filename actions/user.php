<?php

    include_once("../database/models/user.php");
    include_once("../lib/password.php");

    function userExists(string $username, string $password): bool {
        return !!User::get(array(
            "name" => $username,
            "password" => hashPassword($password)
        ), true);
    }

    function createUser(PDO $db, string $username, string $password, string $email, string $address, string $phone_number): bool {

        return User::create(array($username, hashPassword($password)));

        $query = 'INSERT INTO users VALUES(?, ?, ?, ?, ?)';

        return executeQuery($db, $query, array($username, $email, hashPassword($password), $address, $phone_number))[0];
    }
?>