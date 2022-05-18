<?php 
    declare(strict_types=1);

    function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    function comparePassword(string $passwordCandidate, string $hashedPassword): bool {
        return password_verify($passwordCandidate, $hashedPassword);
    }

?>