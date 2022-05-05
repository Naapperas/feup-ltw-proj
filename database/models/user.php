<?php
    declare(strict_types=1);

    include_once('model.php');

    class User extends Model {

        static function create(array $data): array {

            if (count($data) !== 7) throw new Exception("Should pass 7 parameters when creating User entries.");

            static $createQuery = "INSERT INTO User VALUES (NULL, ?, ?, ?, ?, ?, ?, ?);";
            static $retrieveQuery = "SELECT * FROM User WHERE name = ? AND password = ? AND address = ? AND phone_number = ?;";
        
            $creationResults = executeQuery(User::getDb(), $createQuery, $data);

            if ($creationResults[0])
                return getQueryResults(User::getDb(), $retrieveQuery, false, $data);

            return array();
        }

        static function update(int $id, array $newData): array {

            if (count($newData) !== 7) throw new Exception("Should pass 7 parameters when updating User entries.");

            static $updateQuery = "UPDATE User SET name = ?, password = ?. address = ?, phone_number = ?, is_owner = ?, is_client = ?, is_driver = ? WHERE id = ?;";
            static $retrieveQuery = "SELECT * FROM User WHERE id = ?;";
        
            $newData[] = $id;

            $updateResults = executeQuery(User::getDb(), $updateQuery, $newData);

            if ($updateResults[0])
                return getQueryResults(User::getDb(), $retrieveQuery, false, array($id));
                
            return array();
        }

        static function delete(int $id): array {

            static $retrieveQuery = "SELECT * FROM User WHERE id = ?;";
            static $deleteQuery = "DELETE FROM User WHERE id = ?;";
        
            $object = getQueryResults(User::getDb(), $retrieveQuery, false, array($id));
            
            $deleteResults = executeQuery(User::getDb(), $deleteQuery, array($id));

            if ($deleteResults[0])
                return $object;
            
            return array();
        }

        static function get(int|array|null $id): array {

            if ($id === null) {

                $query = "SELECT * FROM User;";

                return getQueryResults(User::getDb(), $query, true);
            }

            $retrieveQuery = "SELECT * FROM User WHERE id = ?;";

            if (gettype($id) == 'integer') {
                
                $object = getQueryResults(User::getDb(), $retrieveQuery, false, array($id));

                return $object ? $object : array();
                
            } else {
                
                $result = array();
                
                foreach($id as $entry_id) {
                    $object = getQueryResults(User::getDb(), $retrieveQuery, false, array($entry_id));
    
                    if ($object)
                        $result[] = $object;

                }

                return $result;
            }
        }
    }
?>