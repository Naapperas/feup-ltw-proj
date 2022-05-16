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

        unset($values['id']);
        $props = array_filter(array_keys($values), function ($prop) { return strcmp($prop, "id"); }, ARRAY_FILTER_USE_KEY);
        $prop_names = implode(', ', $props);
        $prop_values = implode(', ', array_map(function ($s) { return ":$s"; }, $props));

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

        if (!strcmp(gettype($data), 'integer')) {
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

class UserNew extends ModelNew {
    public string $name;
    public string $email;
    public string $address;
    public string $phone_number;
    public string $full_name;

    protected static function getTableName(): string {
        return "User";
    }

    function update(): bool { // in here we could update other 'inter-model' related attributes
        return parent::update();
    }
}

// print_r(UserNew::get());
// print_r(UserNew::get(limit: 2));
// print_r(UserNew::get(1));
// print_r(UserNew::get(array('address' => 'addr')));
// print_r(UserNew::get(array('address' => 'addr'), limit: 2));

// $user = UserNew::get(1);
// 
// print_r($user);
// 
// if ($user !== null) {
// 
//     $user->name = 'fabio';
//     $user->update();
// 
//     print_r(UserNew::get(1));
// }

?>