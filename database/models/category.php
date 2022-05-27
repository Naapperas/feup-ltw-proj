<?php
    declare(strict_types=1);

    require_once('model.php');
    require_once('user.php');

    class Category extends Model {
        public string $name;

        protected static function getTableName(): string {
            return 'Category';
        }
    }
?>