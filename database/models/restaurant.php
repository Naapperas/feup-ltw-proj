<?php
    declare(strict_types=1);

    include_once('model.php');

    class Restaurant extends Model {

        static function create(array $data): array {

            if (count($data) !== 3) throw new Exception("Should pass 3 parameters when creating Restaurant entries.");

            static $createQuery = "INSERT INTO Restaurant VALUES (NULL, ?, ?, ?);";
            static $retrieveQuery = "SELECT * FROM Restaurant WHERE name = ? AND owner = ? AND address = ?;";
        
            $creationResults = executeQuery(Restaurant::getDb(), $createQuery, $data);

            if ($creationResults[0])
                return getQueryResults(Restaurant::getDb(), $retrieveQuery, false, $data);

            return array();
        }

        static function update(int $id, array $newData): array {

            if (count($newData) !== 3) throw new Exception("Should pass 3 parameters when updating Restaurant entries.");

            static $updateQuery = "UPDATE Restaurant SET owner = ?, address = ?. name = ? WHERE id = ?;";
            static $retrieveQuery = "SELECT * FROM Restaurant WHERE id = ?;";
        
            $newData[] = $id;

            $updateResults = executeQuery(Restaurant::getDb(), $updateQuery, $newData);

            if ($updateResults[0])
                return getQueryResults(Restaurant::getDb(), $retrieveQuery, false, array($id));
                
            return array();
        }

        static function delete(int $id): array {

            static $retrieveQuery = "SELECT * FROM Restaurant WHERE id = ?;";
            static $deleteQuery = "DELETE FROM Restaurant WHERE id = ?;";
        
            $object = getQueryResults(Restaurant::getDb(), $retrieveQuery, false, array($id));
            
            $deleteResults = executeQuery(Restaurant::getDb(), $deleteQuery, array($id));

            if ($deleteResults[0])
                return $object;
            
            return array();
        }

        static function get(int|array|null $id = null, bool $named = false): array {

            if ($id === null) {

                $query = "SELECT * FROM Restaurant;";

                return getQueryResults(Restaurant::getDb(), $query, true);
            }

            $retrieveQuery = "SELECT * FROM Restaurant WHERE id = ?";

            if (gettype($id) == 'integer') {
                
                $object = getQueryResults(Restaurant::getDb(), $retrieveQuery, false, array($id));

                return $object ? $object : array();
                
            } else {
                
                $result = array();
                
                if ($named) {

                    $retrieveQuery = "SELECT * FROM Restaurant WHERE ";
                    
                    $attrs = array();
                    $values = array();

                    foreach($id as $attribute=>$value) {
                        $attrs[] = sprintf("%s = ?", $attribute);
                        $values[] = $value;
                    }

                    $retrieveQuery .= implode(" AND ", $attrs);
                    $retrieveQuery .= ";";

                    $result = getQueryResults(Restaurant::getDb(), $retrieveQuery, true, $values);
                } else
                    foreach($id as $entry_id) {
                        $object = getQueryResults(Restaurant::getDb(), $retrieveQuery, false, array($entry_id));
        
                        if ($object)
                            $result[] = $object;

                    }

                return $result;
            }
        }
    }
?>