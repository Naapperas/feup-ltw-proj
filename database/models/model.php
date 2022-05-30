<?php
    declare(strict_types=1);

    require_once(dirname(__DIR__).'/connection.php');
    require_once('filters.php');

    abstract class Model {
        public readonly int $id;
    
        protected static function getDB(): PDO {
            return getDBConnection(dirname(__DIR__).'/main.db');
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
            $prop_names = implode(', ', $props);
            $prop_values = implode(', ', array_map(fn($s) => ":$s", $props));
    
            $table = static::getTableName();
            $query = "INSERT INTO $table ($prop_names) VALUES ($prop_values);";
            $results = executeQuery(static::getDB(), $query, $values);
    
            if ($results[0] === false)
                return null;
    
            return static::getById(intval(static::getDB()->lastInsertId($table)));
        }
    
        static function getById(int|array $idOrIds): static|array|null {
            
            $table = static::getTableName();
            $query = "SELECT * FROM $table WHERE ";

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
                $query .= ';';

                $results = getQueryResults(static::getDB(), $query, true, $queryBuilder->getQueryValues());
        
                if ($results === false)
                    return [];
        
                return array_map(fn (array $modelData) => static::fromArray($modelData), $results);
            }
        }

        static function getAll(int $limit = null): array {

            $table = static::getTableName();
            $query = "SELECT * FROM $table";

            if ($limit !== null)
                $query .= " LIMIT $limit";

            $query .= ';';

            $queryResults = getQueryResults(static::getDb(), $query, true);

            if ($queryResults === false)
                return [];

            return array_map(fn(array $result) => static::fromArray($result), $queryResults);
        }

        static function get(int|array $data = null, int $limit = null, bool $exact = true, array $filters = null): array|static|null {
            $table = static::getTableName();
            $query = "SELECT * FROM $table";
    
            if ($data === null) {
    
                if ($limit !== null)
                    $query .= " LIMIT $limit";
    
                $query .= ';';
    
                $queryResults = getQueryResults(static::getDb(), $query, true);
    
                if ($queryResults === false)
                    return [];
    
                return array_map(fn(array $result) => static::fromArray($result), $queryResults);
            }
    
            $query .= ' WHERE ';
    
            if (is_int($data)) {
                $id = $data;
    
                $query .= 'id = ?;';
    
                // LIMIT does not make sense in this case since we are only getting one single Model instance
    
                $results = getQueryResults(static::getDB(), $query, false, [$id]);
        
                if ($results === false)
                    return null;
        
                return static::fromArray($results);
            } else {
                        
                $attrs = array();
                $values = array();
    
                foreach($data as $attribute=>$value) {

                    $attrs[] = sprintf(sprintf("%%s %s ?", $exact ? "=" : "LIKE"), $attribute);
                    $values[] = $value;
                }
    
                $query .= implode(" AND ", $attrs);
    
                if ($limit !== null) {
                    $query .= " LIMIT $limit";
                }
    
                $query .= ";";
    
                if (!$exact) {
                    $values = array_map(fn ($value) => "%$value%", $values);
                }

                $queryResults = getQueryResults(static::getDb(), $query, true, $values);
    
                if ($queryResults === false)
                    return [];

                return array_map(fn(array $result) => static::fromArray($result), $queryResults);
            }
        }
    
        function delete(): bool {
            $table = static::getTableName();
            $query = "DELETE FROM $table WHERE id = ?;";
    
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
    
            $query = "UPDATE $table SET $subquery WHERE id = :id;";
    
            $results = executeQuery(static::getDB(), $query, [...$props, 'id' => $this->id]);
            return $results[0];
        }
    }
?>