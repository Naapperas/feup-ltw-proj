<?php
    declare(strict_types=1);

    include_once('model.php');

    class Review extends Model {

        static function create(array $data): array {

            if (count($data) !== 4) throw new Exception("Should pass 4 parameters when creating Review entries.");

            static $createQuery = "INSERT INTO Review VALUES (NULL, ?, ?, ?, ?);";
            static $retrieveQuery = "SELECT * FROM Review WHERE score = ? AND text = ? AND client = ? AND restaurant = ?;";
        
            $creationResults = executeQuery(Review::getDb(), $createQuery, $data);

            if ($creationResults[0])
                return getQueryResults(Review::getDb(), $retrieveQuery, false, $data);

            return array();
        }

        static function update(int $id, array $newData): array {

            if (count($newData) !== 4) throw new Exception("Should pass 4 parameters when updating Review entries.");

            static $updateQuery = "UPDATE Review SET score = ?, text = ?. client = ?, restaurant = ? WHERE id = ?;";
            static $retrieveQuery = "SELECT * FROM Review WHERE id = ?;";
        
            $newData[] = $id;

            $updateResults = executeQuery(Review::getDb(), $updateQuery, $newData);

            if ($updateResults[0])
                return getQueryResults(Review::getDb(), $retrieveQuery, false, array($id));
                
            return array();
        }

        static function delete(int $id): array {

            static $retrieveQuery = "SELECT * FROM Review WHERE id = ?;";
            static $deleteQuery = "DELETE FROM Review WHERE id = ?;";
        
            $object = getQueryResults(Review::getDb(), $retrieveQuery, false, array($id));
            
            $deleteResults = executeQuery(Review::getDb(), $deleteQuery, array($id));

            if ($deleteResults[0])
                return $object;
            
            return array();
        }

        static function get(int|array|null $id): array {

            if ($id === null) {

                $query = "SELECT * FROM Review;";

                return getQueryResults(Review::getDb(), $query, true);
            }

            $retrieveQuery = "SELECT * FROM Review WHERE id = ?;";

            if (gettype($id) == 'integer') {
                
                $object = getQueryResults(Review::getDb(), $retrieveQuery, false, array($id));

                return $object ? $object : array();
                
            } else {
                
                $result = array();
                
                foreach($id as $entry_id) {
                    $object = getQueryResults(Review::getDb(), $retrieveQuery, false, array($entry_id));
    
                    if ($object)
                        $result[] = $object;

                }

                return $result;
            }
        }
    }
?>