<?php
declare(strict_types=1);

require_once(dirname(__DIR__).'/connection.php');

abstract class ModelNew {
    public readonly int $id;

    protected static function getDB(): PDO {
        return getDBConnection(dirname(__DIR__).'/main.db');
    }

    protected abstract static function getTableName(): string;

    protected static function fromArray(array $values): static {
        $class = get_called_class();
        $object = new $class();
        $props = get_class_vars($class);

        foreach ($props as $prop => $_) 
            $object->{$prop} = $values[$prop];

        return $object;
    }

    static function create(array $values): ?static {
        $props = array_keys($values);
        $prop_names = implode(', ', $props);
        $prop_values = implode(', ', array_map(function ($s) {
            return ":$s";
        }, $props));

        $table = static::getTableName();
        $query = "INSERT INTO $table ($prop_names) VALUES ($prop_values);";
        $results = executeQuery(static::getDB(), $query, $values);

        if ($results[0] === false)
            return null;

        return static::get(intval(static::getDB()->lastInsertId($table)));
    }

    static function get(int $id): ?static {
        $table = static::getTableName();
        $query = "SELECT * FROM $table WHERE id = ?;";
        $results = getQueryResults(static::getDB(), $query, false, [$id]);

        if ($results === false)
            return null;

        return static::fromArray($results);
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
            if ($prop !== 'id')
                $subquery[] = "$prop = :$prop";
        
        $subquery = implode(', ', $subquery);

        $query = "UPDATE $table SET $subquery WHERE id = :id;";

        $results = executeQuery(static::getDB(), $query, [...$props, 'id' => $this->id]);
        return $results[0];
    }
}

class UserNew extends ModelNew {
    public string $name;
    public string $email;
    public string $address;
    public string $phone_number;
    public string $full_name;

    protected static function getTableName(): string {
        return "User";
    }
}

?>
