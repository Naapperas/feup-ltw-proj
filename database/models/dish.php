<?php
    declare(strict_types=1);

    include_once('model.php');
    include_once('restaurant.php');

    class Dish extends Model {

        public string $name;
        public float $price;

        public int $restaurant;

        protected static function getTableName(): string {
            return 'Dish';
        }

        public function getRestaurant(): ?Restaurant {
            return Restaurant::get($this->restaurant);
        }

        public function getCategories(): array {

            $query = "SELECT category FROM Dish_category WHERE dish = ?;";

            $categories = getQueryResults(static::getDB(), $query, true, [$this->id]);
        
            if ($categories === false) return [];

            $result = array_map(fn($id) => Category::get($id), $categories);

            return $result;
        }

        public function isLikedBy(User $currentUser): bool {

            $query = "SELECT * FROM Favorite_dish WHERE dish = ? AND client = ?;";

            $queryResults = getQueryResults(static::getDB(), $query, false, [$this->id, $currentUser->id]);
        
            if ($queryResults === false) return false;

            return count($queryResults) > 0;
        }
    }
?>