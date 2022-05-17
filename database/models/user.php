<?php
    declare(strict_types=1);

    include_once('model.php');
    include_once('restaurant.php');
    include(dirname(__DIR__, 2).'/lib/password.php');

    class User extends Model {

        public string $name;
        public string $email;
        public string $address;
        public string $phone_number;
        public string $full_name;
    
        protected static function getTableName(): string {
            return "User";
        }

        function validatePassword(string $passwordCandidate): bool {
            
            $query = "SELECT password FROM User WHERE id = ?;";

            $userData = getQueryResults(static::getDB(), $query, false, [$this->id]);

            if ($userData === false) return false;

            return !strcmp(hashPassword($passwordCandidate), $userData['password']);
        }

        function getOwnedRestaurants(): array {
            
            $query = "SELECT * FROM Restaurant WHERE owner = ?;";

            $queryResults = getQueryResults(static::getDB(), $query, true, [$this->id]);
        
            if ($queryResults === false) return [];

            $results = [];
            foreach ($queryResults as $result) {
                $results[] = Restaurant::get($result['id']);
            }

            return $results;
        }
    }
?>