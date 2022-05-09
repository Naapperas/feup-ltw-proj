<?php

    include_once("../database/models/user.php");
    include_once("../lib/password.php");

    function userExists(string $username, string $password): bool {
        return !!User::get(array(
            "name" => $username,
            "password" => hashPassword($password)
        ), true);
    }

    function createUser(string $username, string $password, string $email, string $address, string $phone_number): array {

        return User::create(array($username, $email, hashPassword($password), $address, $phone_number, 0, 1, 0));
    }
?>