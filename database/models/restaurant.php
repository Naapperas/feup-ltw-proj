<?php
    declare(strict_types=1);

    include_once('model.php');
    include_once('user.php');
    include_once('category.php');

    class Restaurant extends Model {

        public string $name;
        public string $address;

        public int $owner;

        protected static function getTableName(): string {
            return "Restaurant";
        }

        public function getOwner(): ?User {
            return User::get($this->owner);
        }

        public function getReviewScore(): ?float {

            $query = "SELECT avg(score) AS average FROM Review WHERE restaurant = ?;";

            $queryResults = getQueryResults(static::getDB(), $query, false, [$this->id]);
        
            if ($queryResults === false) return 0;

            return $queryResults['average']; // returns null if restaurant has no reviews
        }

        public function isLikedBy(User $currentUser): bool {

            $query = "SELECT * FROM Favorite_restaurant WHERE restaurant = ? AND client = ?;";

            $queryResults = getQueryResults(static::getDB(), $query, false, [$this->id, $currentUser->id]);
        
            if ($queryResults === false) return false;

            return count($queryResults) > 0;
        }

        public function getCategories(): array {

            $query = "SELECT category FROM Restaurant_category WHERE restaurant = ?;";

            $categories = getQueryResults(static::getDB(), $query, true, [$this->id]);
        
            if ($categories === false) return [];

            $result = array_map(fn($id) => Category::get($id), $categories);

            return $result;
        }
    }
?>