<?php
    declare(strict_types=1);

    require_once(dirname(__DIR__).'/connection.php');
    require_once('query.php');

    abstract class Model implements JsonSerializable {
        public readonly int $id;
    
        protected static function getDB(): PDO {
            return getDBConnection(dirname(__DIR__).'/main.db');
        }
    
        public function __toString(): string {
            $class = get_called_class();
            $props = get_class_vars($class);

            foreach ($props as $prop => $_)
                $attrs[] = sprintf('%s="%s"', $prop, $this->{$prop});

            return sprintf('%s(%s)', $class, implode(', ', $attrs));
        }

        protected abstract static function getTableName(): string;
    
        protected static function fromArray(array $values): static {
            $class = get_called_class();
            $object = new $class();
            $props = get_class_vars($class);

            foreach ($props as $prop => $value)
                $object->{$prop} = strcmp(gettype($value), "array") ? $values[$prop] : array();
    
            return $object;
        }
    
        static function create(array $values): ?static {
            unset($values['id']);
            $props = array_filter(array_keys($values), fn(string $prop) => strcmp($prop, "id"), ARRAY_FILTER_USE_KEY);
            $prop_names = implode('", "', $props);
            $prop_values = implode(', ', array_map(fn($s) => ":$s", $props));
    
            $table = static::getTableName();
            $query = "INSERT INTO \"$table\" (\"$prop_names\") VALUES ($prop_values);";
            $results = executeQuery(static::getDB(), $query, $values);
    
            if ($results[0] === false)
                return null;
    
            return static::getById(intval(static::getDB()->lastInsertId($table)));
        }
    
        static function getById(int|array $idOrIds, OrderClause $order = null): static|array|null {
            $table = static::getTableName();
            $query = "SELECT * FROM \"$table\" WHERE ";

            if (is_int($idOrIds)) {
                $id = $idOrIds;
    
                $queryBuilder = new Equals('id', $id);

                $query .= $queryBuilder->getQueryString();
                $query .= ';';
        
                $results = getQueryResults(static::getDB(), $query, false, $queryBuilder->getQueryValues());
        
                if ($results === false)
                    return null;
        
                return static::fromArray($results);
            } else {

                $queryBuilder = new In('id', $idOrIds);

                $query .= $queryBuilder->getQueryString();

                if ($order !== null) 
                    $query .= $order->getQueryString();

                $query .= ';';

                $results = getQueryResults(static::getDB(), $query, true, $queryBuilder->getQueryValues());
        
                if ($results === false)
                    return [];
        
                return array_map(fn (array $modelData) => static::fromArray($modelData), $results);
            }
        }

        static function getAll(int $limit = null, OrderClause $order = null): array {

            $table = static::getTableName();
            $query = "SELECT * FROM \"$table\"";

            if ($order !== null)
                $query .= $order->getQueryString();
                
            if ($limit !== null)
                $query .= " LIMIT $limit"; // TODO: turn into LimitClause
            
            $query .= ';';

            $queryResults = getQueryResults(static::getDb(), $query, true);

            if ($queryResults === false)
                return [];

            return array_map(fn(array $result) => static::fromArray($result), $queryResults);
        }

        static function getWithFilters(array $filters, int $limit = null, OrderClause $order = null): array {

            if (count($filters) === 0) return static::getAll($limit, $order);

            $table = static::getTableName();
            $query = "SELECT * FROM \"$table\" WHERE ";

            $queryBuilder = count($filters) === 1 ? $filters[0] : new AndClause($filters);

            $query .= $queryBuilder->getQueryString();

            if ($order !== null)
                $query .= $order->getQueryString();
                
            if ($limit !== null)
                $query .= " LIMIT $limit"; // TODO: same as above

            $query .= ";";

            $queryResults = getQueryResults(static::getDb(), $query, true, $queryBuilder->getQueryValues());

            if ($queryResults === false)
                return [];

            return array_map(fn(array $result) => static::fromArray($result), $queryResults);
        }
    
        function delete(): bool {
            $table = static::getTableName();
            $query = "DELETE FROM \"$table\" WHERE \"id\" = ?;";
    
            $results = executeQuery(static::getDB(), $query, [$this->id]);
            return $results[0];
        }
    
        function update(): bool {
            $table = static::getTableName();
    
            $props = get_object_vars($this);
            unset($props["id"]);
    
            foreach ($props as $prop => $_)
                if ($prop !== 'id' && strcmp(gettype($prop), "array") /* In case any child model has arrays (many-to-many) */)
                    $subquery[] = "$prop = :$prop";
            
            $subquery = implode(', ', $subquery);
    
            $query = "UPDATE \"$table\" SET $subquery WHERE \"id\" = :id;";
    
            $results = executeQuery(static::getDB(), $query, [...$props, 'id' => $this->id]);
            return $results[0];
        }

        protected function getSerializableProperties(): array {
            return [];
        }

        function jsonSerialize() {
            return array_merge(get_object_vars($this), $this->getSerializableProperties());
        }
    }

    trait HasImage {
        public readonly int $id;

        protected abstract static function getImageFolder(): string;

        protected static function getNumberOfDefaultImages(): int {
            return 1;
        }

        function getImagePath(): string {
            $folder = static::getImageFolder();
            $src = "/assets/pictures/$folder/$this->id.webp";
            
            if (!file_exists(dirname(dirname(__DIR__)).$src)) {
                $n = static::getNumberOfDefaultImages();

                if ($n === 1) {
                    $src = "/assets/pictures/$folder/default.svg";
                } else {
                    $p = $this->id % $n;
                    $src = "/assets/pictures/$folder/default$p.svg";
                }
            }
            
            return $src;
        }

        function delete(): bool {
            if (parent::delete()) {
                $folder = static::getImageFolder();
                $src = "/assets/pictures/$folder/$this->id.webp";
                unlink(dirname(dirname(__DIR__)).$src);
                return true;
            }

            return false;
        }

        function getSerializableProperties(): array {
            return array_merge(parent::getSerializableProperties(), ['image' => $this->getImagePath()]);
        }
    }
?>