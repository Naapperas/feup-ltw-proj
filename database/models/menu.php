<?php
    declare(strict_types=1);

    include_once('model.php');
    include_once('restaurant.php');

    class Menu extends Model {

        public string $name;
        public float $price;

        public int $restaurant;

        protected static function getTableName(): string {
            return 'Menu';
        }

        public function getRestaurant(): ?Restaurant {
            return Restaurant::get($this->restaurant);
        }
    }
?>