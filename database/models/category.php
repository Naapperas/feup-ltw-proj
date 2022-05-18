<?php
    declare(strict_types=1);

    include_once('model.php');
    include_once('user.php');

    class Category extends Model {
        public string $name;

        protected static function getTableName(): string {
            return 'Category';
        }
    }
?>