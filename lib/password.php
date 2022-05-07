<?php 

    function hashPassword(string $password): string {
        return sha1($password);
    }

?>