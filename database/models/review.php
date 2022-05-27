<?php
    declare(strict_types=1);

    require_once('model.php');
    require_once('user.php');
    require_once('restaurant.php');

    class Review extends Model {

        public int $score;
        public string $text;

        public int $client;
        public int $restaurant;

        protected static function getTableName(): string {
            return "Review";
        }

        public function getUser(): ?User {
            return User::get($this->client);
        }

        public function getRestaurant(): ?Restaurant {
            return Restaurant::get($this->restaurant);
        }
    }
?>