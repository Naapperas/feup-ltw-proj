<?php
    declare(strict_types=1);

    include_once('model.php');

    class Order extends Model {

        static function create(array $data): array {

            if (count($data) !== 4) throw new Exception("Should pass 4 parameters when creating Order entries.");

            static $createQuery = "INSERT INTO Order VALUES (NULL, ?, ?, ?, ?);";
            static $retrieveQuery = "SELECT * FROM Order WHERE state = ? AND delivery = ? AND user_to_deliver = ? AND driver = ?;";
        
            $creationResults = executeQuery(Order::getDb(), $createQuery, $data);

            if ($creationResults[0])
                return getQueryResults(Order::getDb(), $retrieveQuery, false, $data);

            return array();
        }

        static function update(int $id, array $newData): array {

            if (count($newData) !== 4) throw new Exception("Should pass 4 parameters when updating Order entries.");

            static $updateQuery = "UPDATE Order SET state = ?, delivery = ?. user_to_deliver = ?, driver = ? WHERE id = ?;";
            static $retrieveQuery = "SELECT * FROM Order WHERE id = ?;";
        
            $newData[] = $id;

            $updateResults = executeQuery(Order::getDb(), $updateQuery, $newData);

            if ($updateResults[0])
                return getQueryResults(Order::getDb(), $retrieveQuery, false, array($id));
                
            return array();
        }

        static function delete(int $id): array {

            static $retrieveQuery = "SELECT * FROM Order WHERE id = ?;";
            static $deleteQuery = "DELETE FROM Order WHERE id = ?;";
        
            $object = getQueryResults(Order::getDb(), $retrieveQuery, false, array($id));
            
            $deleteResults = executeQuery(Order::getDb(), $deleteQuery, array($id));

            if ($deleteResults[0])
                return $object;
            
            return array();
        }

        static function get(int|array $id): array {

            $retrieveQuery = "SELECT * FROM Order WHERE id = ?";

            if (gettype($id) == 'integer') {
                
                $object = getQueryResults(Order::getDb(), $retrieveQuery, false, array($id));

                return $object ? $object : array();
                
            } else {
                
                $result = array();
                
                foreach($id as $entry_id) {
                    $object = getQueryResults(Order::getDb(), $retrieveQuery, false, array($entry_id));
    
                    if ($object)
                        $result[] = $object;

                }

                return $result;
            }
        }
    }
?>