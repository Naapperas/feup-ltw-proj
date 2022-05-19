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

            return comparePassword($passwordCandidate, $userData['password']);
        }

        function getOwnedRestaurants(): array {
            
            $query = "SELECT * FROM Restaurant WHERE owner = ?;";

            $queryResults = getQueryResults(static::getDB(), $query, true, [$this->id]);
        
            if ($queryResults === false) return [];

            return array_map(fn(array $id) => Restaurant::get($id)[0], $queryResults);
        }

        function getFavoriteRestaurants(): array {

            $query = "SELECT restaurant AS id FROM Favorite_restaurant WHERE client = ?;";

            $queryResults = getQueryResults(static::getDB(), $query, true, [$this->id]);
        
            if ($queryResults === false) return [];

            return array_map(fn(array $id) => Restaurant::get($id)[0], $queryResults);
        }

        function addLikedRestaurant(int $restaurantId): bool {

            $query = 'INSERT INTO Favorite_restaurant VALUES (?, ?);';

            list($success,) = executeQuery(static::getDB(), $query, [$this->id, $restaurantId]);

            return $success;
        }

        function removeLikedRestaurant(int $restaurantId): bool {

            $query = 'DELETE FROM Favorite_restaurant WHERE client = ? AND restaurant = ?;';

            list($success,) = executeQuery(static::getDB(), $query, [$this->id, $restaurantId]);

            return $success;
        }
    }
?>