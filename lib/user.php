<?php

    include_once("../database/models/user.php");
    include_once("password.php");

    function userExists(string $username): bool {
        return !!User::get(array("name" => $username));
    }

    function createUser(string $username, string $password, string $email, string $address, string $phone_number, string $full_name): ?User {
        return User::create(array(
            'name' => $username, 
            'email' => $email, 
            'password' => hashPassword($password), 
            'address' => $address, 
            'phone_number' => $phone_number, 
            'full_name' => $full_name, 
            'is_owner' => 0, 
            'is_driver' => 0));
    }
?>