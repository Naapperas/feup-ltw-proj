<?php
    declare(strict_types=1);

    require_once(__DIR__.'/model.php');
    require_once(__DIR__.'/user.php');
    require_once(__DIR__.'/restaurant.php');
    require_once(__DIR__.'/response.php');
    require_once(__DIR__.'/query.php');

    class Review extends Model {

        public int $score;
        public string $text;
        public string $review_date;

        public int $client;
        public int $restaurant;

        protected static function getTableName(): string {
            return "Review";
        }

        public function getUser(): ?User {
            return User::getById($this->client);
        }

        public function getRestaurant(): ?Restaurant {
            return Restaurant::getById($this->restaurant);
        }

        public function getResponse(): ?Response {
            return Response::getWithFilters([new Equals('review', $this->id)])[0];
        }
    }
?>