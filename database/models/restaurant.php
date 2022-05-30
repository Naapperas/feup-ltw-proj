<?php
    declare(strict_types=1);

    require_once('model.php');
    require_once('user.php');
    require_once('category.php');
    require_once('dish.php');
    require_once('menu.php');
    require_once('review.php');

    class Restaurant extends Model {

        public string $name;
        public string $address;
        public string $phone_number;
        public string $website;
        public string $opening_time;
        public string $closing_time;

        public int $owner;

        protected static function getTableName(): string {
            return "Restaurant";
        }

        public function getOwner(): ?User {
            return User::getById($this->owner);
        }

        public function getReviewScore(): ?float {

            $query = "SELECT avg(score) AS average FROM Review WHERE restaurant = ?;";

            $queryResults = getQueryResults(static::getDB(), $query, false, [$this->id]);
        
            if ($queryResults === false) return 0;

            return $queryResults['average']; // returns null if restaurant has no reviews
        }

        public function getReviews(int $limit): array {

            $query = "SELECT * FROM Review WHERE restaurant = ? LIMIT ?";

            $reviews = getQueryResults(static::getDB(), $query, true, [$this->id, $limit]);
        
            if ($reviews === false) return 0;

            $result = array_map(fn(array $data) => Review::getById($data['id']), $reviews);

            return $result; 
        }

        public function isLikedBy(User $currentUser): bool {

            $query = "SELECT * FROM Favorite_restaurant WHERE restaurant = ? AND client = ?;";

            $queryResults = getQueryResults(static::getDB(), $query, false, [$this->id, $currentUser->id]);
        
            if ($queryResults === false) return false;

            return count($queryResults) > 0;
        }

        public function getCategories(): array {

            $query = "SELECT category AS id FROM Restaurant_category WHERE restaurant = ?;";

            $categories = getQueryResults(static::getDB(), $query, true, [$this->id]);
        
            if ($categories === false) return [];

            $result = array_map(fn(array $data) => Category::getById($data['id']), $categories);

            return $result;
        }

        public function getOwnedDishes(): array {

            $query = "SELECT * FROM Dish WHERE restaurant = ?;";

            $queryResults = getQueryResults(static::getDB(), $query, true, [$this->id]);
        
            if ($queryResults === false) return [];

            return array_map(fn(array $data) => Dish::getById($data['id']), $queryResults);
        }

        public function getOwnedMenus(): array {

            $query = "SELECT * FROM Menu WHERE restaurant = ?;";

            $queryResults = getQueryResults(static::getDB(), $query, true, [$this->id]);
        
            if ($queryResults === false) return [];

            return array_map(fn(array $data) => Menu::getById($data['id']), $queryResults);
        }

        public function setCategories(array $categories) : bool {

            $deleteQuery = "DELETE FROM Restaurant_category WHERE restaurant = ?;";

            list($success,) = executeQuery(static::getDB(), $deleteQuery, [$this->id]);

            if (count($categories) === 0) return $success;

            // using this query format we avoid making multiple queries to the DB,
            // with the downside of 'having' to hardcode the restaurant id into the query itself, 
            // but since that id comes from the restaurant model itself, there should be no problem (unless the DB is breached)
            $query = sprintf("INSERT INTO Restaurant_category VALUES (%d, ?)", $this->id); 

            for($i = 1; $i < sizeof($categories); $i++) {
                $query.= sprintf(", (%d, ?)", $this->id);
            }

            $query .= ";";

            list($success,) = executeQuery(static::getDB(), $query, $categories);
        
            return $success;
        }

        public function hasCategory(int $categoryID) : bool {

            $query = "SELECT * FROM Restaurant_category WHERE restaurant = ? AND category = ?;";

            $queryResults = getQueryResults(static::getDB(), $query, true, [$this->id, $categoryID]);
        
            if ($queryResults === false) return false;

            return count($queryResults) > 0;
        }

        function getThumbnail(): string {
            $src = "/assets/pictures/restaurant/$this->id.webp";
            
            if (!file_exists(dirname(dirname(__DIR__)).$src)) {
                $src = "/assets/pictures/restaurant/default.webp";
            }

            return $src;
        }
    }
?>
