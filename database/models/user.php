<?php
    declare(strict_types=1);

    require_once('model.php');
    require_once('restaurant.php');
    require_once('dish.php');
    require_once(dirname(__DIR__, 2).'/lib/password.php');

    class User extends Model {
        use HasImage;

        public string $name;
        public string $email;
        public string $address;
        public string $phone_number;
        public string $full_name;
    
        protected static function getTableName(): string {
            return "User";
        }

        protected static function getImageFolder(): string {
            return "user";
        }

        protected static function getNumberOfDefaultImages(): int {
            return 6;
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

            return array_map(fn(array $data) => Restaurant::getById($data['id']), $queryResults);
        }

        function getFavoriteRestaurants(): array {

            $query = "SELECT restaurant AS id FROM Favorite_restaurant WHERE client = ?;";

            $queryResults = getQueryResults(static::getDB(), $query, true, [$this->id]);
        
            if ($queryResults === false) return [];

            return array_map(fn(array $data) => Restaurant::getById($data['id']), $queryResults);
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

        function getFavoriteDishes(): array {

            $query = "SELECT dish AS id FROM Favorite_dish WHERE client = ?;";

            $queryResults = getQueryResults(static::getDB(), $query, true, [$this->id]);
        
            if ($queryResults === false) return [];

            return array_map(fn(array $data) => Dish::getById($data['id']), $queryResults);
        }

        function addLikedDish(int $dishId): bool {

            $query = 'INSERT INTO Favorite_dish VALUES (?, ?);';

            list($success,) = executeQuery(static::getDB(), $query, [$this->id, $dishId]);

            return $success;
        }

        function removeLikedDish(int $dishId): bool {

            $query = 'DELETE FROM Favorite_dish WHERE client = ? AND dish = ?;';

            list($success,) = executeQuery(static::getDB(), $query, [$this->id, $dishId]);

            return $success;
        }
    }
?>