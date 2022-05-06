<?php

    function userExists(PDO $db, string $username, string $password): bool {

        $query = 'SELECT * FROM users WHERE username = ? AND password = ?';

        return !!getQueryResults($db, $query, false, array($username, sha1($password)));;
    }

    function createUser(PDO $db, string $username, string $password, string $email, string $address, string $phone_number): bool {

        $query = 'INSERT INTO users VALUES(?, ?, ?, ?, ?)';

        return executeQuery($db, $query, array($username, $email, sha1($password), $address, $phone_number))[0];
    }
?>