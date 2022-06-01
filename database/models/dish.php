<?php
    declare(strict_types=1);

    require_once('model.php');
    require_once('restaurant.php');

    class Dish extends ModelWithImage {

        public string $name;
        public float $price;

        public int $restaurant;

        protected static function getTableName(): string {
            return 'Dish';
        }

        protected static function getImageFolder(): string {
            return "dish";
        }

        public static function getForCategoryIds(array $categoryIds): array {

            $restaurants = [];
            $query = "SELECT dish AS id FROM Dish_category WHERE category = ?;";

            foreach($categoryIds as $categoryId) {

                $queryResults = getQueryResults(static::getDB(), $query, true, [$categoryId]);

                if ($queryResults === false) $queryResults = [];

                $restaurants = array_merge($restaurants, array_map(fn (array $data) => static::getById($data['id']), $queryResults));
            }

            return $restaurants;
        }

        public function getRestaurant(): ?Restaurant {
            return Restaurant::getById($this->restaurant);
        }

        public function getCategories(): array {

            $query = "SELECT category AS id FROM Dish_category WHERE dish = ?;";

            $categories = getQueryResults(static::getDB(), $query, true, [$this->id]);
        
            if ($categories === false) return [];

            $result = array_map(fn(array $data) => Category::getById($data['id']), $categories);

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