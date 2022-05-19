<?php
    declare(strict_types=1);

    include(dirname(__DIR__).'/connection.php');

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
            $props = array_filter(array_keys($values), fn($prop) => strcmp($prop, "id"), ARRAY_FILTER_USE_KEY);
            $prop_names = implode(', ', $props);
            $prop_values = implode(', ', array_map(fn($s) => ":$s", $props));
    
            $table = static::getTableName();
            $query = "INSERT INTO $table ($prop_names) VALUES ($prop_values);";
            $results = executeQuery(static::getDB(), $query, $values);
    
            if ($results[0] === false)
                return null;
    
            return static::get(intval(static::getDB()->lastInsertId($table)));
        }
    
        static function get(int|array $data = null, int $limit = null): array|static|null {
            $table = static::getTableName();
            $query = "SELECT * FROM $table";
    
            if ($data === null) {
    
                if ($limit !== null)
                    $query .= " LIMIT $limit";
    
                $query .= ';';
    
                $queryResults = getQueryResults(static::getDb(), $query, true);
    
                if ($queryResults === false)
                    return [];
    
                $results = array();
                foreach ($queryResults as $result) {
                    $results[] = static::fromArray($result);
                }
    
                return $results;
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
                    $attrs[] = sprintf("%s = ?", $attribute);
                    $values[] = $value;
                }
    
                $query .= implode(" AND ", $attrs);
    
                if ($limit !== null) {
                    $query .= " LIMIT $limit";
                }
    
                $query .= ";";
    
                $queryResults = getQueryResults(static::getDb(), $query, true, $values);
    
                if ($queryResults === false)
                    return [];
    
                $results = array();
                foreach ($queryResults as $result) {
                    $results[] = static::fromArray($result);
                }
    
                return $results;
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