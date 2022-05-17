<?php
    declare(strict_types=1);

    include_once('model.php');
    include_once('user.php');
    include_once('restaurant.php');

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