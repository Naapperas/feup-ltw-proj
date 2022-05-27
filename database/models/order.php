<?php
    declare(strict_types=1);

    require_once('model.php');
    require_once('user.php');

    class Order extends Model {

        public string $state;
        public bool $delivery;

        public int $user_to_deliver;
        public int $driver;

        protected static function getTableName(): string {
            return "Order";
        }

        public function getDriver(): ?User {
            return User::get($this->driver);
        }
        
        public function getUserToDeliver(): ?User {
            return User::get($this->user_to_deliver);
        }
    }
?>