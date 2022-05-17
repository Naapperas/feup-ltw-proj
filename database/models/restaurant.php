<?php
    declare(strict_types=1);

    include_once('model.php');
    include_once('user.php');

    class Restaurant extends Model {

        public string $name;
        public string $address;

        public int $owner;

        protected static function getTableName(): string {
            return "Restaurant";
        }

        public function getOwner(): ?User {
            return User::get($this->owner);
        }
    }
?>