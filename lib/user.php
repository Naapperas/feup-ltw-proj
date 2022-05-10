<?php

    include_once("../database/models/user.php");
    include_once("password.php");

    function userExists(string $username, string $password): bool {
        return !!User::get(array(
            "name" => $username,
            "password" => hashPassword($password)
        ), true);
    }

    function createUser(string $username, string $password, string $email, string $address, string $phone_number): array {
        return User::create(array($username, $email, hashPassword($password), $address, $phone_number, 0, 0));
    }

    function toggleOwner(int $user_id): array {

        $user = User::get($user_id);

        return User::update($user_id, array(
            $user["name"],
            $user["password"],
            $user["email"],
            $user["address"],
            $user["phone_number"],
            !$user["is_owner"],
            $user["is_driver"],
        ));
    }

    function toggleDriver(int $user_id): array {

        $user = User::get($user_id);

        return User::update($user_id, array(
            $user["name"],
            $user["password"],
            $user["email"],
            $user["address"],
            $user["phone_number"],
            $user["is_owner"],
            !$user["is_driver"],
        ));
    }
?>