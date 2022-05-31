<?php
    declare(strict_types=1);

    require_once('model.php');
    require_once('restaurant.php');
    require_once('dish.php');

    class Menu extends Model {

        public string $name;
        public float $price;

        public int $restaurant;

        protected static function getTableName(): string {
            return 'Menu';
        }

        public function getRestaurant(): ?Restaurant {
            return Restaurant::getById($this->restaurant);
        }

        public function addDish(Dish $dish): bool {

            $query = 'INSERT INTO Dish_menu VALUES (?, ?);';

            list($success,) = executeQuery(static::getDB(), $query, [$dish->id, $this->id]);

            if ($success) {

                $this->price += $dish->price;
                while(!$this->update()); // yikes

                return true;
            } else return false;
        }

        public function getThumbnail(): string {
            $src = "/assets/pictures/menu/$this->id.webp";
            
            if (!file_exists(dirname(dirname(__DIR__)).$src)) {
                $src = "/assets/pictures/menu/default.webp";
            }

            return $src;
        }
    }
?>