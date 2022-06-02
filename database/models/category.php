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

    trait HasCategories {
        public readonly int $id;
        
        protected abstract static function getDB(): PDO;
        protected abstract static function getTableName(): string;
        protected abstract static function getCategoryTableColumn(): string;

        public static function getByCategoryIds(array $categoryIds): array {
            $table = static::getTableName();
            $column = static::getCategoryTableColumn();
            $models = [];
            $query = "SELECT $column AS id FROM {$table}_category WHERE category = ?;";

            foreach($categoryIds as $categoryId) {
                $queryResults = getQueryResults(static::getDB(), $query, true, [$categoryId]);

                if ($queryResults === false) $queryResults = [];

                $models = array_merge($models, array_map(fn (array $data) => static::getById($data['id']), $queryResults));
            }

            return $models;
        }

        public function getCategories(): array {
            $table = static::getTableName();
            $column = static::getCategoryTableColumn();
            $query = "SELECT category AS id FROM {$table}_category WHERE $column = ?;";

            $categories = getQueryResults(static::getDB(), $query, true, [$this->id]);
        
            if ($categories === false) return [];

            $result = array_map(fn(array $data) => Category::getById($data['id']), $categories);

            return $result;
        }

        public function hasCategory(int $categoryID) : bool {
            $table = static::getTableName();
            $column = static::getCategoryTableColumn();
            $query = "SELECT * FROM {$table}_category WHERE $column = ? AND category = ?;";

            $queryResults = getQueryResults(static::getDB(), $query, true, [$this->id, $categoryID]);
        
            if ($queryResults === false) return false;

            return count($queryResults) > 0;
        }

        public function setCategories(array $categories) : bool {
            $table = static::getTableName();
            $column = static::getCategoryTableColumn();
            $deleteQuery = "DELETE FROM {$table}_category WHERE $column = ?;";

            list($success,) = executeQuery(static::getDB(), $deleteQuery, [$this->id]);

            if (count($categories) === 0) return $success;

            // HACK
            // using this query format we avoid making multiple queries to the DB,
            // with the downside of 'having' to hardcode the id into the query itself, 
            // but since that id comes from the model itself, there should be no problem (unless the DB is breached)
            $query = sprintf("INSERT INTO {$table}_category VALUES (%d, ?)", $this->id); 

            for($i = 1; $i < sizeof($categories); $i++) {
                $query.= sprintf(", (%d, ?)", $this->id);
            }

            $query .= ";";

            list($success,) = executeQuery(static::getDB(), $query, $categories);
        
            return $success;
        }
    }
?>