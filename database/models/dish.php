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
    }
?>