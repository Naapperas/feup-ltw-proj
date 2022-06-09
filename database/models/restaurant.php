<?php
    declare(strict_types=1);

    require_once(__DIR__.'/model.php');
    require_once(__DIR__.'/user.php');
    require_once(__DIR__.'/category.php');
    require_once(__DIR__.'/dish.php');
    require_once(__DIR__.'/menu.php');
    require_once(__DIR__.'/review.php');
    require_once(__DIR__.'/query.php');

    class Restaurant extends Model implements JsonSerializable {
        use HasImage, HasCategories;

        public string $name;
        public string $address;
        public string $phone_number;
        public string $website;
        public string $opening_time;
        public string $closing_time;
        public ?float $score;

        public int $owner;

        protected static function getTableName(): string {
            return "Restaurant";
        }

        protected static function getImageFolder(): string {
            return "restaurant";
        }

        protected static function getCategoryTableColumn(): string {
            return 'restaurant';
        }

        public function getOwner(): ?User {
            return User::getById($this->owner);
        }

        public function getReviews(int $limit, OrderClause $order = new OrderClause([['score', false]])): array {

            $query = "SELECT * FROM Review WHERE restaurant = ?";
            
            $query .= $order->getQueryString(); // set default ordering
            $query .= " LIMIT ?;";

            $reviews = getQueryResults(static::getDB(), $query, true, [$this->id, $limit]);
        
            if ($reviews === false) return [];

            $result = array_map(fn(array $data) => Review::getById($data['id']), $reviews);

            return $result; 
        }

        public function isLikedBy(User $currentUser): bool {

            $query = "SELECT * FROM Favorite_restaurant WHERE restaurant = ? AND client = ?;";

            $queryResults = getQueryResults(static::getDB(), $query, false, [$this->id, $currentUser->id]);
        
            if ($queryResults === false) return false;

            return count($queryResults) > 0;
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
    }
?>
