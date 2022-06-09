<?php
    declare(strict_types=1);

    require_once(__DIR__.'/model.php');
    require_once(__DIR__.'/restaurant.php');
    require_once(__DIR__.'/category.php');

    class Dish extends Model implements JsonSerializable {
        use HasImage, HasCategories;

        public string $name;
        public float $price;

        public int $restaurant;

        protected static function getTableName(): string {
            return 'Dish';
        }

        protected static function getImageFolder(): string {
            return "dish";
        }

        protected static function getCategoryTableColumn(): string {
            return 'dish';
        }

        public function getRestaurant(): ?Restaurant {
            return Restaurant::getById($this->restaurant);
        }

        public function isLikedBy(User $currentUser): bool {

            $query = "SELECT * FROM Favorite_dish WHERE dish = ? AND client = ?;";

            $queryResults = getQueryResults(static::getDB(), $query, false, [$this->id, $currentUser->id]);
        
            if ($queryResults === false) return false;

            return count($queryResults) > 0;
        }
    }
?>