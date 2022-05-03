<?php
    declare(strict_types=1);

    include_once('model.php');

    class Dish extends Model {

        static function create(array $data): array {

            if (count($data) !== 3) throw new Exception("Should pass 3 parameters when creating Dish entries.");

            static $createQuery = "INSERT INTO Dish VALUES (NULL, ?, ?, ?);";
            static $retrieveQuery = "SELECT * FROM Dish WHERE name = ? AND price = ? AND restaurant = ?;";
        
            $creationResults = executeQuery(Dish::getDb(), $createQuery, $data);

            if ($creationResults[0])
                return getQueryResults(Dish::getDb(), $retrieveQuery, false, $data);

            return array();
        }

        static function update(int $id, array $newData): array {

            if (count($newData) !== 3) throw new Exception("Should pass 3 parameters when updating Dish entries.");

            static $updateQuery = "UPDATE Dish SET name = ?, price = ?. restaurant = ? WHERE id = ?;";
            static $retrieveQuery = "SELECT * FROM Dish WHERE id = ?;";
        
            $newData[] = $id;

            $updateResults = executeQuery(Dish::getDb(), $updateQuery, $newData);

            if ($updateResults[0])
                return getQueryResults(Dish::getDb(), $retrieveQuery, false, array($id));
                
            return array();
        }

        static function delete(int $id): array {

            static $retrieveQuery = "SELECT * FROM Dish WHERE id = ?;";
            static $deleteQuery = "DELETE FROM Dish WHERE id = ?;";
        
            $object = getQueryResults(Dish::getDb(), $retrieveQuery, false, array($id));
            
            $deleteResults = executeQuery(Dish::getDb(), $deleteQuery, array($id));

            if ($deleteResults[0])
                return $object;
            
            return array();
        }

        static function get(int|array $id): array {

            $retrieveQuery = "SELECT * FROM Dish WHERE id = ?";

            if (gettype($id) == 'integer') {
                
                $object = getQueryResults(Dish::getDb(), $retrieveQuery, false, array($id));

                return $object ? $object : array();
                
            } else {
                
                $result = array();
                
                foreach($id as $entry_id) {
                    $object = getQueryResults(Dish::getDb(), $retrieveQuery, false, array($entry_id));
    
                    if ($object)
                        $result[] = $object;

                }

                return $result;
            }
        }
    }
?>