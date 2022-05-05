<?php

    require_once('connection.php');

    function userExists(PDO $db, string $username, string $password): bool {

        $query = 'SELECT * FROM users WHERE username = ? AND password = ?';

        return !!getQueryResults($db, $query, false, array($username, sha1($password)));;
    }

    function createUser(PDO $db, string $username, string $password, string $email, string $real_name): bool {

        $query = 'INSERT INTO users VALUES(?, ?, ?)';

        return executeQuery($db, $query, array($username, sha1($password), $real_name))[0];
    }
?>